<?php

namespace App\Services\Security;

use App\Models\LoginActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ActivityMonitor
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const SUSPICIOUS_WINDOW_MINUTES = 10;
    private const SUSPICIOUS_DISTANCE_KM = 100;

    public function checkLoginAttempt(string $identifier, string $ip): array
    {
        $cacheKey = "login_attempts:{$identifier}";
        $attempts = Cache::get($cacheKey, ['count' => 0, 'first' => now()]);
        $attempts['count']++;
        $attempts['last_ip'] = $ip;

        Cache::put($cacheKey, $attempts, now()->addMinutes(self::LOCKOUT_MINUTES));

        if ($attempts['count'] >= self::MAX_LOGIN_ATTEMPTS) {
            Log::warning("Account locked due to failed attempts", [
                'identifier' => $identifier,
                'ip' => $ip,
                'attempts' => $attempts['count'],
            ]);
            return ['blocked' => true, 'remaining' => 0];
        }

        return ['blocked' => false, 'remaining' => self::MAX_LOGIN_ATTEMPTS - $attempts['count']];
    }

    public function resetLoginAttempts(string $identifier): void
    {
        Cache::forget("login_attempts:{$identifier}");
    }

    public function isSuspiciousLogin(User $user, string $ip): bool
    {
        $lastActivity = LoginActivity::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$lastActivity) return false;

        $lastIp = $lastActivity->ip_address;
        if ($lastIp === $ip) return false;

        $timeDiff = $lastActivity->created_at->diffInMinutes(now());
        if ($timeDiff < self::SUSPICIOUS_WINDOW_MINUTES) {
            Log::warning("Suspicious login detected", [
                'user_id' => $user->id,
                'email' => $user->email,
                'previous_ip' => $lastIp,
                'current_ip' => $ip,
                'time_diff_minutes' => $timeDiff,
            ]);
            return true;
        }

        return false;
    }

    public function getActiveSessions(int $userId): int
    {
        return LoginActivity::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();
    }

    public function cleanOldLogs(): int
    {
        return LoginActivity::where('created_at', '<', Carbon::now()->subDays(90))->delete();
    }
}
