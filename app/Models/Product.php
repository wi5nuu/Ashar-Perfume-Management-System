<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

protected $fillable = [
    'name',
    'barcode',
    'product_category_id',
    'brand',
    'size',
    'unit',
    'purchase_price',
    'selling_price',
    'wholesale_price',
    'initial_stock',
    'image',
    'description',
    'is_active',
    'track_inventory',
    'supplier_id',
    'minimum_stock',
    'is_refill',
    'refill_price_per_ml',
];

protected $hidden = [
    'purchase_price',
];

protected $casts = [
    'is_active'           => 'boolean',
    'track_inventory'     => 'boolean',
    'is_refill'           => 'boolean',
    'refill_price_per_ml' => 'decimal:2',
];

public function scopeRefill($query)
{
    return $query->where('is_refill', true);
}

public function scopeNonRefill($query)
{
    return $query->where('is_refill', false);
}

public function category()
{
    // Hubungkan ke Model ProductCategory
    return $this->belongsTo(ProductCategory::class, 'product_category_id');
}

public function inventories()
{
    return $this->hasMany(Inventory::class);
}

public function supplier()
{
    return $this->belongsTo(Supplier::class);
}
}