<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IpBlacklist extends Model
{
    protected $table = 'ip_blacklist';

    public const UPDATED_AT = null;

    protected $fillable = ['ip_address', 'reason', 'attempts', 'blocked_until'];

    protected $casts = [
        'blocked_until' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('blocked_until')->orWhere('blocked_until', '>', now());
        });
    }

    public static function isBlocked(string $ip): bool
    {
        return self::active()->where('ip_address', $ip)->exists();
    }

    public static function block(string $ip, string $reason = 'auto', int $minutes = 60): self
    {
        // Use updateOrCreate to avoid duplicate rows when the same IP is
        // blocked multiple times (e.g. burst traffic triggering multiple requests
        // simultaneously before the first insert commits).
        return self::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason'        => $reason,
                'attempts'      => DB::raw('attempts + 1'),
                'blocked_until' => now()->addMinutes($minutes),
            ]
        );
    }

    public static function recordAttempt(string $ip): int
    {
        // Use updateOrCreate so concurrent requests don't create duplicate rows.
        $record = self::updateOrCreate(
            ['ip_address' => $ip],
            ['attempts' => 1]
        );

        if (!$record->wasRecentlyCreated) {
            $record->increment('attempts');
            $record->refresh();
        }

        return $record->attempts;
    }
}
