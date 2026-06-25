<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_id',
        'branch_id',
        'user_id',
        'status',
        'order_date',
        'expected_date',
        'received_date',
        'subtotal',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal'      => 'decimal:2',
        'total_amount'  => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Generate a unique PO number.
     */
    public static function generatePoNumber(): string
    {
        $uuidPart = strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
        return 'PO-' . now()->format('Ymd') . '-' . $uuidPart;
    }

    /**
     * Recalculate totals from items.
     */
    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $this->update([
            'subtotal'     => $subtotal,
            'total_amount' => $subtotal,
        ]);
    }

    /**
     * Check if all items are fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->items->every(fn($item) => $item->received_quantity >= $item->quantity);
    }

    /**
     * Check if any items are partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->items->some(fn($item) => $item->received_quantity > 0 && $item->received_quantity < $item->quantity);
    }
}
