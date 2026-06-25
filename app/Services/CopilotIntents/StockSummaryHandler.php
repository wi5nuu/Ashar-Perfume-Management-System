<?php

namespace App\Services\CopilotIntents;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockSummaryHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $totalItems = Inventory::sum('current_stock');
        $totalProducts = Inventory::where('current_stock', '>', 0)->count();
        $outOfStock = Inventory::where('current_stock', '<=', 0)->count();
        $lowStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();

        return "Ringkasan stok saat ini:\n"
            . "- Total stok: {$totalItems} unit\n"
            . "- Produk dengan stok: {$totalProducts}\n"
            . "- Stok kritis (di bawah minimum): {$lowStock}\n"
            . "- Stok habis: {$outOfStock}";
    }
}
