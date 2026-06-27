<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\WholesaleOrder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Coupon;
use App\Services\AiStrategicService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AiDashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('owner');

        $service = app(AiStrategicService::class);
        $advices = $service->getSellingAdvices();

        $health = $this->computeHealthScore();
        $anomalies = $this->detectAnomalies();
        $peakHours = $this->getPeakHours();
        $todayStats = $this->getTodayStats();
        $trends = $this->getTrendAnalysis();
        $forecast = $this->getRevenueForecast();
        $categoryTrends = $this->getCategoryTrends();
        $cashFlow = $this->getCashFlowInsight();
        $topBottomProducts = $this->getTopBottomProducts();
        $customerInsights = $this->getCustomerInsights();
        $expenseInsight = $this->getExpenseBreakdown();
        $suppliers = $this->getSupplierSummary();
        $branches = $this->getBranchOverview();
        $wholesale = $this->getWholesaleOverview();
        $activities = $this->getRecentActivities();
        $lowStockItems = $this->getLowStockDetails();
        $employeeToday = $this->getEmployeeToday();
        $promos = $this->getPromoSummary();
        $topCustomers = $this->getTopCustomers();
        $revenueComposition = $this->getRevenueComposition();
        $stockValue = $this->getStockValue();
        $insights = $this->generateInsights($health, $trends, $forecast, $anomalies);

        return view('owner.ai-dashboard', compact(
            'advices', 'health', 'anomalies', 'peakHours', 'todayStats',
            'trends', 'forecast', 'categoryTrends', 'cashFlow',
            'topBottomProducts', 'customerInsights', 'expenseInsight', 'insights',
            'suppliers', 'branches', 'wholesale', 'activities',
            'lowStockItems', 'employeeToday', 'promos', 'topCustomers',
            'revenueComposition', 'stockValue'
        ));
    }

    private function computeHealthScore(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $now = Carbon::now();
        $daysElapsed = max(1, (int) $now->diffInDays($start) + 1);
        $daysInMonth = (int) $now->daysInMonth;

        $revenue = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $cogs = (float) TransactionDetail::whereBetween('transaction_details.created_at', [$start, $end])
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));
        $expenses = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $netProfit = $revenue - $cogs - $expenses;
        $margin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        $profitScore = min(30, max(0, ($margin / 20) * 30));

        $lowStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outStock = Inventory::where('current_stock', '<=', 0)->count();
        $nearExpiry = Inventory::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->where('current_stock', '>', 0)->count();
        $totalInv = Product::count();
        $stockIssuePct = $totalInv > 0 ? (($lowStock + $outStock) / $totalInv) * 100 : 0;
        $stockScore = min(25, max(0, 25 - ($stockIssuePct / 100) * 25));

        $prevStart = (clone $start)->subMonth();
        $prevEnd = (clone $end)->subMonth();
        $prevRevenue = (float) Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');
        $growth = $prevRevenue > 0 ? (($revenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $growthScore = min(20, max(0, 10 + ($growth / 50) * 10));

        $cashIn = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('paid_amount');
        $cashOut = $expenses;
        $liquidityRatio = $cashOut > 0 ? $cashIn / $cashOut : 2;
        $liquidityScore = min(15, max(0, ($liquidityRatio / 2) * 15));

        $transactionCount = Transaction::whereBetween('created_at', [$start, $end])->count();
        $avgDaily = $transactionCount / $daysElapsed;
        $efficiencyScore = min(10, max(0, ($avgDaily / 20) * 10));

        $totalScore = round($profitScore + $stockScore + $growthScore + $liquidityScore + $efficiencyScore);

        $grade = $totalScore >= 85 ? 'Sangat Sehat' : ($totalScore >= 70 ? 'Sehat' : ($totalScore >= 50 ? 'Cukup' : ($totalScore >= 30 ? 'Kurang Sehat' : 'Kritis')));
        $gradeClass = $totalScore >= 85 ? 'success' : ($totalScore >= 70 ? 'primary' : ($totalScore >= 50 ? 'warning' : ($totalScore >= 30 ? 'orange' : 'danger')));

        return compact(
            'totalScore', 'grade', 'gradeClass', 'profitScore', 'stockScore',
            'growthScore', 'liquidityScore', 'efficiencyScore', 'margin', 'netProfit',
            'lowStock', 'outStock', 'nearExpiry', 'growth', 'liquidityRatio', 'avgDaily',
            'revenue', 'expenses', 'cogs', 'daysElapsed', 'daysInMonth', 'transactionCount'
        );
    }

    private function detectAnomalies(): array
    {
        $now = Carbon::now();
        $anomalies = [];

        $todaySales = (float) Transaction::whereDate('created_at', $now)->sum('total_amount');
        $yesterdaySales = (float) Transaction::whereDate('created_at', $now->copy()->subDay())->sum('total_amount');
        if ($yesterdaySales > 0 && $todaySales < $yesterdaySales * 0.3) {
            $dropPct = round((1 - $todaySales / $yesterdaySales) * 100, 1);
            $anomalies[] = [
                'type' => 'danger', 'icon' => 'fa-chart-line', 'title' => 'Penjualan Drop Drastis',
                'text' => "Penjualan hari ini turun {$dropPct}% dibanding kemarin (Rp " . number_format($yesterdaySales, 0, ',', '.') . " → Rp " . number_format($todaySales, 0, ',', '.') . ").",
                'action' => 'Cek aktivitas toko, pastikan kasir aktif dan stok tersedia.'
            ];
        } elseif ($todaySales > 0 && $yesterdaySales > 0 && $todaySales < $yesterdaySales * 0.7) {
            $dropPct = round((1 - $todaySales / $yesterdaySales) * 100, 1);
            $anomalies[] = [
                'type' => 'warning', 'icon' => 'fa-chart-line', 'title' => 'Penjualan Menurun',
                'text' => "Penjualan hari ini turun {$dropPct}% dari kemarin.",
                'action' => 'Pantau situasi hingga jam tutup.'
            ];
        }

        $monthExpenses = (float) Expense::whereMonth('created_at', $now->month)->sum('amount');
        $lastMonthExpenses = (float) Expense::whereMonth('created_at', $now->copy()->subMonth()->month)->sum('amount');
        if ($lastMonthExpenses > 0 && $monthExpenses > $lastMonthExpenses * 1.5) {
            $spikePct = round(($monthExpenses / $lastMonthExpenses) * 100, 1);
            $anomalies[] = [
                'type' => 'warning', 'icon' => 'fa-coins', 'title' => 'Pengeluaran Membengkak',
                'text' => "Pengeluaran bulan ini {$spikePct}% dari bulan lalu (Rp " . number_format($lastMonthExpenses, 0, ',', '.') . " → Rp " . number_format($monthExpenses, 0, ',', '.') . ").",
                'action' => 'Review laporan pengeluaran per kategori. Identifikasi pos yang membengkak.'
            ];
        }

        $outOfStock = Inventory::where('current_stock', '<=', 0)->count();
        if ($outOfStock > 20) {
            $anomalies[] = [
                'type' => 'danger', 'icon' => 'fa-box-open', 'title' => "{$outOfStock} SKU Habis",
                'text' => "Terlalu banyak produk habis. Pelanggan potensial bisa beralih ke kompetitor.",
                'action' => 'Prioritaskan restok untuk produk best-seller yang habis.'
            ];
        } elseif ($outOfStock > 10) {
            $anomalies[] = [
                'type' => 'warning', 'icon' => 'fa-box-open', 'title' => "{$outOfStock} SKU Habis",
                'text' => "{$outOfStock} produk habis. Percepat proses Purchase Order.",
                'action' => 'Cek produk best-seller dan segera buat PO.'
            ];
        }

        $todayShift = Shift::whereDate('start_time', $now)->first();
        if (!$todayShift) {
            $anomalies[] = [
                'type' => 'danger', 'icon' => 'fa-clock', 'title' => 'Toko Belum Buka',
                'text' => 'Tidak ada shift yang dibuka hari ini. Toko mungkin belum beroperasi.',
                'action' => 'Segera hubungi penanggung jawab shift.'
            ];
        }

        $todayAttendance = Attendance::whereDate('date', $now)->count();
        if ($todayAttendance === 0) {
            $anomalies[] = [
                'type' => 'info', 'icon' => 'fa-user-clock', 'title' => 'Belum Ada Absensi',
                'text' => 'Belum ada karyawan yang absen hari ini.',
                'action' => 'Ingatkan karyawan untuk melakukan check-in.'
            ];
        }

        $criticalStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)->count();
        if ($criticalStock > 15) {
            $anomalies[] = [
                'type' => 'warning', 'icon' => 'fa-exclamation-triangle', 'title' => "{$criticalStock} Stok Kritis",
                'text' => "{$criticalStock} produk di bawah batas minimum. Berpotensi habis dalam waktu dekat.",
                'action' => 'Buat prioritas Purchase Order untuk produk dengan penjualan tertinggi.'
            ];
        }

        return $anomalies;
    }

    private function getPeakHours(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $hourlyData = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $dailyData = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DAYNAME(created_at) as day_name'), DB::raw('COUNT(*) as count'))
            ->groupBy('day_name')
            ->orderByDesc('count')
            ->get();

        if ($hourlyData->isNotEmpty()) {
            $maxCount = $hourlyData->max('count');
            $quietHour = $hourlyData->sortBy('count')->first();
        } else {
            $maxCount = 0;
            $quietHour = null;
        }

        $peakHour = $hourlyData->sortByDesc('count')->first();
        $peakDay = $dailyData->first();
        $dayMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];

        return compact('hourlyData', 'dailyData', 'peakHour', 'peakDay', 'dayMap', 'maxCount', 'quietHour');
    }

    private function getTodayStats(): array
    {
        $today = Carbon::now()->toDateString();

        $revenue = (float) Transaction::whereDate('created_at', $today)->sum('total_amount');
        $yesterdayRevenue = (float) Transaction::whereDate('created_at', Carbon::now()->subDay())->sum('total_amount');
        $revenueChange = $yesterdayRevenue > 0 ? round((($revenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) : 0;

        $transactions = Transaction::whereDate('created_at', $today)->count();
        $yesterdayTransactions = Transaction::whereDate('created_at', Carbon::now()->subDay())->count();
        $transChange = $yesterdayTransactions > 0 ? round((($transactions - $yesterdayTransactions) / $yesterdayTransactions) * 100, 1) : 0;

        return compact(
            'revenue', 'yesterdayRevenue', 'revenueChange',
            'transactions', 'yesterdayTransactions', 'transChange'
        ) + [
            'customers' => Customer::whereDate('created_at', $today)->count(),
            'low_stock' => Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0)->count(),
            'out_of_stock' => Inventory::where('current_stock', '<=', 0)->count(),
            'pending_orders' => WholesaleOrder::whereIn('status', ['pending', 'reviewed'])->count(),
            'active_shift' => Shift::whereNull('end_time')->with('user')->first(),
            'today_expenses' => (float) Expense::whereDate('created_at', $today)->sum('amount'),
        ];
    }

    private function getTrendAnalysis(): array
    {
        $now = Carbon::now();

        $months = [];
        for ($i = 4; $i >= 0; $i--) {
            $m = (clone $now)->subMonths($i);
            $start = (clone $m)->startOfMonth();
            $end = (clone $m)->endOfMonth();

            $revenue = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
            $count = Transaction::whereBetween('created_at', [$start, $end])->count();
            $expense = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');
            $avgOrder = $count > 0 ? $revenue / $count : 0;

            $months[] = [
                'label' => $m->translatedFormat('M Y'),
                'month' => $m->format('Y-m'),
                'revenue' => $revenue,
                'transactions' => $count,
                'expenses' => $expense,
                'avg_order' => $avgOrder,
            ];
        }

        $revenueChange = [];
        $avgOrderChange = [];
        for ($i = 1; $i < count($months); $i++) {
            $prev = $months[$i - 1]['revenue'] ?? 0;
            $curr = $months[$i]['revenue'] ?? 0;
            $revenueChange[] = $prev > 0 ? round((($curr - $prev) / $prev) * 100, 1) : 0;

            $prevAvg = $months[$i - 1]['avg_order'] ?? 0;
            $currAvg = $months[$i]['avg_order'] ?? 0;
            $avgOrderChange[] = $prevAvg > 0 ? round((($currAvg - $prevAvg) / $prevAvg) * 100, 1) : 0;
        }

        $currentMonth = end($months);
        $prevMonth = $months[count($months) - 2] ?? $currentMonth;

        $revenueTrend = count($revenueChange) >= 2 ? $this->calculateTrend($revenueChange) : 'stabil';

        return compact('months', 'revenueChange', 'avgOrderChange', 'currentMonth', 'prevMonth', 'revenueTrend');
    }

    private function getRevenueForecast(): array
    {
        $now = Carbon::now();
        $startMonth = (clone $now)->startOfMonth();
        $daysElapsed = max(1, (int) $now->diffInDays($startMonth) + 1);
        $daysInMonth = (int) $now->daysInMonth;
        $daysRemaining = $daysInMonth - $daysElapsed;

        $currentRevenue = (float) Transaction::whereBetween('created_at', [$startMonth, $now])->sum('total_amount');
        $dailyAverage = $currentRevenue / $daysElapsed;
        $projectedRevenue = $currentRevenue + ($dailyAverage * $daysRemaining);

        $lastMonthRevenue = (float) Transaction::whereBetween('created_at', [
            (clone $now)->subMonth()->startOfMonth(),
            (clone $now)->subMonth()->endOfMonth()
        ])->sum('total_amount');

        $lastThreeAvg = (float) Transaction::whereBetween('created_at', [
            (clone $now)->subMonths(3)->startOfMonth(),
            (clone $now)->subDay()
        ])->sum('total_amount') / 3;

        $vsLastMonth = $lastMonthRevenue > 0 ? round((($projectedRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        $onTrack = $lastMonthRevenue > 0 ? ($currentRevenue / $lastMonthRevenue) * ($daysInMonth / $daysElapsed) : 1;

        $status = $onTrack >= 1.1 ? 'ahead' : ($onTrack >= 0.9 ? 'on_track' : 'behind');
        $statusLabel = $status === 'ahead' ? 'Di Atas Target' : ($status === 'on_track' ? 'Sesuai Target' : 'Di Bawah Target');
        $statusClass = $status === 'ahead' ? 'success' : ($status === 'on_track' ? 'info' : 'warning');

        $paceNeeded = $daysRemaining > 0 ? (max(0, $lastMonthRevenue - $currentRevenue) / $daysRemaining) : 0;

        return compact(
            'currentRevenue', 'dailyAverage', 'projectedRevenue', 'lastMonthRevenue', 'lastThreeAvg',
            'vsLastMonth', 'onTrack', 'status', 'statusLabel', 'statusClass',
            'daysElapsed', 'daysInMonth', 'daysRemaining', 'paceNeeded'
        );
    }

    private function getCategoryTrends(): array
    {
        $now = Carbon::now();
        $start = (clone $now)->startOfMonth();
        $end = (clone $now)->endOfMonth();
        $prevStart = (clone $now)->subMonth()->startOfMonth();
        $prevEnd = (clone $now)->subMonth()->endOfMonth();

        $current = DB::table('product_categories')
            ->leftJoin('products', 'product_categories.id', '=', 'products.product_category_id')
            ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->whereBetween('transaction_details.created_at', [$start, $end])
            ->select(
                'product_categories.id', 'product_categories.name',
                DB::raw('COALESCE(SUM(transaction_details.quantity), 0) as qty'),
                DB::raw('COALESCE(SUM(transaction_details.subtotal), 0) as revenue')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('revenue')
            ->get()->keyBy('id');

        $previous = DB::table('product_categories')
            ->leftJoin('products', 'product_categories.id', '=', 'products.product_category_id')
            ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->whereBetween('transaction_details.created_at', [$prevStart, $prevEnd])
            ->select(
                'product_categories.id', 'product_categories.name',
                DB::raw('COALESCE(SUM(transaction_details.quantity), 0) as qty'),
                DB::raw('COALESCE(SUM(transaction_details.subtotal), 0) as revenue')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->get()->keyBy('id');

        $categories = [];
        foreach ($current as $id => $c) {
            $prev = $previous->get($id);
            $prevRev = $prev ? (float) $prev->revenue : 0;
            $change = $prevRev > 0 ? round(((floatval($c->revenue) - $prevRev) / $prevRev) * 100, 1) : 0;
            $categories[] = [
                'name' => $c->name,
                'revenue' => (float) $c->revenue,
                'prev_revenue' => $prevRev,
                'qty' => (int) $c->qty,
                'change' => $change,
                'trend' => $change > 5 ? 'up' : ($change < -5 ? 'down' : 'stable'),
            ];
        }

        foreach ($previous as $id => $p) {
            if (!$current->has($id)) {
                $categories[] = [
                    'name' => $p->name,
                    'revenue' => 0,
                    'prev_revenue' => (float) $p->revenue,
                    'qty' => 0,
                    'change' => -100,
                    'trend' => 'down',
                ];
            }
        }

        $topGainer = collect($categories)->sortByDesc('change')->first();
        $topLoser = collect($categories)->sortBy('change')->first();

        return compact('categories', 'topGainer', 'topLoser');
    }

    private function getCashFlowInsight(): array
    {
        $now = Carbon::now();

        $dailyIn = [];
        $dailyOut = [];
        $runningBalance = 0;
        $balances = [];

        for ($i = 20; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i)->toDateString();
            $in = (float) Transaction::whereDate('created_at', $date)->sum('paid_amount');
            $out = (float) Expense::whereDate('created_at', $date)->sum('amount');
            $dailyIn[$date] = $in;
            $dailyOut[$date] = $out;
            $runningBalance += ($in - $out);
            $balances[$date] = $runningBalance;
        }

        $avgDailyIn = count($dailyIn) > 0 ? array_sum($dailyIn) / count($dailyIn) : 0;
        $avgDailyOut = count($dailyOut) > 0 ? array_sum($dailyOut) / count($dailyOut) : 0;

        $currentBalance = $runningBalance;
        $projectedBalance30 = $currentBalance + (($avgDailyIn - $avgDailyOut) * 30);

        $status = $projectedBalance30 > 0 ? 'surplus' : 'defisit';
        $daysUntilZero = $avgDailyOut > $avgDailyIn && $currentBalance > 0
            ? floor($currentBalance / ($avgDailyOut - $avgDailyIn))
            : null;

        return compact('dailyIn', 'dailyOut', 'balances', 'currentBalance', 'projectedBalance30', 'avgDailyIn', 'avgDailyOut', 'status', 'daysUntilZero');
    }

    private function getTopBottomProducts(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $top = TransactionDetail::select(
            'products.name', 'products.brand', 'products.selling_price',
            DB::raw('SUM(transaction_details.quantity) as qty'),
            DB::raw('SUM(transaction_details.subtotal) as revenue')
        )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transaction_details.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name', 'products.brand', 'products.selling_price')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $bottom = TransactionDetail::select(
            'products.name', 'products.brand', 'products.selling_price',
            DB::raw('SUM(transaction_details.quantity) as qty'),
            DB::raw('SUM(transaction_details.subtotal) as revenue')
        )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transaction_details.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name', 'products.brand', 'products.selling_price')
            ->orderBy('qty')
            ->take(5)
            ->get();

        return compact('top', 'bottom');
    }

    private function getCustomerInsights(): array
    {
        $now = Carbon::now();
        $start = (clone $now)->startOfMonth();
        $end = (clone $now)->endOfMonth();
        $prevStart = (clone $now)->subMonth()->startOfMonth();
        $prevEnd = (clone $now)->subMonth()->endOfMonth();

        $newCustomers = Customer::whereBetween('created_at', [$start, $end])->count();
        $prevNewCustomers = Customer::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $newCustomerChange = $prevNewCustomers > 0 ? round((($newCustomers - $prevNewCustomers) / $prevNewCustomers) * 100, 1) : 0;

        $totalCustomers = Customer::count();
        $activeBuyers = Transaction::whereBetween('created_at', [$start, $end])
            ->distinct('customer_id')->count('customer_id');
        $totalBuyers = Transaction::distinct('customer_id')->count('customer_id');
        $repeatBuyers = Transaction::whereBetween('created_at', [$start, $end])
            ->select('customer_id', DB::raw('COUNT(*) as tx_count'))
            ->groupBy('customer_id')
            ->having('tx_count', '>=', 2)
            ->count();

        $avgTransaction = Transaction::whereBetween('created_at', [$start, $end])->avg('total_amount') ?? 0;
        $prevAvgTransaction = Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->avg('total_amount') ?? 0;
        $avgChange = $prevAvgTransaction > 0 ? round((($avgTransaction - $prevAvgTransaction) / $prevAvgTransaction) * 100, 1) : 0;

        return compact(
            'newCustomers', 'prevNewCustomers', 'newCustomerChange',
            'totalCustomers', 'activeBuyers', 'totalBuyers', 'repeatBuyers',
            'avgTransaction', 'prevAvgTransaction', 'avgChange'
        );
    }

    private function getExpenseBreakdown(): array
    {
        $now = Carbon::now();
        $start = (clone $now)->startOfMonth();
        $end = (clone $now)->endOfMonth();

        $byCategory = Expense::select(DB::raw('COALESCE(expense_categories.name, "Tidak Dikategorikan") as category'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereBetween('expenses.created_at', [$start, $end])
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = (float) $byCategory->sum('total');

        $prevStart = (clone $now)->subMonth()->startOfMonth();
        $prevEnd = (clone $now)->subMonth()->endOfMonth();
        $prevByCategory = Expense::select(DB::raw('COALESCE(expense_categories.name, "Tidak Dikategorikan") as category'), DB::raw('SUM(amount) as total'))
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereBetween('expenses.created_at', [$prevStart, $prevEnd])
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->get()->keyBy('category');

        $analysis = [];
        foreach ($byCategory as $c) {
            $prevTotal = (float) ($prevByCategory[$c->category]->total ?? 0);
            $change = $prevTotal > 0 ? round(((floatval($c->total) - $prevTotal) / $prevTotal) * 100, 1) : 0;
            $pct = $totalExpenses > 0 ? round((floatval($c->total) / $totalExpenses) * 100, 1) : 0;
            $analysis[] = [
                'category' => $c->category,
                'total' => (float) $c->total,
                'prev_total' => $prevTotal,
                'count' => $c->count,
                'change' => $change,
                'pct' => $pct,
            ];
        }

        $biggestCategory = collect($analysis)->sortByDesc('total')->first();

        return compact('analysis', 'totalExpenses', 'biggestCategory');
    }

    private function getSupplierSummary(): array
    {
        $total = Supplier::count();
        $poCount = PurchaseOrder::count();
        $poReceived = PurchaseOrder::whereIn('status', ['received', 'partial'])->count();

        $avgLeadTime = PurchaseOrder::whereNotNull('expected_date')->whereNotNull('received_date')
            ->select(DB::raw('COALESCE(AVG(DATEDIFF(received_date, expected_date)), 0) as delay'))
            ->value('delay');
        $avgLeadTime = round((float) $avgLeadTime, 1);

        $top = PurchaseOrder::select('suppliers.name', DB::raw('COUNT(*) as po_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->whereIn('status', ['received', 'partial'])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spent')
            ->first();

        return compact('total', 'poCount', 'poReceived', 'avgLeadTime', 'top');
    }

    private function getBranchOverview(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $branchData = Branch::where('is_active', true)
            ->withAggregate('transactions as revenue', 'sum(total_amount)', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->withAggregate('transactions as tx_count', 'count(*)', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->withAggregate('expenses as expenses_sum', 'sum(amount)', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get()
            ->map(function ($b) {
                $revenue = (float) ($b->revenue ?? 0);
                return [
                    'name' => $b->name,
                    'revenue' => $revenue,
                    'transactions' => (int) ($b->tx_count ?? 0),
                    'expenses' => (float) ($b->expenses_sum ?? 0),
                    'profit' => $revenue - (float) ($b->expenses_sum ?? 0),
                ];
            })->sortByDesc('revenue')->values();

        $totalRevenue = $branchData->sum('revenue');
        $totalExpenses = $branchData->sum('expenses');
        $branchCount = $branchData->count();
        $bestBranch = $branchData->first();

        return compact('branchData', 'totalRevenue', 'totalExpenses', 'branchCount', 'bestBranch');
    }

    private function getWholesaleOverview(): array
    {
        $statuses = ['pending', 'reviewed', 'on_progress', 'packed', 'shipped', 'delivered', 'completed', 'cancelled'];
        $rawCounts = WholesaleOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $defaults = array_fill_keys($statuses, 0);
        $counts = array_merge($defaults, $rawCounts);
        $total = WholesaleOrder::count();
        $totalRevenue = (float) WholesaleOrder::whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');
        $pendingCount = WholesaleOrder::whereIn('status', ['pending', 'reviewed'])->count();

        return compact('counts', 'total', 'totalRevenue', 'pendingCount');
    }

    private function getRecentActivities(): array
    {
        $transactions = Transaction::with(['branch', 'user', 'customer'])
            ->latest()->take(10)->get()->map(function ($t) {
                return [
                    'icon' => 'fa-receipt',
                    'color' => 'success',
                    'title' => $t->invoice_number,
                    'description' => ($t->branch->name ?? '') . ' — ' . ($t->user->name ?? '') . ($t->customer ? ' — ' . $t->customer->name : ''),
                    'amount' => (float) $t->total_amount,
                    'time' => $t->created_at->diffForHumans(),
                ];
            });

        $orders = WholesaleOrder::with('customer')
            ->latest()->take(5)->get()->map(function ($o) {
                $statusLabels = ['pending'=>'Baru','reviewed'=>'Dikonfir','on_progress'=>'Diproses','packed'=>'Packing','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Batal'];
                return [
                    'icon' => 'fa-boxes-packing',
                    'color' => 'info',
                    'title' => $o->invoice_number . ' (' . ($statusLabels[$o->status] ?? $o->status) . ')',
                    'description' => $o->recipient_name . ' — ' . ($o->customer->name ?? ''),
                    'amount' => (float) $o->total_amount,
                    'time' => $o->created_at->diffForHumans(),
                ];
            });

        $activities = $transactions->concat($orders)->sortByDesc('time')->take(15)->values();
        return compact('activities');
    }

    private function getLowStockDetails(): array
    {
        $items = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->with('product')
            ->orderBy('current_stock')
            ->take(10)
            ->get()
            ->map(function ($i) use ($dailySales) {
                $dailySold = max(0.1, (float) ($dailySales[$i->product_id] ?? 0));
                $daysLeft = round($i->current_stock / $dailySold);
                return [
                    'name' => $i->product->name ?? 'Produk #' . $i->product_id,
                    'stock' => $i->current_stock,
                    'min' => $i->minimum_stock,
                    'days_left' => $daysLeft,
                    'daily_sold' => round($dailySold, 1),
                    'pct' => $i->minimum_stock > 0 ? round(($i->current_stock / $i->minimum_stock) * 100) : 0,
                ];
            });

        $totalCritical = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)->count();
        $totalOut = Inventory::where('current_stock', '<=', 0)->count();

        return compact('items', 'totalCritical', 'totalOut');
    }

    private function getEmployeeToday(): array
    {
        $now = Carbon::now()->toDateString();
        $attendances = Attendance::whereDate('date', $now)
            ->whereNull('time_out')
            ->with('user')
            ->get()
            ->map(function ($a) {
                $timeIn = $a->time_in ? Carbon::parse($a->time_in)->format('H:i') : '-';
                return [
                    'name' => $a->user->name ?? $a->employee_name ?? 'Karyawan',
                    'time_in' => $timeIn,
                    'role' => $a->user->role ?? '-',
                ];
            });

        $totalToday = Attendance::whereDate('date', $now)->count();
        $activeNow = $attendances->count();
        $activeShift = Shift::whereNull('end_time')->with('user')->first();

        return compact('attendances', 'totalToday', 'activeNow', 'activeShift');
    }

    private function getPromoSummary(): array
    {
        $active = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expiration_date')->orWhere('expiration_date', '>=', Carbon::now());
            })->count();

        $totalUsage = Coupon::sum('used_count');
        $totalCoupons = Coupon::count();

        $topPromo = Coupon::where('is_active', true)
            ->orderByDesc('used_count')->first();

        return compact('active', 'totalUsage', 'totalCoupons', 'topPromo');
    }

    private function getTopCustomers(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $topRetail = Transaction::select('customers.name', 'customers.phone',
                DB::raw('COUNT(*) as tx_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereNotNull('transactions.customer_id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get();

        $topWholesale = WholesaleOrder::select('users.name', 'users.phone',
                DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->join('users', 'wholesale_orders.user_id', '=', 'users.id')
            ->whereIn('status', ['completed', 'delivered', 'shipped'])
            ->whereBetween('wholesale_orders.created_at', [$start, $end])
            ->groupBy('users.id', 'users.name', 'users.phone')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get();

        return compact('topRetail', 'topWholesale');
    }

    private function getRevenueComposition(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $now = Carbon::now();

        $today = $now->toDateString();
        $todayCash = (float) Transaction::whereDate('created_at', $today)->where('payment_method', 'cash')->sum('total_amount');
        $todayDebt = (float) Transaction::whereDate('created_at', $today)->where('payment_method', 'debt')->sum('total_amount');
        $todayTransfer = (float) Transaction::whereDate('created_at', $today)->where('payment_method', 'transfer')->sum('total_amount');

        // Total for the month by day
        $dailyRev = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $monthTotal = (float) $dailyRev->sum();

        // Weekday distribution
        $weekdayRev = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DAYNAME(created_at) as day_name'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('day_name')
            ->get()
            ->keyBy('day_name');

        $dayNames = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
        $weekdays = [];
        foreach ($dayNames as $en => $id) {
            $d = $weekdayRev->get($en);
            $rev = $d ? (float) $d->total : 0;
            $pct = $monthTotal > 0 ? round(($rev / $monthTotal) * 100, 1) : 0;
            $weekdays[] = ['label' => $id, 'revenue' => $rev, 'pct' => $pct];
        }

        return compact('todayCash', 'todayDebt', 'todayTransfer', 'dailyRev', 'weekdays', 'monthTotal');
    }

    private function getStockValue(): array
    {
        $totalItems = (int) Inventory::sum('current_stock');
        $totalProducts = Product::count();
        $totalValue = (float) Inventory::sum(DB::raw('current_stock * COALESCE(cost_per_unit, 0)'));

        $avgCost = $totalItems > 0 ? $totalValue / $totalItems : 0;

        $categories = DB::table('product_categories')
            ->leftJoin('products', 'product_categories.id', '=', 'products.product_category_id')
            ->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
            ->select('product_categories.name',
                DB::raw('COALESCE(SUM(inventories.current_stock), 0) as total_qty'),
                DB::raw('COALESCE(SUM(inventories.current_stock * COALESCE(inventories.cost_per_unit, 0)), 0) as total_val'))
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('total_val')
            ->get();

        return compact('totalItems', 'totalProducts', 'totalValue', 'avgCost', 'categories');
    }

    private function generateInsights(array $health, array $trends, array $forecast, array $anomalies): array
    {
        $insights = [];

        if ($health['totalScore'] >= 85) {
            $insights[] = ['type' => 'positive', 'icon' => 'fa-star', 'text' => "Skor kesehatan {$health['totalScore']}/100 — Kinerja sangat baik! Pertahankan konsistensi operasional."];
        } elseif ($health['totalScore'] >= 70) {
            $insights[] = ['type' => 'positive', 'icon' => 'fa-thumbs-up', 'text' => "Skor kesehatan {$health['totalScore']}/100 — Bisnis dalam kondisi sehat. Fokus pada peningkatan skor menuju 'Sangat Sehat' (>85)."];
        } elseif ($health['totalScore'] >= 50) {
            $weakPillar = collect(['profitScore' => 'Profitabilitas', 'stockScore' => 'Kesehatan Stok', 'growthScore' => 'Pertumbuhan', 'liquidityScore' => 'Likuiditas', 'efficiencyScore' => 'Efisiensi'])
                ->sortBy(fn($l, $k) => $health[$k])->keys()->first();
            $weakLabel = collect(['profitScore' => 'Profitabilitas', 'stockScore' => 'Kesehatan Stok', 'growthScore' => 'Pertumbuhan', 'liquidityScore' => 'Likuiditas', 'efficiencyScore' => 'Efisiensi'])->get($weakPillar);
            $insights[] = ['type' => 'warning', 'icon' => 'fa-exclamation-circle', 'text' => "Skor kesehatan {$health['totalScore']}/100 — Butuh perbaikan. Fokus utama: {$weakLabel} (skor " . round($health[$weakPillar]) . "/" . str_replace(['profitScore','stockScore','growthScore','liquidityScore','efficiencyScore'], ['30','25','20','15','10'], $weakPillar) . ")."];
        } else {
            $insights[] = ['type' => 'danger', 'icon' => 'fa-exclamation-triangle', 'text' => "Skor kesehatan {$health['totalScore']}/100 — KRITIS. Segera lakukan evaluasi menyeluruh dan intervensi di semua pilar."];
        }

        $trendMonths = $trends['months'] ?? [];
        if (count($trendMonths) >= 2) {
            $trendDesc = $trends['revenueTrend'];
            if ($trendDesc === 'naik') {
                $insights[] = ['type' => 'positive', 'icon' => 'fa-chart-line', 'text' => "Pendapatan konsisten naik dalam 3 bulan terakhir. Ekspansi produk atau cabang layak dipertimbangkan."];
            } elseif ($trendDesc === 'turun') {
                $lastRev = end($trendMonths)['revenue'] ?? 0;
                $firstRev = reset($trendMonths)['revenue'] ?? 0;
                $totalDrop = $firstRev > 0 ? round((($lastRev - $firstRev) / $firstRev) * 100, 1) : 0;
                $insights[] = ['type' => 'danger', 'icon' => 'fa-chart-line', 'text' => "Pendapatan menurun {$totalDrop}% dalam 3 bulan. Evaluasi strategi pemasaran dan operasional."];
            }
        }

        if ($forecast['status'] === 'behind') {
            $insights[] = ['type' => 'warning', 'icon' => 'fa-hourglass-half', 'text' => "Proyeksi akhir bulan Rp " . number_format($forecast['projectedRevenue'], 0, ',', '.') . " ({$forecast['vsLastMonth']}% vs bulan lalu). Butuh rata-rata Rp " . number_format($forecast['paceNeeded'], 0, ',', '.') . "/hari untuk menyamai bulan lalu."];
        } elseif ($forecast['status'] === 'ahead') {
            $insights[] = ['type' => 'positive', 'icon' => 'fa-rocket', 'text' => "Proyeksi akhir bulan Rp " . number_format($forecast['projectedRevenue'], 0, ',', '.') . " ({$forecast['vsLastMonth']}% vs bulan lalu). Performa di atas ekspektasi!"];
        }

        if (count($anomalies) > 0) {
            $criticalAnomalies = collect($anomalies)->where('type', 'danger')->count();
            if ($criticalAnomalies > 0) {
                $insights[] = ['type' => 'danger', 'icon' => 'fa-bell', 'text' => "Ada {$criticalAnomalies} anomali kritis yang membutuhkan perhatian segera."];
            }
        }

        $cashFlowStatus = $this->getCashFlowInsight();
        if ($cashFlowStatus['status'] === 'defisit') {
            $daysUntilZero = $cashFlowStatus['daysUntilZero'];
            if ($daysUntilZero !== null && $daysUntilZero < 30) {
                $insights[] = ['type' => 'danger', 'icon' => 'fa-water', 'text' => "Arus kas diperkirakan defisit dalam {$daysUntilZero} hari jika pola pengeluaran tidak dikendalikan."];
            }
        }

        return $insights;
    }

    private function calculateTrend(array $changes): string
    {
        $positive = count(array_filter($changes, fn($c) => $c > 0));
        $negative = count(array_filter($changes, fn($c) => $c < 0));

        if ($positive >= 2) return 'naik';
        if ($negative >= 2) return 'turun';
        return 'stabil';
    }
}
