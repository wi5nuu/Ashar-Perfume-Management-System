<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WholesaleOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'branch_id',
        'customer_id',
        'package_target_amount',
        'total_amount',
        'shipping_cost',
        'status',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'shipping_courier',
        'delivery_handler',
        'handler_id',
        'packing_days',
        'estimated_arrival',
        'notes',
        'barcode',
        'tracking_number',
        'cancellation_reason',
        'confirmed_at',
        'reviewed_at',
        'packed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'estimated_arrival' => 'datetime',
        'confirmed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'package_target_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'packing_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(WholesaleOrderDetail::class);
    }
}
