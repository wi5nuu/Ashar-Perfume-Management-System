<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class StockValuationController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->branch_id;
        $query = Inventory::with(['product', 'branch'])
            ->whereHas('product', fn($q) => $q->where('is_active', true));

        if ($branchId) $query->where('branch_id', $branchId);

        $items = $query->get()->map(function ($inv) {
            $avgPrice = $inv->product->purchase_price;
            $value = $inv->current_stock * $avgPrice;
            return [
                'product' => $inv->product->name,
                'sku' => $inv->product->sku,
                'branch' => $inv->branch?->name,
                'stock' => $inv->current_stock,
                'avg_price' => $avgPrice,
                'value' => $value,
            ];
        });

        $totalValue = $items->sum('value');
        $totalItems = $items->sum('stock');

        return view('reports.stock-valuation', compact('items', 'totalValue', 'totalItems', 'branchId'));
    }
}
