<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'warehouse_id',
        'current_stock',
        'bulk_stock_ml',
        'minimum_stock',
        'cost_per_unit',
        'expiration_date',
        'batch_number',
        'stock_in',
        'stock_out',
        'supplier_id',
        'date_received',
        'date_sold',
    ];

    // Casting tipe data agar Laravel otomatis mengubahnya menjadi objek Carbon (tanggal)
    protected $casts = [
        'expiration_date' => 'date',
        'date_received' => 'date',
        'date_sold' => 'date',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'bulk_stock_ml' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
    ];

    /**
     * Relasi ke model Product.
     * Satu data inventory dimiliki oleh satu produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'inventory_id');
    }
}