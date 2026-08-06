<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Security\BackupService;
use App\Services\Security\LogViewerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    protected BackupService $backup;
    protected LogViewerService $logViewer;

    public function __construct(BackupService $backup, LogViewerService $logViewer)
    {
        $this->backup = $backup;
        $this->logViewer = $logViewer;
        $this->middleware('can:manage_settings');
    }

    public function backupIndex()
    {
        $backups = $this->backup->list();
        return view('admin.monitoring.backup', compact('backups'));
    }

    public function backupCreate()
    {
        try {
            $result = $this->backup->create(true);
            if ($result['success']) {
                return redirect()->route('admin.monitoring.backup')
                    ->with('success', "Backup SQL berhasil: {$result['filename']}");
            }
            return redirect()->route('admin.monitoring.backup')
                ->with('error', "Backup gagal: {$result['error']}");
        } catch (\Exception $e) {
            Log::error('Backup via UI gagal', ['error' => $e->getMessage()]);
            return redirect()->route('admin.monitoring.backup')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function backupCsv()
    {
        try {
            $result = $this->backup->createCsv();
            if ($result['success']) {
                $rows   = number_format($result['total_rows']);
                $tables = $result['tables'];
                return redirect()->route('admin.monitoring.backup')
                    ->with('success', "Backup CSV berhasil: {$result['filename']} ({$tables} tabel, {$rows} baris)");
            }
            return redirect()->route('admin.monitoring.backup')
                ->with('error', "Backup CSV gagal: {$result['error']}");
        } catch (\Exception $e) {
            Log::error('Backup CSV via UI gagal', ['error' => $e->getMessage()]);
            return redirect()->route('admin.monitoring.backup')
                ->with('error', 'Gagal membuat backup CSV: ' . $e->getMessage());
        }
    }

    public function backupXlsx()
    {
        try {
            $result = $this->backup->createXlsx();
            if ($result['success']) {
                $rows   = number_format($result['total_rows']);
                $tables = $result['tables'];
                return redirect()->route('admin.monitoring.backup')
                    ->with('success', "Backup XLSX berhasil: {$result['filename']} ({$tables} tabel, {$rows} baris)");
            }
            return redirect()->route('admin.monitoring.backup')
                ->with('error', "Backup XLSX gagal: {$result['error']}");
        } catch (\Exception $e) {
            Log::error('Backup XLSX via UI gagal', ['error' => $e->getMessage()]);
            return redirect()->route('admin.monitoring.backup')
                ->with('error', 'Gagal membuat backup XLSX: ' . $e->getMessage());
        }
    }

    public function backupDelete(string $encodedFilename)
    {
        $filename = basename(base64_decode(strtr($encodedFilename, '-_~', '+/=')));

        // Whitelist: hanya ekstensi yang valid, prefix harus apms-backup-
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['sql', 'enc', 'zip', 'xlsx'], true)
            || !str_starts_with($filename, 'apms-backup-')) {
            abort(400, 'Nama file backup tidak valid.');
        }

        if ($this->backup->delete($filename)) {
            return redirect()->route('admin.monitoring.backup')
                ->with('success', "Backup '{$filename}' berhasil dihapus.");
        }
        return redirect()->route('admin.monitoring.backup')
            ->with('error', 'Gagal menghapus backup. File mungkin sudah tidak ada.');
    }

    public function backupDownload(string $encodedFilename)
    {
        $filename = basename(base64_decode(strtr($encodedFilename, '-_~', '+/=')));

        // Whitelist: hanya ekstensi yang valid, prefix harus apms-backup-
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['sql', 'enc', 'zip', 'xlsx'], true)
            || !str_starts_with($filename, 'apms-backup-')) {
            abort(400, 'Nama file backup tidak valid.');
        }

        $path = config('security.backup.path', storage_path('backups'))
                . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        // MIME type berdasarkan ekstensi
        $mime = match($ext) {
            'sql'  => 'application/sql',
            'enc'  => 'application/octet-stream',
            'zip'  => 'application/zip',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };

        // Use StreamedResponse to guarantee correct Content-Type for binary files
        // (xlsx, zip). response()->download() with a headers array can have its
        // Content-Type overridden by Symfony's auto-detection when nosniff is set.
        $fileSize = filesize($path);
        return response()->streamDownload(function () use ($path) {
            $handle = fopen($path, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 8192);
                flush();
            }
            fclose($handle);
        }, $filename, [
            'Content-Type'        => $mime,
            'Content-Length'      => $fileSize,
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    public function logViewer(Request $request)
    {
        $level = $request->get('level');
        $search = $request->get('search');
        $page = (int)$request->get('page', 1);

        $logs = $this->logViewer->getLogs($level, $search, 50, $page);
        $levels = $this->logViewer->getLevels();
        $stats = $this->logViewer->getErrorRate();

        return view('admin.monitoring.logs', compact('logs', 'levels', 'stats', 'level', 'search'));
    }
}
