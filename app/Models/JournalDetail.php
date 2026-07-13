<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    protected $fillable = ['journal_entry_id', 'account_id', 'debit', 'credit', 'memo'];
    protected $casts = ['debit' => 'float', 'credit' => 'float'];

    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function account() { return $this->belongsTo(ChartOfAccount::class, 'account_id'); }
}
