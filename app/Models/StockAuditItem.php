<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAuditItem extends Model
{
    protected $fillable = [
        'stock_audit_id',
        'product_id',
        'system_stock',
        'physical_stock',
        'discrepancy',
        'notes',
    ];

    public function audit()
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->physical_stock !== null) {
                $item->discrepancy = $item->physical_stock - $item->system_stock;
            }
        });
    }
}
