<?php

namespace App\Services\CopilotIntents;

use App\Models\WholesaleOrder;
use Carbon\Carbon;

class WholesaleOrderHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $pending = WholesaleOrder::whereIn('status', ['pending', 'processed'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        if ($pending->isEmpty()) {
            return 'Tidak ada pesanan grosir yang pending.';
        }

        $lines = ['Pesanan grosir yang perlu diproses:'];
        foreach ($pending as $order) {
            $lines[] = "- #{$order->id}: {$order->customer_name} — Rp "
                . number_format($order->total_amount, 0, ',', '.')
                . " ({$order->status}) — "
                . Carbon::parse($order->created_at)->format('d/m/Y H:i');
        }

        return implode("\n", $lines);
    }
}
