<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $hidden = [
        'cash_breakdown',
        'denominations',
        'closing_photo_path',
        'manager_notes',
        'initial_cash',
        'expected_cash',
        'actual_cash',
        'discrepancy',
    ];

    protected $fillable = [
        'user_id',
        'branch_id',
        'start_time',
        'end_time',
        'initial_cash',
        'expected_cash',
        'actual_cash',
        'discrepancy',
        'status',
        'notes',
        'closing_photo_path',
        'photo_status',
        'photo_reviewed_by',
        'cash_breakdown',
        'denominations',
        'reviewed_at',
        'reviewed_by',
        'manager_notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'initial_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'discrepancy' => 'decimal:2',
        'cash_breakdown' => 'array',
        'denominations' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'photo_reviewed_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
