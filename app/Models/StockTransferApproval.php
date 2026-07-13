<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferApproval extends Model
{
    protected $fillable = ['stock_transfer_id','requested_by','approved_by','status','notes','approved_at'];
    protected $casts = ['approved_at'=>'datetime'];

    public function transfer() { return $this->belongsTo(StockTransfer::class,'stock_transfer_id'); }
    public function requester() { return $this->belongsTo(User::class,'requested_by'); }
    public function approver() { return $this->belongsTo(User::class,'approved_by'); }
}
