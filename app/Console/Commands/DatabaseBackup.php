<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--filename= : Custom filename}';
    protected $description = 'Backup MySQL database using mysqldump';

    public function handle(): int
    {
        $db = config('database.connections.mysql');
        $filename = $this->option('filename') ?? sprintf('backup-%s.sql', now()->format('Y-m-d-H-i-s'));
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $path = "{$dir}/{$filename}";

        $cmd = sprintf('mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($db['host']), escapeshellarg($db['username']),
            escapeshellarg($db['password']), escapeshellarg($db['database']), escapeshellarg($path));

        $this->info("Backing up to {$filename}...");
        exec($cmd, $output, $code);

        if ($code === 0) {
            $this->info('Backup created: ' . $filename . ' (' . round(filesize($path)/1024/1024, 2) . ' MB)');
            return Command::SUCCESS;
        }
        $this->error('Backup failed');
        return Command::FAILURE;
    }
}
