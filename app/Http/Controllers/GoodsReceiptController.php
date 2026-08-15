<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GoodsReceipt;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\Accounting\AutoPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GoodsReceiptController extends Controller
{
    public function index()
    {
        Gate::authorize('goods_receipts.view');

        $user = auth()->user();
        $query = GoodsReceipt::with(['product', 'recorder', 'branch'])
            ->latest('received_date')
            ->latest('id');

        if (! $user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $receipts = $query->paginate(20);

        $stats = [];
        if ($user->isOwner()) {
            $stats['total_quantity'] = GoodsReceipt::sum('quantity');
            $stats['total_cost'] = GoodsReceipt::sum('total_cost');
            $stats['this_month_quantity'] = GoodsReceipt::whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)->sum('quantity');
            $stats['this_month_cost'] = GoodsReceipt::whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)->sum('total_cost');
        } else {
            $stats['total_quantity'] = GoodsReceipt::where('branch_id', $user->branch_id)->sum('quantity');
            $stats['total_cost'] = GoodsReceipt::where('branch_id', $user->branch_id)->sum('total_cost');
            $stats['this_month_quantity'] = GoodsReceipt::where('branch_id', $user->branch_id)
                ->whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)->sum('quantity');
            $stats['this_month_cost'] = GoodsReceipt::where('branch_id', $user->branch_id)
                ->whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)->sum('total_cost');
        }

        return view('goods-receipts.index', compact('receipts', 'stats'));
    }

    public function create()
    {
        Gate::authorize('goods_receipts.create');

        $user = auth()->user();

        // Eager load inventory pusat (branch_id = null) untuk tampilkan stok saat ini di form
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->with(['inventories' => function ($q) {
                $q->whereNull('branch_id');
            }])
            ->get()
            ->each(function ($p) {
                // Buat accessor sementara: $product->centralInventory
                $p->centralInventory = $p->inventories->first();
            });

        $branches = $user->isOwner()
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('goods-receipts.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        Gate::authorize('goods_receipts.create');

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'supplier_name' => 'nullable|string|max:255',
            'delivery_person' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'received_date' => 'required|date',
            'expiration_date' => 'nullable|date|after:received_date',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = auth()->user();
        if (! $user->isOwner() && ! empty($validated['branch_id']) && $user->branch_id != $validated['branch_id']) {
            abort(403, 'Anda hanya dapat mencatat penerimaan untuk cabang sendiri.');
        }

        DB::transaction(function () use ($validated, $user) {
            $receipt = GoodsReceipt::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'supplier_name' => $validated['supplier_name'] ?? null,
                'delivery_person' => $validated['delivery_person'] ?? null,
                'origin' => $validated['origin'] ?? null,
                'received_date' => $validated['received_date'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'unit_cost' => $validated['unit_cost'],
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => $user->id,
                'branch_id' => $validated['branch_id'] ?? null,
            ]);

            // Enterprise double-entry posting (fail-safe, idempotent).
            app(AutoPostingService::class)->postGoodsReceipt($receipt);

            // Cari inventory: utamakan branch_id=null (stok terpusat)
            $inventory = Inventory::where('product_id', $validated['product_id'])
                ->whereNull('branch_id')
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                $inventory = Inventory::create([
                    'product_id' => $validated['product_id'],
                    'branch_id' => null,
                    'current_stock' => 0,
                    'stock_in' => 0,
                    'stock_out' => 0,
                    'minimum_stock' => 10,
                ]);
            }

            $product = Product::find($validated['product_id']);
            if (! $product) {
                return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
            }

            $stockBefore = (int) $inventory->current_stock;
            $bulkBefore = (int) ($inventory->bulk_stock_ml ?? 0);

            if ($product->is_refill) {
                // Bibit parfum: quantity dalam ml → langsung tambah current_stock (ml bibit)
                $inventory->increment('current_stock', $validated['quantity']);
                $inventory->increment('stock_in', $validated['quantity']);
            } else {
                // Botol/packaging: quantity dalam pcs → tambah bulk_stock_ml saja
                // current_stock (ml bibit) tidak berubah
                $mlPerUnit = (float) preg_replace('/[^0-9.]/', '', $product->size ?? '30');
                $totalMl = $mlPerUnit * $validated['quantity'];
                $inventory->increment('bulk_stock_ml', $totalMl);
                $inventory->increment('stock_in', $validated['quantity']);
            }
            $inventory->refresh();

            $stockAfter = (int) $inventory->current_stock;
            $bulkAfter = (int) ($inventory->bulk_stock_ml ?? 0);

            // Update tanggal kadaluarsa & biaya per unit jika ada
            $updateData = ['cost_per_unit' => $validated['unit_cost']];
            if (! empty($validated['expiration_date'])) {
                $updateData['expiration_date'] = $validated['expiration_date'];
                $updateData['date_received'] = $validated['received_date'];
            }
            $inventory->update($updateData);

            // Catat pergerakan stok
            $movementQty = $product->is_refill
                ? $validated['quantity']  // bibit: ml
                : $bulkAfter - $bulkBefore;  // botol: delta bulk_stock_ml dalam ml

            InventoryMovement::record(
                productId: $validated['product_id'],
                branchId: null,
                type: 'purchase',
                quantity: $movementQty,
                stockBefore: $product->is_refill ? $stockBefore : $bulkBefore,
                stockAfter: $product->is_refill ? $stockAfter : $bulkAfter,
                refType: 'goods_receipt',
                notes: 'Penerimaan barang dari '.($validated['supplier_name'] ?? 'supplier')
                            .' | '.($product->is_refill ? $validated['quantity'].' ml bibit' : $validated['quantity'].' botol ('.$movementQty.' ml)'),
                userId: $user->id,
            );
        });

        return redirect()->route('goods-receipts.index')
            ->with('success', 'Penerimaan barang berhasil dicatat. Stok telah diperbarui.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        Gate::authorize('goods_receipts.view');

        $user = auth()->user();
        if (! $user->isOwner() && $goodsReceipt->branch_id !== $user->branch_id) {
            abort(403);
        }

        $goodsReceipt->load(['product.inventories', 'recorder', 'branch']);

        return view('goods-receipts.show', compact('goodsReceipt'));
    }
}
