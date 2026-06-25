<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'phone',
        'email',
        'manager_name',
        'is_active',
        'opening_date',
        'shift_start',
        'shift_end',
        'operational_hours',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_date' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // BUG-04 FIX: Relasi wholesale yang hilang
    public function wholesaleOrders(): HasMany
    {
        return $this->hasMany(WholesaleOrder::class);
    }

    /**
     * Get total revenue for this branch within a date range.
     */
    public function revenueForPeriod($startDate, $endDate): float
    {
        return (float) $this->transactions()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
    }

    /**
     * Get total expenses for this branch within a date range.
     */
    public function expensesForPeriod($startDate, $endDate): float
    {
        return (float) $this->expenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
}
