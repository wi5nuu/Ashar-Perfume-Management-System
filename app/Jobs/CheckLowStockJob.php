<?php

namespace App\Jobs;

use App\Events\LowStockAlert;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->with('product')
            ->get();

        foreach ($lowStockItems as $item) {
            LowStockAlert::dispatch(
                $item->product_id,
                $item->product->name,
                $item->current_stock,
                $item->minimum_stock
            );
        }
    }
}
