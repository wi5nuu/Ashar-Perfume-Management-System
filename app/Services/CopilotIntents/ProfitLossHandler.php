<?php

namespace App\Services\CopilotIntents;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();

        $totalSales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $totalExpenses = Expense::whereDate('created_at', $today)->sum('amount');

        $totalCost = TransactionDetail::whereDate('created_at', $today)
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * products.purchase_price'));

        $grossProfit = $totalSales - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;

        return "Laba rugi hari ini:\n"
            . "- Omzet: Rp " . number_format($totalSales, 0, ',', '.') . "\n"
            . "- HPP: Rp " . number_format($totalCost, 0, ',', '.') . "\n"
            . "- Laba kotor: Rp " . number_format($grossProfit, 0, ',', '.') . "\n"
            . "- Pengeluaran: Rp " . number_format($totalExpenses, 0, ',', '.') . "\n"
            . "- Laba bersih: Rp " . number_format($netProfit, 0, ',', '.');
    }
}
