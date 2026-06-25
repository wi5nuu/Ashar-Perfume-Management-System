<?php

namespace App\Services\Security;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosAntiTamperingService
{
    public function validateCartItem(array $item, int $branchId): array
    {
        $product = Product::with(['inventories' => function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])->find($item['product_id']);

        if (!$product) {
            throw new \RuntimeException("Produk #{$item['product_id']} tidak ditemukan.");
        }

        $inventory = $product->inventories->first();
        $dbPrice = (float) ($product->selling_price ?? 0);
        $dbStock = (int) ($inventory->current_stock ?? 0);
        $clientQty = (int) ($item['quantity'] ?? 0);
        $clientPrice = (float) ($item['price'] ?? 0);

        if ($clientQty <= 0) {
            throw new \RuntimeException("Jumlah tidak valid untuk produk {$product->name}.");
        }

        $priceDiff = abs($dbPrice - $clientPrice);
        if ($priceDiff > 100) {
            Log::warning("PRICE TAMPERING DETECTED", [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'db_price' => $dbPrice,
                'client_price' => $clientPrice,
                'user_id' => auth()->id(),
                'branch_id' => $branchId,
            ]);
            throw new \RuntimeException("Harga produk {$product->name} tidak valid. Gunakan harga sistem.");
        }

        if ($dbStock < $clientQty) {
            throw new \RuntimeException("Stok {$product->name} tidak mencukupi. Tersedia: {$dbStock}");
        }

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $clientQty,
            'price' => $dbPrice,
            'subtotal' => $dbPrice * $clientQty,
            'stock_available' => $dbStock,
        ];
    }

    public function validateCart(array $cart, int $branchId): array
    {
        $validated = [];
        $total = 0;

        foreach ($cart as $item) {
            $validItem = $this->validateCartItem($item, $branchId);
            $validated[] = $validItem;
            $total += $validItem['subtotal'];
        }

        return ['items' => $validated, 'total' => $total];
    }

    public function deductStock(int $productId, int $quantity, int $branchId): void
    {
        $affected = DB::statement(
            "UPDATE inventories SET current_stock = GREATEST(0, current_stock - ?)
             WHERE product_id = ? AND branch_id = ? AND current_stock >= ?",
            [$quantity, $productId, $branchId, $quantity]
        );

        if (!$affected) {
            throw new \RuntimeException("Gagal mengurangi stok. Mungkin stok tidak mencukupi.");
        }

        $this->clearStockCache($productId, $branchId);
    }

    public function restoreStock(int $productId, int $quantity, int $branchId): void
    {
        DB::statement(
            "UPDATE inventories SET current_stock = current_stock + ? WHERE product_id = ? AND branch_id = ?",
            [$quantity, $productId, $branchId]
        );

        $this->clearStockCache($productId, $branchId);
    }

    private function clearStockCache(int $productId, int $branchId): void
    {
        Cache::forget("inventory_stock_{$productId}_{$branchId}");
    }

    public function getValidatedTotal(int $transactionId): float
    {
        return (float) DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->where('transaction_details.transaction_id', $transactionId)
            ->select(DB::raw('SUM(transaction_details.price * transaction_details.quantity) as total'))
            ->value('total') ?? 0;
    }
}
