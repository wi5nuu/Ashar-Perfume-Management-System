<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUnansweredQuery extends Model
{
    protected $fillable = ['query_text', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
