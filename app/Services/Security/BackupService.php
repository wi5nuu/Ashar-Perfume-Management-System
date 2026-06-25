<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupService
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = config('security.backup.path', storage_path('backups'));
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    private function findBinaryPath(): string
    {
        $configPath = config('database.connections.mysql.dump.dump_binary_path');
        if (!empty($configPath)) {
            return rtrim($configPath, '\\/') . DIRECTORY_SEPARATOR;
        }

        $which = PHP_OS_FAMILY === 'Windows' ? 'where.exe mysqldump 2>NUL' : 'which mysqldump 2>/dev/null';
        $found = trim(shell_exec($which));
        if (!empty($found)) {
            return '';
        }

        $paths = [
            'C:\\xampp\\mysql\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.31-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.32-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.33-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.34-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.36-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.37-winx64\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.30\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.32\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.33\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.34\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.35\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 9.0\\bin\\',
        ];
        foreach ($paths as $p) {
            if (file_exists($p . 'mysqldump.exe')) {
                return $p;
            }
        }

        return '';
    }

    public function create(): array
    {
        $db = config('database.connections.mysql');
        $filename = 'apms-backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        $mysqlBinPath = $this->findBinaryPath();
        $process = new Process([
            $mysqlBinPath . 'mysqldump',
            '--protocol=TCP',
            '--host=' . $db['host'],
            '--port=' . ($db['port'] ?? 3306),
            '--user=' . $db['username'],
            '--routines',
            '--single-transaction',
            '--quick',
            $db['database'],
        ]);

        $process->setEnv(['MYSQL_PWD' => $db['password'], 'MYSQL_TCP_PORT' => $db['port'] ?? '3306'] + $_ENV);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Backup failed', ['error' => $process->getErrorOutput()]);
            return ['success' => false, 'error' => 'Gagal membuat backup database: ' . $process->getErrorOutput()];
        }

        file_put_contents($filepath, $process->getOutput());

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            return ['success' => false, 'error' => 'File backup kosong.'];
        }

        $originalSize = filesize($filepath);
        $encryptedPath = $filepath . '.enc';

        if (config('security.backup.encryption_enabled', true)) {
            $key = config('app.key');
            $iv = random_bytes(16);
            $data = file_get_contents($filepath);
            $encrypted = $iv . openssl_encrypt($data, 'aes-256-cbc', substr(hash('sha256', $key), 0, 32), OPENSSL_RAW_DATA, $iv);
            file_put_contents($encryptedPath, $encrypted);
            unlink($filepath);
            $finalPath = $encryptedPath;
        } else {
            $finalPath = $filepath;
        }

        $this->cleanupOldBackups();

        return [
            'success' => true,
            'filename' => basename($finalPath),
            'path' => $finalPath,
            'size' => filesize($finalPath),
            'original_size' => $originalSize,
        ];
    }

    public function restore(string $filename): array
    {
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . basename($filename);

        if (!file_exists($filepath)) {
            return ['success' => false, 'error' => 'File backup tidak ditemukan.'];
        }

        $isEncrypted = str_ends_with($filepath, '.enc');
        $sqlPath = $filepath;

        if ($isEncrypted) {
            $key = config('app.key');
            $data = file_get_contents($filepath);
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', substr(hash('sha256', $key), 0, 32), OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                return ['success' => false, 'error' => 'Gagal mendekripsi backup. Kunci tidak valid atau file rusak.'];
            }
            $sqlPath = $filepath . '.tmp.sql';
            file_put_contents($sqlPath, $decrypted);
        }

        $db = config('database.connections.mysql');
        $mysqlBinPath = $this->findBinaryPath();

        $process = new Process([
            $mysqlBinPath . 'mysql',
            '--protocol=TCP',
            '--host=' . $db['host'],
            '--port=' . ($db['port'] ?? 3306),
            '--user=' . $db['username'],
            $db['database'],
        ]);

        $process->setEnv(['MYSQL_PWD' => $db['password'], 'MYSQL_TCP_PORT' => $db['port'] ?? '3306'] + $_ENV);
        $process->setTimeout(300);
        $process->setInput(file_get_contents($sqlPath));
        $process->run();

        if ($isEncrypted && file_exists($sqlPath)) {
            unlink($sqlPath);
        }

        if (!$process->isSuccessful()) {
            Log::error('Restore failed', ['error' => $process->getErrorOutput()]);
            return ['success' => false, 'error' => 'Gagal merestore database: ' . $process->getErrorOutput()];
        }

        return ['success' => true, 'message' => 'Database berhasil direstore.'];
    }

    public function list(): array
    {
        $files = glob($this->backupPath . DIRECTORY_SEPARATOR . 'apms-backup-*.sql*');
        $backups = [];

        foreach ($files as $path) {
            $backups[] = [
                'filename' => basename($path),
                'size' => filesize($path),
                'created_at' => date('Y-m-d H:i:s', filemtime($path)),
                'is_encrypted' => str_ends_with($path, '.enc'),
            ];
        }

        rsort($backups);
        return $backups;
    }

    public function delete(string $filename): bool
    {
        $path = $this->backupPath . DIRECTORY_SEPARATOR . basename($filename);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    protected function cleanupOldBackups(): void
    {
        $files = $this->list();
        $daily = config('security.backup.retention_daily', 7);
        $weekly = config('security.backup.retention_weekly', 4);
        $monthly = config('security.backup.retention_monthly', 3);

        $toKeep = [];
        $now = now();

        foreach ($files as $backup) {
            $date = \Carbon\Carbon::parse($backup['created_at']);
            $daysOld = $date->diffInDays($now);

            if ($daysOld <= $daily) {
                $toKeep[] = $backup['filename'];
            } elseif ($daysOld <= $daily + ($weekly * 7)) {
                if ($date->isMonday()) {
                    $toKeep[] = $backup['filename'];
                }
            } elseif ($date->day <= 7) {
                $toKeep[] = $backup['filename'];
            }
        }

        foreach ($files as $backup) {
            if (!in_array($backup['filename'], $toKeep)) {
                $this->delete($backup['filename']);
            }
        }
    }
}
