<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class PullDatabaseBackup extends Command
{
    protected $signature = 'db:pull-backup
                            {--file= : Specific backup file to pull (default: latest)}
                            {--list : List available backups}
                            {--no-import : Download only, do not import}';

    protected $description = 'Pull database backup from Backblaze and optionally import it';

    public function handle(): int
    {
        $disk = Storage::disk('backblaze');

        if ($this->option('list')) {
            return $this->listBackups($disk);
        }

        $file = $this->option('file') ?? $this->findLatestBackup($disk);

        if (!$file) {
            $this->error('No backup files found.');
            return 1;
        }

        $this->info("Downloading: {$file}");

        $localPath = storage_path('app/dumps/' . basename($file));

        // Ensure dumps directory exists
        if (!is_dir(dirname($localPath))) {
            mkdir(dirname($localPath), 0755, true);
        }

        // Stream download for large files
        $stream = $disk->readStream($file);
        if (!$stream) {
            $this->error("Failed to open stream for {$file}");
            return 1;
        }

        $localFile = fopen($localPath, 'w');
        while (!feof($stream)) {
            fwrite($localFile, fread($stream, 8192));
        }
        fclose($stream);
        fclose($localFile);

        $this->info("Downloaded to: {$localPath}");

        // Handle gzipped files
        if (str_ends_with($localPath, '.gz')) {
            $this->info('Decompressing...');
            $unzippedPath = substr($localPath, 0, -3);
            Process::run("gunzip -kf {$localPath}");
            $localPath = $unzippedPath;
        }

        if ($this->option('no-import')) {
            $this->info('Skipping import (--no-import flag set)');
            return 0;
        }

        // Import to database
        $database = config('database.connections.mysql.database');
        $this->info("Importing to database: {$database}");

        if (!$this->confirm("This will overwrite the {$database} database. Continue?")) {
            return 0;
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $result = Process::run(
            "mysql -h{$host} -P{$port} -u{$username} -p{$password} {$database} < {$localPath}"
        );

        if ($result->failed()) {
            $this->error('Import failed: ' . $result->errorOutput());
            return 1;
        }

        $this->info('Import complete.');

        return 0;
    }

    protected function listBackups($disk): int
    {
        $files = collect($disk->allFiles())
            ->filter(fn($f) => str_ends_with($f, '.sql') || str_ends_with($f, '.sql.gz'))
            ->sortDesc()
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No backup files found.');
            return 0;
        }

        $this->info('Available backups:');
        foreach ($files as $file) {
            $size = number_format($disk->size($file) / 1024 / 1024, 2) . ' MB';
            $this->line("  {$file} ({$size})");
        }

        return 0;
    }

    protected function findLatestBackup($disk): ?string
    {
        return collect($disk->allFiles())
            ->filter(fn($f) => str_ends_with($f, '.sql') || str_ends_with($f, '.sql.gz'))
            ->sortDesc()
            ->first();
    }
}
