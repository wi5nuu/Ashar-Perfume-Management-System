<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WholesaleProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'type',
        'unit',
        'pieces_per_unit',
        'price_per_unit',
        'price_per_ml',
        'stock',
        'description',
        'is_active',
    ];

    public function getPricePerPieceAttribute(): float
    {
        if ($this->pieces_per_unit <= 0) return (float) $this->price_per_unit;
        return (float) $this->price_per_unit / $this->pieces_per_unit;
    }

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_unit' => 'decimal:2',
        'price_per_ml' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
