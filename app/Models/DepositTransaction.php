<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositTransaction extends Model
{
    protected $fillable = ['deposit_account_id','type','amount','balance_before','balance_after','description','created_by'];
    protected $casts = ['amount'=>'float','balance_before'=>'float','balance_after'=>'float'];

    public function account() { return $this->belongsTo(DepositAccount::class,'deposit_account_id'); }
}
