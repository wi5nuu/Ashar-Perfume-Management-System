<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositAccount extends Model
{
    protected $fillable = ['customer_id', 'balance', 'status'];
    protected $casts = ['balance' => 'float'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function transactions() { return $this->hasMany(DepositTransaction::class, 'deposit_account_id'); }

    public function deposit(float $amount, string $description='', int $userId=null): DepositTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $this->balance - $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'created_by' => $userId,
        ]);
    }

    public function withdraw(float $amount, string $description='', int $userId=null): DepositTransaction
    {
        if ($this->balance < $amount) throw new \Exception('Saldo tidak mencukupi');
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $this->balance + $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'created_by' => $userId,
        ]);
    }
}
