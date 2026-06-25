<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\User;
use App\Models\Shift;
use App\Models\Expense;
use App\Models\Coupon;
use App\Models\WholesaleOrder;
use App\Models\Attendance;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Contracts\CopilotEngineInterface;

class AiCopilotService implements CopilotEngineInterface
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-haiku-4-5-20251001';
    private const MAX_TOKENS = 1024;
    private const MAX_TOOL_ROUNDS = 5;
    private const SESSION_MESSAGES = 'ai_copilot_messages';

    public static function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'get_branch_info',
                'description' => 'Mengambil informasi jumlah dan daftar nama cabang toko yang aktif. Tidak memerlukan parameter.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_stock_summary',
                'description' => 'Mengambil ringkasan kondisi gudang: total produk, total item fisik, jumlah stok kritis, stok habis, produk mendekati kadaluarsa, dan estimasi nilai gudang.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_critical_or_out_of_stock_products',
                'description' => 'Mengambil daftar produk yang stoknya kritis (di bawah batas minimum) atau sudah habis (stok = 0). Gunakan parameter status untuk memilih kategori.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => ['kritis', 'habis'],
                            'description' => 'kritis = stok di bawah minimum tapi masih ada; habis = stok = 0',
                        ],
                    ],
                    'required' => ['status'],
                ],
            ],
            [
                'name' => 'get_incoming_stock',
                'description' => 'Mengambil daftar barang yang dijadwalkan masuk (Purchase Order) pada tanggal tertentu, atau barang yang sudah diterima pada tanggal tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'date' => [
                            'type' => 'string',
                            'description' => 'Tanggal dalam format YYYY-MM-DD. Default: hari ini.',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => ['expected', 'received'],
                            'description' => 'expected = barang dijadwalkan datang; received = barang sudah diterima',
                        ],
                    ],
                    'required' => ['type'],
                ],
            ],
            [
                'name' => 'get_sales_summary',
                'description' => 'Mengambil total penjualan (omzet), jumlah transaksi, dan rata-rata per transaksi pada periode tertentu. Periode bisa: hari_ini, kemarin, minggu_ini, minggu_lalu, bulan_ini, bulan_lalu, tahun_ini.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['hari_ini', 'kemarin', 'minggu_ini', 'minggu_lalu', 'bulan_ini', 'bulan_lalu', 'tahun_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_best_selling_products',
                'description' => 'Mengambil daftar produk dengan penjualan/kuantitas terjual tertinggi pada periode tertentu, diurutkan dari yang paling laris.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['hari_ini', 'kemarin', 'minggu_ini', 'minggu_lalu', 'bulan_ini', 'bulan_lalu', 'tahun_ini', 'semua_waktu'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'Jumlah produk teratas yang ditampilkan (default 10)',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_profit_loss',
                'description' => 'Mengambil laporan laba rugi: total pendapatan, harga pokok penjualan (HPP), laba kotor, total pengeluaran, laba bersih, dan margin keuntungan pada periode tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['hari_ini', 'kemarin', 'minggu_ini', 'minggu_lalu', 'bulan_ini', 'bulan_lalu', 'tahun_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_customer_count',
                'description' => 'Mengambil jumlah total pelanggan terdaftar di sistem dan jumlah pelanggan baru dalam 30 hari terakhir.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_customer_origin',
                'description' => 'Mengambil sebaran asal kota/wilayah pelanggan berdasarkan data alamat yang tersimpan. Catatan: data alamat bersifat bebas (free text) sehingga pengelompokan bersifat estimasi berdasarkan kata pertama pada alamat.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_employee_info',
                'description' => 'Mengambil data karyawan: total, jumlah aktif, komposisi role. Juga menampilkan status absensi pada tanggal tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'date' => [
                            'type' => 'string',
                            'description' => 'Tanggal untuk cek absensi dalam format YYYY-MM-DD. Default: hari ini.',
                        ],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_shift_status',
                'description' => 'Mengambil informasi shift yang sedang aktif: petugas kasir, waktu buka, modal awal, dan total penjualan shift ini.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_active_promos',
                'description' => 'Mengambil daftar kupon/promo yang sedang aktif beserta detail diskon, masa berlaku, dan jumlah pemakaian.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_wholesale_order_status',
                'description' => 'Mengambil informasi pesanan grosir: jumlah pesanan per status dan total nominalnya.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => [
                            'type' => 'string',
                            'enum' => ['pending', 'reviewed', 'on_progress', 'packed', 'shipped', 'delivered', 'completed', 'cancelled'],
                            'description' => 'Filter berdasarkan status pesanan. Jika tidak diisi, semua status akan ditampilkan.',
                        ],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_wholesale_customer_tier',
                'description' => 'Mengambil informasi tier pelanggan grosir: jumlah pelanggan per tier (Regular/VIP/Silver/Gold/Platinum) dan total nominal per tier.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_referral_leaderboard',
                'description' => 'Mengambil ringkasan program referral: total referral, top referrer, dan jumlah pelanggan yang sudah memiliki referral.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_inventory_movement',
                'description' => 'Mengambil riwayat pergerakan stok: barang masuk (pembelian, retur) dan barang keluar (penjualan, kadaluarsa) dalam periode tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['hari_ini', 'minggu_ini', 'bulan_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_supplier_performance',
                'description' => 'Mengambil performa supplier: jumlah PO, total pembelian, rata-rata lead time, dan status pesanan.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required' => [],
                ],
            ],
            [
                'name' => 'get_expense_analytics',
                'description' => 'Mengambil analisis pengeluaran: total per kategori, tren pengeluaran, dan pengeluaran terbesar pada periode tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['minggu_ini', 'bulan_ini', 'bulan_lalu', 'tahun_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_branch_comparison',
                'description' => 'Membandingkan performa antar cabang: penjualan, pengeluaran, dan laba bersih per cabang dalam periode tertentu.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['bulan_ini', 'bulan_lalu', 'tahun_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_product_category_performance',
                'description' => 'Mengambil performa per kategori produk: total penjualan, jumlah produk terjual, dan margin per kategori.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['bulan_ini', 'bulan_lalu', 'tahun_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_business_health_score',
                'description' => 'Mengambil skor kesehatan bisnis secara keseluruhan (0-100) berdasarkan 5 pilar: likuiditas, profitabilitas, stok, pertumbuhan, dan efisiensi.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['bulan_ini', 'bulan_lalu'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_anomaly_detection',
                'description' => 'Mendeteksi anomali dalam bisnis: penjualan yang turun drastis, pengeluaran tak wajar, stok yang tidak normal, atau aktivitas mencurigakan.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => (object)[],
                    'required'=> [],
                ],
            ],
            [
                'name' => 'get_peak_hours_analysis',
                'description' => 'Mengambil analisis jam sibuk dan hari sibuk untuk membantu penjadwalan karyawan.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'period' => [
                            'type' => 'string',
                            'enum' => ['minggu_ini', 'bulan_ini'],
                            'description' => 'Periode waktu yang dianalisis',
                        ],
                    ],
                    'required' => ['period'],
                ],
            ],
            [
                'name' => 'get_tracking_info',
                'description' => 'Mencari informasi pengiriman pesanan grosir berdasarkan nomor resi atau nomor invoice.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Nomor resi atau nomor invoice pesanan',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
        ];
    }

    public function handle(string $query, array $context = []): string
    {
        $sessionMessages = $context['messages'] ?? null;
        $result = $this->chat($query, $sessionMessages);
        return $result['reply'];
    }

    public function chat(string $message, ?array $sessionMessages = null): array
    {
        try {
            $apiKey = config('services.anthropic.api_key');
            if (empty($apiKey)) {
                return [
                    'reply' => 'Konfigurasi API Anthropic belum lengkap. Silakan hubungi administrator untuk mengatur ANTHROPIC_API_KEY.',
                    'messages' => $sessionMessages ?? [],
                ];
            }

            $messages = $sessionMessages ?? [];
            $messages[] = ['role' => 'user', 'content' => $message];
            $messages = array_slice($messages, -10);

            $tools = self::getToolDefinitions();
            $finalReply = $this->runAgenticLoop($messages, $tools);

            $newHistory = $sessionMessages ?? [];
            $newHistory[] = ['role' => 'user', 'content' => $message];
            $newHistory[] = ['role' => 'assistant', 'content' => $finalReply];
            $newHistory = array_slice($newHistory, -6);

            return [
                'reply' => $finalReply,
                'messages' => $newHistory,
            ];
        } catch (\Exception $e) {
            Log::error('AiCopilotService Error: ' . $e->getMessage());
            return [
                'reply' => 'Maaf, terjadi kesalahan sistem saat memproses pertanyaan Anda. Silakan coba beberapa saat lagi. Apabila masalah berlanjut, hubungi administrator.',
                'messages' => $sessionMessages ?? [],
            ];
        }
    }

    private function runAgenticLoop(array $messages, array $tools): string
    {
        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            $response = $this->callClaudeApi($messages, $tools);

            $stopReason = $response['stop_reason'] ?? 'end_turn';
            $content = $response['content'] ?? [];

            if ($stopReason === 'end_turn') {
                $texts = array_filter($content, fn($b) => ($b['type'] ?? '') === 'text');
                if (!empty($texts)) {
                    return $texts[0]['text'] ?? 'Maaf, tidak ada jawaban yang dihasilkan.';
                }
                return 'Maaf, tidak ada jawaban yang dihasilkan.';
            }

            if ($stopReason === 'tool_use') {
                $toolUseBlocks = array_filter($content, fn($b) => ($b['type'] ?? '') === 'tool_use');

                $assistantContent = [];
                $toolResults = [];

                foreach ($toolUseBlocks as $block) {
                    $toolName = $block['name'] ?? '';
                    $toolInput = $block['input'] ?? [];
                    $toolId = $block['id'] ?? '';

                    $assistantContent[] = [
                        'type' => 'tool_use',
                        'id' => $toolId,
                        'name' => $toolName,
                        'input' => $toolInput,
                    ];

                    try {
                        $result = $this->executeTool($toolName, $toolInput);
                    } catch (\Exception $e) {
                        Log::error("Tool execution error [{$toolName}]: " . $e->getMessage());
                        $result = 'Error: ' . $e->getMessage();
                    }

                    $toolResults[] = [
                        'type' => 'tool_result',
                        'tool_use_id' => $toolId,
                        'content' => $result,
                    ];
                }

                $messages[] = ['role' => 'assistant', 'content' => $assistantContent];
                $messages[] = ['role' => 'user', 'content' => $toolResults];
            }
        }

        return 'Maaf, proses analisis memakan waktu terlalu lama. Silakan coba pertanyaan yang lebih spesifik.';
    }

    private function callClaudeApi(array $messages, array $tools): array
    {
        $apiKey = config('services.anthropic.api_key');

        $systemPrompt = $this->getSystemPrompt();

        $payload = [
            'model' => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'system' => $systemPrompt,
            'messages' => $this->formatMessages($messages),
            'tools' => $tools,
        ];

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(60)->post(self::API_URL, $payload);

        if (!$response->successful()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Claude API error [{$status}]: {$body}");
            throw new \Exception("API request failed with status {$status}");
        }

        return $response->json();
    }

    private function formatMessages(array $messages): array
    {
        $formatted = [];
        foreach ($messages as $msg) {
            $role = $msg['role'] ?? 'user';
            $content = $msg['content'] ?? '';

            if (is_string($content)) {
                $formatted[] = ['role' => $role, 'content' => $content];
            } elseif (is_array($content)) {
                $formatted[] = ['role' => $role, 'content' => $content];
            }
        }
        return $formatted;
    }

    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah APMS Copilot, asisten kecerdasan buatan premium untuk sistem manajemen Ashar Parfum — solusi retail parfum dengan dukungan multi-cabang.

