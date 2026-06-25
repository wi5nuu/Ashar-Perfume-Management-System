<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleRedemption extends Model
{
    protected $fillable = [
        'name', 'description', 'credits_required', 'reward_type',
        'reward_value', 'meta', 'is_active', 'max_uses_per_customer',
    ];

    protected $casts = [
        'meta' => 'json',
        'is_active' => 'boolean',
    ];
}
