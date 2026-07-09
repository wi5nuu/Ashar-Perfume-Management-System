<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $hidden = ['unit_cost', 'subtotal'];

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'received_quantity',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'quantity'           => 'integer',
        'received_quantity'  => 'integer',
        'unit_cost'          => 'decimal:2',
        'subtotal'           => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Remaining quantity to be received.
     */
    public function getRemainingAttribute(): int
    {
        return max(0, $this->quantity - $this->received_quantity);
    }
}
