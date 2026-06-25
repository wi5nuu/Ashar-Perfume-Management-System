<?php

namespace App\Jobs;

use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckExpiringProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiringSoon = Inventory::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->with('product')
            ->get();

        foreach ($expiringSoon as $item) {
            Log::warning("Expiring product: {$item->product->name} expired pada {$item->expiration_date}");
        }
    }
}
