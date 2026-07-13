<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';
    protected $fillable = ['code', 'name', 'type', 'normal_balance', 'level', 'parent_id', 'is_active', 'description'];
    protected $casts = ['is_active' => 'boolean', 'level' => 'integer'];

    public const TYPES = ['asset' => 'Aset', 'liability' => 'Kewajiban', 'equity' => 'Ekuitas', 'income' => 'Pendapatan', 'expense' => 'Beban'];
    public const NORMAL_BALANCE = ['asset' => 'debit', 'liability' => 'kredit', 'equity' => 'kredit', 'income' => 'kredit', 'expense' => 'debit'];

    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id'); }
    public function journalDetails() { return $this->hasMany(JournalDetail::class, 'account_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByType($q, string $t) { return $q->where('type', $t); }

    public function balance(): float
    {
        $debit = (float) $this->journalDetails()->sum('debit');
        $credit = (float) $this->journalDetails()->sum('credit');
        return ($this->normal_balance === 'debit' ? $debit - $credit : $credit - $debit);
    }
}
