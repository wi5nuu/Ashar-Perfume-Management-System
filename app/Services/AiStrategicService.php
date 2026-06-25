<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiStrategicService
{
    public function getSellingAdvices()
    {
        $advices = [];

        $crossSell = $this->getCrossSellOpportunity();
        if ($crossSell) {
            $advices[] = [
                'type' => 'cross_sell',
                'title' => 'Paket Bundling Strategis',
                'text' => "Pelanggan sering membeli *{$crossSell->p1_name}* bersamaan dengan *{$crossSell->p2_name}* ({$crossSell->frequency}x dalam 3 bulan). Buat paket bundling spesial atau tawarkan diskon saat pembelian kedua produk.",
                'icon' => 'fa-layer-group',
                'color' => 'text-success'
            ];
        }

        $slowMoving = $this->getSlowMovingProduct();
        if ($slowMoving) {
            $advices[] = [
                'type' => 'slow_moving',
                'title' => 'Revitalisasi Produk Lambat',
                'text' => "*{$slowMoving->name}* (sisa {$slowMoving->current_stock} unit) tidak terjual dalam 30 hari. Beri diskon musiman, pindahkan ke area display utama, atau jadikan bonus pembelian untuk mempercepat perputaran.",
                'icon' => 'fa-clock',
                'color' => 'text-warning'
            ];
        }

        $highMargin = $this->getHighProfitPotential();
        if ($highMargin) {
            $advices[] = [
                'type' => 'high_profit',
                'title' => 'Fokus Produk Margins Tinggi',
                'text' => "*{$highMargin->name}* memiliki laba per unit sangat tinggi (Rp " . number_format($highMargin->margin, 0, ',', '.') . "). Tingkatkan visibility-nya di etalase dan latih tim kasir untuk menawarkannya sebagai produk premium pilihan.",
                'icon' => 'fa-chart-pie',
                'color' => 'text-primary'
            ];
        }

        $peakTime = $this->getPeakTimeRecommendation();
        if ($peakTime) {
            $advices[] = [
                'type' => 'peak_time',
                'title' => 'Optimasi Jadwal Karyawan',
                'text' => "Jam sibuk sekitar pukul *{$peakTime->hour}:00* ({$peakTime->count} transaksi). Pastikan jumlah kasir mencukupi dan stok produk cepat saji terisi penuh menjelang jam tersebut.",
                'icon' => 'fa-users',
                'color' => 'text-info'
            ];
        }

        $deadStock = $this->getDeadStockValue();
        if ($deadStock) {
            $advices[] = [
                'type' => 'dead_stock',
                'title' => 'Peringatan Potensi Rugi',
                'text' => "Ada *{$deadStock->count} produk* mendekati kadaluarsa (total {$deadStock->units} unit, nilai Rp " . number_format($deadStock->value, 0, ',', '.') . "). Segera terapkan strategi obral atau donasi untuk mengurangi kerugian.",
                'icon' => 'fa-exclamation-triangle',
                'color' => 'text-danger'
            ];
        }

        $nextReorder = $this->getNextReorderRecommendation();
        if ($nextReorder) {
            $advices[] = [
                'type' => 'restock',
                'title' => 'Rekomendasi Restok Otomatis',
                'text' => "Produk *{$nextReorder->name}* (sisa {$nextReorder->stock} dari minimum {$nextReorder->min}) perlu segera dipesan. Berdasarkan kecepatan penjualan ({$nextReorder->daily_sold} unit/hari), stok diperkirakan habis dalam {$nextReorder->days_left} hari. Segera lakukan Purchase Order.",
                'icon' => 'fa-truck-loading',
                'color' => 'text-secondary'
            ];
        }

        $newCustomerTrend = $this->getCustomerTrend();
        if ($newCustomerTrend) {
            $advices[] = [
                'type' => 'customer_trend',
                'title' => 'Tren Pertumbuhan Pelanggan',
                'text' => "Dalam 30 hari terakhir, ada *{$newCustomerTrend->new} pelanggan baru* ({$newCustomerTrend->pct}% dari total). Pertahankan momentum ini dengan program loyalitas atau diskon untuk pembelian kedua.",
                'icon' => 'fa-users',
                'color' => 'text-success'
            ];
        }

        $expenseAdvice = $this->getExpenseAdvice();
        if ($expenseAdvice) {
            $advices[] = $expenseAdvice;
        }

        $pricingAdvice = $this->getPricingRecommendation();
        if ($pricingAdvice) {
            $advices[] = $pricingAdvice;
        }

        $stockOptimization = $this->getStockOptimization();
        if ($stockOptimization) {
            $advices[] = $stockOptimization;
        }

        $branchInsight = $this->getBranchInsight();
        if ($branchInsight) {
            $advices[] = $branchInsight;
        }

        $seasonalAdvice = $this->getSeasonalAdvice();
        if ($seasonalAdvice) {
            $advices[] = $seasonalAdvice;
        }

        return $advices;
    }

    protected function getCrossSellOpportunity()
    {
        return DB::table('transaction_details as t1')
            ->join('transaction_details as t2', 't1.transaction_id', '=', 't2.transaction_id')
            ->join('products as p1', 't1.product_id', '=', 'p1.id')
            ->join('products as p2', 't2.product_id', '=', 'p2.id')
            ->select('p1.name as p1_name', 'p2.name as p2_name', DB::raw('COUNT(*) as frequency'))
            ->where('t1.product_id', '<', 't2.product_id')
            ->where('t1.created_at', '>=', Carbon::now()->subMonths(3))
            ->groupBy('p1.id', 'p2.id', 'p1.name', 'p2.name')
            ->orderByDesc('frequency')
            ->first();
    }

    protected function getSlowMovingProduct()
    {
        $recentlySoldIds = TransactionDetail::where('created_at', '>=', Carbon::now()->subDays(30))
            ->pluck('product_id')->unique()->toArray();

        return Product::join('inventories', 'products.id', '=', 'inventories.product_id')
            ->whereNotIn('products.id', $recentlySoldIds)
            ->where('inventories.current_stock', '>', 5)
            ->select('products.name', 'inventories.current_stock')
            ->orderByDesc('inventories.current_stock')
            ->first();
    }

    protected function getHighProfitPotential()
    {
        return Product::join('inventories', 'products.id', '=', 'inventories.product_id')
            ->select('products.name', DB::raw('(products.selling_price - inventories.cost_per_unit) as margin'))
            ->where('inventories.current_stock', '>', 0)
            ->orderByDesc('margin')
            ->first();
    }

    protected function getPeakTimeRecommendation()
    {
        return Transaction::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();
    }

    protected function getDeadStockValue()
    {
        $nearExp = Inventory::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->where('current_stock', '>', 0);

        $totalUnits = (int) (clone $nearExp)->sum('current_stock');
        $totalVal = (float) (clone $nearExp)->sum(DB::raw('current_stock * COALESCE(cost_per_unit, 0)'));
        $count = $nearExp->count();

        if ($count === 0) return null;

        return (object) ['count' => $count, 'units' => $totalUnits, 'value' => $totalVal];
    }

    protected function getNextReorderRecommendation()
    {
        $nearCritical = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->with('product')
            ->orderByRaw('(current_stock / minimum_stock) ASC')
            ->first();

        if (!$nearCritical) return null;

        $dailySold = (float) TransactionDetail::where('product_id', $nearCritical->product_id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('quantity') / 30;
        $dailySold = max(0.1, $dailySold);
        $daysLeft = round($nearCritical->current_stock / $dailySold);

        return (object) [
            'name' => $nearCritical->product->name,
            'stock' => $nearCritical->current_stock,
            'min' => $nearCritical->minimum_stock,
            'daily_sold' => round($dailySold, 1),
            'days_left' => $daysLeft,
        ];
    }

    protected function getCustomerTrend()
    {
        $newCount = Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $total = Customer::count();
        if ($newCount === 0) return null;
        $pct = $total > 0 ? round(($newCount / $total) * 100, 1) : 0;
        return (object) ['new' => $newCount, 'pct' => $pct];
    }

    protected function getExpenseAdvice(): ?array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $totalExpense = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $totalRevenue = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');

        if ($totalRevenue <= 0 || $totalExpense <= 0) return null;

        $ratio = round(($totalExpense / $totalRevenue) * 100, 1);

        if ($ratio > 80) {
            $biggestCategory = Expense::select(DB::raw('COALESCE(expense_categories.name, "Tidak Dikategorikan") as category'), DB::raw('SUM(amount) as total'))
                ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->whereBetween('expenses.created_at', [$start, $end])
                ->groupBy('expense_categories.id', 'expense_categories.name')
                ->orderByDesc('total')
                ->first();

            $topCatName = $biggestCategory->category ?? '-';
            $topCatAmount = $biggestCategory->total ?? 0;

            return [
                'type' => 'expense_warning',
                'title' => 'Peringatan Rasio Biaya Tinggi',
                'text' => "Rasio pengeluaran terhadap pendapatan mencapai *{$ratio}%* (ambang batas aman 50%). Kategori terbesar: *{$topCatName}* (Rp " . number_format($topCatAmount, 0, ',', '.') . "). Evaluasi ulang anggaran dan identifikasi pos yang bisa ditekan.",
                'icon' => 'fa-chart-line',
                'color' => 'text-danger'
            ];
        }

        return null;
    }

    protected function getPricingRecommendation(): ?array
    {
        $products = Product::join('inventories', 'products.id', '=', 'inventories.product_id')
            ->where('inventories.current_stock', '>', 0)
            ->select(
                'products.id', 'products.name', 'products.selling_price',
                'inventories.cost_per_unit', 'inventories.current_stock'
            )
            ->get();

        $recommendations = [];

        foreach ($products as $p) {
            $cost = (float) ($p->cost_per_unit ?? 0);
            $price = (float) ($p->selling_price ?? 0);
            if ($cost <= 0 || $price <= 0) continue;

            $margin = $price - $cost;
            $marginPct = ($margin / $price) * 100;

            if ($marginPct < 10) {
                $recommendations[] = [
                    'name' => $p->name,
                    'current_price' => $price,
                    'cost' => $cost,
                    'margin_pct' => round($marginPct, 1),
                    'suggested_price' => (int) ceil($cost * 1.25 / 100) * 100,
                ];
            }
        }

        if (empty($recommendations)) return null;

        usort($recommendations, fn($a, $b) => $a['margin_pct'] <=> $b['margin_pct']);
        $worst = $recommendations[0];

        if ($worst['margin_pct'] < 5) {
            return [
                'type' => 'pricing',
                'title' => '⚠️ Produk dengan Margin Terlalu Tipis',
                'text' => "*{$worst['name']}* hanya memiliki margin {$worst['margin_pct']}% (harga jual Rp " . number_format($worst['current_price'], 0, ',', '.') . ", modal Rp " . number_format($worst['cost'], 0, ',', '.') . "). Disarankan naikkan harga menjadi Rp " . number_format($worst['suggested_price'], 0, ',', '.') . " (margin 20%). Ada {$this->countLowMarginProducts($products)} produk dengan margin <10%.",
                'icon' => 'fa-tags',
                'color' => 'text-danger'
            ];
        }

        return null;
    }

    protected function countLowMarginProducts($products): int
    {
        $count = 0;
        foreach ($products as $p) {
            $cost = (float) ($p->cost_per_unit ?? 0);
            $price = (float) ($p->selling_price ?? 0);
            if ($cost > 0 && $price > 0) {
                $marginPct = (($price - $cost) / $price) * 100;
                if ($marginPct < 10) $count++;
            }
        }
        return $count;
    }

    protected function getStockOptimization(): ?array
    {
        $overstock = Inventory::where('current_stock', '>', 100)
            ->whereColumn('current_stock', '>', DB::raw('minimum_stock * 5'))
            ->with('product')
            ->orderByDesc('current_stock')
            ->first();

        if (!$overstock) return null;

        $monthlySales = (float) TransactionDetail::where('product_id', $overstock->product_id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('quantity');

        $monthsOfStock = $monthlySales > 0 ? round($overstock->current_stock / $monthlySales, 1) : 999;
        $recommendedMax = max($overstock->minimum_stock * 3, (int) ceil($monthlySales * 2));

        if ($monthsOfStock > 6) {
            return [
                'type' => 'overstock',
                'title' => 'Optimasi Stok Berlebih',
                'text' => "*{$overstock->product->name}* memiliki stok {$overstock->current_stock} unit (cukup untuk {$monthsOfStock} bulan). Disarankan batasi stok maksimal {$recommendedMax} unit. Kelebihan stok mengikat modal dan risiko kadaluarsa.",
                'icon' => 'fa-warehouse',
                'color' => 'text-warning'
            ];
        }

        return null;
    }

    protected function getBranchInsight(): ?array
    {
        $branches = Branch::where('is_active', true)->get();
        if ($branches->count() < 2) return null;

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $performance = [];
        foreach ($branches as $b) {
            $revenue = (float) Transaction::where('branch_id', $b->id)
                ->whereBetween('created_at', [$start, $end])->sum('total_amount');
            $performance[] = ['name' => $b->name, 'revenue' => $revenue];
        }

        usort($performance, fn($a, $b) => $a['revenue'] <=> $b['revenue']);
        $lowest = $performance[0];
        $highest = end($performance);

        if ($highest['revenue'] > 0 && $lowest['revenue'] < $highest['revenue'] * 0.3) {
            $gapPct = round(($lowest['revenue'] / $highest['revenue']) * 100, 1);
            return [
                'type' => 'branch_gap',
                'title' => 'Kesenjangan Kinerja Cabang',
                'text' => "Cabang *{$lowest['name']}* hanya mencapai {$gapPct}% dari pendapatan cabang terbaik (*{$highest['name']}*). Analisis praktik cabang terbaik dan terapkan SOP yang seragam di semua cabang.",
                'icon' => 'fa-store-alt',
                'color' => 'text-info'
            ];
        }

        return null;
    }

    protected function getSeasonalAdvice(): ?array
    {
        $now = Carbon::now();
        $start = (clone $now)->subMonth();
        $end = (clone $now);

        $lastMonthRev = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');

        $samePeriodLastYear = (clone $now)->subYear();
        $yearStart = (clone $samePeriodLastYear)->subMonth();
        $yearEnd = clone $samePeriodLastYear;

        $lastYearRev = (float) Transaction::whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_amount');

        if ($lastYearRev <= 0) return null;

        $yoyChange = round((($lastMonthRev - $lastYearRev) / $lastYearRev) * 100, 1);

        if ($yoyChange > 20) {
            $monthName = $now->translatedFormat('F');
            return [
                'type' => 'seasonal_growth',
                'title' => 'Pertumbuhan Year-over-Year',
                'text' => "Pendapatan {$monthName} naik *{$yoyChange}%* dibanding periode yang sama tahun lalu. Identifikasi faktor pendorong dan pertahankan strategi yang berhasil. Siapkan stok lebih untuk mengantisipasi lonjakan serupa.",
                'icon' => 'fa-calendar-alt',
                'color' => 'text-success'
            ];
        }

        if ($yoyChange < -20) {
            $monthName = $now->translatedFormat('F');
            return [
                'type' => 'seasonal_decline',
                'title' => 'Penurunan Year-over-Year',
                'text' => "Pendapatan {$monthName} turun *{$yoyChange}%* dibanding tahun lalu. Evaluasi perubahan yang terjadi: apakah ada kompetitor baru, perubahan harga, atau penurunan kualitas layanan.",
                'icon' => 'fa-calendar-alt',
                'color' => 'text-danger'
            ];
        }

        return null;
    }
}
