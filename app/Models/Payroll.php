<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $hidden = ['basic_salary', 'allowance', 'deduction', 'total_salary'];

    protected $fillable = [
        'user_id',
        'month',
        'basic_salary',
        'allowance',
        'deduction',
        'total_salary',
        'attendance_days',
        'status',
    ];

    protected $casts = [
        'basic_salary'    => 'decimal:2',
        'allowance'       => 'decimal:2',
        'deduction'       => 'decimal:2',
        'total_salary'    => 'decimal:2',
        'attendance_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
