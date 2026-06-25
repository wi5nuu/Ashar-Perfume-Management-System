<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleCustomerRedemption extends Model
{
    protected $fillable = [
        'customer_id', 'redemption_id', 'credits_spent',
        'status', 'used_at', 'expires_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function redemption()
    {
        return $this->belongsTo(WholesaleRedemption::class);
    }
}
