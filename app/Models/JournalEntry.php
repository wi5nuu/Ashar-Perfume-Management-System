<?php

namespace App\Models;

use App\Services\Accounting\AccountingPeriodService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_number', 'period_id', 'branch_id', 'transaction_id', 'transaction_type',
        'date', 'description', 'total_debit', 'total_credit', 'status',
        'posted_at', 'created_by', 'reversed_by', 'reversed_at', 'reversal_of',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'float',
        'total_credit' => 'float',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_POSTED = 'posted';

    public const STATUS_REVERSED = 'reversed';

    public function details()
    {
        return $this->hasMany(JournalDetail::class, 'journal_entry_id');
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversal()
    {
        return $this->hasOne(self::class, 'reversal_of');
    }

    public function scopePosted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_POSTED);
    }

    public function scopeOfPeriod(Builder $q, int $periodId): Builder
    {
        return $q->where('period_id', $periodId);
    }

    public function scopeForSource(Builder $q, string $type, int $id): Builder
    {
        return $q->where('transaction_type', $type)->where('transaction_id', $id);
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function post(): void
    {
        if (! $this->isBalanced()) {
            throw new \DomainException('Jurnal tidak balanced — posting ditolak.');
        }
        if ($this->isPosted()) {
            throw new \DomainException('Jurnal ini sudah diposting.');
        }
        if ($this->period && $this->period->is_closed) {
            throw new \DomainException("Periode {$this->period->name} sudah ditutup — posting ditolak.");
        }
        $this->update(['status' => self::STATUS_POSTED, 'posted_at' => now()]);
    }

    /**
     * Reversal entry — mirrors the original with inverted debits/credits.
     * The original keeps its own status so the audit trail stays immutable.
     */
    public function reverse(int $userId): self
    {
        if (! $this->isPosted()) {
            throw new \DomainException('Hanya jurnal berstatus posted yang dapat di-reverse.');
        }
        if ($this->reversal()->exists()) {
            throw new \DomainException('Jurnal ini sudah memiliki jurnal pembalik.');
        }

        $reversal = DB::transaction(function () use ($userId) {
            $period = $this->period ?? AccountingPeriodService::currentOrCreate();

            $reversal = self::create([
                'journal_number' => 'JRV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'period_id' => $period->id,
                'branch_id' => $this->branch_id,
                'transaction_id' => $this->transaction_id,
                'transaction_type' => $this->transaction_type,
                'date' => now(),
                'description' => 'Pembalikan: '.$this->description,
                'total_debit' => $this->total_credit,
                'total_credit' => $this->total_debit,
                'status' => self::STATUS_POSTED,
                'posted_at' => now(),
                'created_by' => $userId,
                'reversal_of' => $this->id,
            ]);

            foreach ($this->details as $detail) {
                $reversal->details()->create([
                    'account_id' => $detail->account_id,
                    'contact_type' => $detail->contact_type,
                    'contact_id' => $detail->contact_id,
                    'debit' => $detail->credit,
                    'credit' => $detail->debit,
                    'memo' => 'Pembalikan: '.($detail->memo ?? $this->description),
                ]);
            }

            $this->update([
                'status' => self::STATUS_REVERSED,
                'reversed_by' => $userId,
                'reversed_at' => now(),
            ]);

            return $reversal;
        });

        return $reversal;
    }

    public function deleteDraft(): void
    {
        if ($this->isPosted()) {
            throw new \DomainException('Jurnal sudah diposting — gunakan reverse.');
        }
        DB::transaction(function () {
            $this->details()->delete();
            $this->delete();
        });
    }
}
