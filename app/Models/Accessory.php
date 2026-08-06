<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category',
        'brand',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'current_stock',
        'minimum_stock',
        'unit',
        'image',
        'description',
        'is_active',
        'supplier_id',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'purchase_price'  => 'decimal:2',
        'selling_price'   => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'current_stock'   => 'integer',
        'minimum_stock'   => 'integer',
    ];

    // Kategori aksesori yang tersedia
    public static array $categories = [
        'botol'       => 'Botol Parfum',
        'sarung'      => 'Sarung Botol',
        'alat_peracik'=> 'Alat Peracik',
        'kemasan'     => 'Kemasan / Packaging',
        'label'       => 'Label / Stiker',
        'lainnya'     => 'Lainnya',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    /**
     * Kurangi stok aksesori secara aman.
     * Throws RuntimeException jika stok tidak cukup.
     */
    public function deductStock(int $qty): void
    {
        if ($this->current_stock < $qty) {
            throw new \RuntimeException(
                "Stok aksesori tidak cukup untuk '{$this->name}'. Tersedia: {$this->current_stock}, Dibutuhkan: {$qty}"
            );
        }
        $this->decrement('current_stock', $qty);
    }

    /**
     * Tambah stok aksesori.
     */
    public function addStock(int $qty): void
    {
        $this->increment('current_stock', $qty);
    }
}
