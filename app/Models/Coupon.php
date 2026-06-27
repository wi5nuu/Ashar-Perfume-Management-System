<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'is_percentage',
        'expiration_date',
        'customer_id',
        'max_usage',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'is_percentage' => 'boolean',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'max_usage' => 'integer',
        'used_count' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
