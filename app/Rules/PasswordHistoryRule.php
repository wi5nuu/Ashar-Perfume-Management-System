<?php

namespace App\Rules;

use App\Models\PasswordHistory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class PasswordHistoryRule implements ValidationRule
{
    public function __construct(protected int $userId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $historyCount = config('security.password_policy.history_count', 5);

        $recentPasswords = PasswordHistory::where('user_id', $this->userId)
            ->latest()
            ->take($historyCount)
            ->get();

        foreach ($recentPasswords as $history) {
            if (Hash::check($value, $history->password)) {
                $fail("Kata sandi tidak boleh sama dengan {$historyCount} kata sandi sebelumnya.");
                return;
            }
        }
    }
}
