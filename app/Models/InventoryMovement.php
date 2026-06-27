<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Records every stock movement for full audit trail.
 *
 * Types: sale, bonus, return, purchase, adjustment, transfer_in, transfer_out, void.
 * quantity is signed: positive = stock in, negative = stock out.
 * stock_before and stock_after provide full chain of custody.
 */
class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'inventory_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity'     => 'integer',
        'stock_before' => 'integer',
        'stock_after'  => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record a stock movement atomically.
     *
     * @param int    $productId     Product ID
     * @param int|null $branchId    Branch ID
     * @param string $type          Movement type (sale, bonus, purchase, adjustment, void, etc.)
     * @param int    $quantity      Signed quantity (positive = in, negative = out)
     * @param int    $stockBefore   Stock level before this movement
     * @param int    $stockAfter    Stock level after this movement
     * @param string|null $refType  Reference model type (e.g., 'transaction')
     * @param int|null    $refId    Reference model ID
     * @param string|null $notes    Optional notes
     * @param int|null    $userId   Who performed this movement
     */
    public static function record(
        int $productId,
        ?int $branchId,
        string $type,
        int $quantity,
        int $stockBefore,
        int $stockAfter,
        ?string $refType = null,
        ?int $refId = null,
        ?string $notes = null,
        ?int $userId = null,
    ): self {
        return static::create([
            'product_id'     => $productId,
            'branch_id'      => $branchId,
            'type'           => $type,
            'quantity'       => $quantity,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'reference_type' => $refType,
            'reference_id'   => $refId,
            'notes'          => $notes,
            'user_id'        => $userId ?? auth()->id(),
        ]);
    }
}
