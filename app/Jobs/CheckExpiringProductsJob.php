<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
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
        // Resolve system user: prefer a dedicated system/owner account,
        // fall back to the first active owner, then null (allows nullable user_id).
        $systemUserId = User::where('role', 'owner')
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');

        $expiringSoon = Inventory::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->with('product:id,name')
            ->get();

        foreach ($expiringSoon as $item) {
            // Skip if an identical alert was already logged today to avoid duplicates.
            $alreadyLogged = AuditLog::where('action', 'expiry_alert')
                ->where('target_id', $item->product_id)
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyLogged) {
                continue;
            }

            AuditLog::create([
                'user_id'      => $systemUserId,
                'action'       => 'expiry_alert',
                'target_model' => Product::class,
                'target_id'    => $item->product_id,
                'old_data'     => null,
                'new_data'     => json_encode([
                    'product_name'    => $item->product?->name,
                    'expiration_date' => $item->expiration_date,
                    'current_stock'   => $item->current_stock,
                    'branch_id'       => $item->branch_id,
                ]),
                'ip_address'   => '127.0.0.1',
            ]);
        }
    }
}
