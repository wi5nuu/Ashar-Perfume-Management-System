<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $hidden = [
        'portal_token',
        'nik',
        'phone',
        'email',
    ];

    protected $fillable = [
        'customer_code',
        'nik',
        'name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'type',
        'address',
        'is_active',
        'points',
        'aroma_preferences',
        'portal_token',
        'branch_id',
        'loyalty_rank',
        'total_credits_earned',
        'total_credits_spent',
        'gold_points',
        'lifetime_spend',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'portal_token' => 'encrypted',
        'nik' => 'encrypted',
        'points' => 'integer',
        'aroma_preferences' => 'array',
        'total_credits_earned' => 'integer',
        'total_credits_spent' => 'integer',
        'gold_points' => 'integer',
        'lifetime_spend' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = 'CUST-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function wholesaleOrders()
    {
        return $this->hasMany(WholesaleOrder::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Total omzet dari semua channel (retail + grosir)
     */
    public function getTotalRevenueAttribute(): float
    {
        $retail    = (float) $this->transactions->sum('total_amount');
        $wholesale = (float) $this->wholesaleOrders
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        return $retail + $wholesale;
    }
}
