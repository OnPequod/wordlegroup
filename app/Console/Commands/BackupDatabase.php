<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {type=daily : Backup type (daily, weekly, monthly)}';

    protected $description = 'Backup the database to Backblaze B2';

    public function handle(): int
    {
        $type = $this->argument('type');

        if (!in_array($type, ['daily', 'weekly', 'monthly'])) {
            $this->error("Invalid backup type: {$type}. Must be daily, weekly, or monthly.");
            return self::FAILURE;
        }

        $disk = Storage::disk('backblaze');

        if (!config('filesystems.disks.backblaze.key')) {
            $this->error('Backblaze not configured. Set BACKBLAZE_KEY_ID and BACKBLAZE_SECRET in environment.');
            return self::FAILURE;
        }

        $connection = config('database.default');
        $db = config("database.connections.{$connection}");

        if (!in_array($db['driver'], ['mysql', 'mariadb', 'pgsql'])) {
            $this->error("No dump strategy for driver '{$db['driver']}' (connection '{$connection}').");
            return self::FAILURE;
        }

        $this->info("Starting {$type} database backup...");

        $filename = $this->getFilename($type);
        $s3Path = "backups/{$type}/{$filename}";
        $tempFile = storage_path("app/temp_{$filename}");

        // Ensure temp directory exists
        if (!is_dir(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }

        $this->info("Dumping database: {$db['database']}");

        // The password travels in the environment, never in argv where any user on
        // the host can read it out of `ps`.
        if ($db['driver'] === 'pgsql') {
            $env = ['PGPASSWORD' => $db['password']];
            $dump = sprintf(
                'pg_dump --host=%s --port=%s --username=%s --no-owner --no-acl %s',
                escapeshellarg($db['host']),
                escapeshellarg((string) $db['port']),
                escapeshellarg($db['username']),
                escapeshellarg($db['database'])
            );
        } else {
            $env = ['MYSQL_PWD' => $db['password']];
            $dump = sprintf(
                'mysqldump -h %s -P %s -u %s --single-transaction --quick --lock-tables=false --ssl=false %s',
                escapeshellarg($db['host']),
                escapeshellarg((string) $db['port']),
                escapeshellarg($db['username']),
                escapeshellarg($db['database'])
            );
        }

        $result = Process::timeout(300)->env($env)->run(
            "{$dump} | gzip > " . escapeshellarg($tempFile)
        );

        if (!$result->successful()) {
            $this->error("Database dump failed: " . $result->errorOutput());
            @unlink($tempFile);
            return self::FAILURE;
        }

        if (!file_exists($tempFile) || filesize($tempFile) === 0) {
            $this->error("Backup file is empty or was not created.");
            @unlink($tempFile);
            return self::FAILURE;
        }

        // A pipeline's exit code is gzip's, not the dump tool's — a dump that dies
        // halfway still yields a valid, non-empty gzip that would replace a good
        // backup. Only the dump tool's own completion trailer proves the stream ran
        // to the end, so refuse to upload anything that lacks it.
        if (!$this->dumpIsComplete($tempFile, $db['driver'])) {
            $this->error("Dump is truncated or corrupt (no completion trailer); refusing to upload it.");
            @unlink($tempFile);
            return self::FAILURE;
        }

        $fileSize = $this->formatBytes(filesize($tempFile));
        $this->info("Backup created and verified: {$fileSize}");

        // Upload to Backblaze (overwrites existing file with same name)
        $this->info("Uploading to Backblaze: {$s3Path}");

        try {
            $disk->put($s3Path, fopen($tempFile, 'r'));
            $this->info("Upload complete.");
        } catch (\Exception $e) {
            $this->error("Backblaze upload failed: " . $e->getMessage());
            @unlink($tempFile);
            return self::FAILURE;
        }

        // Clean up temp file
        @unlink($tempFile);

        $this->info("{$type} backup completed successfully: {$filename}");

        return self::SUCCESS;
    }

    protected function dumpIsComplete(string $gzFile, string $driver): bool
    {
        $integrity = Process::timeout(120)->run('gzip -t ' . escapeshellarg($gzFile));

        if (!$integrity->successful()) {
            return false;
        }

        $trailer = Process::timeout(120)->run(
            'gzip -cd ' . escapeshellarg($gzFile) . ' | tail -c 512'
        );

        $marker = $driver === 'pgsql'
            ? 'PostgreSQL database dump complete'
            : 'Dump completed';

        return $trailer->successful() && str_contains($trailer->output(), $marker);
    }

    /**
     * Get filename based on backup type.
     * - daily: weekday name (monday.sql.gz) - 7 rotating files
     * - weekly: week of month (week-1.sql.gz) - 5 rotating files
     * - monthly: month name (january.sql.gz) - 12 rotating files
     */
    protected function getFilename(string $type): string
    {
        $now = Carbon::now();

        return match ($type) {
            'daily' => strtolower($now->format('l')) . '.sql.gz',        // monday, tuesday, etc.
            'weekly' => 'week-' . $now->weekOfMonth . '.sql.gz',         // week-1 to week-5
            'monthly' => strtolower($now->format('F')) . '.sql.gz',      // january, february, etc.
            default => $now->format('Y-m-d') . '.sql.gz',
        };
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
