<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number',
        'transaction_id',
        'user_id',
        'branch_id',
        'reason',
        'status',
        'total_refund',
        'approved_at',
        'approved_by',
        'completed_at',
    ];

    protected $casts = [
        'total_refund'  => 'decimal:2',
        'approved_at'   => 'datetime',
        'completed_at'  => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateReturnNumber(): string
    {
        $uuidPart = strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
        return 'RET-' . now()->format('Ymd') . '-' . $uuidPart;
    }

    public function recalculate(): void
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_refund' => $total]);
    }
}
