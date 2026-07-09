<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPrice extends Model
{
    protected $hidden = ['unit_cost'];

    protected $fillable = [
        'supplier_id',
        'product_id',
        'unit_cost',
        'minimum_order_qty',
        'last_quoted_at',
    ];

    protected $casts = [
        'unit_cost'        => 'decimal:2',
        'minimum_order_qty' => 'integer',
        'last_quoted_at'   => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get or create supplier price for a given product.
     */
    public static function getPrice(int $supplierId, int $productId): ?self
    {
        return static::where('supplier_id', $supplierId)
            ->where('product_id', $productId)
            ->first();
    }
}
