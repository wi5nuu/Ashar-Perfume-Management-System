<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnownDevice extends Model
{
    public $timestamps = true;
    public const UPDATED_AT = null;

    protected $hidden = ['ip_address', 'user_agent', 'fingerprint'];

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'fingerprint',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function isKnown(int $userId, string $ip, string $userAgent): bool
    {
        $fp = self::fingerprint($ip, $userAgent);
        return self::where('user_id', $userId)
            ->where('fingerprint', $fp)
            ->exists();
    }

    public static function register(int $userId, string $ip, string $userAgent): self
    {
        $fp = self::fingerprint($ip, $userAgent);
        return self::firstOrCreate([
            'user_id' => $userId,
            'fingerprint' => $fp,
        ], [
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public static function fingerprint(string $ip, string $userAgent): string
    {
        return hash('sha256', $ip . '|' . $userAgent);
    }
}
