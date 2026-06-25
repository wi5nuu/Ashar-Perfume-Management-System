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
            $result = $this->backup->create();
            if ($result['success']) {
                return redirect()->route('admin.monitoring.backup')
                    ->with('success', "Backup berhasil: {$result['filename']}");
            }
            return redirect()->route('admin.monitoring.backup')
                ->with('error', "Backup gagal: {$result['error']}");
        } catch (\Exception $e) {
            Log::error('Backup via UI gagal', ['error' => $e->getMessage()]);
            return redirect()->route('admin.monitoring.backup')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function backupDelete(string $encodedFilename)
    {
        $filename = basename(base64_decode($encodedFilename));
        if ($this->backup->delete($filename)) {
            return redirect()->route('admin.monitoring.backup')
                ->with('success', "Backup '{$filename}' berhasil dihapus.");
        }
        return redirect()->route('admin.monitoring.backup')
            ->with('error', "Gagal menghapus backup.");
    }

    public function backupDownload(string $encodedFilename)
    {
        $filename = base64_decode($encodedFilename);
        $path = config('security.backup.path', storage_path('backups')) . DIRECTORY_SEPARATOR . basename($filename);
        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }
        return response()->download($path);
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
