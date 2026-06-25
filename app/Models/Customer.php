<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
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
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'portal_token' => 'encrypted',
        'nik' => 'encrypted',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // BUG-03 FIX: Relasi yang hilang ke WholesaleOrder
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
