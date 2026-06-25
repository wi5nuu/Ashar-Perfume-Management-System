<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return self::create([
            'ip_address' => $ip,
            'reason' => $reason,
            'attempts' => 10,
            'blocked_until' => now()->addMinutes($minutes),
        ]);
    }

    public static function recordAttempt(string $ip): int
    {
        $record = self::where('ip_address', $ip)->first();
        if ($record) {
            $record->increment('attempts');
            return $record->attempts;
        }
        self::create(['ip_address' => $ip, 'attempts' => 1]);
        return 1;
    }
}
