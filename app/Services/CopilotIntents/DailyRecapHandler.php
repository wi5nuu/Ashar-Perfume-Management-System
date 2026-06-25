<?php

namespace App\Services\CopilotIntents;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Shift;
use App\Models\WholesaleOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyRecapHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();

        $sales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $salesCount = Transaction::whereDate('created_at', $today)->count();
        $expenses = Expense::whereDate('created_at', $today)->sum('amount');
        $expenseCount = Expense::whereDate('created_at', $today)->count();

        $openShifts = Shift::whereDate('opened_at', $today)->whereNull('closed_at')->count();
        $wholesalePending = WholesaleOrder::whereIn('status', ['pending', 'processed'])->count();

        $outOfStock = Inventory::where('current_stock', '<=', 0)->count();
        $lowStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();

        return "Rekap harian — " . $today->format('d/m/Y') . "\n"
            . "Penjualan: {$salesCount} transaksi, Rp " . number_format($sales, 0, ',', '.') . "\n"
            . "Pengeluaran: {$expenseCount} transaksi, Rp " . number_format($expenses, 0, ',', '.') . "\n"
            . "Shift masih buka: {$openShifts}\n"
            . "Pesanan grosir pending: {$wholesalePending}\n"
            . "Stok habis: {$outOfStock} | Stok kritis: {$lowStock}";
    }
}
