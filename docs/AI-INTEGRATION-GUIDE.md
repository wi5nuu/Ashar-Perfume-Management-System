# AI Integration Guide - Yunwu.ai API
## APMS - Ashar Grosir Perfume Management System

---

## Quick Start - Terminal Commands

### 1. Test API Connection (Python)

```bash
# Jalankan test script yang sudah dibuat
python test_yunwu_api.py
```

### 2. Cara Memanggil API dari Terminal (cURL)

```bash
# Basic request
curl https://yunwu.ai/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_YUNWU_API_KEY_HERE" \
  -d '{
    "model": "gpt-4o",
    "messages": [
      {"role": "user", "content": "Hello, siapa kamu?"}
    ]
  }'

# Generate product description
curl https://yunwu.ai/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_YUNWU_API_KEY_HERE" \
  -d '{
    "model": "gpt-4o",
    "messages": [
      {"role": "system", "content": "Kamu adalah copywriter expert untuk produk parfum"},
      {"role": "user", "content": "Buatkan deskripsi untuk Dior Sauvage EDP 100ml"}
    ],
    "temperature": 0.8,
    "max_tokens": 200
  }'
```

### 3. Cara Memanggil dari PowerShell

```powershell
# Menggunakan Invoke-RestMethod
$headers = @{
    "Content-Type" = "application/json"
    "Authorization" = "Bearer YOUR_YUNWU_API_KEY_HERE"
}

$body = @{
    model = "gpt-4o"
    messages = @(
        @{
            role = "user"
            content = "Berikan 5 tips untuk meningkatkan penjualan parfum"
        }
    )
} | ConvertTo-Json

Invoke-RestMethod -Uri "https://yunwu.ai/v1/chat/completions" -Method Post -Headers $headers -Body $body
```

---

## Integrasi ke Laravel Backend APMS

### Step 1: Setup Environment Variables

Edit file .env:

```env
# Yunwu.ai API Configuration
YUNWU_API_KEY=YOUR_YUNWU_API_KEY_HERE
YUNWU_BASE_URL=https://yunwu.ai/v1
YUNWU_MODEL=gpt-4o
YUNWU_TIMEOUT=30
```

### Step 2: Tambahkan Config Service

Edit config/services.php:

```php
<?php

return [
    // ... existing services

    'yunwu' => [
        'api_key' => env('YUNWU_API_KEY'),
        'base_url' => env('YUNWU_BASE_URL', 'https://yunwu.ai/v1'),
        'model' => env('YUNWU_MODEL', 'gpt-4o'),
        'timeout' => env('YUNWU_TIMEOUT', 30),
    ],
];
```

### Step 3: Install HTTP Client (jika belum)

```bash
composer require guzzlehttp/guzzle
```

### Step 4: Buat AI Service Class

