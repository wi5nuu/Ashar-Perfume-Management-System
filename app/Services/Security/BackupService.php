<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\Process\Process;
use ZipArchive;

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

        // where.exe (Windows) / which (Linux) — return path baris pertama jika ditemukan
        $which = PHP_OS_FAMILY === 'Windows' ? 'where.exe mysqldump 2>NUL' : 'which mysqldump 2>/dev/null';
        $found = trim((string) shell_exec($which));
        if (!empty($found)) {
            // Binary ditemukan di PATH — tidak perlu prefix direktori
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

    public function create(bool $noEncrypt = false): array
    {
        $db       = config('database.connections.mysql');
        $filename = 'apms-backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        $mysqlBinPath = $this->findBinaryPath();
        $process = new Process([
            $mysqlBinPath . 'mysqldump',
            '--protocol=TCP',
            '--host='  . $db['host'],
            '--port='  . ($db['port'] ?? 3306),
            '--user='  . $db['username'],
            '--routines',
            '--single-transaction',
            '--quick',
            $db['database'],
        ]);

        $process->setEnv(['MYSQL_PWD' => $db['password'], 'MYSQL_TCP_PORT' => $db['port'] ?? '3306']);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Backup failed', ['error' => $process->getErrorOutput()]);
            return ['success' => false, 'error' => 'Gagal membuat backup database: ' . $process->getErrorOutput()];
        }

        // Tulis output ke file — cek return value agar tidak silent fail
        $written = file_put_contents($filepath, $process->getOutput());
        if ($written === false) {
            return ['success' => false, 'error' => 'Gagal menulis file backup ke disk. Periksa permission folder dan disk space.'];
        }

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            return ['success' => false, 'error' => 'File backup kosong setelah ditulis.'];
        }

        $originalSize  = filesize($filepath);
        $encryptedPath = $filepath . '.enc';

        if (!$noEncrypt && config('security.backup.encryption_enabled', false)) {
            $key = config('app.key');
            $iv  = random_bytes(16);
            // Baca file SQL untuk dienkripsi
            $data      = file_get_contents($filepath);
            $encrypted = $iv . openssl_encrypt(
                $data, 'aes-256-cbc',
                substr(hash('sha256', $key), 0, 32),
                OPENSSL_RAW_DATA, $iv
            );
            unset($data); // bebaskan memory segera
            $encWritten = file_put_contents($encryptedPath, $encrypted);
            if ($encWritten === false) {
                return ['success' => false, 'error' => 'Gagal menulis file backup terenkripsi.'];
            }
            unlink($filepath);
            $finalPath = $encryptedPath;
        } else {
            $finalPath = $filepath;
        }

        $this->cleanupOldBackups();

        return [
            'success'       => true,
            'filename'      => basename($finalPath),
            'path'          => $finalPath,
            'size'          => filesize($finalPath),
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
        $sqlPath     = $filepath;
        $tmpCreated  = false;

        try {
            if ($isEncrypted) {
                $key       = config('app.key');
                $data      = file_get_contents($filepath);
                $iv        = substr($data, 0, 16);
                $encrypted = substr($data, 16);
                $decrypted = openssl_decrypt(
                    $encrypted, 'aes-256-cbc',
                    substr(hash('sha256', $key), 0, 32),
                    OPENSSL_RAW_DATA, $iv
                );
                if ($decrypted === false) {
                    return ['success' => false, 'error' => 'Gagal mendekripsi backup. Kunci tidak valid atau file rusak.'];
                }
                $sqlPath    = $filepath . '.tmp.sql';
                file_put_contents($sqlPath, $decrypted);
                $tmpCreated = true;
                unset($data, $decrypted); // bebaskan memory segera
            }

            $db           = config('database.connections.mysql');
            $mysqlBinPath = $this->findBinaryPath();

            $process = new Process([
                $mysqlBinPath . 'mysql',
                '--protocol=TCP',
                '--host='  . $db['host'],
                '--port='  . ($db['port'] ?? 3306),
                '--user='  . $db['username'],
                $db['database'],
            ]);

            $process->setEnv(['MYSQL_PWD' => $db['password'], 'MYSQL_TCP_PORT' => $db['port'] ?? '3306']);
            $process->setTimeout(600);

            // Pakai file handle agar tidak load seluruh SQL ke memory (hindari OOM)
            $handle = fopen($sqlPath, 'rb');
            $process->setInput($handle);
            $process->run();
            fclose($handle);

            if (!$process->isSuccessful()) {
                Log::error('Restore failed', ['error' => $process->getErrorOutput()]);
                return ['success' => false, 'error' => 'Gagal merestore database: ' . $process->getErrorOutput()];
            }

            return ['success' => true, 'message' => 'Database berhasil direstore.'];

        } finally {
            // Selalu hapus file tmp, bahkan jika exception terjadi
            if ($tmpCreated && file_exists($sqlPath)) {
                @unlink($sqlPath);
            }
        }
    }

    public function list(): array
    {
        // Scan semua file di backup dir lalu filter by ekstensi yang valid
        // Hindari glob('*.sql') yang juga match '*.sql.enc'
        $allowed = ['sql', 'enc', 'zip', 'xlsx'];
        $files   = [];

        if (is_dir($this->backupPath)) {
            foreach (scandir($this->backupPath) as $entry) {
                if ($entry === '.' || $entry === '..') continue;

                // Hanya proses file dengan prefix backup APMS
                if (!str_starts_with($entry, 'apms-backup-')) continue;

                $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) continue;

                $fullPath = $this->backupPath . DIRECTORY_SEPARATOR . $entry;
                if (!is_file($fullPath)) continue;

                $files[] = $fullPath;
            }
        }

        $backups = [];
        foreach ($files as $path) {
            $size = @filesize($path);
            if ($size === false) {
                Log::warning('BackupService::list() gagal baca ukuran file', ['path' => $path]);
                $size = 0;
            }
            $mtime = @filemtime($path);
            $backups[] = [
                'filename'     => basename($path),
                'size'         => $size,
                'created_at'   => $mtime ? date('Y-m-d H:i:s', $mtime) : now()->toDateTimeString(),
                'is_encrypted' => str_ends_with($path, '.enc'),
            ];
        }

        // Urutkan terbaru dulu berdasarkan waktu modifikasi
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

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

    /**
     * Backup semua tabel ke CSV dalam satu ZIP file.
     * Setiap tabel = satu file CSV terpisah di dalam ZIP.
     */
    public function createCsv(): array
    {
        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipName   = "apms-backup-csv-{$timestamp}.zip";
        $zipPath   = $this->backupPath . DIRECTORY_SEPARATOR . $zipName;
        $tempDir   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "apms-csv-{$timestamp}";

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tables    = DB::select('SHOW TABLES');
        $dbName    = config('database.connections.mysql.database');
        $tableKey  = "Tables_in_{$dbName}";
        $totalRows = 0;

        try {
            foreach ($tables as $tableRow) {
                $table   = $tableRow->$tableKey;
                $csvFile = $tempDir . DIRECTORY_SEPARATOR . "{$table}.csv";
                $handle  = @fopen($csvFile, 'w');
                if ($handle === false) {
                    throw new \RuntimeException("Gagal membuat file CSV untuk tabel: {$table}");
                }

                // BOM agar Excel baca UTF-8 dengan benar
                fwrite($handle, "\xEF\xBB\xBF");

                // Header kolom
                $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
                $headers = array_map(fn($c) => $c->Field, $columns);
                fputcsv($handle, $headers);

                // Data — stream per 500 baris, gunakan while bukan do-while
                // agar tabel kosong tidak query tidak perlu
                $offset = 0;
                $chunk  = 500;
                while (true) {
                    $rows = DB::table($table)->offset($offset)->limit($chunk)->get();
                    foreach ($rows as $row) {
                        fputcsv($handle, (array) $row);
                        $totalRows++;
                    }
                    $offset += $chunk;
                    if ($rows->count() < $chunk) break;
                }

                fclose($handle);
            }

            // ZIP semua CSV — close() HARUS berhasil sebelum cleanup
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Gagal membuat file ZIP.');
            }
            $csvFiles = glob($tempDir . DIRECTORY_SEPARATOR . '*.csv') ?: [];
            foreach ($csvFiles as $csvFile) {
                $zip->addFile($csvFile, basename($csvFile));
            }
            // close() return false jika gagal — throw agar finally tetap jalan
            if ($zip->close() === false) {
                throw new \RuntimeException('Gagal menutup file ZIP.');
            }

        } finally {
            // Cleanup temp files selalu dijalankan, bahkan jika exception
            $remaining = glob($tempDir . DIRECTORY_SEPARATOR . '*.csv') ?: [];
            foreach ($remaining as $f) {
                @unlink($f);
            }
            @rmdir($tempDir);
        }

        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            return ['success' => false, 'error' => 'File backup CSV kosong.'];
        }

        Log::info('Backup CSV berhasil', ['filename' => $zipName, 'total_rows' => $totalRows]);

        $this->cleanupOldBackups();

        return [
            'success'    => true,
            'filename'   => $zipName,
            'path'       => $zipPath,
            'size'       => filesize($zipPath),
            'tables'     => count($tables),
            'total_rows' => $totalRows,
        ];
    }

    /**
     * Backup semua tabel ke XLSX (Excel).
     * Setiap tabel = satu sheet di dalam satu file XLSX.
     */
    public function createXlsx(): array
    {
        $timestamp   = now()->format('Y-m-d-H-i-s');
        $filename    = "apms-backup-xlsx-{$timestamp}.xlsx";
        $filepath    = $this->backupPath . DIRECTORY_SEPARATOR . $filename;
        $tables      = DB::select('SHOW TABLES');
        $dbName      = config('database.connections.mysql.database');
        $tableKey    = "Tables_in_{$dbName}";
        $totalRows   = 0;
        $sheetIndex  = 0;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($tables as $tableRow) {
            $table     = $tableRow->$tableKey;
            // Sheet name max 31 karakter (batasan Excel)
            $sheetName = mb_strlen($table) > 31 ? mb_substr($table, 0, 31) : $table;

            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
            $spreadsheet->addSheet($sheet, $sheetIndex++);

            // Header kolom — baris 1
            $columns  = DB::select("SHOW COLUMNS FROM `{$table}`");
            $headers  = array_map(fn($c) => $c->Field, $columns);
            $colCount = count($headers);

            foreach ($headers as $colIdx => $header) {
                $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . '1';
                $sheet->setCellValue($coord, $header);
            }

            // Style header: bold putih, background biru APMS
            if ($colCount > 0) {
                $lastCol     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                $headerRange = "A1:{$lastCol}1";
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a45']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Freeze baris header
                $sheet->freezePane('A2');
            }

            // Data — stream per 500 baris, while bukan do-while agar tabel kosong aman
            $rowNum = 2;
            $offset = 0;
            $chunk  = 500;
            while (true) {
                $rows = DB::table($table)->offset($offset)->limit($chunk)->get();
                foreach ($rows as $row) {
                    foreach (array_values((array) $row) as $colIdx => $value) {
                        $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . $rowNum;
                        $sheet->setCellValue($coord, is_scalar($value) ? $value : json_encode($value));
                    }
                    $rowNum++;
                    $totalRows++;
                }
                $offset += $chunk;
                if ($rows->count() < $chunk) break;
            }

            // Auto-size kolom (max 50 karakter)
            foreach (range(1, $colCount) as $colIdx) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $dim = $sheet->getColumnDimension($col);
                $dim->setAutoSize(true);
            }
        }

        // Jika tidak ada tabel sama sekali, jangan simpan file kosong
        if ($spreadsheet->getSheetCount() === 0) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            return ['success' => false, 'error' => 'Tidak ada tabel untuk di-backup.'];
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filepath);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            return ['success' => false, 'error' => 'File backup XLSX kosong.'];
        }

        Log::info('Backup XLSX berhasil', ['filename' => $filename, 'total_rows' => $totalRows]);

        $this->cleanupOldBackups();

        return [
            'success'    => true,
            'filename'   => $filename,
            'path'       => $filepath,
            'size'       => filesize($filepath),
            'tables'     => count($tables),
            'total_rows' => $totalRows,
        ];
    }

    protected function cleanupOldBackups(): void
    {
        try {
            $files   = $this->list();
            $daily   = config('security.backup.retention_daily', 7);
            $weekly  = config('security.backup.retention_weekly', 4);
            $monthly = config('security.backup.retention_monthly', 3);

            $toKeep = [];
            $now    = now();

            foreach ($files as $backup) {
                try {
                    $date    = \Carbon\Carbon::parse($backup['created_at']);
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
                } catch (\Exception $e) {
                    // Jika satu file gagal diparse, skip — jangan hapus file itu
                    $toKeep[] = $backup['filename'];
                    Log::warning('cleanupOldBackups: gagal parse tanggal', [
                        'file'  => $backup['filename'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            foreach ($files as $backup) {
                if (!in_array($backup['filename'], $toKeep)) {
                    $this->delete($backup['filename']);
                }
            }
        } catch (\Exception $e) {
            Log::error('cleanupOldBackups gagal', ['error' => $e->getMessage()]);
        }
    }
}
