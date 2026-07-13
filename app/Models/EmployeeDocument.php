<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['user_id','type','filename','original_name','mime_type','size','expiry_date','is_verified','verified_by','verified_at','notes'];
    protected $casts = ['expiry_date'=>'date','is_verified'=>'boolean','verified_at'=>'datetime','size'=>'integer'];

    public function employee() { return $this->belongsTo(User::class,'user_id'); }
    public function verifier() { return $this->belongsTo(User::class,'verified_by'); }
}
