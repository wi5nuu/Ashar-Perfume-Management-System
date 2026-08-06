<?php

namespace App\Jobs;

use App\Events\LowStockAlert;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CheckLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->with('product')
            ->get();

        foreach ($lowStockItems as $item) {
            // Deduplicate: skip if we already broadcast an alert for this item
            // in the last 60 minutes to avoid flooding the notification channel.
            $dedupeKey = "low_stock_alerted:{$item->product_id}:{$item->branch_id}";
            if (Cache::has($dedupeKey)) {
                continue;
            }
            Cache::put($dedupeKey, true, now()->addMinutes(60));

            LowStockAlert::dispatch(
                $item->product_id,
                $item->product->name,
                $item->current_stock,
                $item->minimum_stock,
                $item->branch_id  // pass branch scope so the event broadcasts on the correct channel
            );
        }
    }
}
