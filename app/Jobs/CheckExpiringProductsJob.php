<?php

namespace App\Jobs;

use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            \App\Models\AuditLog::create([
                'user_id' => 1,
                'action' => 'expiry_alert',
                'target_model' => \App\Models\Product::class,
                'target_id' => $item->product_id,
                'old_data' => null,
                'new_data' => json_encode([
                    'expiration_date' => $item->expiration_date,
                    'current_stock' => $item->current_stock,
                    'branch_id' => $item->branch_id,
                ]),
                'ip_address' => '127.0.0.1',
            ]);
        }
    }
}
