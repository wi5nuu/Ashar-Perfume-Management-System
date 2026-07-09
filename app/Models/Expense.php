<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $hidden = ['proof_image', 'vendor'];

    protected $fillable = [
        'user_id',
        'branch_id',
        'category_id',
        'description',
        'amount',
        'vendor',
        'type',
        'date',
        'proof_image',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
