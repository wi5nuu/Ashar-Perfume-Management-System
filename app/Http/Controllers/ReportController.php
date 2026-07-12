<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Customer;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected function scopeBranch($query)
    {
        $user = auth()->user();
        if (!$user->isOwner() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }
        return $query;
    }

    public function index()
    {
        Gate::authorize('view_reports');
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        // Combined Revenue: RetailTransactions + WholesaleOrders
        $retailRevenue = Transaction::whereMonth('created_at', now()->month)->sum('total_amount') ?? 0;
        $wholesaleRevenue = \App\Models\WholesaleOrder::whereMonth('created_at', now()->month)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount') ?? 0;
        $totalCombinedRevenue = $retailRevenue + $wholesaleRevenue;

        // Housing Stats (New)
        $housingStats = [
            'mess' => \App\Models\User::where('is_staying_in_mess', true)->count(),
            'house' => \App\Models\User::where('is_staying_in_mess', false)->count(),
        ];

        // Build report cards
        $reportCards = [
            [
                'title' => 'Total Omzet (Gabungan)',
                'value' => 'Rp ' . number_format($totalCombinedRevenue, 0, ',', '.'),
                'color' => 'primary',
                'icon' => 'fas fa-chart-line',
                'link' => route('reports.sales')
            ],
            [
                'title' => 'Total Transaksi',
                'value' => Transaction::whereMonth('created_at', now()->month)->count(),
                'color' => 'info',
                'icon' => 'fas fa-exchange-alt',
                'link' => route('reports.sales')
            ],
            [
                'title' => 'Total Produk',
                'value' => Product::count(),
                'color' => 'success',
                'icon' => 'fas fa-box',
                'link' => '#'
            ],
            [
                'title' => 'Staf di Mes',
                'value' => $housingStats['mess'] . ' Orang',
                'color' => 'warning',
                'icon' => 'fas fa-home',
                'link' => '#'
            ]
        ];

        $recentReports = collect([]);
        
        // Monthly statistics
        $totalProductsSold = DB::table('transaction_details')
            ->whereMonth('created_at', now()->month)
            ->sum('quantity') ?? 0;
            
        // Calculate Expenses for the month
        $monthlyExpenses = Expense::whereMonth('date', now()->month)->sum('amount') ?? 0;

        // BUG-11 FIX: Tambahkan perhitungan COGS
        $monthlyCOGS = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->select(DB::raw('SUM(transaction_details.purchase_price * transaction_details.quantity) as total_cogs'))
            ->value('total_cogs') ?? 0;

        $monthlyStats = [
            'revenue' => $totalCombinedRevenue,
            'expenses' => $monthlyExpenses,
            'transactions' => Transaction::whereMonth('created_at', now()->month)->count() ?? 0,
            'products_sold' => $totalProductsSold,
            'cogs' => $monthlyCOGS,
            'profit' => $totalCombinedRevenue - $monthlyCOGS - $monthlyExpenses
        ];

        // Chart data for monthly performance (last 6 months)
        $monthlyChartData = ['labels' => [], 'revenue' => [], 'expenses' => [], 'profit' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $revRetail = Transaction::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') ?? 0;
            $revWholesale = \App\Models\WholesaleOrder::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount') ?? 0;
            $rev = $revRetail + $revWholesale;

            $exp = Expense::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount') ?? 0;
                
            $monthlyChartData['labels'][] = $date->format('M Y');
            $monthlyChartData['revenue'][] = $rev;
            $monthlyChartData['expenses'][] = $exp;
            $monthlyChartData['profit'][] = $rev - $exp;
        }

        return view('reports.index', compact('reportCards', 'recentReports', 'monthlyStats', 'monthlyChartData', 'housingStats'));
    }
    
    public function sales(Request $request)
    {
        Gate::authorize('view_reports');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $type = $request->get('type', 'daily');
        
        $query = $this->scopeBranch(Transaction::whereBetween('created_at', [$startDate, $endDate]));
        
        if ($type === 'daily') {
            $sales = $query->selectRaw('DATE(created_at) as date, 
                COUNT(*) as transaction_count, 
                SUM(total_amount) as total_sales')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } elseif ($type === 'monthly') {
            $sales = $query->selectRaw('MONTH(created_at) as month, 
                YEAR(created_at) as year,
                COUNT(*) as transaction_count, 
                SUM(total_amount) as total_sales')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else {
            $sales = collect();
        }
        
        $totalSales = $sales->sum('total_sales');
        $totalTransactions = $sales->sum('transaction_count');

        // Payment method distribution
        $paymentData = $this->scopeBranch(Transaction::whereBetween('created_at', [$startDate, $endDate]))
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();
        
        return view('reports.sales', compact('sales', 'totalSales', 'totalTransactions', 'startDate', 'endDate', 'type', 'paymentData'));
    }
    
    public function inventory()
    {
        Gate::authorize('view_reports');
        $lowStock = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereColumn('inventories.current_stock', '<', 'inventories.minimum_stock')
            ->where('inventories.current_stock', '>', 0)
            ->select('products.name', 'inventories.*')
            ->get();
        
        $outOfStock = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->where('inventories.current_stock', 0)
            ->select('products.name', 'inventories.*')
            ->get();
        
        $expiringSoon = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereNotNull('inventories.expiration_date')
            ->where('inventories.expiration_date', '<=', Carbon::now()->addDays(30))
            ->select('products.name', 'inventories.*')
            ->get();
        
        return view('reports.inventory', compact('lowStock', 'outOfStock', 'expiringSoon'));
    }
    
    public function profitLoss(Request $request)
    {
        Gate::authorize('view_reports');
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        // Revenue (Retail)
        $retailRevenue = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total_amount');

        // Revenue (Grosir - hanya yang selesai)
        $wholesaleRevenue = \App\Models\WholesaleOrder::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalRevenue = $retailRevenue + $wholesaleRevenue;

        // BUG-10 FIX: COGS (Harga Pokok Penjualan)
        $cogs = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereMonth('transactions.created_at', $month)
            ->whereYear('transactions.created_at', $year)
            ->selectRaw('SUM(transaction_details.purchase_price * transaction_details.quantity) as total')
            ->value('total') ?? 0;

        // Gross Profit
        $grossProfit = $totalRevenue - $cogs;

        // TODO (Siti - Backlog Agustus): Bug Data Leak Laporan Laba Rugi
        // Saat ini query expenses mengambil total dari semua cabang.
        // Solusi: Gunakan $this->scopeBranch(Expense::whereMonth(...)) agar data akurat.
        // Operating Expenses
        $expenses = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // Net Profit
        $netProfit = $grossProfit - $expenses;
        
        // Buat variabel profit agar kompatibel dengan view lama (atau gunakan netProfit di view)
        $profit = $netProfit;

        // Expense breakdown
        $expenseBreakdown = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.name')
            ->get();
        
        // Revenue by payment method
        $revenueByMethod = $this->scopeBranch(Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year))
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();
        
        return view('reports.profit-loss', compact(
            'totalRevenue', 'retailRevenue', 'wholesaleRevenue',
            'cogs', 'grossProfit', 'expenses', 'netProfit', 'profit',
            'month', 'year', 'expenseBreakdown', 'revenueByMethod'
        ) + ['revenue' => $totalRevenue]);
    }
    
    public function exportSales(Request $request)
    {
        Gate::authorize('view_reports');
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period !== 'custom') {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'last_week':
                    $startDate = Carbon::now()->subWeek()->startOfWeek();
                    $endDate = Carbon::now()->subWeek()->endOfWeek();
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
            }
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $sales = $this->scopeBranch(Transaction::whereBetween('created_at', [$startDate, $endDate]))
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalSales = $sales->sum('total_amount');
        
        $pdf = Pdf::loadView('reports.exports.sales-pdf', compact('sales', 'startDate', 'endDate', 'period', 'totalSales'));
        return $pdf->stream('sales-report-' . $period . '-' . date('Y-m-d') . '.pdf');
    }
    
    public function customerAnalytics()
    {
        Gate::authorize('view_reports');
        $topCustomers = $this->scopeBranch(Customer::withSum('transactions', 'total_amount'))
            ->orderBy('transactions_sum_total_amount', 'desc')
            ->limit(10)
            ->get();
        
        $customerGrowth = $this->scopeBranch(Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $customerTypes = $this->scopeBranch(Customer::selectRaw('type, COUNT(*) as count'))
            ->groupBy('type')
            ->get();
        
        return view('reports.customers', compact('topCustomers', 'customerGrowth', 'customerTypes'));
    }

    public function exportLowStock()
    {
        Gate::authorize('view_reports');
        $lowStock = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereColumn('inventories.current_stock', '<', 'inventories.minimum_stock')
            ->where('inventories.current_stock', '>', 0)
            ->select('products.name', 'inventories.*')
            ->get();
            
        $pdf = Pdf::loadView('reports.exports.inventory-pdf', [
            'items' => $lowStock,
            'title' => 'Laporan Stok Rendah',
            'type' => 'low_stock'
        ]);
        return $pdf->stream('low-stock-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportExpiry()
    {
        Gate::authorize('view_reports');
        $expiringSoon = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereNotNull('inventories.expiration_date')
            ->where('inventories.expiration_date', '<=', Carbon::now()->addDays(30))
            ->select('products.name', 'inventories.*')
            ->get();
            
        $pdf = Pdf::loadView('reports.exports.inventory-pdf', [
            'items' => $expiringSoon,
            'title' => 'Laporan Produk Akan Kadaluarsa',
            'type' => 'expiry'
        ]);
        return $pdf->stream('expiry-report-' . date('Y-m-d') . '.pdf');
    }

    // ─── Export CSV: Transaksi ────────────────────────────────────────────────
    public function exportCsvTransactions(Request $request)
    {
        Gate::authorize('view_reports');
        $startDate = $request->date('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->date('end_date', Carbon::now()->endOfMonth()->toDateString());

        $transactions = $this->scopeBranch(Transaction::with(['user', 'customer']))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'laporan-transaksi-' . $startDate . '-sd-' . $endDate . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            $safe = fn($v) => is_string($v) && strlen($v) > 0 && in_array($v[0], ['=', '+', '-', '@']) ? "'" . $v : $v;
            fputcsv($handle, ['No Invoice', 'Tanggal', 'Kasir', 'Pelanggan', 'Metode Bayar', 'Subtotal', 'Diskon', 'PPN', 'Total', 'Bayar', 'Kembalian', 'Tipe']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    "'" . $t->invoice_number,
                    $t->created_at->format('d/m/Y H:i'),
                    $safe($t->user->name ?? '-'),
                    $safe($t->customer?->name ?? 'Umum'),
                    strtoupper($t->payment_method),
                    $t->subtotal,
                    $t->discount,
                    $t->tax_amount,
                    $t->total_amount,
                    $t->paid_amount,
                    $t->change_amount,
                    $t->customer_type ?? 'retail',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Export CSV: Inventory ────────────────────────────────────────────────
    public function exportCsvInventory()
    {
        Gate::authorize('view_reports');
        $items = DB::table('inventories')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->select(
                'products.name as product_name',
                'products.size',
                'products.unit',
                'product_categories.name as category',
                'inventories.current_stock',
                'inventories.minimum_stock',
                'inventories.cost_per_unit',
                'inventories.expiration_date'
            )
            ->orderBy('products.name')
            ->get();

        $filename = 'laporan-inventory-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            $safe = fn($v) => is_string($v) && strlen($v) > 0 && in_array($v[0], ['=', '+', '-', '@']) ? "'" . $v : $v;
            fputcsv($handle, ['Nama Produk', 'Ukuran', 'Satuan', 'Kategori', 'Stok Saat Ini', 'Stok Minimum', 'Harga Beli/Unit', 'Status', 'Tgl Kadaluarsa']);
            foreach ($items as $item) {
                fputcsv($handle, [
                    $safe($item->product_name),
                    $safe($item->size),
                    $safe($item->unit),
                    $safe($item->category ?? '-'),
                    $item->current_stock,
                    $item->minimum_stock,
                    $item->cost_per_unit,
                    $item->current_stock <= 0 ? 'OUT OF STOCK' : ($item->current_stock <= $item->minimum_stock ? 'LOW STOCK' : 'OK'),
                    $item->expiration_date ?? 'N/A',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sales report to Excel.
     */
    public function exportSalesExcel(Request $request)
    {
        Gate::authorize('view_reports');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesExport($startDate, $endDate),
            'sales-report-' . $startDate . '-to-' . $endDate . '.xlsx'
        );
    }

    /**
     * Export inventory to Excel.
     */
    public function exportInventoryExcel()
    {
        Gate::authorize('view_reports');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryExport(),
            'inventory-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export profit/loss to Excel.
     */
    public function exportProfitLossExcel(Request $request)
    {
        Gate::authorize('view_reports');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProfitLossExport($startDate, $endDate),
            'profit-loss-' . $startDate . '-to-' . $endDate . '.xlsx'
        );
    }
}