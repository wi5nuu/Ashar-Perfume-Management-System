<?php

namespace App\Services\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadSecurityService
{
    private array $allowedMimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'text/csv', 'text/plain', 'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    private array $blockedExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bat', 'cmd', 'pl', 'py', 'htaccess', 'shtml'];

    private int $maxFileSize = 5120;

    public function validate(UploadedFile $file): bool
    {
        $mime = $file->getMimeType();
        if (!in_array($mime, $this->allowedMimes, true)) {
            Log::warning("FILE UPLOAD REJECTED: Invalid MIME type", [
                'mime' => $mime,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'user_id' => auth()->id(),
            ]);
            throw new \RuntimeException("Tipe file tidak diizinkan: {$mime}");
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->blockedExtensions, true)) {
            throw new \RuntimeException("Ekstensi file tidak diizinkan.");
        }

        if ($file->getSize() > $this->maxFileSize * 1024) {
            throw new \RuntimeException("Ukuran file maksimal {$this->maxFileSize}KB.");
        }

        if ($mime === 'image/jpeg' || $mime === 'image/png') {
            $this->validateImageContent($file);
        }

        return true;
    }

    private function validateImageContent(UploadedFile $file): void
    {
        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \RuntimeException("File gambar rusak atau tidak valid.");
        }

        $exif = @exif_read_data($file->getPathname(), 'EXEC', true);
        if ($exif !== false && $this->containsPhpCode($file)) {
            throw new \RuntimeException("File gambar mengandung kode mencurigakan.");
        }
    }

    private function containsPhpCode(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getPathname());
        return preg_match('/<\?(php|=)\s/i', $content) === 1;
    }

    public function store(UploadedFile $file, string $path = 'uploads'): string
    {
        $this->validate($file);

        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $stored = Storage::disk('public')->putFileAs($path, $file, $filename);

        if (!$stored) {
            throw new \RuntimeException("Gagal menyimpan file.");
        }

        Log::info("File uploaded securely", [
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $filename,
            'path' => $stored,
            'size' => $file->getSize(),
            'user_id' => auth()->id(),
        ]);

        return $stored;
    }

    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}
