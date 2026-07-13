<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    protected $fillable = ['branch_id', 'user_id', 'month', 'year', 'target_amount', 'bonus_percentage'];
    protected $casts = ['target_amount' => 'float', 'bonus_percentage' => 'float'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function achievement(): float
    {
        $start = now()->setYear($this->year)->setMonth($this->month)->startOfMonth();
        $end = (clone $start)->endOfMonth();
        $sales = Transaction::whereBetween('created_at', [$start, $end])
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when($this->user_id, fn($q) => $q->where('user_id', $this->user_id))
            ->sum('total_amount');
        return $this->target_amount > 0 ? round(($sales / $this->target_amount) * 100, 1) : 0;
    }
}
