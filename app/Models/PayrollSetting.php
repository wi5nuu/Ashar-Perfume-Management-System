<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $hidden = ['allowance', 'deduction', 'overtime_rate'];

    protected $fillable = ['user_id', 'allowance', 'deduction', 'overtime_rate'];

    protected $casts = [
        'allowance' => 'decimal:2',
        'deduction' => 'decimal:2',
        'overtime_rate' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
