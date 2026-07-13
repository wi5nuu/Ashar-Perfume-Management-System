<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_closed', 'closed_at', 'closed_by'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_closed' => 'boolean', 'closed_at' => 'datetime'];

    public function scopeOpen($q) { return $q->where('is_closed', false); }

    public static function current(): ?self
    {
        return static::where('start_date', '<=', now())->where('end_date', '>=', now())->where('is_closed', false)->first();
    }

    public function close(int $userId): void
    {
        $this->update(['is_closed' => true, 'closed_at' => now(), 'closed_by' => $userId]);
    }
}
