<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockRequest extends Model
{
    protected $fillable = [
        'request_number', 'branch_id', 'requested_by', 'approved_by',
        'status', 'notes', 'delivery_method', 'delivery_date',
        'received_date', 'receipt_notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'received_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->request_number = 'SR-' . now()->format('Ymd') . '-' . str_pad(
                static::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT
            );
        });
    }
}
