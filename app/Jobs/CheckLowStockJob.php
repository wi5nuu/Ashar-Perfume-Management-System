<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->with('product')
            ->get();

        foreach ($lowStockItems as $item) {
            Log::warning("Low stock alert: {$item->product->name} hanya memiliki {$item->current_stock} stok (min: {$item->minimum_stock})");
        }
    }
}