Buat file pp/Services/AiService.php:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.yunwu.api_key');
        $this->baseUrl = config('services.yunwu.base_url');
        $this->model = config('services.yunwu.model');
        $this->timeout = config('services.yunwu.timeout');
    }

    /**
     * Send chat completion request
     */
    public function chat(array $messages, float $temperature = 0.7, int $maxTokens = 500): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('AI API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new \Exception('AI API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('AI Service error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate product description
     */
    public function generateProductDescription(string $productName, array $attributes = []): string
    {
        $attributesText = empty($attributes) ? '' : '\n\nAtribut produk: ' . implode(', ', $attributes);

        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah expert copywriter untuk produk parfum. Buatlah deskripsi yang menarik, persuasif, dan SEO-friendly dalam bahasa Indonesia.'
            ],
            [
                'role' => 'user',
                'content' => "Buatkan deskripsi produk untuk: {$productName}{$attributesText}\n\nDeskripsi harus mencakup kesan, keunggulan, dan cocok untuk siapa."
            ]
        ];

        return $this->chat($messages, 0.8, 300);
    }

    /**
     * Customer service assistant
     */
    public function customerServiceResponse(string $question, array $context = []): string
    {
        $contextText = empty($context) ? '' : '\n\nKonteks: ' . json_encode($context, JSON_UNESCAPED_UNICODE);

        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah customer service APMS (Ashar Grosir Perfume Management System). Bantu customer dengan ramah, profesional, dan informatif.{$contextText}'
            ],
            [
                'role' => 'user',
                'content' => $question
            ]
        ];

        return $this->chat($messages, 0.7, 400);
    }

    /**
     * Analyze sales data
     */
    public function analyzeSalesData(array $salesData): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah data analyst expert. Analisis data penjualan dan berikan insight yang actionable.'
            ],
            [
                'role' => 'user',
                'content' => "Analisis data penjualan berikut dan berikan insight:\n\n" . json_encode($salesData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            ]
        ];

        return $this->chat($messages, 0.5, 500);
    }

    /**
     * Generate marketing content
     */
    public function generateMarketingContent(string $type, array $data): string
    {
        $prompts = [
            'email' => 'Buatkan email marketing untuk promosi produk parfum',
            'whatsapp' => 'Buatkan pesan WhatsApp singkat untuk broadcast pelanggan',
            'instagram' => 'Buatkan caption Instagram yang engaging untuk produk',
            'promo' => 'Buatkan teks promosi yang menarik'
        ];

        $prompt = $prompts[$type] ?? 'Buatkan konten marketing';

        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah marketing expert untuk produk parfum. Buat konten yang menarik dan persuasif.'
            ],
            [
                'role' => 'user',
                'content' => "{$prompt}\n\nData: " . json_encode($data, JSON_UNESCAPED_UNICODE)
            ]
        ];

        return $this->chat($messages, 0.8, 300);
    }
}
```

### Step 5: Buat AI Controller

Buat file pp/Http/Controllers/Api/V1/AiController.php:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AiService $aiService
    ) {}

    /**
     * Generate product description
     * POST /api/v1/ai/product-description
     */
    public function generateProductDescription(Request $request): JsonResponse
    {
        $request->validate([
            'product_name' => 'required|string',
            'attributes' => 'nullable|array',
        ]);

        try {
            $description = $this->aiService->generateProductDescription(
                $request->product_name,
                $request->attributes ?? []
            );

            return $this->successResponse([
                'description' => $description
            ], 'Product description generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Customer service chatbot
     * POST /api/v1/ai/customer-service
     */
    public function customerService(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string',
            'context' => 'nullable|array',
        ]);

        try {
            $response = $this->aiService->customerServiceResponse(
                $request->question,
                $request->context ?? []
            );

            return $this->successResponse([
                'response' => $response
            ], 'Response generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Analyze sales data
     * POST /api/v1/ai/analyze-sales
     */
    public function analyzeSales(Request $request): JsonResponse
    {
        $request->validate([
            'sales_data' => 'required|array',
        ]);

        try {
            $analysis = $this->aiService->analyzeSalesData($request->sales_data);

            return $this->successResponse([
                'analysis' => $analysis
            ], 'Sales analysis generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Generate marketing content
     * POST /api/v1/ai/marketing-content
     */
    public function generateMarketingContent(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:email,whatsapp,instagram,promo',
            'data' => 'required|array',
        ]);

        try {
            $content = $this->aiService->generateMarketingContent(
                $request->type,
                $request->data
            );

            return $this->successResponse([
                'content' => $content
            ], 'Marketing content generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Custom AI chat
     * POST /api/v1/ai/chat
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'messages' => 'required|array',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:1|max:2000',
        ]);

        try {
            $response = $this->aiService->chat(
                $request->messages,
                $request->temperature ?? 0.7,
                $request->max_tokens ?? 500
            );

            return $this->successResponse([
                'response' => $response
            ], 'AI response generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
```

### Step 6: Tambahkan Routes

Edit outes/api.php:

```php
<?php

use App\Http\Controllers\Api\V1\AiController;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    
    // AI Features
    Route::prefix('ai')->group(function () {
        Route::post('/product-description', [AiController::class, 'generateProductDescription']);
        Route::post('/customer-service', [AiController::class, 'customerService']);
        Route::post('/analyze-sales', [AiController::class, 'analyzeSales']);
        Route::post('/marketing-content', [AiController::class, 'generateMarketingContent']);
        Route::post('/chat', [AiController::class, 'chat']);
    });
    
});
```

---

## Cara Memanggil API dari Terminal (Laravel)

### 1. Via Artisan Tinker

```bash
# Masuk ke Laravel Tinker
php artisan tinker

# Test AI Service
$ai = app(\App\Services\AiService::class);

# Generate product description
$description = $ai->generateProductDescription('Dior Sauvage EDP 100ml');
echo $description;

# Customer service
$response = $ai->customerServiceResponse('Bagaimana cara order?');
echo $response;
```

### 2. Via cURL ke Laravel API

