<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {type=daily : Backup type (daily, weekly, monthly)}
                            {--cleanup : Clean up old backups based on retention policy}';

    protected $description = 'Backup the database to Backblaze B2';

    protected array $retention = [
        'daily' => 7,
        'weekly' => 4,
        'monthly' => 12,
    ];

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

        $this->info("Starting {$type} database backup...");

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $database = config('database.connections.mysql.database');
        $filename = "{$database}_{$type}_{$timestamp}.sql.gz";
        $s3Path = "backups/{$type}/{$filename}";
        $tempFile = storage_path("app/temp_{$filename}");

        // Ensure temp directory exists
        if (!is_dir(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }

        // Build mysqldump command
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $this->info("Dumping database: {$database}");

        $result = Process::timeout(300)->run(
            "mysqldump -h {$host} -P {$port} -u {$username} -p'{$password}' " .
            "--single-transaction --quick --lock-tables=false --ssl=false {$database} | gzip > {$tempFile}"
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

        $fileSize = $this->formatBytes(filesize($tempFile));
        $this->info("Backup created: {$fileSize}");

        // Upload to Backblaze
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

        // Clean up old backups if requested
        if ($this->option('cleanup')) {
            $this->cleanupOldBackups($disk, $type);
        }

        $this->info("{$type} backup completed successfully: {$filename}");

        return self::SUCCESS;
    }

    protected function cleanupOldBackups($disk, string $type): void
    {
        $retention = $this->retention[$type];
        $path = "backups/{$type}";

        $this->info("Cleaning up old {$type} backups (keeping {$retention})...");

        $files = collect($disk->files($path))
            ->filter(fn($file) => str_ends_with($file, '.sql.gz'))
            ->sortDesc()
            ->values();

        if ($files->count() <= $retention) {
            $this->info("No cleanup needed. {$files->count()} backups exist.");
            return;
        }

        $toDelete = $files->slice($retention);

        foreach ($toDelete as $file) {
            $disk->delete($file);
            $this->info("Deleted old backup: " . basename($file));
        }

        $this->info("Cleanup complete. Deleted " . $toDelete->count() . " old backups.");
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
