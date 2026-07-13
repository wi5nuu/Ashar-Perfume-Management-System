<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['journal_number', 'period_id', 'transaction_id', 'transaction_type', 'date', 'description', 'total_debit', 'total_credit', 'status', 'posted_at', 'created_by'];
    protected $casts = ['date' => 'date', 'total_debit' => 'float', 'total_credit' => 'float', 'posted_at' => 'datetime'];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';
    public const STATUS_REVERSED = 'reversed';

    public function details() { return $this->hasMany(JournalDetail::class, 'journal_entry_id'); }
    public function period() { return $this->belongsTo(AccountingPeriod::class, 'period_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isBalanced(): bool { return abs($this->total_debit - $this->total_credit) < 0.01; }

    public function post(): void
    {
        if (!$this->isBalanced()) throw new \Exception('Jurnal tidak balanced');
        $this->update(['status' => self::STATUS_POSTED, 'posted_at' => now()]);
    }
}
