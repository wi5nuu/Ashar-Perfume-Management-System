<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'receipt_number', 'product_id', 'quantity', 'supplier_name',
        'delivery_person', 'origin', 'received_date', 'unit_cost',
        'total_cost', 'notes', 'recorded_by', 'branch_id',
    ];

    protected $casts = [
        'received_date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->receipt_number = 'GR-' . now()->format('Ymd') . '-' . str_pad(
                static::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT
            );
            $model->total_cost = $model->quantity * $model->unit_cost;
        });
    }
}
