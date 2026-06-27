<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_type',
        'user_id',
        'branch_id',
        'subtotal',
        'discount',
        'discount_type',
        'discount_percent',
        'tax_amount',
        'total_amount',
        'final_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'ewallet_type',
        'transfer_type',
        'receipt_visibility',
        'payment_status',
        'debt_amount',
        'coupon_id',
        'payment_proof_image',
        'notes',
        'tax_enabled',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'final_amount'     => 'decimal:2',
        'discount'         => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'debt_amount'      => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
        'tax_enabled'      => 'boolean',
        'created_at'       => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // BUG-01: Relasi yang hilang ke DebtPayment
    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * Total yang sudah dibayar (pembayaran awal + semua cicilan hutang)
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->paid_amount
            + (float) $this->debtPayments->sum('amount');
    }

    /**
     * Sisa hutang real-time berdasarkan relasi
     */
    public function getRealDebtAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->getTotalPaidAttribute());
    }
}
