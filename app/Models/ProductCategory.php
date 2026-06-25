<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    // Tambahkan ini agar kolom 'name' dan 'description' bisa diisi
    protected $fillable = [
        'name', 
        'description',
        'color'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}