<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAudit extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'audit_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(StockAuditItem::class);
    }
}
