<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WholesaleOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'wholesale_order_id',
        'product_id',
        'wholesale_product_id',
        'product_name',
        'quantity',
        'volume_ml',
        'unit',
        'price',
        'price_per_ml',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'price_per_ml' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'volume_ml' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(WholesaleOrder::class, 'wholesale_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function wholesaleProduct()
    {
        return $this->belongsTo(WholesaleProduct::class);
    }
}
