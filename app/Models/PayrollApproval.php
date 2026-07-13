<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollApproval extends Model
{
    protected $fillable = ['payroll_id','requested_by','approved_by','status','notes','approved_at'];
    protected $casts = ['approved_at'=>'datetime'];

    public function payroll() { return $this->belongsTo(Payroll::class); }
    public function requester() { return $this->belongsTo(User::class,'requested_by'); }
    public function approver() { return $this->belongsTo(User::class,'approved_by'); }
}
