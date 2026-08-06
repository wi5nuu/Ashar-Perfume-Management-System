<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class GoodsReceipt extends Model
{
    protected $hidden = ['unit_cost', 'total_cost', 'notes'];

    protected $fillable = [
        'receipt_number', 'product_id', 'quantity', 'supplier_name',
        'delivery_person', 'origin', 'received_date', 'expiration_date',
        'unit_cost', 'total_cost', 'notes', 'recorded_by', 'branch_id',
    ];

    protected $casts = [
        'received_date'   => 'date',
        'expiration_date' => 'date',
        'quantity'        => 'integer',
        'unit_cost'       => 'decimal:2',
        'total_cost'      => 'decimal:2',
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
            // Use a DB-level lock to prevent race conditions when multiple
            // receipts are created simultaneously on the same day.
            $sequence = DB::transaction(function () {
                $count = static::whereDate('created_at', now())
                    ->lockForUpdate()
                    ->count();
                return $count + 1;
            });

            $model->receipt_number = 'GR-' . now()->format('Ymd') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $model->total_cost     = $model->quantity * $model->unit_cost;
        });
    }
}
