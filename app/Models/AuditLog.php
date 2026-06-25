<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'target_model',
        'target_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'tags',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'tags' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
