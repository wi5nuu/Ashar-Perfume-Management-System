<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Services\Contracts\CopilotEngineInterface;
use App\Services\CopilotIntents\CopilotIntentHandler;
use Illuminate\Support\Facades\Log;
use Sastrawi\Stemmer\StemmerFactory;

class RuleBasedCopilotEngine implements CopilotEngineInterface
{
    private const PHRASE_WEIGHT = 3;
    private const KEYWORD_WEIGHT = 1.5;
    private const FUZZY_WEIGHT = 0.5;
    private const THRESHOLD = 1.5;
    private const AMBIGUITY_RATIO = 0.5;

    private array $intents;
    private array $reservedWords;
    private \Sastrawi\Stemmer\StemmerInterface $stemmer;

    public function __construct()
    {
        $config = config('ai_intents');
        $this->intents = $config['intents'] ?? [];
        $this->reservedWords = $config['reserved_words'] ?? [];

        $factory = new StemmerFactory();
        $this->stemmer = $factory->createStemmer();
    }

    public function handle(string $query, array $context = []): string
    {
        $original = trim($query);
        $normalized = $this->normalize($original);

        $stemmedQuery = $this->stemmer->stem($normalized);
        $stemmedWords = array_values(array_filter(explode(' ', $stemmedQuery)));

        $scores = $this->scoreIntents($original, $normalized, $stemmedQuery, $stemmedWords);

        if (!empty($scores)) {
            $best = $scores[0];
            if ($best['score'] >= self::THRESHOLD) {
                if (count($scores) > 1 && $scores[1]['score'] >= self::THRESHOLD) {
                    $ratio = $scores[1]['score'] / $best['score'];
                    if ($ratio > (1 - self::AMBIGUITY_RATIO)) {
                        return "Pertanyaan Anda dapat diartikan sebagai:\n"
                            . "1. " . $this->getIntentLabel($best['intent']) . "\n"
                            . "2. " . $this->getIntentLabel($scores[1]['intent']) . "\n\n"
                            . "Silakan perjelas pertanyaan Anda.";
                    }
                }
                return $this->runHandler($best['intent']);
            }
        }

        if ($this->containsReservedWord($normalized)) {
            $this->logUnanswered($original, $context['user_id'] ?? null);
            return 'Maaf, saya tidak dapat menjawab pertanyaan tersebut. '
                . 'Ketikan "help" atau "bantuan" untuk melihat daftar pertanyaan yang bisa saya jawab.';
        }

        $productResult = $this->searchProduct($original, $normalized);
        if ($productResult !== null) {
            return $productResult;
        }

        $this->logUnanswered($original, $context['user_id'] ?? null);
        return 'Maaf, saya tidak dapat menjawab pertanyaan tersebut. '
            . 'Ketikan "help" atau "bantuan" untuk melihat daftar pertanyaan yang bisa saya jawab.';
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function scoreIntents(string $original, string $normalized, string $stemmedQuery, array $stemmedWords): array
    {
        $results = [];

        foreach ($this->intents as $key => $intent) {
            $score = 0;

            foreach ($intent['phrases'] as $phrase) {
                $stemmedPhrase = $this->stemmer->stem($phrase);

                $phraseWords = explode(' ', $phrase);
                $queryWords = explode(' ', $original);
                if (count(array_intersect($phraseWords, $queryWords)) === count($phraseWords)) {
                    $score += self::PHRASE_WEIGHT;
                    continue;
                }

                $stemmedPhraseWords = explode(' ', $stemmedPhrase);
                if (count(array_intersect($stemmedPhraseWords, $stemmedWords)) === count($stemmedPhraseWords)) {
                    $score += self::PHRASE_WEIGHT;
                }
            }

            foreach ($intent['keywords'] as $keyword) {
                $stemmedKeyword = $this->stemmer->stem($keyword);
                foreach ($stemmedWords as $word) {
                    if ($word === $stemmedKeyword) {
                        $score += self::KEYWORD_WEIGHT;
                    } elseif ($this->fuzzyMatch($word, $stemmedKeyword)) {
                        $score += self::FUZZY_WEIGHT;
                    }
                }
            }

            if ($score > 0) {
                $results[] = [
                    'intent' => $key,
                    'score' => $score,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    private function fuzzyMatch(string $word, string $target): bool
    {
        if (strlen($word) < 4) {
            return levenshtein($word, $target) <= 1;
        }
        return levenshtein($word, $target) <= 2;
    }

    private function containsReservedWord(string $normalized): bool
    {
        $words = explode(' ', $normalized);
        foreach ($words as $word) {
            if (in_array($word, $this->reservedWords, true)) {
                return true;
            }
        }
        return false;
    }

    private function searchProduct(string $original, string $normalized): ?string
    {
        $words = explode(' ', $normalized);
        $searchTerm = $original;

        $product = Product::where('name', 'like', "%{$searchTerm}%")
            ->orWhere('brand', 'like', "%{$searchTerm}%")
            ->first();

        if ($product) {
            $inventory = Inventory::where('product_id', $product->id)->first();
            $stock = $inventory ? $inventory->current_stock : 0;
            $price = number_format($product->selling_price, 0, ',', '.');
            $purchasePrice = number_format($product->purchase_price, 0, ',', '.');

            return "{$product->name}"
                . ($product->brand ? " ({$product->brand})" : '')
                . ($product->size ? " - {$product->size}" : '')
                . " | Harga jual: Rp {$price}"
                . " | Harga beli: Rp {$purchasePrice}"
                . " | Stok: {$stock}";
        }

        return null;
    }

    private function getIntentLabel(string $key): string
    {
        $labels = [
            'best_selling_products' => 'Produk terlaris',
            'incoming_stock' => 'Barang masuk',
            'branch_info' => 'Info cabang',
            'customer_count' => 'Jumlah pelanggan',
            'customer_origin' => 'Asal pelanggan',
            'stock_summary' => 'Ringkasan stok',
            'critical_stock' => 'Stok kritis/habis',
            'sales_summary' => 'Penjualan',
            'profit_loss' => 'Laba rugi',
            'expense_summary' => 'Pengeluaran',
            'employee_info' => 'Info karyawan',
            'shift_status' => 'Status shift',
            'active_promos' => 'Promo aktif',
            'wholesale_status' => 'Pesanan grosir',
            'daily_recap' => 'Rekap harian',
            'attendance_info' => 'Absensi',
        ];
        return $labels[$key] ?? $key;
    }

    private function runHandler(string $intentKey): string
    {
        $intent = $this->intents[$intentKey] ?? null;
        if (!$intent || !isset($intent['handler'])) {
            return 'Terjadi kesalahan: handler tidak ditemukan.';
        }

        $handlerClass = $intent['handler'];
        if (!class_exists($handlerClass)) {
            Log::error("RuleBasedCopilotEngine: Handler class {$handlerClass} not found for intent {$intentKey}");
            return 'Terjadi kesalahan: handler tidak ditemukan.';
        }

        $handler = app($handlerClass);
        if (!$handler instanceof CopilotIntentHandler) {
            Log::error("RuleBasedCopilotEngine: Handler {$handlerClass} does not implement CopilotIntentHandler");
            return 'Terjadi kesalahan: handler tidak valid.';
        }

        try {
            return $handler->handle();
        } catch (\Exception $e) {
            Log::error("RuleBasedCopilotEngine: Handler {$handlerClass} error: " . $e->getMessage());
            return 'Terjadi kesalahan saat memproses data. Silakan coba lagi.';
        }
    }

    private function logUnanswered(string $query, $userId = null): void
    {
        try {
            \App\Models\AiUnansweredQuery::create([
                'query_text' => $query,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('RuleBasedCopilotEngine: Failed to log unanswered query: ' . $e->getMessage());
        }
    }
}
