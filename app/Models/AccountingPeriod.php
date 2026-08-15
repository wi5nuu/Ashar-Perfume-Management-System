<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AccountingPeriod extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_closed', 'closed_at', 'closed_by'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public function scopeOpen(Builder $q): Builder
    {
        return $q->where('is_closed', false);
    }

    public function journals()
    {
        return $this->hasMany(JournalEntry::class, 'period_id');
    }

    public static function current(): ?self
    {
        return static::where('start_date', '<=', now())->where('end_date', '>=', now())->where('is_closed', false)->first();
    }

    public function close(int $userId): void
    {
        if ($this->is_closed) {
            throw new \DomainException("Periode {$this->name} sudah ditutup.");
        }
        if ($this->journals()->where('status', JournalEntry::STATUS_DRAFT)->exists()) {
            throw new \DomainException('Masih ada jurnal draft — posting atau hapus terlebih dahulu.');
        }
        $this->update(['is_closed' => true, 'closed_at' => now(), 'closed_by' => $userId]);
    }

    public function contains(\DateTimeInterface|string $date): bool
    {
        $date = is_string($date) ? Carbon::parse($date) : $date;

        return $date->between($this->start_date, $this->end_date);
    }
}
