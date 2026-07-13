<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'channel', 'event', 'enabled'];
    protected $casts = ['enabled' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }

    public static function isEnabled(User $user, string $event, string $channel = 'database'): bool
    {
        $pref = static::where('user_id', $user->id)->where('event', $event)->where('channel', $channel)->first();
        return $pref ? $pref->enabled : true;
    }
}
