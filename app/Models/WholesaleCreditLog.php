<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
