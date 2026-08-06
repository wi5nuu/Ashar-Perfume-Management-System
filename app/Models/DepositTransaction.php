<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositTransaction extends Model
{
    protected $fillable = ['deposit_account_id','type','amount','balance_before','balance_after','description','created_by'];
    // Use decimal:2 instead of float to prevent floating-point rounding errors
    // on financial values stored in the database.
    protected $casts = ['amount'=>'decimal:2','balance_before'=>'decimal:2','balance_after'=>'decimal:2'];

    public function account() { return $this->belongsTo(DepositAccount::class,'deposit_account_id'); }
}
