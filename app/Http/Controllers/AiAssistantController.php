<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\WholesaleOrder;
use App\Models\User;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Shift;
use App\Models\Attendance;
use App\Services\AiCopilotService;
use App\Services\AiStrategicService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AiAssistantController extends Controller
{
    protected $strategicService;
    protected $copilotService;
    protected ?int $scopeBranchId = null;

    public function __construct(AiStrategicService $strategicService, AiCopilotService $copilotService)
    {
        $this->strategicService = $strategicService;
        $this->copilotService = $copilotService;
        $user = auth()->user();
        if ($user && !$user->isOwner() && $user->branch_id) {
            $this->scopeBranchId = $user->branch_id;
        }
    }

    private function scope($query): mixed
    {
        return $this->scopeBranchId ? $query->where('branch_id', $this->scopeBranchId) : $query;
    }

    public function ask(Request $request)
    {
        Gate::authorize('view_reports');

        $message = $request->input('message', '');
        $sessionMessages = $request->input('messages', null);

        // Try Claude AI Copilot first if API key is configured
        $apiKey = config('services.anthropic.api_key');
        if (!empty($apiKey)) {
            try {
                $result = $this->copilotService->chat($message, $sessionMessages);
                return response()->json([
                    'reply' => $result['reply'],
                    'messages' => $result['messages'] ?? null,
                    'source' => 'ai',
                    'timestamp' => Carbon::now()->format('H:i')
                ]);
            } catch (\Exception $e) {
                // Fall through to rule-based
            }
        }

        // Rule-based fallback
        $response = $this->handleRuleBased($message);

        return response()->json([
            'reply' => $response,
            'source' => 'rule',
            'timestamp' => Carbon::now()->format('H:i')
        ]);
    }

    private function handleRuleBased(string $message): string
    {
        $msg = strtolower($message);

        // GREETINGS
        if (str_contains($msg, 'halo') || str_contains($msg, 'hi') || str_contains($msg, 'hai') || str_contains($msg, 'pagi') || str_contains($msg, 'siang') || str_contains($msg, 'sore') || str_contains($msg, 'malam') || str_contains($msg, 'siapa')) {
            return $this->greeting();
        }

        // STRATEGIC ADVICE
        if (str_contains($msg, 'saran') || str_contains($msg, 'tips') || str_contains($msg, 'strategi') || str_contains($msg, 'promo') || str_contains($msg, 'jual') || str_contains($msg, 'rekomendasi')) {
            return $this->getStrategicAdvice();
        }

        // SALES
        if (str_contains($msg, 'penjualan') || str_contains($msg, 'omzet') || str_contains($msg, 'pendapatan') || str_contains($msg, 'pemasukan')) {
            if (str_contains($msg, 'hari ini') || str_contains($msg, 'sekarang')) {
                return $this->salesToday();
            }
            if (str_contains($msg, 'kemarin')) {
                return $this->salesYesterday();
            }
            if (str_contains($msg, 'bulan') || str_contains($msg, '30 hari')) {
                return $this->salesThisMonth();
            }
            return $this->salesToday();
        }

        // STOCK
        if (str_contains($msg, 'stok') || str_contains($msg, 'habis') || str_contains($msg, 'inventory') || str_contains($msg, 'gudang') || str_contains($msg, 'barang')) {
            if (str_contains($msg, 'kritis') || str_contains($msg, 'menipis') || str_contains($msg, 'hampir')) {
                return $this->criticalStock();
            }
            if (str_contains($msg, 'habis') || str_contains($msg, 'kosong')) {
                return $this->outOfStock();
            }
            if (str_contains($msg, 'kadaluarsa') || str_contains($msg, 'expired') || str_contains($msg, 'kedaluwarsa')) {
                return $this->expiringStock();
            }
            return $this->stockSummary();
        }

        // BEST SELLERS
        if (str_contains($msg, 'terlaris') || str_contains($msg, 'populer') || str_contains($msg, 'laku') || str_contains($msg, 'best') || str_contains($msg, 'favorit')) {
            return $this->bestSellers();
        }

        // WHOLESALE
        if (str_contains($msg, 'grosir') || str_contains($msg, 'wholesale')) {
            if (str_contains($msg, 'tier') || str_contains($msg, 'level') || str_contains($msg, 'vip')) {
                return $this->wholesaleTiers();
            }
            if (str_contains($msg, 'referral') || str_contains($msg, 'referensi')) {
                return $this->referralSummary();
            }
            return $this->wholesaleSummary();
        }

        // EXPENSES
        if (str_contains($msg, 'pengeluaran') || str_contains($msg, 'biaya') || str_contains($msg, 'expense') || str_contains($msg, 'belanja') || str_contains($msg, 'hutang')) {
            return $this->expenseSummary();
        }

        // PROFIT / LOSS
        if (str_contains($msg, 'laba') || str_contains($msg, 'rugi') || str_contains($msg, 'profit') || str_contains($msg, 'untung')) {
            return $this->profitSummary();
        }

        // SUPPLIER / PURCHASE
        if (str_contains($msg, 'supplier') || str_contains($msg, 'pemasok') || str_contains($msg, 'vendor') || str_contains($msg, 'pembelian') || str_contains($msg, 'po ')) {
            return $this->supplierSummary();
        }

        // SHIFT / ATTENDANCE
        if (str_contains($msg, 'shift') || str_contains($msg, 'kasir')) {
            return $this->shiftStatus();
        }
        if (str_contains($msg, 'absensi') || str_contains($msg, 'hadir') || str_contains($msg, 'karyawan') || str_contains($msg, 'pegawai')) {
            return $this->employeeSummary();
        }

        // BUSINESS HEALTH
        if (str_contains($msg, 'kesehatan') || str_contains($msg, 'sehat') || str_contains($msg, 'health') || str_contains($msg, 'score') || str_contains($msg, 'skor')) {
            return $this->businessHealth();
        }

        // CUSTOMER
        if (str_contains($msg, 'pelanggan') || str_contains($msg, 'customer')) {
            return $this->customerSummary();
        }

        // HELP
        if (str_contains($msg, 'help') || str_contains($msg, 'bantuan') || str_contains($msg, 'cara') || str_contains($msg, 'fitur') || str_contains($msg, 'bisa')) {
            return $this->helpMessage();
        }

        // TRACKING
        if (str_contains($msg, 'resi') || str_contains($msg, 'tracking') || str_contains($msg, 'kirim') || preg_match('/GROSIR-\d+/', $msg)) {
            return $this->trackOrder($message);
        }

        return $this->fallbackMessage();
    }

    private function greeting(): string
    {
        $hour = Carbon::now()->format('H');
        $greet = $hour < 10 ? 'Pagi' : ($hour < 15 ? 'Siang' : ($hour < 18 ? 'Sore' : 'Malam'));
        $shift = Shift::whereNull('end_time')->with('user')->first();
        $shiftInfo = '';
        if ($shift) {
            $shiftInfo = "\n\nShift saat ini dipandu oleh *" . ($shift->user?->name ?? 'Unknown') . "* sejak " . ($shift->start_time?->format('H:i') ?? '-') . ".";
        }
        return "Selamat {$greet}! Saya adalah *APMS Assistant*, asisten cerdas sistem manajemen Ashar Parfum.\n\nSaya siap membantu Anda memantau penjualan, stok, pengeluaran, dan memberikan analisis bisnis secara real-time. Ada yang bisa saya bantu?{$shiftInfo}";
    }

    private function getStrategicAdvice(): string
    {
        $advices = $this->strategicService->getSellingAdvices();
        if (count($advices) > 0) {
            $response = "Berdasarkan data terkini, berikut rekomendasi strategis untuk bisnis Anda:\n\n";
            foreach ($advices as $advice) {
                $response .= "💡 *{$advice['title']}*\n{$advice['text']}\n\n";
            }
            $response .= "Terapkan saran di atas secara bertahap dan pantau dampaknya terhadap penjualan.";
            return $response;
        }
        return "Saya sedang menganalisis pola bisnis Anda. Saat ini data masih terbatas — teruslah mencatat transaksi agar rekomendasi semakin akurat.";
    }

    private function salesToday(): string
    {
        $today = Carbon::today();
        $retail = (float) $this->scope(Transaction::query())->whereDate('created_at', $today)->sum('total_amount');
        $retailCount = $this->scope(Transaction::query())->whereDate('created_at', $today)->count();
        $wholesale = (float) $this->scope(WholesaleOrder::query())->whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total_amount');
        $wholesaleCount = $this->scope(WholesaleOrder::query())->whereDate('created_at', $today)->where('status', '!=', 'cancelled')->count();

        $response = "📊 *Ringkasan Penjualan — " . $today->format('d/m/Y') . "*\n\n";
        if ($retailCount > 0) {
            $avg = $retail / $retailCount;
            $response .= "▸ Eceran: *Rp " . number_format($retail, 0, ',', '.') . "* ({$retailCount} transaksi)\n";
            $response .= "  Rata-rata: Rp " . number_format($avg, 0, ',', '.') . "/transaksi\n";
        } else {
            $response .= "▸ Eceran: Belum ada transaksi hari ini.\n";
        }
        if ($wholesaleCount > 0) {
            $response .= "▸ Grosir: *Rp " . number_format($wholesale, 0, ',', '.') . "* ({$wholesaleCount} pesanan)\n";
        }
        $total = $retail + $wholesale;
        $response .= "\nTotal keseluruhan: *Rp " . number_format($total, 0, ',', '.') . "*";
        return $response;
    }

    private function salesYesterday(): string
    {
        $yesterday = Carbon::yesterday();
        $retail = (float) $this->scope(Transaction::query())->whereDate('created_at', $yesterday)->sum('total_amount');
        $retailCount = $this->scope(Transaction::query())->whereDate('created_at', $yesterday)->count();
        $response = "📊 *Penjualan " . $yesterday->format('d/m/Y') . "*\n\n";
        $response .= "▸ Eceran: *Rp " . number_format($retail, 0, ',', '.') . "* ({$retailCount} transaksi)";

        $todaySales = (float) $this->scope(Transaction::query())->whereDate('created_at', Carbon::today())->sum('total_amount');
        if ($retail > 0 && $todaySales > 0) {
            $pct = round(($todaySales / $retail) * 100, 1);
            $response .= "\n\nHari ini: " . ($pct >= 100 ? "naik {$pct}%" : "baru {$pct}% dari kemarin");
        }
        return $response;
    }

    private function salesThisMonth(): string
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $retail = (float) $this->scope(Transaction::query())->whereBetween('created_at', [$start, $end])->sum('total_amount');
        $retailCount = $this->scope(Transaction::query())->whereBetween('created_at', [$start, $end])->count();

        $lastStart = Carbon::now()->subMonth()->startOfMonth();
        $lastEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastRetail = (float) $this->scope(Transaction::query())->whereBetween('created_at', [$lastStart, $lastEnd])->sum('total_amount');

        $response = "📊 *Penjualan Bulan " . $start->translatedFormat('F Y') . "*\n\n";
        $response .= "▸ Total: *Rp " . number_format($retail, 0, ',', '.') . "*\n";
        $response .= "▸ Transaksi: {$retailCount}\n";
        $response .= "▸ Rata-rata: Rp " . number_format($retailCount > 0 ? $retail / $retailCount : 0, 0, ',', '.') . "/transaksi\n";

        if ($lastRetail > 0) {
            $change = round((($retail - $lastRetail) / $lastRetail) * 100, 1);
            $trend = $change >= 0 ? 'naik 📈' : 'turun 📉';
            $response .= "\nDibanding bulan lalu: {$trend} " . number_format(abs($change), 1) . "%";
        }
        return $response;
    }

    private function stockSummary(): string
    {
        $totalItems = (int) $this->scope(Inventory::query())->sum('current_stock');
        $totalProd = Product::count();
        $lowCount = $this->scope(Inventory::query())->whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0)->count();
        $outCount = $this->scope(Inventory::query())->where('current_stock', '<=', 0)->count();
        $totalVal = (float) $this->scope(Inventory::query())->sum(DB::raw('current_stock * COALESCE(cost_per_unit, 0)'));

        $response = "📦 *Ringkasan Inventaris*\n\n";
        $response .= "▸ Total produk: {$totalProd}\n";
        $response .= "▸ Total item fisik: " . number_format($totalItems) . " unit\n";
        $response .= "▸ Stok kritis: {$lowCount} produk\n";
        $response .= "▸ Stok habis: {$outCount} produk\n";
        $response .= "▸ Nilai gudang: Rp " . number_format($totalVal, 0, ',', '.') . "\n";

        if ($lowCount > 0 || $outCount > 0) {
            $response .= "\n⚠️ Perhatian: Ada {$lowCount} produk kritis dan {$outCount} habis. Segera lakukan restok.";
        }
        return $response;
    }

    private function criticalStock(): string
    {
        $items = $this->scope(Inventory::query())
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->with('product')
            ->orderBy('current_stock')
            ->take(5)
            ->get();

        if ($items->isEmpty()) {
            return "✅ Tidak ada produk dengan stok kritis. Seluruh stok dalam kondisi aman.";
        }

        $response = "⚠️ *Produk dengan Stok Kritis*\n\n";
        foreach ($items as $i) {
            $pct = $i->minimum_stock > 0 ? round(($i->current_stock / $i->minimum_stock) * 100) : 0;
            $response .= "▸ *{$i->product->name}*: sisa {$i->current_stock} dari minimum {$i->minimum_stock} ({$pct}%)\n";
        }
        $total = $this->scope(Inventory::query())->whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0)->count();
        if ($total > 5) {
            $response .= "\n...dan " . ($total - 5) . " produk lainnya.";
        }
        $response .= "\n\nSegera lakukan pemesanan ulang untuk menghindari kehabisan stok.";
        return $response;
    }

    private function outOfStock(): string
    {
        $count = $this->scope(Inventory::query())->where('current_stock', '<=', 0)->count();
        if ($count === 0) {
            return "✅ Tidak ada produk yang stoknya habis. Kondisi gudang terkendali.";
        }
        return "📦 Ada *{$count} produk* yang stoknya sedang habis. Segera lakukan pembelian ulang agar penjualan tidak terhambat.";
    }

    private function expiringStock(): string
    {
        $nearExp = $this->scope(Inventory::query())
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->where('current_stock', '>', 0)
            ->with('product')
            ->orderBy('expiration_date')
            ->take(5)
            ->get();

        if ($nearExp->isEmpty()) {
            return "✅ Tidak ada produk yang mendekati kadaluarsa dalam 90 hari ke depan.";
        }

        $response = "⏰ *Produk Mendekati Kadaluarsa (90 hari)*\n\n";
        foreach ($nearExp as $i) {
            $expDate = Carbon::parse($i->expiration_date)->format('d/m/Y');
            $response .= "▸ *{$i->product->name}*: {$i->current_stock} unit — kedaluwarsa {$expDate}\n";
        }
        $total = $this->scope(Inventory::query())
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->where('current_stock', '>', 0)->count();
        if ($total > 5) {
            $response .= "\n...dan " . ($total - 5) . " produk lainnya.";
        }
        $response .= "\n\nPertimbangkan untuk memberikan diskon atau bundling agar produk cepat terjual.";
        return $response;
    }

    private function bestSellers(): string
    {
        $tops = TransactionDetail::select(
                'products.name',
                'products.brand',
                DB::raw('SUM(transaction_details.quantity) as total_qty'),
                DB::raw('SUM(transaction_details.subtotal) as total_rev')
            )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transaction_details.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('products.id', 'products.name', 'products.brand')
            ->orderByDesc('total_qty')
            ->take(5);

        if ($this->scopeBranchId) {
            $tops->where('transactions.branch_id', $this->scopeBranchId);
        }
        $tops = $tops->get();

        if ($tops->isEmpty()) {
            return "Belum ada data penjualan yang cukup untuk menentukan produk terlaris.";
        }

        $response = "🏆 *Produk Terlaris (30 hari terakhir)*\n\n";
        $i = 1;
        foreach ($tops as $t) {
            $response .= "{$i}. *{$t->name}* ({$t->brand}) — {$t->total_qty} unit — Rp " . number_format($t->total_rev, 0, ',', '.') . "\n";
            $i++;
        }
        return $response;
    }

    private function wholesaleSummary(): string
    {
        $counts = $this->scope(WholesaleOrder::query())
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusLabels = [
            'pending' => 'Menunggu Review', 'reviewed' => 'Dikonfirmasi',
            'on_progress' => 'Diproses', 'packed' => 'Di-packing',
            'shipped' => 'Dikirim', 'delivered' => 'Diterima',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];

        $response = "📋 *Ringkasan Pesanan Grosir*\n\n";
        foreach ($statusLabels as $key => $label) {
            $count = $counts[$key] ?? 0;
            if ($count > 0) {
                $response .= "▸ {$label}: {$count} pesanan\n";
            }
        }
        $total = $counts->sum();
        $response .= "\nTotal: {$total} pesanan";
        return $response;
    }

    private function wholesaleTiers(): string
    {
        $customers = User::where('role', 'wholesale_customer')->get();
        $total = $customers->count();
        $tierConfig = ['Platinum' => 50000000, 'Gold' => 30000000, 'Silver' => 20000000, 'VIP' => 10000000, 'Regular' => 0];
        $tierCounts = array_fill_keys(array_keys($tierConfig), 0);

        foreach ($customers as $c) {
            $spent = WholesaleOrder::where(function ($q) use ($c) {
                $q->where('recipient_phone', $c->phone)
                  ->orWhereHas('customer', fn($cq) => $cq->where('email', $c->email));
            })->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');

            $assigned = 'Regular';
            foreach (['Platinum' => 50000000, 'Gold' => 30000000, 'Silver' => 20000000, 'VIP' => 10000000] as $t => $min) {
                if ($spent >= $min) { $assigned = $t; break; }
            }
            $tierCounts[$assigned]++;
        }

        $response = "👑 *Distribusi Tier Pelanggan Grosir*\n\n";
        foreach ($tierConfig as $tier => $min) {
            $pct = $total > 0 ? round(($tierCounts[$tier] / $total) * 100, 1) : 0;
            $response .= "▸ {$tier}: {$tierCounts[$tier]} pelanggan ({$pct}%)\n";
        }
        $response .= "\nTotal: {$total} pelanggan grosir";
        return $response;
    }

    private function referralSummary(): string
    {
        $withCode = User::where('role', 'wholesale_customer')->whereNotNull('referral_code')->count();
        $referred = User::where('role', 'wholesale_customer')->whereNotNull('referred_by_id')->count();
        $top = User::where('role', 'wholesale_customer')->withCount('referrals')->orderByDesc('referrals_count')->first();

        $response = "🔗 *Program Referral*\n\n";
        $response .= "▸ Pelanggan dengan kode referral: {$withCode}\n";
        $response .= "▸ Berhasil mereferensikan: {$referred}\n";
        if ($top && $top->referrals_count > 0) {
            $response .= "▸ Top referrer: {$top->name} ({$top->referrals_count} referral)\n";
        }
        return $response;
    }

    private function expenseSummary(): string
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $total = (float) $this->scope(Expense::query())->whereBetween('created_at', [$start, $end])->sum('amount');
        $count = $this->scope(Expense::query())->whereBetween('created_at', [$start, $end])->count();

        $topCat = $this->scope(Expense::query())->whereBetween('created_at', [$start, $end])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $response = "💰 *Ringkasan Pengeluaran Bulan " . $start->translatedFormat('F Y') . "*\n\n";
        $response .= "▸ Total: *Rp " . number_format($total, 0, ',', '.') . "*\n";
        $response .= "▸ Transaksi: {$count}\n";
        if ($topCat) {
            $pct = $total > 0 ? round(($topCat->total / $total) * 100, 1) : 0;
            $response .= "▸ Kategori terbesar: {$topCat->category} ({$pct}%)\n";
        }

        $prevTotal = (float) $this->scope(Expense::query())->whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('amount');
        if ($prevTotal > 0) {
            $change = round((($total - $prevTotal) / $prevTotal) * 100, 1);
            $trend = $change >= 0 ? 'naik' : 'turun';
            $response .= "\nDibanding bulan lalu: {$trend} " . number_format(abs($change), 1) . "%";
        }
        return $response;
    }

    private function profitSummary(): string
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $revenue = (float) $this->scope(Transaction::query())->whereBetween('created_at', [$start, $end])->sum('total_amount');
        $cogsQuery = TransactionDetail::whereBetween('transaction_details.created_at', [$start, $end])
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id');
        if ($this->scopeBranchId) {
            $cogsQuery->where('transactions.branch_id', $this->scopeBranchId);
        }
        $cogs = (float) $cogsQuery->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));
        $expenses = (float) $this->scope(Expense::query())->whereBetween('created_at', [$start, $end])->sum('amount');

        $gross = $revenue - $cogs;
        $net = $gross - $expenses;
        $margin = $revenue > 0 ? round(($net / $revenue) * 100, 1) : 0;

        $response = "📈 *Laporan Laba Rugi — " . $start->translatedFormat('F Y') . "*\n\n";
        $response .= "▸ Pendapatan: Rp " . number_format($revenue, 0, ',', '.') . "\n";
        $response .= "▸ HPP: Rp " . number_format($cogs, 0, ',', '.') . "\n";
        $response .= "▸ Laba kotor: Rp " . number_format($gross, 0, ',', '.') . "\n";
        $response .= "▸ Pengeluaran: Rp " . number_format($expenses, 0, ',', '.') . "\n";
        $response .= "▸ *Laba bersih: Rp " . number_format($net, 0, ',', '.') . "*\n";
        $response .= "▸ Margin: {$margin}%";
        return $response;
    }

    private function supplierSummary(): string
    {
        $totalSup = Supplier::count();
        $totalPO = PurchaseOrder::count();
        $pendingPO = PurchaseOrder::whereIn('status', ['sent', 'partial'])->count();
        $recentPO = PurchaseOrder::with('supplier')->latest()->take(5)->get();

        $response = "🏭 *Ringkasan Supplier & Pembelian*\n\n";
        $response .= "▸ Total supplier: {$totalSup}\n";
        $response .= "▸ Total PO: {$totalPO}\n";
        $response .= "▸ PO menunggu: {$pendingPO}\n";

        if ($recentPO->isNotEmpty()) {
            $response .= "\nPO Terbaru:\n";
            foreach ($recentPO as $po) {
                $supplier = $po->supplier->name ?? '-';
                $total = 'Rp ' . number_format($po->total_amount ?? 0, 0, ',', '.');
                $response .= "▸ {$po->po_number} — {$supplier} — {$total}\n";
            }
        }
        return $response;
    }

    private function shiftStatus(): string
    {
        $shift = Shift::whereNull('end_time')->with('user')->first();
        if (!$shift) {
            return "⏰ Saat ini *tidak ada shift aktif*. Belum ada petugas yang melakukan Open Shift.";
        }
        $startTime = $shift->start_time;
        $durasi = $startTime ? $startTime->diffForHumans(Carbon::now(), ['parts' => 2]) : 'baru saja';
        $openTime = $startTime ? $startTime->format('H:i') : '-';
        $todaySales = (float) $this->scope(Transaction::query())->whereDate('created_at', Carbon::today())
            ->where('shift_id', $shift->id)->sum('total_amount');
        return "⏰ *Shift Aktif*\n\n▸ Petugas: " . ($shift->user?->name ?? 'Unknown') . "\n▸ Dibuka: {$openTime} ({$durasi} lalu)\n▸ Modal: Rp " . number_format($shift->initial_cash ?? 0, 0, ',', '.') . "\n▸ Penjualan shift: Rp " . number_format($todaySales, 0, ',', '.');
    }

    private function employeeSummary(): string
    {
        $active = User::where('is_active', true)->count();
        $todayPresent = $this->scope(Attendance::query())->whereDate('date', Carbon::today())->whereNull('time_out')->count();
        $todayAll = $this->scope(Attendance::query())->whereDate('date', Carbon::today())->count();

        $response = "👥 *Ringkasan Karyawan*\n\n";
        $response .= "▸ Karyawan aktif: {$active}\n";
        $response .= "▸ Hadir hari ini: {$todayPresent}\n";
        $response .= "▸ Total absensi: {$todayAll}\n";

        $roles = User::where('is_active', true)->select('role', DB::raw('COUNT(*) as count'))->groupBy('role')->orderByDesc('count')->get();
        if ($roles->isNotEmpty()) {
            $response .= "\nKomposisi:\n";
            foreach ($roles as $r) {
                $response .= "▸ {$r->role}: {$r->count}\n";
            }
        }
        return $response;
    }

    private function businessHealth(): string
    {
        $now = Carbon::now();
        $revenue = (float) $this->scope(Transaction::query())->whereMonth('created_at', $now->month)->sum('total_amount');
        $cogsQuery = TransactionDetail::whereMonth('transaction_details.created_at', $now->month)
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id');
        if ($this->scopeBranchId) {
            $cogsQuery->where('transactions.branch_id', $this->scopeBranchId);
        }
        $cogs = (float) $cogsQuery->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));
        $expenses = (float) $this->scope(Expense::query())->whereMonth('created_at', $now->month)->sum('amount');
        $netProfit = $revenue - $cogs - $expenses;
        $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        $lowStock = $this->scope(Inventory::query())->whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outStock = $this->scope(Inventory::query())->where('current_stock', '<=', 0)->count();
        $totalInv = Product::count();

        $stockScore = $totalInv > 0 ? max(0, 100 - (($lowStock + $outStock) / $totalInv) * 100) : 100;
        $profitScore = max(0, min(100, ($margin / 20) * 100));
        $healthScore = round(($profitScore + $stockScore) / 2);

        $grade = $healthScore >= 85 ? 'Sangat Sehat 🟢' : ($healthScore >= 70 ? 'Sehat 🟢' : ($healthScore >= 50 ? 'Cukup 🟡' : ($healthScore >= 30 ? 'Kurang Sehat 🟠' : 'Kritis 🔴')));

        $response = "🏥 *Skor Kesehatan Bisnis*\n\n";
        $response .= "Skor: *{$healthScore}/100* — {$grade}\n\n";
        $response .= "▸ Margin laba: {$margin}%\n";
        $response .= "▸ Stok bermasalah: {$lowStock} kritis, {$outStock} habis\n";
        $response .= "▸ Laba bersih: Rp " . number_format($netProfit, 0, ',', '.') . "\n";

        if ($healthScore < 50) {
            $response .= "\n⚠️ Perhatian: Skor menurun. Fokus pada peningkatan penjualan dan efisiensi biaya.";
        }
        return $response;
    }

    private function customerSummary(): string
    {
        $total = $this->scope(\App\Models\Customer::query())->count();
        $recent = $this->scope(\App\Models\Customer::query())->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $wholesaleUsers = User::where('role', 'wholesale_customer')->count();

        return "👤 *Ringkasan Pelanggan*\n\n▸ Total pelanggan retail: {$total}\n▸ Pelanggan baru (30 hari): {$recent}\n▸ Pelanggan grosir terdaftar: {$wholesaleUsers}";
    }

    private function trackOrder(string $message): string
    {
        preg_match('/(GROSIR-\d{8}-[A-Z0-9]+|\d{5,})/i', $message, $m);
        $query = $m[1] ?? $message;

        $order = WholesaleOrder::where('invoice_number', 'LIKE', "%{$query}%")
            ->orWhere('tracking_number', 'LIKE', "%{$query}%")
            ->first();

        if (!$order) {
            return "Pesanan dengan nomor '{$query}' tidak ditemukan. Pastikan nomor invoice atau resi benar.";
        }

        $statusLabels = [
            'pending' => 'Menunggu Review', 'reviewed' => 'Dikonfirmasi',
            'on_progress' => 'Diproses', 'packed' => 'Di-packing',
            'shipped' => 'Dikirim', 'delivered' => 'Diterima',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];

        $response = "📦 *Informasi Pengiriman*\n\n";
        $response .= "▸ Invoice: {$order->invoice_number}\n";
        $response .= "▸ Penerima: {$order->recipient_name}\n";
        $response .= "▸ Status: {$statusLabels[$order->status]}\n";
        $response .= "▸ Total: Rp " . number_format($order->total_amount ?? 0, 0, ',', '.') . "\n";
        if ($order->tracking_number) $response .= "▸ Resi: {$order->tracking_number}\n";
        if ($order->shipping_courier) $response .= "▸ Kurir: {$order->shipping_courier}\n";
        return $response;
    }

    private function helpMessage(): string
    {
        return "🤖 *Bantuan APMS Assistant*\n\nSaya bisa menjawab pertanyaan seperti:\n\n"
            . "📊 *Penjualan*\n\"Berapa penjualan hari ini?\"\n\"Penjualan bulan ini?\"\n\"Omzet kemarin?\"\n\n"
            . "📦 *Stok & Produk*\n\"Cek stok hampir habis\"\n\"Produk apa yang paling laris?\"\n\"Ada kadaluarsa?\"\n\n"
            . "💰 *Keuangan*\n\"Laba rugi bulan ini\"\n\"Total pengeluaran\"\n\"Skor kesehatan bisnis\"\n\n"
            . "👥 *Pelanggan & Karyawan*\n\"Info pelanggan\"\n\"Siapa yang hadir?\"\n\"Status shift?\"\n\n"
            . "📋 *Grosir & Supplier*\n\"Ringkasan pesanan grosir\"\n\"Tier pelanggan grosir\"\n\"Info supplier\"\n\"Lacak resi GROSIR-20260624-XXXX\"\n\n"
            . "💡 *Strategi*\n\"Berikan saran jualan\"\n\"Tips meningkatkan penjualan\"";
    }

    private function fallbackMessage(): string
    {
        return "Maaf, saya belum bisa menjawab pertanyaan tersebut. Silakan coba pertanyaan lain atau ketik \"bantuan\" untuk melihat fitur yang tersedia.\n\n"
            . "Tips: Coba tanya tentang penjualan, stok, laba rugi, pelanggan grosir, atau minta saran strategi bisnis.";
    }
}
