<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleCreditLog extends Model
{
    protected $fillable = [
        'customer_id', 'credits', 'gold_points', 'type',
        'description', 'reference_type', 'reference_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
