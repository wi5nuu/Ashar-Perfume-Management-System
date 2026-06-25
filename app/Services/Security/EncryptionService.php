<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Crypt;

class EncryptionService
{
    public function encryptField(?string $value): ?string
    {
        if ($value === null) return null;
        return Crypt::encryptString($value);
    }

    public function decryptField(?string $value): ?string
    {
        if ($value === null) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function encryptArray(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->encryptField($data[$field]);
            }
        }
        return $data;
    }

    public function decryptArray(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->decryptField($data[$field]);
            }
        }
        return $data;
    }

    public function isEncrypted(string $value): bool
    {
        return str_starts_with($value, 'eyJ') || (strlen($value) > 50 && !str_contains($value, ' '));
    }
}
