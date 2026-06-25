<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 10) {
            $fail('Kata sandi minimal 10 karakter.');
            return;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Kata sandi harus mengandung huruf kapital (A-Z).');
            return;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $fail('Kata sandi harus mengandung huruf kecil (a-z).');
            return;
        }

        if (!preg_match('/[0-9]/', $value)) {
            $fail('Kata sandi harus mengandung angka (0-9).');
            return;
        }

        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Kata sandi harus mengandung karakter khusus (!@#$% etc).');
            return;
        }
    }
}
