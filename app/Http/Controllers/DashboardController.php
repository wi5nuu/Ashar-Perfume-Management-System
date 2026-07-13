<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Expense;
use App\Models\TransactionDetail;
use App\Models\WholesaleOrder;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\StockRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SmartInsightService;
use App\Traits\ResolvesPeriod;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    use ResolvesPeriod;

    protected SmartInsightService $insightService;

    public function __construct(SmartInsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;
        $user  = auth()->user();

        // Branch scoping helper: non-owners see only their branch
        $scopeBranch = function ($query) use ($user) {
            if (!$user->isOwner() && $user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
            return $query;
        };

        // Cache key helper — invalidates when period changes
        $branchKey = $user->isOwner() ? 'all' : ($user->branch_id ?? 'none');
        $ck = fn(string $k) => $this->periodCacheKey("dash.{$branchKey}.{$k}", $period);

        // ── 1. Retail Sales Stats (cached per period, branch-scoped) ─────────────
        $todaySales        = Cache::remember($ck('today_sales'), 60, fn() =>
            $scopeBranch(Transaction::query())->whereDate('created_at', $today)->sum('total_amount'));
        $todayTransactions = Cache::remember($ck('today_transactions'), 60, fn() =>
            $scopeBranch(Transaction::query())->whereDate('created_at', $today)->count());
        $periodSales       = Cache::remember($ck('period_sales'), 120, fn() =>
            $scopeBranch(Transaction::query())->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'));
        $monthSales        = Cache::remember($ck('month_sales'), 300, fn() =>
            $scopeBranch(Transaction::query())->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('total_amount'));

        // ── 1b. Wholesale Sales (cached, branch-scoped) ──────────────────────────
        $wholesaleQuery = fn() => $scopeBranch(WholesaleOrder::query())->where('status', '!=', 'cancelled');

        $wholesaleSalesToday  = Cache::remember($ck('ws_today'), 60, fn() =>
            (clone $wholesaleQuery())->whereDate('created_at', $today)->sum('total_amount'));
        $wholesaleSalesPeriod = Cache::remember($ck('ws_period'), 120, fn() =>
            (clone $wholesaleQuery())->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'));
        $wholesaleSalesMonth  = Cache::remember($ck('ws_month'), 300, fn() =>
            (clone $wholesaleQuery())->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('total_amount'));

        $totalCombinedRevenue = $periodSales + $wholesaleSalesPeriod;

        // ── 1c. Global Counts (cached 5 min, branch-scoped where applicable) ──────────────
        $totalProducts  = Cache::remember('dash_total_products', 300, fn() => Product::count());
        $totalCustomers = Cache::remember("dash_total_customers.{$branchKey}", 300, fn() => Customer::count());

        // ── 2. Stock Alerts (cached 2 min, branch-scoped) ────────────────────
        $lowStockProductsCount = Cache::remember("dash_low_stock_count.{$branchKey}", 120, function () use ($user) {
            $q = DB::table('inventories')->whereColumn('current_stock', '<=', 'minimum_stock');
            if (!$user->isOwner() && $user->branch_id) {
                $q->where('branch_id', $user->branch_id);
            }
            return $q->count();
        });
        try {
            $lowStockAlerts = Cache::remember("dash_low_stock_alerts.{$branchKey}", 120, function () use ($user) {
                $q = DB::table('inventories')
                    ->join('products', 'inventories.product_id', '=', 'products.id')
                    ->select('products.name', 'inventories.current_stock', 'inventories.minimum_stock')
                    ->where(function ($sub) {
                        $sub->whereColumn('inventories.current_stock', '<=', 'inventories.minimum_stock')
                          ->orWhere('inventories.current_stock', '<', 5);
                    });
                if (!$user->isOwner() && $user->branch_id) {
                    $q->where('inventories.branch_id', $user->branch_id);
                }
                return $q->take(5)->get();
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch low stock alerts for dashboard', ['error' => $e->getMessage()]);
            $lowStockAlerts = collect();
        }

        $expiringAlerts = collect();

        // ── 3. Recent Transactions (branch-scoped) ────────────────────────
        $recentTransactions = Cache::remember("dash_recent_transactions.{$branchKey}", 30, function () use ($scopeBranch) {
            return $scopeBranch(Transaction::query())->with(['customer', 'user'])->latest()->take(10)->get();
        });

        // ── 3b. Wholesale Summary ─────────────────────────────────────────
        $wholesaleSummary = Cache::remember("dash_wholesale_summary.{$branchKey}", 120, function () use ($scopeBranch) {
            return $scopeBranch(WholesaleOrder::query())->select('status', DB::raw('count(*) as total'))
                ->where('status', '!=', 'cancelled')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();
        });

        // ── 3c. Active Staff ──────────────────────────────────────────────
        $activeStaff = $scopeBranch(Attendance::query())->whereDate('date', $today)
            ->where('status', 'present')
            ->whereNull('time_out')
            ->get();

        // ── 4. Financial Calculations (role-gated) ────────────────────────
        $periodExpenses    = 0;
        $periodCOGS        = 0;
        $periodGrossProfit = 0;
        $periodProfit      = 0;
        $monthExpenses     = 0;
        $canViewFinance    = $user->can('expenses.view');

        if ($canViewFinance) {
            $periodExpenses = Cache::remember($ck('period_expenses'), 120, fn() =>
                $scopeBranch(Expense::query())->whereBetween('date', [$startDate, $endDate])->sum('amount'));
            $monthExpenses  = Cache::remember($ck('month_expenses'), 300, fn() =>
                $scopeBranch(Expense::query())->whereMonth('date', $month)->whereYear('date', $year)->sum('amount'));

            $periodCOGS = Cache::remember($ck('period_cogs'), 120, function () use ($startDate, $endDate, $user) {
                $q = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                    ->whereBetween('transactions.created_at', [$startDate, $endDate]);
                if (!$user->isOwner() && $user->branch_id) {
                    $q->where('transactions.branch_id', $user->branch_id);
                }
                return $q->select(DB::raw('SUM(transaction_details.purchase_price * transaction_details.quantity) as total_cogs'))
                    ->value('total_cogs') ?? 0;
            });

            $periodGrossProfit = $totalCombinedRevenue - $periodCOGS;
            $periodProfit      = $periodGrossProfit - $periodExpenses;
        }

        // ── 4b. Stock Requests Summary (branch-scoped) ────────────────────
        $pendingStockRequests = Cache::remember("dash_pending_sr.{$branchKey}", 120, function () use ($scopeBranch) {
            return $scopeBranch(StockRequest::query())->where('status', 'pending')->count();
        });
        $recentStockRequests = Cache::remember("dash_recent_sr.{$branchKey}", 60, function () use ($scopeBranch) {
            return $scopeBranch(StockRequest::query())->with(['requester'])->latest()->take(5)->get();
        });
        $stockRequestStats = Cache::remember("dash_sr_stats.{$branchKey}", 300, function () use ($scopeBranch, $month, $year) {
            return $scopeBranch(StockRequest::query())
                ->select('status', DB::raw('count(*) as total'))
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();
        });

        // ── 4c. Today's Expenses (expenses.view required) ─────────────────
        $todayExpenses = 0;
        $recentExpenses = collect();
        if ($canViewFinance) {
            $todayExpenses = Cache::remember($ck('today_expenses'), 60, fn() =>
                $scopeBranch(Expense::query())->whereDate('date', $today)->sum('amount'));
            $recentExpenses = Cache::remember("dash_recent_expenses.{$branchKey}", 60, function () use ($scopeBranch) {
                return $scopeBranch(Expense::query())->with(['category'])->latest()->take(5)->get();
            });
        }

        // ── 5. Chart Data (reports.view required) ─────────────────────────
        $salesData = collect();
        $paymentData = [];
        $topProducts = collect();
        $smartInsights = [];

        if (auth()->user()->can('reports.view')) {
            $salesData = Cache::remember("dash_monthly_sales.{$branchKey}.{$year}", 3600, function () use ($year, $scopeBranch) {
                return $this->getMonthlySalesData($year, $scopeBranch);
            });

            $paymentData = Cache::remember($ck('payment_data'), 300, function () use ($startDate, $endDate, $scopeBranch) {
                return $scopeBranch(Transaction::query())->whereBetween('created_at', [$startDate, $endDate])
                    ->select('payment_method', DB::raw('COUNT(*) as total'))
                    ->groupBy('payment_method')
                    ->pluck('total', 'payment_method')
                    ->toArray();
            });

            // ── 5b. Top Products (cached per branch + month) ──────────────
            $topProducts = Cache::remember("dash_top_products.{$branchKey}.{$month}", 3600, function () use ($month, $branchKey) {
                $query = DB::table('transaction_details')
                    ->join('products', 'transaction_details.product_id', '=', 'products.id')
                    ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                    ->select('products.id', 'products.name', 'products.selling_price',
                        DB::raw('SUM(transaction_details.quantity) as total_sold'))
                    ->whereMonth('transactions.created_at', $month)
                    ->groupBy('products.id', 'products.name', 'products.selling_price')
                    ->orderBy('total_sold', 'desc')
                    ->take(5);

                if ($branchKey !== 'all') {
                    $query->where('transactions.branch_id', $branchKey);
                }

                return $query->get();
            });

            // ── Smart Insights ────────────────────────────────────────────
            try {
                $smartInsights = $this->insightService->generateInsights();
            } catch (\Exception $e) {
                Log::error('Smart insights failed', ['error' => $e->getMessage()]);
                $smartInsights = [];
            }
        }

        // ── 7. Active Shift ───────────────────────────────────────────────
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();

        // ── 8. Branch Revenue (owner only) ────────────────────────────────
        $branchRevenueData = collect();
        if ($user->isOwner()) {
            $branchRevenueData = Branch::where('is_active', true)
                ->withCount('users')
                ->withAggregate(['transactions as period_revenue' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate])], 'total_amount', 'sum')
                ->withAggregate(['transactions as today_revenue' => fn($q) => $q->whereDate('created_at', $today)], 'total_amount', 'sum')
                ->withAggregate(['expenses as period_expenses' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])], 'amount', 'sum')
                ->get()
                ->map(function ($branch) {
                    $branch->period_revenue  = (float) ($branch->period_revenue ?? 0);
                    $branch->today_revenue   = (float) ($branch->today_revenue ?? 0);
                    $branch->period_expenses = (float) ($branch->period_expenses ?? 0);
                    $branch->period_profit   = $branch->period_revenue - $branch->period_expenses;
                    return $branch;
                });
        }

        $profit = $periodProfit;

        return view('dashboard.index', compact(
            'todaySales', 'todayTransactions', 'periodSales',
            'lowStockProductsCount', 'lowStockAlerts',
            'totalCustomers', 'recentTransactions', 'topProducts', 'periodExpenses',
            'salesData', 'expiringAlerts', 'activeShift', 'wholesaleSalesToday',
            'monthSales', 'monthExpenses', 'smartInsights', 'paymentData',
            'profit', 'recentStockRequests', 'stockRequestStats',
            'recentExpenses'
        ));
    }

    /**
     * JSON stats endpoint for live dashboard updates.
     */
    public function getStats(Request $request)
    {
        if (!auth()->user()->can('reports.view')) {
            return response()->json([]);
        }

        $user = auth()->user();
        $period = $request->get('period', 'this_month');
        [$startDate, $endDate] = $this->resolvePeriod($period);

        $scope = fn($q) => $user->isOwner() || !$user->branch_id ? $q : $q->where('branch_id', $user->branch_id);

        $canViewFinance = $user->can('expenses.view');

        $todaySales  = $scope(Transaction::query())->whereDate('created_at', Carbon::today())->sum('total_amount');
        $periodSales = $scope(Transaction::query())->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');

        $periodExpenses = 0;
        $netProfit      = 0;

        if ($canViewFinance) {
            $periodExpenses = $scope(Expense::query())->whereBetween('date', [$startDate, $endDate])->sum('amount');

            $periodCOGS = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->whereBetween('transactions.created_at', [$startDate, $endDate]);
            if (!$user->isOwner() && $user->branch_id) {
                $periodCOGS->where('transactions.branch_id', $user->branch_id);
            }
            $periodCOGS = $periodCOGS->select(DB::raw('SUM(transaction_details.purchase_price * transaction_details.quantity) as total_cogs'))
                ->value('total_cogs') ?? 0;

            $netProfit = $periodSales - $periodCOGS - $periodExpenses;
        }

        $inventoryScope = $user->isOwner() || !$user->branch_id ? '' : $user->branch_id;
        $lowStockCount = Cache::remember('dash_low_stock_count_stats_' . $inventoryScope, 120, function () use ($inventoryScope) {
            $q = DB::table('inventories');
            if ($inventoryScope) $q->where('branch_id', $inventoryScope);
            return $q->where(function ($q) {
                    $q->whereColumn('current_stock', '<=', 'minimum_stock')
                      ->orWhere('current_stock', '<', 5);
                })->count();
        });

        $totalCustomers = Cache::remember('dash_total_customers_stats', 300, fn() => Customer::count());

        $wholesaleToday = $scope(WholesaleOrder::query())->whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $wholesalePeriod = $scope(WholesaleOrder::query())->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $fmt = fn($v) => 'Rp ' . number_format($v, 0, ',', '.');

        return response()->json([
            'todaySales'      => $fmt($todaySales),
            'periodSales'     => $fmt($periodSales),
            'wholesaleToday'  => $fmt($wholesaleToday),
            'wholesalePeriod' => $fmt($wholesalePeriod),
            'periodExpenses'  => $fmt($periodExpenses),
            'netProfit'       => $fmt($netProfit),
            'lowStockCount'   => $lowStockCount,
            'totalCustomers'  => $totalCustomers,
            'smartInsights'   => $this->insightService->getRoleSpecificInsights($user->role),
        ]);
    }

    /**
     * YoY / MoM comparison data (AJAX endpoint).
     */
    public function comparison(Request $request)
    {
        if (!auth()->user()->can('reports.view')) {
            return response()->json([]);
        }

        $mode = $request->get('mode', 'mom'); // mom | yoy
        $user = auth()->user();

        $scopeBranch = function ($query) use ($user) {
            if (!$user->isOwner() && $user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
            return $query;
        };

        $now = Carbon::now();

        if ($mode === 'yoy') {
            // This year vs last year
            $currentYear  = $now->year;
            $previousYear = $now->year - 1;

            $currentStart  = Carbon::createFromDate($currentYear, 1, 1)->startOfDay();
            $currentEnd    = Carbon::createFromDate($currentYear, $now->month, $now->day)->endOfDay();
            $previousStart = Carbon::createFromDate($previousYear, 1, 1)->startOfDay();
            $previousEnd   = Carbon::createFromDate($previousYear, $now->month, $now->day)->endOfDay();

            $currentLabel  = "Jan–{$now->format('d M')} {$currentYear}";
            $previousLabel = "Jan–{$now->format('d M')} {$previousYear}";
        } else {
            // This month vs last month
            $currentStart  = $now->copy()->startOfMonth();
            $currentEnd    = $now->copy()->endOfMonth();
            $previousStart = $now->copy()->subMonth()->startOfMonth();
            $previousEnd   = $now->copy()->subMonth()->endOfMonth();

            $currentLabel  = $now->format('M Y');
            $previousLabel = $now->copy()->subMonth()->format('M Y');
        }

        // Helper: compute KPIs for a date range
        $compute = function (Carbon $start, Carbon $end) use ($scopeBranch, $user) {
            $revenue = (float) $scopeBranch(Transaction::query())
                ->whereBetween('created_at', [$start, $end])->sum('total_amount');

            $transactions = (int) $scopeBranch(Transaction::query())
                ->whereBetween('created_at', [$start, $end])->count();

            $wholesaleRevenue = (float) $scopeBranch(WholesaleOrder::query())
                ->where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$start, $end])->sum('total_amount');

            $wholesaleOrders = (int) $scopeBranch(WholesaleOrder::query())
                ->where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$start, $end])->count();

            $totalRevenue = $revenue + $wholesaleRevenue;
            $totalTransactions = $transactions + $wholesaleOrders;

            $cogs = (float) (TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->whereBetween('transactions.created_at', [$start, $end])
                ->when(!$user->isOwner() && $user->branch_id, fn($q) => $q->where('transactions.branch_id', $user->branch_id))
                ->select(DB::raw('SUM(transaction_details.purchase_price * transaction_details.quantity) as cogs'))
                ->value('cogs') ?? 0);

            $expenses = (float) $scopeBranch(Expense::query())
                ->whereBetween('date', [$start, $end])->sum('amount');

            $profit = $totalRevenue - $cogs - $expenses;
            $avgBasket = $transactions > 0 ? $revenue / $transactions : 0;

            return [
                'revenue'       => $totalRevenue,
                'transactions'  => $totalTransactions,
                'profit'        => $profit,
                'avg_basket'    => $avgBasket,
            ];
        };

        $current  = $compute($currentStart, $currentEnd);
        $previous = $compute($previousStart, $previousEnd);

        // Delta helper
        $delta = fn($cur, $prev) => $prev > 0 ? round((($cur - $prev) / $prev) * 100, 1) : ($cur > 0 ? 100 : 0);

        $fmt = fn($v) => 'Rp ' . number_format($v, 0, ',', '.');

        return response()->json([
            'mode'          => $mode,
            'current_label' => $currentLabel,
            'previous_label'=> $previousLabel,
            'kpis' => [
                'revenue' => [
                    'label'    => 'Total Revenue',
                    'current'  => $fmt($current['revenue']),
                    'previous' => $fmt($previous['revenue']),
                    'delta'    => $delta($current['revenue'], $previous['revenue']),
                ],
                'transactions' => [
                    'label'    => 'Transaksi (Eceran+Grosir)',
                    'current'  => $current['transactions'],
                    'previous' => $previous['transactions'],
                    'delta'    => $delta($current['transactions'], $previous['transactions']),
                ],
                'profit' => [
                    'label'    => 'Laba Bersih',
                    'current'  => $fmt($current['profit']),
                    'previous' => $fmt($previous['profit']),
                    'delta'    => $delta($current['profit'], $previous['profit']),
                ],
                'avg_basket' => [
                    'label'    => 'Rata-rata Belanja',
                    'current'  => $fmt($current['avg_basket']),
                    'previous' => $fmt($previous['avg_basket']),
                    'delta'    => $delta($current['avg_basket'], $previous['avg_basket']),
                ],
            ],
        ]);
    }

    private function getMonthlySalesData(int $year, ?\Closure $scopeBranch = null): array
    {
        $query = Transaction::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as sales')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month');

        if ($scopeBranch) {
            $scopeBranch($query);
        }

        $monthlySales = $query->get()->keyBy('month');

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = [
                'month' => Carbon::create()->month($m)->format('M'),
                'sales' => (float) ($monthlySales->get($m)?->sales ?? 0),
            ];
        }
        return $data;
    }
}
