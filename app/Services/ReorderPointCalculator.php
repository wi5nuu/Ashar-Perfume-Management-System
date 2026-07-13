<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\TransactionDetail;

class ReorderPointCalculator
{
    public function calculate(Product $product, ?int $branchId=null): array
    {
        $daysBack = 90;
        $since = now()->subDays($daysBack);
        $sold = (int) TransactionDetail::where('product_id', $product->id)
            ->whereHas('transaction', fn($q) => $q->where('created_at','>=',$since))
            ->when($branchId, fn($q) => $q->whereHas('transaction', fn($qq) => $qq->where('branch_id',$branchId)))
            ->sum('quantity');

        $avgDaily = $daysBack > 0 ? $sold / $daysBack : 0;
        $leadTime = $product->lead_time_days ?? 7;
        $safetyStock = $avgDaily * 3;
        $reorderPoint = ($avgDaily * $leadTime) + $safetyStock;
        $stock = Inventory::where('product_id', $product->id)
            ->when($branchId, fn($q) => $q->where('branch_id',$branchId))
            ->value('current_stock') ?? 0;

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'avg_daily_sales' => round($avgDaily, 2),
            'lead_time_days' => $leadTime,
            'safety_stock' => round($safetyStock),
            'reorder_point' => round($reorderPoint),
            'current_stock' => $stock,
            'needs_reorder' => $stock <= $reorderPoint,
            'shortage' => $stock <= $reorderPoint ? round($reorderPoint - $stock) : 0,
        ];
    }

    public function calculateAll(?int $branchId=null): array
    {
        $products = Product::where('is_active', true)->get();
        $results = $products->map(fn($p) => $this->calculate($p, $branchId))->toArray();
        usort($results, fn($a,$b) => $b['needs_reorder'] <=> $a['needs_reorder']);
        return $results;
    }
}
