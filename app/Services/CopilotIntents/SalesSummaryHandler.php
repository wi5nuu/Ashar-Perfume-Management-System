<?php

namespace App\Services\CopilotIntents;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesSummaryHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $sales = Transaction::whereDate('created_at', $today)->get();
        $count = $sales->count();
        $total = $sales->sum('total_amount');

        $cashTotal = $sales->where('payment_method', 'cash')->sum('total_amount');
        $qrisTotal = $sales->where('payment_method', 'qris')->sum('total_amount');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('total_amount');

        return "Penjualan hari ini:\n"
            . "- Total transaksi: {$count}\n"
            . "- Total omzet: Rp " . number_format($total, 0, ',', '.') . "\n"
            . "- Tunai: Rp " . number_format($cashTotal, 0, ',', '.') . "\n"
            . "- QRIS: Rp " . number_format($qrisTotal, 0, ',', '.') . "\n"
            . "- Transfer: Rp " . number_format($transferTotal, 0, ',', '.');
    }
}
