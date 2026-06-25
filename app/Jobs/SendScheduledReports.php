<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Mail\WeeklySalesReport;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Shift;
use App\Models\WholesaleOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendScheduledReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $type; // daily | weekly

    public function __construct(string $type = 'daily')
    {
        $this->type = $type;
    }

    public function handle(): void
    {
        $recipients = Setting::getValue('report_email_recipients', '');
        if (empty($recipients)) {
            return;
        }

        $emails = array_filter(array_map('trim', explode(',', $recipients)));
        if (empty($emails)) {
            return;
        }

        if ($this->type === 'daily') {
            $this->sendDaily($emails);
        } elseif ($this->type === 'weekly') {
            $this->sendWeekly($emails);
        }
    }

    private function sendDaily(array $emails): void
    {
        $today = Carbon::today();

        $retailSales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $retailCount = Transaction::whereDate('created_at', $today)->count();

        $wholesaleSales = WholesaleOrder::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $totalRevenue = $retailSales + $wholesaleSales;

        $expenses = Expense::whereDate('date', $today)->sum('amount');

        // Top products today
        $topProducts = DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->select('products.name', DB::raw('SUM(transaction_details.quantity) as total_sold'))
            ->whereDate('transactions.created_at', $today)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Cash discrepancies (shifts with discrepancies today)
        $discrepancies = Shift::whereDate('created_at', $today)
            ->whereNotNull('cash_breakdown')
            ->get()
            ->filter(fn($s) => abs(($s->cash_breakdown['discrepancy'] ?? 0)) > 0)
            ->take(5);

        $data = [
            'date'          => $today->format('d M Y'),
            'retail_sales'  => (float) $retailSales,
            'retail_count'  => $retailCount,
            'wholesale_sales' => (float) $wholesaleSales,
            'total_revenue' => (float) $totalRevenue,
            'expenses'      => (float) $expenses,
            'top_products'  => $topProducts,
            'discrepancies' => $discrepancies,
        ];

        Mail::to($emails)->queue(new DailySalesReport($data));
    }

    private function sendWeekly(array $emails): void
    {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();

        $retailSales = Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $wholesaleSales = WholesaleOrder::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $totalRevenue = $retailSales + $wholesaleSales;
        $expenses = Expense::whereBetween('date', [$start, $end])->sum('amount');
        $profit = $totalRevenue - $expenses;

        // COGS
        $cogs = (float) (DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select(DB::raw('SUM(transaction_details.purchase_price * transaction_details.quantity) as cogs'))
            ->value('cogs') ?? 0);

        $netProfit = $totalRevenue - $cogs - $expenses;

        // Low stock alerts
        $lowStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.name', 'inventories.current_stock', 'inventories.minimum_stock')
            ->take(10)
            ->get();

        $data = [
            'start_date'    => $start->format('d M Y'),
            'end_date'      => $end->format('d M Y'),
            'total_revenue' => (float) $totalRevenue,
            'expenses'      => (float) $expenses,
            'cogs'          => $cogs,
            'net_profit'    => $netProfit,
            'low_stock'     => $lowStock,
        ];

        Mail::to($emails)->queue(new WeeklySalesReport($data));
    }
}
