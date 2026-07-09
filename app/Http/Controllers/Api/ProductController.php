<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|max:100']);

        $query = $request->input('q');
        $branchId = auth()->user()->branch_id;

        $products = Product::select('id', 'name', 'selling_price', 'sku', 'image')
            ->with(['inventories' => function ($q) use ($branchId) {
                if ($branchId) $q->where('branch_id', $branchId);
                $q->select('id', 'product_id', 'current_stock');
            }])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', $query);
            })
            ->where('is_active', true)
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $product->selling_price,
                    'price_formatted' => 'Rp ' . number_format($product->selling_price, 0, ',', '.'),
                    'stock' => (int) ($product->inventories->first()?->current_stock ?? 0),
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                ];
            });

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $branchId = auth()->user()->branch_id;

        $product->load(['inventories' => function ($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }, 'category']);

        return response()->json($product->only([
            'id', 'name', 'barcode', 'selling_price', 'wholesale_price',
            'image', 'is_refill', 'refill_price_per_ml', 'brand', 'size',
            'unit', 'description', 'is_active', 'category', 'inventories',
        ]));
    }
}
