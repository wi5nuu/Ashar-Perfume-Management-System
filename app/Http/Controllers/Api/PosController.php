<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Security\PosAntiTamperingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PosController extends Controller
{
    public function __construct(protected PosAntiTamperingService $antiTamper) {}

    public function validateCart(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:999',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $branchId = auth()->user()->branch_id;
        if (!$branchId) {
            return response()->json(['error' => 'Branch not assigned'], 403);
        }

        try {
            $result = $this->antiTamper->validateCart($request->items, $branchId);
            return response()->json([
                'valid' => true,
                'items' => $result['items'],
                'total' => $result['total'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['valid' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function calculateChange(Request $request): JsonResponse
    {
        $request->validate([
            'total' => 'required|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        $total = (float) $request->total;
        $payment = (float) $request->payment_amount;

        if ($payment < $total) {
            return response()->json([
                'error' => 'Pembayaran kurang dari total belanja',
                'short' => $total - $payment,
            ], 422);
        }

        return response()->json([
            'change' => $payment - $total,
            'change_formatted' => 'Rp ' . number_format($payment - $total, 0, ',', '.'),
        ]);
    }

    public function checkStock(int $product): JsonResponse
    {
        $branchId = auth()->user()->branch_id;
        if (!$branchId) {
            return response()->json(['error' => 'Branch not assigned'], 403);
        }

        $stock = Cache::remember("inventory_stock_{$product}_{$branchId}", 60, function () use ($product, $branchId) {
            return \App\Models\Inventory::where('product_id', $product)
                ->where('branch_id', $branchId)
                ->value('current_stock') ?? 0;
        });

        $productData = Product::select('id', 'name', 'selling_price')->find($product);

        return response()->json([
            'product_id' => $product,
            'name' => $productData->name ?? 'Unknown',
            'stock' => (int) $stock,
            'price' => (float) ($productData->selling_price ?? 0),
            'price_formatted' => 'Rp ' . number_format($productData->selling_price ?? 0, 0, ',', '.'),
        ]);
    }
}
