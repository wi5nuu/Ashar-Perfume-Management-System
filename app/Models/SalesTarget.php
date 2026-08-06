<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    protected $fillable = ['branch_id', 'user_id', 'month', 'year', 'target_amount', 'bonus_percentage'];
    
    // Use 'decimal' cast for currency precision instead of 'float' to avoid
    // floating-point rounding issues. 'year' is validated in controller rules.
    protected $casts = [
        'target_amount' => 'decimal:2',
        'bonus_percentage' => 'decimal:2',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function achievement(): float
    {
        $start = now()->setYear($this->year)->setMonth($this->month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $sales = Transaction::whereBetween('created_at', [$start, $end])
            ->when($this->branch_id, fn($q) => $q->where('branch_id', $this->branch_id))
            ->when($this->user_id,   fn($q) => $q->where('user_id',   $this->user_id))
            ->sum('total_amount');

        return $this->target_amount > 0
            ? round(($sales / $this->target_amount) * 100, 1)
            : 0;
    }
}