FUNGSI UTAMA:
Anda bertugas memberikan analisis mendalam, wawasan strategis, dan rekomendasi berbasis data kepada Owner/pengelola bisnis terkait: penjualan, stok & inventaris, laba rugi, pelanggan retail & grosir, karyawan & absensi, shift operasional, pengeluaran, promo & kupon, supplier & pembelian, perbandingan cabang, performa kategori produk, dan tracking pengiriman.

ATURAN WAJIB:
1. Jawab HANYA berdasarkan hasil tool yang dipanggil. Jangan pernah mengarang angka, data, estimasi, atau informasi apa pun.
2. Jika tool mengembalikan data kosong atau null, katakan dengan jujur bahwa data tidak tersedia. Jangan memberi jawaban generik yang menyesatkan.
3. Gunakan Bahasa Indonesia formal, profesional, dan sopan. Hindari emoji, bahasa gaul, atau singkatan tidak baku.
4. Format nominal uang dalam Rupiah (Rp) menggunakan format Indonesia (contoh: Rp 1.500.000). Gunakan satuan juta/miliar untuk angka besar.
5. Jika pertanyaan ambigu (tidak menyebutkan periode waktu, cabang, atau parameter penting), tanyakan klarifikasi singkat alih-alih menebak parameter.
6. Jika data yang diminta tidak tersedia di sistem, katakan "Data tersebut belum tersedia di sistem" — jangan mengarang.
7. Jika pengguna bertanya di luar konteks bisnis APMS (cuaca, berita umum, hiburan, dll), tolak dengan sopan dan arahkan kembali ke topik bisnis.
8. Anda boleh memanggil BANYAK TOOL dalam satu giliran jika pertanyaan mencakup beberapa topik. Misalnya "berapa penjualan dan stok kritis hari ini?" — panggil get_sales_summary DAN get_critical_or_out_of_stock_products.
9. Setiap kali selesai menjawab, berikan 2-3 saran pertanyaan lanjutan yang relevan dan strategis dalam format terpisah.
10. Untuk pertanyaan tentang tren atau perbandingan, berikan analisis perubahan (naik/turun) dan rekomendasi tindakan.
11. Jika skor kesehatan bisnis di bawah 50, sertakan rekomendasi prioritas perbaikan.
PROMPT;
    }

    private function executeTool(string $name, array $input): string
    {
        return match ($name) {
            'get_branch_info' => $this->getBranchInfo(),
            'get_stock_summary' => $this->getStockSummary(),
            'get_critical_or_out_of_stock_products' => $this->getCriticalOrOutOfStock($input),
            'get_incoming_stock' => $this->getIncomingStock($input),
            'get_sales_summary' => $this->getSalesSummary($input),
            'get_best_selling_products' => $this->getBestSellingProducts($input),
            'get_profit_loss' => $this->getProfitLoss($input),
            'get_customer_count' => $this->getCustomerCount(),
            'get_customer_origin' => $this->getCustomerOrigin(),
            'get_employee_info' => $this->getEmployeeInfo($input),
            'get_shift_status' => $this->getShiftStatus(),
            'get_active_promos' => $this->getActivePromos(),
            'get_wholesale_order_status' => $this->getWholesaleOrderStatus($input),
            'get_wholesale_customer_tier' => $this->getWholesaleCustomerTier(),
            'get_referral_leaderboard' => $this->getReferralLeaderboard(),
            'get_inventory_movement' => $this->getInventoryMovement($input),
            'get_supplier_performance' => $this->getSupplierPerformance(),
            'get_expense_analytics' => $this->getExpenseAnalytics($input),
            'get_branch_comparison' => $this->getBranchComparison($input),
            'get_product_category_performance' => $this->getProductCategoryPerformance($input),
            'get_business_health_score' => $this->getBusinessHealthScore($input),
            'get_anomaly_detection' => $this->getAnomalyDetection(),
            'get_peak_hours_analysis' => $this->getPeakHoursAnalysis($input),
            'get_tracking_info' => $this->getTrackingInfo($input),
            default => "Tool '{$name}' tidak dikenal.",
        };
    }

    private function getBranchInfo(): string
    {
        $branches = Branch::where('is_active', true)->get(['id', 'name', 'city', 'manager_name']);
        $total = $branches->count();

        if ($total === 0) {
            return 'Tidak ada cabang yang terdaftar dan aktif dalam sistem.';
        }

        $lines = ["Jumlah cabang aktif: {$total}."];
        foreach ($branches as $b) {
            $city = $b->city ? " - {$b->city}" : '';
            $manager = $b->manager_name ? " | Manager: {$b->manager_name}" : '';
            $lines[] = "- {$b->name}{$city}{$manager}";
        }
        return implode("\n", $lines);
    }

    private function getStockSummary(): string
    {
        $totalProd = Product::count();
        $totalItems = (int) Inventory::sum('current_stock');
        $lowCount = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)->count();
        $outCount = Inventory::where('current_stock', '<=', 0)->count();

        $expCount = Inventory::whereNotNull('expiration_date')
            ->where('expiration_date', '<', Carbon::now())
            ->where('current_stock', '>', 0)->count();

        $nearExpCount = Inventory::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->where('current_stock', '>', 0)->count();

        $totalVal = (float) Inventory::sum(DB::raw('current_stock * COALESCE(cost_per_unit, 0)'));

        $lines = [
            "Ringkasan Inventory Gudang:",
            "- Total produk dalam katalog: {$totalProd}",
            "- Total item fisik di gudang: {$totalItems}",
            "- Produk stok kritis (di bawah minimum): {$lowCount}",
            "- Produk stok habis: {$outCount}",
        ];

        if ($expCount > 0) {
            $lines[] = "- Produk sudah kadaluarsa: {$expCount}";
        }
        if ($nearExpCount > 0) {
            $lines[] = "- Produk akan kadaluarsa dalam 90 hari: {$nearExpCount}";
        }

        $formattedVal = 'Rp ' . number_format($totalVal, 0, ',', '.');
        $lines[] = "- Estimasi nilai gudang: {$formattedVal}";

        return implode("\n", $lines);
    }

    private function getCriticalOrOutOfStock(array $input): string
    {
        $status = $input['status'] ?? '';

        if ($status === 'kritis') {
            $items = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>', 0)
                ->with('product')->orderBy('current_stock', 'asc')->take(30)->get();
            $count = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>', 0)->count();

            if ($items->isEmpty()) {
                return 'Tidak ada produk dengan stok kritis. Seluruh stok berada di atas batas minimum.';
            }

            $lines = ["Produk dengan stok kritis (di bawah batas minimum): ditemukan {$count} item."];
            foreach ($items as $i) {
                $pct = $i->minimum_stock > 0 ? round(($i->current_stock / $i->minimum_stock) * 100) : 0;
                $lines[] = "- {$i->product->name} ({$i->product->size}{$i->product->unit}): sisa {$i->current_stock} ({$pct}% dari minimum {$i->minimum_stock})";
            }
            return implode("\n", $lines);
        }

        if ($status === 'habis') {
            $items = Inventory::where('current_stock', '<=', 0)
                ->with('product')->take(30)->get();
            $count = Inventory::where('current_stock', '<=', 0)->count();

            if ($items->isEmpty()) {
                return 'Tidak ada produk yang stoknya habis. Kondisi gudang aman.';
            }

            $lines = ["Produk dengan stok habis: ditemukan {$count} item."];
            foreach ($items as $i) {
                $lines[] = "- {$i->product->name} ({$i->product->size}{$i->product->unit})";
            }
            return implode("\n", $lines);
        }

        return 'Parameter status tidak valid. Gunakan "kritis" atau "habis".';
    }

    private function getIncomingStock(array $input): string
    {
        $date = $input['date'] ?? Carbon::now()->toDateString();
        $type = $input['type'] ?? 'expected';

        $dateObj = Carbon::parse($date)->startOfDay();

        if ($type === 'expected') {
            $orders = PurchaseOrder::whereDate('expected_date', '=', $dateObj)
                ->whereIn('status', ['sent', 'partial'])
                ->with('supplier', 'items.product')
                ->get();

            if ($orders->isEmpty()) {
                return "Tidak ada barang yang dijadwalkan masuk pada tanggal {$dateObj->format('d/m/Y')}.";
            }

            $lines = ["Barang dijadwalkan masuk pada {$dateObj->format('d/m/Y')}:"];
            foreach ($orders as $po) {
                $supplier = $po->supplier->name ?? 'Unknown';
                $lines[] = "- PO {$po->po_number} dari {$supplier} (status: {$po->status}):";
                foreach ($po->items as $item) {
                    $remaining = $item->quantity - ($item->received_quantity ?? 0);
                    $productName = $item->product->name ?? 'Produk #' . $item->product_id;
                    if ($remaining > 0) {
                        $lines[] = "  * {$productName} — {$remaining} pcs";
                    }
                }
            }
            return implode("\n", $lines);
        }

        if ($type === 'received') {
            $orders = PurchaseOrder::whereDate('received_date', '=', $dateObj)
                ->whereIn('status', ['received', 'partial'])
                ->with('supplier', 'items.product')
                ->get();

            if ($orders->isEmpty()) {
                return "Tidak ada barang yang diterima pada tanggal {$dateObj->format('d/m/Y')}.";
            }

            $lines = ["Barang diterima pada {$dateObj->format('d/m/Y')}:"];
            foreach ($orders as $po) {
                $supplier = $po->supplier->name ?? 'Unknown';
                $lines[] = "- PO {$po->po_number} dari {$supplier} (total: Rp " . number_format($po->total_amount ?? 0, 0, ',', '.') . "):";
                foreach ($po->items as $item) {
                    $received = $item->received_quantity ?? 0;
                    $productName = $item->product->name ?? 'Produk #' . $item->product_id;
                    if ($received > 0) {
                        $unitCost = $item->unit_cost ?? 0;
                        $subtotal = $received * $unitCost;
                        $lines[] = "  * {$productName} — {$received} pcs (Rp " . number_format($subtotal, 0, ',', '.') . ")";
                    }
                }
            }
            return implode("\n", $lines);
        }

        return 'Parameter type tidak valid. Gunakan "expected" atau "received".';
    }

    private function getSalesSummary(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'hari_ini');

        $sales = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $count = Transaction::whereBetween('created_at', [$start, $end])->count();

        if ($count === 0) {
            return "Tidak terdapat transaksi pada periode {$label}.";
        }

        $avg = $sales / $count;

        $lines = [
            "Laporan Penjualan — {$label}:",
            "- Total pendapatan: Rp " . number_format($sales, 0, ',', '.'),
            "- Jumlah transaksi: {$count}",
            "- Rata-rata per transaksi: Rp " . number_format($avg, 0, ',', '.'),
        ];

        if (in_array($input['period'] ?? '', ['bulan_ini', 'bulan_lalu', 'minggu_ini', 'minggu_lalu'])) {
            $periods = [
                'bulan_ini' => ['bulan_lalu', 'bulan ini'],
                'bulan_lalu' => ['dua_bulan_lalu', 'bulan lalu'],
                'minggu_ini' => ['minggu_lalu', 'minggu ini'],
                'minggu_lalu' => ['dua_minggu_lalu', 'minggu lalu'],
            ];

            if (isset($periods[$input['period']])) {
                [$prevPeriod, $prevLabel] = $periods[$input['period']];
                [$prevStart, $prevEnd] = $this->parsePeriod($prevPeriod);
                $prevSales = (float) Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');

                if ($prevSales > 0) {
                    $change = round((($sales - $prevSales) / $prevSales) * 100, 1);
                    $trend = $change >= 0 ? 'naik' : 'turun';
                    $lines[] = "- Perubahan dibanding {$prevLabel}: {$trend} " . number_format(abs($change), 1) . '%';
                }
            }
        }

        return implode("\n", $lines);
    }

    private function getBestSellingProducts(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');
        $limit = min((int) ($input['limit'] ?? 10), 50);

        $tops = TransactionDetail::select(
                'products.name',
                'products.brand',
                'products.size',
                'products.unit',
                DB::raw('SUM(transaction_details.quantity) as total_qty'),
                DB::raw('SUM(transaction_details.subtotal) as total_rev')
            )
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transaction_details.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name', 'products.brand', 'products.size', 'products.unit')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        if ($tops->isEmpty()) {
            return "Belum ada data penjualan untuk periode {$label}.";
        }

        $lines = ["Produk Terlaris — {$label}:"];
        $rank = 1;
        foreach ($tops as $t) {
            $lines[] = "{$rank}. {$t->name} ({$t->brand}, {$t->size}{$t->unit}) — {$t->total_qty} pcs terjual — Rp " . number_format($t->total_rev, 0, ',', '.');
            $rank++;
        }

        return implode("\n", $lines);
    }

    private function getProfitLoss(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');

        $revenue = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');

        $cogs = (float) TransactionDetail::whereBetween('transaction_details.created_at', [$start, $end])
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));

        $expenses = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;
        $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        $lines = [
            "Laporan Laba Rugi — {$label}:",
            "- Total pendapatan (omzet): Rp " . number_format($revenue, 0, ',', '.'),
            "- Harga pokok penjualan (HPP): Rp " . number_format($cogs, 0, ',', '.'),
            "- Laba kotor: Rp " . number_format($grossProfit, 0, ',', '.'),
            "- Total pengeluaran: Rp " . number_format($expenses, 0, ',', '.'),
            "- Laba bersih: Rp " . number_format($netProfit, 0, ',', '.'),
            "- Margin keuntungan: {$margin}%",
        ];

        return implode("\n", $lines);
    }

    private function getCustomerCount(): string
    {
        $total = Customer::count();
        $recent = Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $retail = Customer::where('type', 'retail')->count();
        $wholesale = Customer::where('type', 'wholesale')->count();

        return "Jumlah Pelanggan Terdaftar:\n"
            . "- Total: {$total}\n"
            . "- Retail: {$retail}\n"
            . "- Grosir: {$wholesale}\n"
            . "- Pendaftaran baru (30 hari): {$recent}";
    }

    private function getCustomerOrigin(): string
    {
        $customers = Customer::whereNotNull('address')
            ->where('address', '!=', '')
            ->get(['address', 'name']);

        $total = Customer::count();
        $withAddress = $customers->count();

        $cities = [];
        foreach ($customers as $c) {
            $parts = explode(',', $c->address);
            $first = trim($parts[0]);
            if (strlen($first) > 1) {
                $cities[$first] = ($cities[$first] ?? 0) + 1;
            }
        }
        arsort($cities);

        $lines = ["Sebaran Asal Pelanggan:"];
        $lines[] = "- Total pelanggan: {$total}";
        $lines[] = "- Pelanggan dengan data alamat: {$withAddress}";

        if (!empty($cities)) {
            $lines[] = "";
            $lines[] = "Berdasarkan alamat yang tercatat:";
            $rank = 1;
            foreach (array_slice($cities, 0, 15) as $city => $count) {
                $lines[] = "{$rank}. {$city}: {$count} pelanggan";
                $rank++;
            }
            $lines[] = "";
            $lines[] = "Catatan: Data alamat bersifat free-text, pengelompokan ini estimasi berdasarkan teks alamat.";
        } else {
            $lines[] = "Tidak ada data alamat pelanggan yang tersimpan untuk dianalisis sebaran asalnya.";
        }

        return implode("\n", $lines);
    }

    private function getEmployeeInfo(array $input): string
    {
        $total = User::count();
        $active = User::where('is_active', true)->count();
        $roles = User::where('is_active', true)
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->orderByDesc('count')
            ->pluck('count', 'role');

        $dateStr = $input['date'] ?? Carbon::now()->toDateString();
        $dateObj = Carbon::parse($dateStr);

        $presentToday = Attendance::whereDate('date', '=', $dateObj)
            ->whereNull('time_out')
            ->get();

        $allToday = Attendance::whereDate('date', '=', $dateObj)->count();

        $lines = [
            "Informasi Karyawan:",
            "- Total karyawan: {$total}",
            "- Karyawan aktif: {$active}",
        ];

        if ($roles->isNotEmpty()) {
            $lines[] = "- Komposisi role:";
            foreach ($roles as $role => $count) {
                $lines[] = "  * {$role}: {$count} orang";
            }
        }

        $lines[] = "";
        $lines[] = "Absensi {$dateObj->format('d/m/Y')}:";

        if ($presentToday->isNotEmpty()) {
            $lines[] = "- Sedang bekerja hari ini:";
            foreach ($presentToday as $a) {
                $name = $a->employee_name ?? $a->cashier_name ?? $a->user->name ?? 'Karyawan';
                $timeIn = $a->time_in ? Carbon::parse($a->time_in)->format('H:i') : '-';
                $lines[] = "  * {$name} (masuk: {$timeIn})";
            }
        } else {
            $lines[] = "- Tidak ada karyawan yang sedang bekerja (belum check in).";
        }

        $lines[] = "- Total catatan absensi hari ini: {$allToday}";

        return implode("\n", $lines);
    }

    private function getShiftStatus(): string
    {
        $shift = Shift::whereNull('end_time')->with('user')->first();

        if (!$shift) {
            return "Saat ini tidak ada shift yang aktif. Belum ada petugas yang melakukan Open Shift.";
        }

        $name = $shift->user->name ?? 'Tim Toko';
        $start = $shift->start_time instanceof Carbon
            ? $shift->start_time->format('H:i')
            : Carbon::parse($shift->start_time)->format('H:i');

        $startCarbon = $shift->start_time instanceof Carbon
            ? $shift->start_time
            : Carbon::parse($shift->start_time);

        $durasi = $startCarbon->diffForHumans(Carbon::now(), ['parts' => 2]);

        $modal = 'Rp ' . number_format($shift->initial_cash ?? 0, 0, ',', '.');
        $todaySales = (float) Transaction::whereDate('created_at', '=', Carbon::now()->toDateString())
            ->where('shift_id', $shift->id)->sum('total_amount');

        return "Shift yang Sedang Aktif:\n"
            . "- Petugas kasir: {$name}\n"
            . "- Waktu buka: {$start} ({$durasi} yang lalu)\n"
            . "- Modal awal: {$modal}\n"
            . "- Penjualan shift ini: Rp " . number_format($todaySales, 0, ',', '.');
    }

    private function getActivePromos(): string
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expiration_date')
                  ->orWhere('expiration_date', '>=', Carbon::now());
            })
            ->get();

        if ($coupons->isEmpty()) {
            return 'Saat ini tidak ada kupon promo yang aktif. Silakan buat kupon baru melalui menu Kupon & Loyalty.';
        }

        $lines = ["Kupon Promo Aktif ({$coupons->count()} kupon):"];
        foreach ($coupons as $c) {
            $discount = $c->is_percentage ? "{$c->value}%" : 'Rp ' . number_format($c->value, 0, ',', '.');
            $typeLabel = match ($c->type) {
                'discount' => 'Diskon',
                'bonus' => 'Bonus',
                'cashback' => 'Cashback',
                default => $c->type,
            };
            $exp = $c->expiration_date ? 's.d. ' . Carbon::parse($c->expiration_date)->format('d/m/Y') : 'tanpa batas';
            $usage = $c->max_usage > 0 ? "{$c->used_count}/{$c->max_usage} kali" : "{$c->used_count} kali";
            $lines[] = "- {$c->code} ({$typeLabel} {$discount}, berlaku {$exp}, dipakai {$usage})";
        }

        return implode("\n", $lines);
    }

    private function getWholesaleOrderStatus(array $input): string
    {
        $statusFilter = $input['status'] ?? null;

        $query = WholesaleOrder::query();
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $orders = $query->with('customer')->orderBy('created_at', 'desc')->take(20)->get();
        $counts = WholesaleOrder::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $lines = ["Status Pesanan Grosir:"];

        $allStatuses = ['pending', 'reviewed', 'on_progress', 'packed', 'shipped', 'delivered', 'completed', 'cancelled'];
        foreach ($allStatuses as $s) {
            $c = isset($counts[$s]) ? $counts[$s] : (object) ['count' => 0, 'total' => 0];
            $totalFormatted = $c->total ? 'Rp ' . number_format($c->total, 0, ',', '.') : 'Rp 0';
            $lines[] = "- {$s}: {$c->count} pesanan ({$totalFormatted})";
        }

        if ($statusFilter && $orders->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "Daftar pesanan dengan status '{$statusFilter}':";
            foreach ($orders as $o) {
                $customer = $o->customer->name ?? 'Pelanggan';
                $total = 'Rp ' . number_format($o->total_amount ?? 0, 0, ',', '.');
                $date = $o->created_at ? Carbon::parse($o->created_at)->format('d/m/Y') : '-';
                $lines[] = "- {$o->invoice_number} — {$customer} — {$total} ({$date})";
            }
        }

        return implode("\n", $lines);
    }

    private function getWholesaleCustomerTier(): string
    {
        $customers = User::where('role', 'wholesale_customer')->get();
        $total = $customers->count();

        $tierConfig = [
            'Regular' => ['min' => 0, 'max' => 9999999],
            'VIP' => ['min' => 10000000, 'max' => 19999999],
            'Silver' => ['min' => 20000000, 'max' => 29999999],
            'Gold' => ['min' => 30000000, 'max' => 49999999],
            'Platinum' => ['min' => 50000000, 'max' => PHP_INT_MAX],
        ];

        $tierCounts = [];
        $tierRevenue = [];
        foreach ($tierConfig as $tier => $range) {
            $tierCounts[$tier] = 0;
            $tierRevenue[$tier] = 0;
        }

        foreach ($customers as $c) {
            $spent = WholesaleOrder::where(function ($q) use ($c) {
                $q->where('recipient_phone', $c->phone)
                  ->orWhereHas('customer', fn($cq) => $cq->where('email', $c->email));
            })->whereIn('status', ['completed', 'delivered', 'shipped'])->sum('total_amount');

            foreach ($tierConfig as $tier => $range) {
                if ($spent >= $range['min'] && $spent <= $range['max']) {
                    $tierCounts[$tier]++;
                    $tierRevenue[$tier] += $spent;
                    break;
                }
            }
        }

        $lines = ["Distribusi Tier Pelanggan Grosir (Total: {$total}):"];
        foreach ($tierConfig as $tier => $range) {
            $pct = $total > 0 ? round(($tierCounts[$tier] / $total) * 100, 1) : 0;
            $rev = 'Rp ' . number_format($tierRevenue[$tier], 0, ',', '.');
            $lines[] = "- {$tier}: {$tierCounts[$tier]} pelanggan ({$pct}%) — Total belanja: {$rev}";
        }
        return implode("\n", $lines);
    }

    private function getReferralLeaderboard(): string
    {
        $totalWithReferral = User::where('role', 'wholesale_customer')->whereNotNull('referral_code')->count();
        $totalReferred = User::where('role', 'wholesale_customer')->whereNotNull('referred_by_id')->count();
        $topReferrer = User::where('role', 'wholesale_customer')
            ->withCount('referrals')
            ->orderByDesc('referrals_count')
            ->first();

        $lines = ["Program Referral — Ringkasan:"];
        $lines[] = "- Pelanggan dengan kode referral: {$totalWithReferral}";
        $lines[] = "- Pelanggan yang direferensikan: {$totalReferred}";

        if ($topReferrer && $topReferrer->referrals_count > 0) {
            $lines[] = "- Top referrer: {$topReferrer->name} ({$topReferrer->referrals_count} referral)";
        } else {
            $lines[] = "- Belum ada aktivitas referral yang tercatat.";
        }
        return implode("\n", $lines);
    }

    private function getInventoryMovement(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');

        $soldQty = (float) TransactionDetail::whereBetween('created_at', [$start, $end])->sum('quantity');
        $soldVal = (float) TransactionDetail::whereBetween('transaction_details.created_at', [$start, $end])
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));

        $receivedQty = (float) PurchaseOrder::whereBetween('received_date', [$start, $end])
            ->whereIn('status', ['received', 'partial'])
            ->with('items')
            ->get()
            ->sum(fn($po) => $po->items->sum('received_quantity'));

        $expiredQty = (float) Inventory::whereNotNull('expiration_date')
            ->where('expiration_date', '<', Carbon::now())
            ->where('current_stock', '>', 0)
            ->sum('current_stock');

        $totalInv = (float) Inventory::sum('current_stock');
        $turnover = $totalInv > 0 ? round($soldQty / $totalInv, 2) : 0;

        $lines = ["Pergerakan Stok — {$label}:"];
        $lines[] = "- Barang terjual: " . number_format($soldQty, 0) . " unit (Rp " . number_format($soldVal, 0, ',', '.') . " HPP)";
        $lines[] = "- Barang diterima: " . number_format($receivedQty, 0) . " unit";
        $lines[] = "- Aset kadaluarsa: " . number_format($expiredQty, 0) . " unit (potensi rugi)";
        $lines[] = "- Perputaran stok (turnover ratio): {$turnover}x";
        return implode("\n", $lines);
    }

    private function getSupplierPerformance(): string
    {
        $suppliers = Supplier::withCount(['purchaseOrders as po_count'])
            ->get();

        $total = $suppliers->count();
        $lines = ["Performa Supplier (Total: {$total}):"];

        foreach ($suppliers->take(10) as $s) {
            $poCount = PurchaseOrder::where('supplier_id', $s->id)->count();
            $totalSpent = (float) PurchaseOrder::where('supplier_id', $s->id)
                ->whereIn('status', ['received', 'partial'])->sum('total_amount');

            $avgLeadTime = PurchaseOrder::where('supplier_id', $s->id)
                ->whereNotNull('expected_date')->whereNotNull('received_date')
                ->select(DB::raw('AVG(DATEDIFF(received_date, expected_date)) as avg_delay'))
                ->value('avg_delay');

            $delayNote = $avgLeadTime !== null
                ? ($avgLeadTime <= 0 ? 'tepat waktu' : "rata-rata telat " . round($avgLeadTime) . " hari")
                : 'belum ada data';

            $spent = 'Rp ' . number_format($totalSpent, 0, ',', '.');
            $lines[] = "- {$s->name}: {$poCount} PO, {$spent}, {$delayNote}";
        }

        if ($total > 10) $lines[] = "- ... dan " . ($total - 10) . " supplier lainnya.";
        return implode("\n", $lines);
    }

    private function getExpenseAnalytics(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');

        $total = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $count = Expense::whereBetween('created_at', [$start, $end])->count();

        $byCategory = Expense::select(DB::raw('COALESCE(expense_categories.name, "Tidak Dikategorikan") as category'), DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereBetween('expenses.created_at', [$start, $end])
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $biggest = Expense::whereBetween('created_at', [$start, $end])
            ->orderByDesc('amount')->first();

        $lines = ["Analisis Pengeluaran — {$label}:"];
        $lines[] = "- Total pengeluaran: Rp " . number_format($total, 0, ',', '.');
        $lines[] = "- Jumlah transaksi: {$count}";

        if ($byCategory->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "Per Kategori:";
            foreach ($byCategory as $c) {
                $pct = $total > 0 ? round(($c->total / $total) * 100, 1) : 0;
                $lines[] = "- {$c->category}: Rp " . number_format($c->total, 0, ',', '.') . " ({$pct}%, {$c->count} transaksi)";
            }
        }

        if ($biggest) {
            $lines[] = "";
            $lines[] = "- Pengeluaran terbesar: {$biggest->description} (Rp " . number_format($biggest->amount, 0, ',', '.') . ")";
        }
        return implode("\n", $lines);
    }

    private function getBranchComparison(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            return 'Tidak ada cabang aktif untuk dibandingkan.';
        }

        $lines = ["Perbandingan Kinerja Cabang — {$label}:"];

        foreach ($branches as $b) {
            $sales = (float) Transaction::where('branch_id', $b->id)
                ->whereBetween('created_at', [$start, $end])->sum('total_amount');
            $count = Transaction::where('branch_id', $b->id)
                ->whereBetween('created_at', [$start, $end])->count();
            $expenses = (float) Expense::where('branch_id', $b->id)
                ->whereBetween('created_at', [$start, $end])->sum('amount');
            $profit = $sales - $expenses;

            $lines[] = "- {$b->name}: {$count} transaksi, Rp " . number_format($sales, 0, ',', '.') . " (omzet), Rp " . number_format($expenses, 0, ',', '.') . " (biaya), laba bersih Rp " . number_format($profit, 0, ',', '.');
        }
        return implode("\n", $lines);
    }

    private function getProductCategoryPerformance(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');

        $categories = DB::table('product_categories')
            ->leftJoin('products', 'product_categories.id', '=', 'products.product_category_id')
            ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->whereBetween('transaction_details.created_at', [$start, $end])
            ->select(
                'product_categories.name',
                DB::raw('COUNT(DISTINCT products.id) as product_count'),
                DB::raw('SUM(transaction_details.quantity) as qty_sold'),
                DB::raw('SUM(transaction_details.subtotal) as revenue')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('revenue')
            ->get();

        if ($categories->isEmpty()) {
            return "Belum ada data penjualan per kategori untuk periode {$label}.";
        }

        $lines = ["Performa Kategori Produk — {$label}:"];
        foreach ($categories as $c) {
            $rev = 'Rp ' . number_format($c->revenue ?? 0, 0, ',', '.');
            $lines[] = "- {$c->name}: {$c->product_count} produk, {$c->qty_sold} unit terjual, {$rev}";
        }
        return implode("\n", $lines);
    }

    private function getBusinessHealthScore(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');
        $now = Carbon::now();

        // 1. Profitability Score (30%)
        $revenue = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
        $cogs = (float) TransactionDetail::whereBetween('transaction_details.created_at', [$start, $end])
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * COALESCE(products.purchase_price, 0)'));
        $expenses = (float) Expense::whereBetween('created_at', [$start, $end])->sum('amount');
        $netProfit = $revenue - $cogs - $expenses;
        $margin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        $profitScore = min(30, max(0, ($margin / 20) * 30));

        // 2. Stock Health (25%)
        $lowStock = Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count();
        $outStock = Inventory::where('current_stock', '<=', 0)->count();
        $totalInv = Product::count();
        $stockIssuePct = $totalInv > 0 ? (($lowStock + $outStock) / $totalInv) * 100 : 0;
        $stockScore = min(25, max(0, 25 - ($stockIssuePct / 100) * 25));

        // 3. Growth Score (20%)
        $prevStart = (clone $start)->subMonth();
        $prevEnd = (clone $end)->subMonth();
        $prevRevenue = (float) Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');
        $growth = $prevRevenue > 0 ? (($revenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $growthScore = min(20, max(0, 10 + ($growth / 50) * 10));

        // 4. Liquidity (15%)
        $cashIn = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('paid_amount');
        $cashOut = $expenses;
        $liquidityRatio = $cashOut > 0 ? $cashIn / $cashOut : 2;
        $liquidityScore = min(15, max(0, ($liquidityRatio / 2) * 15));

        // 5. Operational Efficiency (10%)
        $transactionCount = Transaction::whereBetween('created_at', [$start, $end])->count();
        $daysActive = max(1, (int) $now->diffInDays($start));
        $avgDaily = $transactionCount / $daysActive;
        $efficiencyScore = min(10, max(0, ($avgDaily / 20) * 10));

        $totalScore = round($profitScore + $stockScore + $growthScore + $liquidityScore + $efficiencyScore);

        $grade = $totalScore >= 85 ? 'Sangat Sehat' : ($totalScore >= 70 ? 'Sehat' : ($totalScore >= 50 ? 'Cukup' : ($totalScore >= 30 ? 'Kurang Sehat' : 'Kritis')));

        $lines = ["Skor Kesehatan Bisnis — {$label}:"];
        $lines[] = "Skor Total: {$totalScore}/100 — {$grade}";
        $lines[] = "";
        $lines[] = "Detail Pilar:";
        $lines[] = "- Profitabilitas: " . round($profitScore) . "/30 (margin " . round($margin, 1) . "%, laba bersih Rp " . number_format($netProfit, 0, ',', '.') . ")";
        $lines[] = "- Kesehatan Stok: " . round($stockScore) . "/25 ({$lowStock} kritis, {$outStock} habis)";
        $lines[] = "- Pertumbuhan: " . round($growthScore) . "/20 (pertumbuhan " . round($growth, 1) . "%)";
        $lines[] = "- Likuiditas: " . round($liquidityScore) . "/15 (rasio " . round($liquidityRatio, 2) . "x)";
        $lines[] = "- Efisiensi: " . round($efficiencyScore) . "/10 (rata-rata " . round($avgDaily, 1) . " transaksi/hari)";
        $lines[] = "";
        if ($totalScore < 50) {
            $lines[] = "⚠️ Perhatian: Skor kesehatan menurun. Fokus pada peningkatan penjualan dan efisiensi biaya.";
        } elseif ($totalScore >= 85) {
            $lines[] = "🌟 Kinerja sangat baik! Pertahankan konsistensi dan ekspansi.";
        }
        return implode("\n", $lines);
    }

    private function getAnomalyDetection(): string
    {
        $now = Carbon::now();
        $anomalies = [];

        // 1. Sales drop compared to yesterday
        $todaySales = (float) Transaction::whereDate('created_at', $now)->sum('total_amount');
        $yesterdaySales = (float) Transaction::whereDate('created_at', $now->copy()->subDay())->sum('total_amount');
        if ($yesterdaySales > 0 && $todaySales < $yesterdaySales * 0.3) {
            $anomalies[] = "🔻 Penjualan hari ini turun drastis (" . round(($todaySales / $yesterdaySales) * 100, 1) . "% dari kemarin)";
        }

        // 2. Unusual expense spike
        $monthExpenses = (float) Expense::whereMonth('created_at', $now->month)->sum('amount');
        $lastMonthExpenses = (float) Expense::whereMonth('created_at', $now->copy()->subMonth()->month)->sum('amount');
        if ($lastMonthExpenses > 0 && $monthExpenses > $lastMonthExpenses * 1.5) {
            $anomalies[] = "🔺 Pengeluaran bulan ini naik signifikan (" . round(($monthExpenses / $lastMonthExpenses) * 100, 1) . "% dari bulan lalu)";
        }

        // 3. Zero stock spike
        $outOfStock = Inventory::where('current_stock', '<=', 0)->count();
        if ($outOfStock > 20) {
            $anomalies[] = "📦 Terlalu banyak produk habis ({$outOfStock} SKU). Segera lakukan restok.";
        }

        // 4. No shift opened today
        $todayShift = Shift::whereDate('start_time', $now)->first();
        if (!$todayShift) {
            $anomalies[] = "⏰ Belum ada shift yang dibuka hari ini. Toko mungkin belum beroperasi.";
        }

        // 5. No attendance today
        $todayAttendance = Attendance::whereDate('date', $now)->count();
        if ($todayAttendance === 0) {
            $anomalies[] = "👥 Belum ada absensi karyawan hari ini.";
        }

        if (empty($anomalies)) {
            return "Tidak ada anomali terdeteksi. Semua indikator bisnis dalam batas normal.";
        }

        $lines = ["Anomali Terdeteksi (" . count($anomalies) . "):"];
        foreach ($anomalies as $a) {
            $lines[] = $a;
        }
        return implode("\n", $lines);
    }

    private function getPeakHoursAnalysis(array $input): string
    {
        [$start, $end, $label] = $this->parsePeriod($input['period'] ?? 'bulan_ini');

        $hourlyData = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $dailyData = Transaction::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DAYNAME(created_at) as day_name'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('day_name')
            ->orderByDesc('count')
            ->get();

        $peakHour = $hourlyData->sortByDesc('count')->first();
        $peakDay = $dailyData->first();

        $lines = ["Analisis Jam & Hari Sibuk — {$label}:"];

        if ($peakHour) {
            $lines[] = "- Jam tersibuk: {$peakHour->hour}:00 ({$peakHour->count} transaksi, Rp " . number_format($peakHour->total, 0, ',', '.') . ")";
        }
        if ($peakDay) {
            $lines[] = "- Hari tersibuk: {$peakDay->day_name} ({$peakDay->count} transaksi, Rp " . number_format($peakDay->total, 0, ',', '.') . ")";
        }

        if ($hourlyData->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "Distribusi per Jam:";
            foreach ($hourlyData as $h) {
                $bar = str_repeat('█', max(1, round(($h->count / $hourlyData->max('count')) * 10)));
                $lines[] = "  {$h->hour}:00 {$bar} ({$h->count})";
            }
        }

        if ($dailyData->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "Distribusi per Hari:";
            $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $dayMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
            foreach ($dayOrder as $d) {
                $data = $dailyData->firstWhere('day_name', $d);
                if ($data) {
                    $count = $data->count;
                    $total = 'Rp ' . number_format($data->total, 0, ',', '.');
                    $lines[] = "  {$dayMap[$d]}: {$count} transaksi ({$total})";
                }
            }
        }
        return implode("\n", $lines);
    }

    private function getTrackingInfo(array $input): string
    {
        $query = $input['query'] ?? '';

        $order = WholesaleOrder::where('invoice_number', 'LIKE', "%{$query}%")
            ->orWhere('tracking_number', 'LIKE', "%{$query}%")
            ->with('details')
            ->first();

        if (!$order) {
            return "Pesanan dengan nomor '{$query}' tidak ditemukan.";
        }

        $statusLabels = [
            'pending' => 'Menunggu Review', 'reviewed' => 'Dikonfirmasi',
            'on_progress' => 'Diproses', 'packed' => 'Di-packing',
            'shipped' => 'Dikirim', 'delivered' => 'Diterima',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];

        $lines = ["Informasi Pengiriman:"];
        $lines[] = "- Invoice: {$order->invoice_number}";
        $lines[] = "- Penerima: {$order->recipient_name}";
        $lines[] = "- Status: " . ($statusLabels[$order->status] ?? $order->status);
        $lines[] = "- Total: Rp " . number_format($order->total_amount ?? 0, 0, ',', '.');

        if ($order->tracking_number) {
            $lines[] = "- No. Resi: {$order->tracking_number}";
        }
        if ($order->shipping_courier) {
            $lines[] = "- Kurir: {$order->shipping_courier}";
        }
        if ($order->shipping_address) {
            $lines[] = "- Alamat: {$order->shipping_address}";
        }

        $date = $order->created_at ? Carbon::parse($order->created_at)->format('d/m/Y H:i') : '-';
        $lines[] = "- Dibuat: {$date}";
        return implode("\n", $lines);
    }

    private function parsePeriod(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'hari_ini' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Hari Ini (' . $now->format('d/m/Y') . ')'],
            'kemarin' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(), 'Kemarin (' . $now->copy()->subDay()->format('d/m/Y') . ')'],
            'minggu_ini' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'Minggu Ini'],
            'minggu_lalu' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek(), 'Minggu Lalu'],
            'bulan_ini' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $now->translatedFormat('F Y')],
            'bulan_lalu' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth(), $now->copy()->subMonth()->translatedFormat('F Y')],
            'dua_bulan_lalu' => [$now->copy()->subMonths(2)->startOfMonth(), $now->copy()->subMonths(2)->endOfMonth(), $now->copy()->subMonths(2)->translatedFormat('F Y')],
            'dua_minggu_lalu' => [$now->copy()->subWeeks(2)->startOfWeek(), $now->copy()->subWeeks(2)->endOfWeek(), 'Dua Minggu Lalu'],
            'tahun_ini' => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'Tahun ' . $now->format('Y')],
            'semua_waktu' => [Carbon::create(2000, 1, 1), $now->copy()->endOfDay(), 'Sepanjang Waktu'],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), $period],
        };
    }
}
