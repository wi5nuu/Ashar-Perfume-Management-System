<?php

namespace App\Services\CopilotIntents;

use App\Models\TransactionDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BestSellingHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $topProducts = TransactionDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereDate('created_at', $today)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        if ($topProducts->isEmpty()) {
            return 'Belum ada data penjualan hari ini.';
        }

        $lines = [];
        $rank = 1;
        foreach ($topProducts as $item) {
            $product = Product::find($item->product_id);
            $name = $product ? $product->name : '(produk dihapus)';
            $lines[] = "{$rank}. {$name} — {$item->total_qty} pcs (Rp " . number_format($item->total_revenue, 0, ',', '.') . ')';
            $rank++;
        }

        return "Berikut 5 produk terlaris hari ini:\n" . implode("\n", $lines);
    }
}
