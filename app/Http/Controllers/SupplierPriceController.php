<?php

namespace App\Http\Controllers;

use App\Models\SupplierPrice;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SupplierPriceController extends Controller
{
    /**
     * List all supplier prices with filtering.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_inventory');

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $selectedSupplier = $request->get('supplier_id');

        $query = SupplierPrice::with(['supplier', 'product']);

        if ($selectedSupplier) {
            $query->where('supplier_id', $selectedSupplier);
        }

        $prices = $query->orderBy('product_id')->paginate(25);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'size', 'unit']);

        return view('supplier-prices.index', compact('prices', 'suppliers', 'selectedSupplier', 'products'));
    }

    /**
     * Store or update a supplier price entry.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_inventory');

        $validated = $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'product_id'        => 'required|exists:products,id',
            'unit_cost'         => 'required|numeric|min:0',
            'minimum_order_qty' => 'nullable|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                SupplierPrice::where([
                        'supplier_id' => $validated['supplier_id'],
                        'product_id'  => $validated['product_id'],
                    ])->lockForUpdate()->first();
                SupplierPrice::updateOrCreate(
                    [
                        'supplier_id' => $validated['supplier_id'],
                        'product_id'  => $validated['product_id'],
                    ],
                    [
                        'unit_cost'        => $validated['unit_cost'],
                        'minimum_order_qty' => $validated['minimum_order_qty'] ?? null,
                        'last_quoted_at'   => now(),
                    ]
                );
            });

            Log::info('Supplier price saved', [
                'supplier_id' => $validated['supplier_id'],
                'product_id'  => $validated['product_id'],
                'user_id'     => auth()->id(),
            ]);

            return back()->with('success', 'Harga supplier berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan harga: ' . $e->getMessage());
        }
    }

    /**
     * Delete a supplier price entry.
     */
    public function destroy(SupplierPrice $supplierPrice)
    {
        Gate::authorize('manage_inventory');

        try {
            $supplierPrice->delete();
            return back()->with('success', 'Harga supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus harga: ' . $e->getMessage());
        }
    }
}
