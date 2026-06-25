<?php

namespace App\Services\CopilotIntents;

use App\Models\Inventory;
use App\Models\Product;
use Carbon\Carbon;

class CriticalStockHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $critical = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->with('product')
            ->orderBy('current_stock')
            ->get();

        $outOfStock = Inventory::where('current_stock', '<=', 0)
            ->with('product')
            ->get();

        $lines = [];

        if ($critical->isNotEmpty()) {
            $lines[] = 'Stok kritis (di bawah minimum):';
            foreach ($critical->take(10) as $inv) {
                $name = $inv->product ? $inv->product->name : '(produk dihapus)';
                $lines[] = "- {$name}: {$inv->current_stock} (min: {$inv->minimum_stock})";
            }
            if ($critical->count() > 10) {
                $lines[] = '... dan ' . ($critical->count() - 10) . ' produk lainnya.';
            }
            $lines[] = '';
        }

        if ($outOfStock->isNotEmpty()) {
            $lines[] = 'Stok habis:';
            foreach ($outOfStock->take(10) as $inv) {
                $name = $inv->product ? $inv->product->name : '(produk dihapus)';
                $lines[] = "- {$name}";
            }
            if ($outOfStock->count() > 10) {
                $lines[] = '... dan ' . ($outOfStock->count() - 10) . ' produk lainnya.';
            }
        }

        if (empty($lines)) {
            return 'Semua stok dalam kondisi baik. Tidak ada produk yang kritis atau habis.';
        }

        return implode("\n", $lines);
    }
}
