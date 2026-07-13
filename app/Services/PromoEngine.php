<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class PromoEngine
{
    protected array $rules = [];

    public function addRule(string $name, callable $condition, callable $discount): self
    {
        $this->rules[] = compact('name', 'condition', 'discount');
        return $this;
    }

    public function apply(Collection $items, float $subtotal): array
    {
        $applied = [];
        $totalDiscount = 0;

        foreach ($this->rules as $rule) {
            if ($rule['condition']($items, $subtotal)) {
                $discount = $rule['discount']($items, $subtotal);
                $applied[] = ['rule' => $rule['name'], 'discount' => $discount];
                $totalDiscount += $discount;
            }
        }

        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total' => $subtotal - $totalDiscount,
            'applied_rules' => $applied,
        ];
    }

    public static function buyXGetYDiscount(int $x, int $y, float $discountPct): self
    {
        return (new self)->addRule("Beli {$x} gratis {$y}", function ($items, $subtotal) use ($x) {
            return $items->sum('quantity') >= $x;
        }, function ($items, $subtotal) use ($y, $discountPct) {
            $cheapest = $items->sortBy('price')->first();
            return ($cheapest['price'] ?? 0) * $y * $discountPct;
        });
    }

    public static function minPurchaseDiscount(float $minAmount, float $discountPct, float $maxDiscount): self
    {
        return (new self)->addRule("Min. belanja Rp ".number_format($minAmount,0), function ($items, $subtotal) use ($minAmount) {
            return $subtotal >= $minAmount;
        }, function ($items, $subtotal) use ($discountPct, $maxDiscount) {
            return min($subtotal * $discountPct, $maxDiscount);
        });
    }
}
