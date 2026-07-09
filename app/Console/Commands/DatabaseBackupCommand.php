<?php

namespace App\Console\Commands;

use App\Services\Security\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'backup:database
                            {--list : Tampilkan daftar backup}
                            {--delete= : Hapus file backup tertentu}
                            {--restore= : Restore database dari file backup}
                            {--no-encrypt : Nonaktifkan enkripsi untuk backup ini}';

    protected $description = 'Buat, daftar, atau restore backup database dengan enkripsi';

    public function handle(BackupService $backup): int
    {
        if ($this->option('list')) {
            return $this->listBackups($backup);
        }

        if ($delete = $this->option('delete')) {
            return $this->deleteBackup($backup, $delete);
        }

        if ($restore = $this->option('restore')) {
            return $this->restoreBackup($backup, $restore);
        }

        return $this->createBackup($backup);
    }

    protected function createBackup(BackupService $backup): int
    {
        $this->info('Membuat backup database...');

        $result = $backup->create($this->option('no-encrypt'));

        if ($result['success']) {
            $size = round($result['size'] / 1024 / 1024, 2);
            $this->info("Backup berhasil: {$result['filename']} ({$size} MB)");
            Log::info('Backup database berhasil', ['file' => $result['filename']]);
            return Command::SUCCESS;
        }

        $this->error("Backup gagal: {$result['error']}");
        return Command::FAILURE;
    }

    protected function listBackups(BackupService $backup): int
    {
        $backups = $backup->list();

        if (empty($backups)) {
            $this->warn('Belum ada backup.');
            return Command::SUCCESS;
        }

        $headers = ['File', 'Ukuran', 'Tanggal', 'Enkripsi'];
        $rows = [];

        foreach ($backups as $b) {
            $size = round($b['size'] / 1024 / 1024, 2) . ' MB';
            $rows[] = [
                $b['filename'],
                $size,
                $b['created_at'],
                $b['is_encrypted'] ? 'AES-256-CBC' : 'Tidak',
            ];
        }

        $this->table($headers, $rows);
        $totalSize = array_sum(array_column($backups, 'size'));
        $this->info("Total: " . count($backups) . " backup (" . round($totalSize / 1024 / 1024, 2) . " MB)");

        return Command::SUCCESS;
    }

    protected function restoreBackup(BackupService $backup, string $filename): int
    {
        if (!$this->confirm("PERINGATAN: Ini akan MENIMPA seluruh database! Lanjutkan?")) {
            $this->info('Dibatalkan.');
            return Command::SUCCESS;
        }

        $this->info('Merestore database...');
        $result = $backup->restore($filename);

        if ($result['success']) {
            $this->info("Restore berhasil: {$result['message']}");
            return Command::SUCCESS;
        }

        $this->error("Restore gagal: {$result['error']}");
        return Command::FAILURE;
    }

    protected function deleteBackup(BackupService $backup, string $filename): int
    {
        if ($backup->delete($filename)) {
            $this->info("Backup '{$filename}' berhasil dihapus.");
            return Command::SUCCESS;
        }

        $this->error("Gagal menghapus backup '{$filename}'.");
        return Command::FAILURE;
    }
}
