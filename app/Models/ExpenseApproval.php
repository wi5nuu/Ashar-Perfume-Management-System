<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseApproval extends Model
{
    protected $fillable = ['expense_id','requested_by','approved_by','status','notes','approved_at'];
    protected $casts = ['approved_at'=>'datetime'];

    public function requester() { return $this->belongsTo(User::class,'requested_by'); }
    public function approver() { return $this->belongsTo(User::class,'approved_by'); }
    public function expense() { return $this->belongsTo(Expense::class); }

    public function approve(int $uid, ?string $notes = null): void
    {
        if ($this->status !== 'pending') {
            throw new \LogicException("Pengajuan sudah dalam status '{$this->status}' dan tidak dapat disetujui.");
        }
        $this->update(['status' => 'approved', 'approved_by' => $uid, 'approved_at' => now(), 'notes' => $notes]);
    }

    public function reject(int $uid, string $notes): void
    {
        if ($this->status !== 'pending') {
            throw new \LogicException("Pengajuan sudah dalam status '{$this->status}' dan tidak dapat ditolak.");
        }
        $this->update(['status' => 'rejected', 'approved_by' => $uid, 'approved_at' => now(), 'notes' => $notes]);
    }
}