```bash
# Login dulu untuk mendapat token
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@apms.com",
    "password": "password"
  }'

# Simpan token yang didapat
TOKEN="your_token_here"

# Generate product description
curl -X POST http://localhost:8000/api/v1/ai/product-description \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_name": "Chanel No. 5 EDP 100ml",
    "attributes": ["floral", "elegant", "timeless"]
  }'

# Customer service chatbot
curl -X POST http://localhost:8000/api/v1/ai/customer-service \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "Bagaimana cara order dalam jumlah besar?",
    "context": {"customer_type": "grosir"}
  }'

# Analyze sales
curl -X POST http://localhost:8000/api/v1/ai/analyze-sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sales_data": {
      "total_sales": 5000000,
      "total_orders": 50,
      "top_products": ["Product A", "Product B"]
    }
  }'
```

### 3. Via Postman

1. Import collection dengan endpoints di atas
2. Set environment variable untuk BASE_URL dan TOKEN
3. Test semua AI endpoints

---

## Use Cases APMS dengan AI

### 1. Auto-Generate Product Descriptions

```php
// Saat menambah produk baru
$product = Product::create([...]);

// Generate description otomatis
$ai = app(AiService::class);
$description = $ai->generateProductDescription(
    $product->name,
    [$product->brand, $product->category, $product->size]
);

$product->update(['description' => $description]);
```

### 2. Customer Service Chatbot di WhatsApp

```php
// Webhook dari WhatsApp
public function handleWhatsApp(Request $request)
{
    $question = $request->message;
    $ai = app(AiService::class);
    
    $response = $ai->customerServiceResponse($question);
    
    // Send via WhatsApp API
    WhatsApp::sendMessage($request->phone, $response);
}
```

### 3. Sales Report Analysis

```php
// Generate monthly report dengan AI insights
$salesData = Order::whereMonth('created_at', now()->month)->get();

$ai = app(AiService::class);
$analysis = $ai->analyzeSalesData([
    'total_revenue' => $salesData->sum('total'),
    'total_orders' => $salesData->count(),
    'top_products' => $salesData->pluck('items')->flatten()->groupBy('product_id'),
]);

// Kirim email report dengan AI insights
Mail::to('manager@apms.com')->send(new MonthlyReport($analysis));
```

### 4. Marketing Content Generator

```php
// Generate promo content
$ai = app(AiService::class);

$emailContent = $ai->generateMarketingContent('email', [
    'promo' => 'Diskon 20% untuk semua produk',
    'valid_until' => '31 July 2026'
]);

$whatsappContent = $ai->generateMarketingContent('whatsapp', [
    'product' => 'Dior Sauvage',
    'price' => 'Rp 1.500.000',
    'stock' => 'Terbatas'
]);
```

---

## Testing Commands

```bash
# Test Python script
python test_yunwu_api.py

# Test Laravel service
php artisan tinker
>>> $ai = app(\App\Services\AiService::class);
>>> $ai->generateProductDescription('Test Product');

# Test API endpoint
curl -X POST http://localhost:8000/api/v1/ai/product-description \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_name": "Test Product"}'
```

---

## Troubleshooting

### Error 429 (Rate Limit)
- Tunggu beberapa detik sebelum request berikutnya
- Gunakan model gpt-4o yang lebih stabil
- Implementasikan retry mechanism dengan exponential backoff

### Error 503 (Service Unavailable)
- Service sedang down, coba lagi nanti
- Gunakan model alternatif: claude-3-5-sonnet-20241022

### Error 401 (Unauthorized)
- Periksa API key Anda
- Pastikan tidak ada spasi atau karakter tambahan di API key

### Encoding Error di Windows
- Script Python sudah di-fix dengan UTF-8 encoding
- Jika masih error, jalankan: chcp 65001 di Command Prompt

---

## Security Best Practices

1. **Jangan commit API key ke Git**
   - Tambahkan .env ke .gitignore
   - Gunakan environment variables

2. **Implementasikan rate limiting**
   - Batasi jumlah request per user
   - Gunakan cache untuk response yang sama

3. **Monitor usage**
   - Track berapa token yang digunakan
   - Set alert jika usage terlalu tinggi

4. **Handle errors gracefully**
   - Selalu gunakan try-catch
   - Berikan fallback response jika AI gagal

---

## Next Steps

1. Implementasikan AI Service ke Laravel backend
2. Tambahkan AI features ke mobile app
3. Setup monitoring untuk track AI usage
4. Buat admin panel untuk manage AI configurations
5. Implementasikan caching untuk optimize cost

---

**Dokumentasi dibuat:** July 18, 2026
**API Provider:** yunwu.ai
**Model:** gpt-4o, claude-3-5-sonnet
