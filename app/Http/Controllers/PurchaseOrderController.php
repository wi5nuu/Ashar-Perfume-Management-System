<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage_inventory');

        $query = PurchaseOrder::with(['supplier', 'user', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $user = auth()->user();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $orders = $query->latest('order_date')->paginate(15);

        return view('purchase-orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        Gate::authorize('manage_inventory');

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'purchase_price', 'size', 'unit']);
        $supplierId = $request->get('supplier_id');
        $supplierPrices = [];

        if ($supplierId) {
            $supplierPrices = SupplierPrice::where('supplier_id', $supplierId)
                ->pluck('unit_cost', 'product_id')
                ->toArray();
        }

        return view('purchase-orders.create', compact('suppliers', 'products', 'supplierId', 'supplierPrices'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_inventory');

        $validated = $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'expected_date'  => 'nullable|date|after_or_equal:today',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ]);

        try {
            $po = DB::transaction(function () use ($validated) {
                $user = auth()->user();
                $branchId = $user->branch_id;

                $po = PurchaseOrder::create([
                    'po_number'     => PurchaseOrder::generatePoNumber(),
                    'supplier_id'   => $validated['supplier_id'],
                    'branch_id'     => $branchId,
                    'user_id'       => $user->id,
                    'status'        => 'draft',
                    'order_date'    => Carbon::today(),
                    'expected_date' => $validated['expected_date'] ?? null,
                    'notes'         => $validated['notes'] ?? null,
                ]);

                $subtotal = 0;
                foreach ($validated['items'] as $item) {
                    $lineTotal = $item['quantity'] * $item['unit_cost'];
                    $subtotal += $lineTotal;

                    $po->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'unit_cost'  => $item['unit_cost'],
                        'subtotal'   => $lineTotal,
                    ]);

                    // Update supplier price list
                    SupplierPrice::where([
                            'supplier_id' => $validated['supplier_id'],
                            'product_id' => $item['product_id'],
                        ])->lockForUpdate()->first();
                    SupplierPrice::updateOrCreate(
                        ['supplier_id' => $validated['supplier_id'], 'product_id' => $item['product_id']],
                        ['unit_cost' => $item['unit_cost'], 'last_quoted_at' => now()]
                    );
                }

                $po->update(['subtotal' => $subtotal, 'total_amount' => $subtotal]);

                return $po;
            });

            Log::info('Purchase Order created', [
                'po_id'     => $po->id,
                'po_number' => $po->po_number,
                'user_id'   => auth()->id(),
            ]);

            return redirect()->route('purchase-orders.show', $po)
                ->with('success', 'Purchase Order berhasil dibuat: ' . $po->po_number);
        } catch (\Exception $e) {
            Log::error('PO creation failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return back()->withInput()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage_inventory');

        $purchaseOrder->load(['supplier', 'user', 'branch', 'items.product']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Mark PO as sent to supplier.
     */
    public function send(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage_inventory');

        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO draft yang bisa dikirim.');
        }

        $purchaseOrder->update(['status' => 'sent']);
        return back()->with('success', 'PO telah dikirim ke supplier.');
    }

    /**
     * Show receive form for a sent PO.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        return redirect()->route('purchase-orders.index');
    }

    public function showReceive(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage_inventory');

        if (!in_array($purchaseOrder->status, ['sent', 'partial'])) {
            return back()->with('error', 'PO harus berstatus sent atau partial untuk menerima barang.');
        }

        $purchaseOrder->load(['items.product']);

        return view('purchase-orders.receive', compact('purchaseOrder'));
    }

    /**
     * Receive goods — atomic stock increment with audit trail.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage_inventory');

        if (!in_array($purchaseOrder->status, ['sent', 'partial'])) {
            return back()->with('error', 'PO harus berstatus sent atau partial.');
        }

        $validated = $request->validate([
            'received' => 'required|array',
            'received.*' => 'integer|min:0',
        ]);

        $user = auth()->user();
        $branchId = $purchaseOrder->branch_id ?? $user->branch_id;

        try {
            DB::transaction(function () use ($purchaseOrder, $validated, $branchId, $user) {
                foreach ($purchaseOrder->items as $item) {
                    $receivedQty = (int) ($validated['received'][$item->id] ?? 0);
                    if ($receivedQty <= 0) continue;

                    $newReceived = min($item->received_quantity + $receivedQty, $item->quantity);
                    $actualReceived = $newReceived - $item->received_quantity;

                    if ($actualReceived <= 0) continue;

                    // Update PO item received quantity
                    $item->update(['received_quantity' => $newReceived]);

                    // Atomic stock increment with pessimistic lock
                    $query = Inventory::where('product_id', $item->product_id);
                    if ($branchId) $query->where('branch_id', $branchId);
                    $inventory = $query->lockForUpdate()->first();

                    if ($inventory) {
                        $oldStock = (int) $inventory->current_stock;
                        $newStock = $oldStock + $actualReceived;

                        $inventory->update([
                            'current_stock' => $newStock,
                            'cost_per_unit' => $item->unit_cost,
                        ]);

                        InventoryMovement::record(
                            productId:   $item->product_id,
                            branchId:    $branchId,
                            type:        'purchase',
                            quantity:    $actualReceived,
                            stockBefore: $oldStock,
                            stockAfter:  $newStock,
                            refType:     'purchase_order',
                            refId:       $purchaseOrder->id,
                            notes:       'PO: ' . $purchaseOrder->po_number,
                            userId:      $user->id,
                        );
                    } else {
                        // Create new inventory record
                        $inventory = Inventory::create([
                            'product_id'    => $item->product_id,
                            'branch_id'     => $branchId,
                            'current_stock' => $actualReceived,
                            'minimum_stock' => 10,
                            'cost_per_unit' => $item->unit_cost,
                        ]);

                        InventoryMovement::record(
                            productId:   $item->product_id,
                            branchId:    $branchId,
                            type:        'purchase',
                            quantity:    $actualReceived,
                            stockBefore: 0,
                            stockAfter:  $actualReceived,
                            refType:     'purchase_order',
                            refId:       $purchaseOrder->id,
                            notes:       'PO: ' . $purchaseOrder->po_number . ' (new inventory)',
                            userId:      $user->id,
                        );
                    }
                }

                // Update PO status
                $purchaseOrder->refresh();
                if ($purchaseOrder->isFullyReceived()) {
                    $purchaseOrder->update([
                        'status'        => 'received',
                        'received_date' => Carbon::today(),
                    ]);
                } else {
                    $purchaseOrder->update(['status' => 'partial']);
                }
            });

            Log::info('PO goods received', [
                'po_id'   => $purchaseOrder->id,
                'user_id' => $user->id,
            ]);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Barang berhasil diterima dan stok diperbarui.');
        } catch (\Exception $e) {
            Log::error('PO receive failed', ['po_id' => $purchaseOrder->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menerima barang: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a purchase order.
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage_inventory');

        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return back()->with('error', 'PO yang sudah diterima atau dibatalkan tidak bisa dibatalkan lagi.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'PO berhasil dibatalkan.');
    }

    /**
     * Get supplier prices for a given supplier (AJAX).
     */
    public function getSupplierPrices(int $supplierId)
    {
        Gate::authorize('manage_inventory');

        $prices = SupplierPrice::with('product')
            ->where('supplier_id', $supplierId)
            ->get()
            ->map(fn($sp) => [
                'product_id'  => $sp->product_id,
                'product_name' => $sp->product->name ?? '-',
                'unit_cost'   => (float) $sp->unit_cost,
                'min_qty'     => $sp->minimum_order_qty,
            ]);

        return response()->json($prices);
    }
}
