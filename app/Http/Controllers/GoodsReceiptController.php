<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GoodsReceiptController extends Controller
{
    public function index()
    {
        Gate::authorize('goods_receipts.view');

        $user = auth()->user();
        $query = GoodsReceipt::with(['product', 'recorder', 'branch'])->latest();

        if (!$user->isOwner()) {
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
        if ($user->isOwner()) {
            $products = Product::where('is_active', true)->orderBy('name')->get();
            $branches = Branch::where('is_active', true)->get();
        } else {
            $products = Product::where('is_active', true)->orderBy('name')->get();
            $branches = Branch::where('id', $user->branch_id)->get();
        }

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
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = auth()->user();
        if (!$user->isOwner() && $user->branch_id != $validated['branch_id']) {
            abort(403, 'Anda hanya dapat mencatat penerimaan untuk cabang sendiri.');
        }

        DB::transaction(function () use ($validated, $user) {
            GoodsReceipt::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'supplier_name' => $validated['supplier_name'],
                'delivery_person' => $validated['delivery_person'],
                'origin' => $validated['origin'],
                'received_date' => $validated['received_date'],
                'unit_cost' => $validated['unit_cost'],
                'notes' => $validated['notes'],
                'recorded_by' => $user->id,
                'branch_id' => $validated['branch_id'],
            ]);

            $inventory = Inventory::where([
                'product_id' => $validated['product_id'],
                'branch_id' => $validated['branch_id'],
            ])->lockForUpdate()->first();

            if (!$inventory) {
                $inventory = Inventory::create([
                    'product_id' => $validated['product_id'],
                    'branch_id' => $validated['branch_id'],
                    'current_stock' => 0,
                    'stock_in' => 0,
                    'stock_out' => 0,
                ]);
            }

            $inventory->increment('current_stock', $validated['quantity']);
            $inventory->increment('stock_in', $validated['quantity']);
        });

        return redirect()->route('goods-receipts.index')
            ->with('success', 'Penerimaan barang berhasil dicatat.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        Gate::authorize('goods_receipts.view');

        $user = auth()->user();
        if (!$user->isOwner() && $goodsReceipt->branch_id !== $user->branch_id) {
            abort(403);
        }

        $goodsReceipt->load(['product', 'recorder', 'branch']);

        return view('goods-receipts.show', compact('goodsReceipt'));
    }
}
