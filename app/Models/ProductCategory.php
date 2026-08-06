<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    // Tier constants
    const TIER_PREMIUM = 'premium';
    const TIER_SEDANG  = 'sedang';
    const TIER_BIASA   = 'biasa';

    protected $fillable = [
        'name',
        'description',
        'color',
        'tier',
    ];

    protected $casts = [
        'tier' => 'string',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }

    /** Apakah kategori ini tier Premium */
    public function isPremium(): bool
    {
        return $this->tier === self::TIER_PREMIUM;
    }

    /** Apakah kategori ini tier Biasa (untuk bonus) */
    public function isBiasa(): bool
    {
        return $this->tier === self::TIER_BIASA;
    }

    /** Label tampilan beserta emoji tier */
    public function tierLabel(): string
    {
        return match($this->tier) {
            self::TIER_PREMIUM => '⭐ Premium',
            self::TIER_SEDANG  => '🥈 Sedang',
            self::TIER_BIASA   => '🏷️ Biasa',
            default            => $this->tier,
        };
    }
}