<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $hidden = ['ip_address', 'user_agent'];

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'city',
        'country',
        'is_suspicious',
    ];

    /**
     * Disable standard updated_at since logs are immutable.
     */
    protected $casts = [
        'is_suspicious' => 'boolean',
    ];

    public const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
