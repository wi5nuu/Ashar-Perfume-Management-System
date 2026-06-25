<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogViewerService
{
    public function getLogs(string $level = null, string $search = null, int $perPage = 50, int $page = 1): array
    {
        $logPath = storage_path('logs');
        $files = File::glob($logPath . '/laravel-*.log');

        if (empty($files)) {
            $files = File::glob($logPath . '/laravel.log');
        }

        $entries = [];

        foreach ($files as $file) {
            $content = File::get($file);
            $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}).*?\] (\w+)\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2}|$)/s';

            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $timestamp = $match[1] ?? '';
                $environment = $match[2] ?? '';
                $logLevel = $match[3] ?? '';
                $message = trim($match[4] ?? '');

                if ($level && $logLevel !== strtoupper($level)) {
                    continue;
                }

                if ($search && !str_contains(strtolower($message), strtolower($search))) {
                    continue;
                }

                $entries[] = [
                    'timestamp' => $timestamp,
                    'environment' => $environment,
                    'level' => $logLevel,
                    'message' => $message,
                    'file' => basename($file),
                ];
            }
        }

        $entries = array_reverse($entries);
        $total = count($entries);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($entries, $offset, $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int)ceil($total / $perPage),
        ];
    }

    public function getLevels(): array
    {
        return ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
    }

    public function getErrorRate(): array
    {
        $logPath = storage_path('logs');
        $files = File::glob($logPath . '/laravel-*.log');

        if (empty($files)) {
            $files = File::glob($logPath . '/laravel.log');
        }

        $errorCount = 0;
        $warningCount = 0;
        $infoCount = 0;
        $totalLines = 0;

        foreach ($files as $file) {
            $content = File::get($file);
            $lines = substr_count($content, "\n") + 1;
            $totalLines += $lines;
            $errorCount += preg_match_all('/\.ERROR:/', $content);
            $warningCount += preg_match_all('/\.WARNING:/', $content);
            $infoCount += preg_match_all('/\.INFO:/', $content);
        }

        return [
            'total_lines' => $totalLines,
            'errors' => $errorCount,
            'warnings' => $warningCount,
            'info' => $infoCount,
            'error_rate' => $totalLines > 0 ? round(($errorCount / $totalLines) * 100, 2) : 0,
        ];
    }
}
