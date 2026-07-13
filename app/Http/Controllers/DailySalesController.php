<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\WholesaleOrder;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailySalesController extends Controller
{
    public function __invoke(Request $request)
    {
        $date = $request->date ?: now()->toDateString();
        $branchId = $request->branch_id;

        $retail = Transaction::whereDate('created_at', $date);
        if ($branchId) $retail->where('branch_id', $branchId);
        $retailSales = (float) $retail->sum('total_amount');
        $retailCount = (int) $retail->count();

        $wQuery = WholesaleOrder::whereDate('created_at', $date)->where('status', '!=', 'cancelled');
        if ($branchId) $wQuery->where('branch_id', $branchId);
        $wholesaleSales = (float) $wQuery->sum('total_amount');
        $wholesaleCount = (int) $wQuery->count();

        $totalSales = $retailSales + $wholesaleSales;
        $totalTransactions = $retailCount + $wholesaleCount;

        $expenses = Expense::whereDate('date', $date)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $topProducts = TransactionDetail::select('product_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(transaction_details.purchase_price * quantity) as total'))
            ->whereHas('transaction', fn($q) => $q->whereDate('created_at', $date))
            ->when($branchId, fn($q) => $q->whereHas('transaction', fn($qq) => $qq->where('branch_id', $branchId)))
            ->groupBy('product_id')->orderByDesc('qty')->with('product')->limit(10)->get();

        return view('reports.daily-sales', compact('date', 'retailSales', 'retailCount', 'wholesaleSales', 'wholesaleCount', 'totalSales', 'totalTransactions', 'expenses', 'topProducts'));
    }
}
