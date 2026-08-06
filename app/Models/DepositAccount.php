<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DepositAccount extends Model
{
    protected $fillable = ['customer_id', 'balance', 'status'];
    protected $casts = ['balance' => 'decimal:2'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function transactions() { return $this->hasMany(DepositTransaction::class, 'deposit_account_id'); }

    public function deposit(float $amount, string $description = '', ?int $userId = null): DepositTransaction
    {
        return DB::transaction(function () use ($amount, $description, $userId) {
            // Lock the row for the duration of the transaction to prevent
            // concurrent deposits/withdrawals from producing a wrong balance.
            $fresh = static::lockForUpdate()->findOrFail($this->id);

            $balanceBefore = (float) $fresh->balance;
            $fresh->increment('balance', $amount);
            $balanceAfter = (float) $fresh->balance;

            // Sync the in-memory model so callers see the updated value.
            $this->balance = $fresh->balance;

            return $fresh->transactions()->create([
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'created_by'     => $userId,
            ]);
        });
    }

    public function withdraw(float $amount, string $description = '', ?int $userId = null): DepositTransaction
    {
        return DB::transaction(function () use ($amount, $description, $userId) {
            // Lock the row so the balance check + decrement are atomic.
            $fresh = static::lockForUpdate()->findOrFail($this->id);

            if ((float) $fresh->balance < $amount) {
                throw new \Exception('Saldo tidak mencukupi');
            }

            $balanceBefore = (float) $fresh->balance;
            $fresh->decrement('balance', $amount);
            $balanceAfter = (float) $fresh->balance;

            // Sync the in-memory model so callers see the updated value.
            $this->balance = $fresh->balance;

            return $fresh->transactions()->create([
                'type'           => 'withdrawal',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'description'    => $description,
                'created_by'     => $userId,
            ]);
        });
    }
}
