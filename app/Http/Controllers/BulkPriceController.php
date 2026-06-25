<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class BulkPriceController extends Controller
{
    /**
     * Show bulk price update form with preview.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_products');

        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'size', 'unit', 'selling_price', 'wholesale_price', 'purchase_price']);
        $history = PriceHistory::with(['product', 'user'])->latest()->paginate(20);

        return view('products.bulk-price', compact('products', 'history'));
    }

    /**
     * Apply bulk price change.
     */
    public function update(Request $request)
    {
        Gate::authorize('manage_products');

        $validated = $request->validate([
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'price_type'    => 'required|in:selling_price,wholesale_price,purchase_price',
            'change_mode'   => 'required|in:percentage,fixed',
            'change_value'  => 'required|numeric',
        ]);

        $priceType  = $validated['price_type'];
        $changeMode = $validated['change_mode'];
        $changeVal  = (float) $validated['change_value'];

        try {
            $count = DB::transaction(function () use ($validated, $priceType, $changeMode, $changeVal) {
                $count = 0;
                $user = auth()->user();

                foreach ($validated['product_ids'] as $productId) {
                    $product = Product::findOrFail($productId);
                    $oldPrice = (float) $product->{$priceType};

                    $newPrice = match ($changeMode) {
                        'percentage' => $oldPrice + ($oldPrice * $changeVal / 100),
                        'fixed'      => $changeVal,
                    };

                    $newPrice = max(0, round($newPrice, 2));

                    $product->update([$priceType => $newPrice]);

                    PriceHistory::create([
                        'product_id'  => $productId,
                        'price_type'  => $priceType,
                        'old_price'   => $oldPrice,
                        'new_price'   => $newPrice,
                        'change_type' => $changeMode,
                        'user_id'     => $user->id,
                    ]);

                    $count++;
                }

                return $count;
            });

            Log::info('Bulk price update', [
                'count'   => $count,
                'type'    => $priceType,
                'mode'    => $changeMode,
                'value'   => $changeVal,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', "Berhasil mengupdate harga {$count} produk.");
        } catch (\Exception $e) {
            Log::error('Bulk price update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal update harga: ' . $e->getMessage());
        }
    }
}
