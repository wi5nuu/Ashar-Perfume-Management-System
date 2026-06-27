<?php

namespace App\Http\Controllers;

use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('manage_inventory');
        $user = auth()->user();
        $query = StockAudit::with(['user', 'items']);
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }
        $audits = $query->latest()->paginate(20);
        return view('stock_audits.index', compact('audits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('stock_audits.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_inventory');
        $request->validate([
            'notes' => 'nullable|string',
            'limit' => 'required|integer|min:1|max:50',
        ]);

        return DB::transaction(function () use ($request) {
            $branchId = auth()->user()->branch_id;

            $audit = StockAudit::create([
                'user_id' => auth()->id(),
                'branch_id' => $branchId,
                'audit_date' => now(),
                'status' => 'draft',
                'notes' => $request->notes,
            ]);

            $query = Inventory::query();
            if ($branchId) $query->where('branch_id', $branchId);
            $inventories = $query->inRandomOrder()
                ->take($request->limit)
                ->with('product')
                ->get();

            foreach ($inventories as $inventoryRecord) {
                StockAuditItem::create([
                    'stock_audit_id' => $audit->id,
                    'product_id'     => $inventoryRecord->product_id,
                    'system_stock'   => $inventoryRecord->current_stock,
                ]);
            }

            return redirect()->route('stock_audits.show', $audit->id)
                ->with('success', 'Audit stok acak berhasil dibuat! Silakan hitung fisik barang.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAudit $stockAudit)
    {
        Gate::authorize('manage_inventory');
        $stockAudit->load(['items.product', 'user']);
        return view('stock_audits.show', compact('stockAudit'));
    }

    /**
     * Update the items in the audit.
     */
    public function edit(StockAudit $stockAudit)
    {
        return redirect()->route('stock_audits.show', $stockAudit);
    }

    public function update(Request $request, StockAudit $stockAudit)
    {
        return redirect()->route('stock_audits.show', $stockAudit);
    }

    public function updateItems(Request $request, StockAudit $stockAudit)
    {
        Gate::authorize('manage_inventory');

        if ($stockAudit->status === 'completed') {
            return back()->with('error', 'Audit ini sudah selesai dan tidak bisa diubah.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.physical_stock' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $itemIds = array_keys($request->items);
        $existingItems = StockAuditItem::whereIn('id', $itemIds)->get()->keyBy('id');
        foreach ($request->items as $itemId => $itemData) {
            $item = $existingItems->get($itemId);
            if (!$item) continue;
            $item->update([
                'physical_stock' => $itemData['physical_stock'],
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        if ($request->has('complete')) {
            $stockAudit->load('items');
            
            DB::transaction(function () use ($stockAudit) {
                foreach ($stockAudit->items as $item) {
                    if ($item->physical_stock !== null) {
                        $query = Inventory::where('product_id', $item->product_id);
                        $invBranchId = $stockAudit->branch_id ?? auth()->user()->branch_id;
                        if ($invBranchId) $query->where('branch_id', $invBranchId);
                        $inventory = $query->lockForUpdate()->first();
                        if ($inventory) {
                            $inventory->update(['current_stock' => $item->physical_stock]);
                        }
                    }
                }
                $stockAudit->update(['status' => 'completed']);
            });

            return redirect()->route('stock_audits.index')
                ->with('success', 'Audit stok telah diselesaikan dan inventory telah diperbarui!');
        }

        return back()->with('success', 'Jumlah fisik berhasil disimpan sementara.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAudit $stockAudit)
    {
        Gate::authorize('manage_inventory');
        $stockAudit->delete();
        return redirect()->route('stock_audits.index')->with('success', 'Audit berhasil dihapus.');
    }
}
