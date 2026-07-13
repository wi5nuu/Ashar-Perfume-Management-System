<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Http\Requests\AdjustInventoryRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Events\LowStockAlert;

class InventoryController extends Controller
{
    /**
     * Display inventory overview with branch scoping.
     */
    public function index()
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        $warehouseId = request()->get('warehouse_id');

        $query = Inventory::with(['product', 'branch', 'warehouse']);
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        $inventories = $query->latest()->paginate(20)->appends(request()->query());

        $lowStockQuery = Inventory::with('product')
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->where('current_stock', '>', 0);

        $outOfStockQuery = Inventory::with('product')
            ->where('current_stock', '<=', 0);

        $now             = Carbon::now()->startOfDay();
        $thirtyDaysAhead = Carbon::now()->addDays(30)->endOfDay();

        $expiringSoonQuery = Inventory::with('product')
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$now, $thirtyDaysAhead]);

        if (!$user->isOwner()) {
            $lowStockQuery->where('branch_id', $user->branch_id);
            $outOfStockQuery->where('branch_id', $user->branch_id);
            $expiringSoonQuery->where('branch_id', $user->branch_id);
        }
        if ($warehouseId) {
            $lowStockQuery->where('warehouse_id', $warehouseId);
            $outOfStockQuery->where('warehouse_id', $warehouseId);
            $expiringSoonQuery->where('warehouse_id', $warehouseId);
        }

        $lowStock     = $lowStockQuery->get();
        $outOfStock   = $outOfStockQuery->get();
        $expiringSoon = $expiringSoonQuery->get();

        $totalInventoryValue = $inventories->sum(function ($item) {
            return $item->current_stock * ($item->cost_per_unit ?? 0);
        });

        $products = Product::where('is_active', true)->get();

        // Warehouses for filter dropdown
        $warehouses = \App\Models\Warehouse::where('is_active', true)
            ->when(!$user->isOwner(), fn($q) => $q->where('branch_id', $user->branch_id))
            ->get();

        return view('inventory.index', compact(
            'inventories',
            'lowStock',
            'outOfStock',
            'expiringSoon',
            'totalInventoryValue',
            'products',
            'warehouses'
        ));
    }

    /**
     * Adjust stock with pessimistic locking and full audit trail.
     *
     * BEFORE (MISSING AUDIT TRAIL):
     *   No StockAudit record created for manual adjustments.
     *   No InventoryMovement recorded.
     *   No lockForUpdate — race condition with concurrent sales.
     *
     * AFTER (SECURE + AUDITABLE):
     *   - Uses lockForUpdate() inside DB::transaction.
     *   - Records InventoryMovement with type='adjustment'.
     *   - Validates stock doesn't go negative for subtract.
     *   - Never exposes internal errors to the client.
     */
    public function adjust(AdjustInventoryRequest $request)
    {
        Gate::authorize('manage_inventory');

        $validated = $request->validated();
        $user      = Auth::user();

        try {
            return DB::transaction(function () use ($validated, $user) {
                // Lock the inventory row for concurrent access safety
                $query = Inventory::where('product_id', $validated['product_id']);
                if ($user->isOwner() && !empty($validated['branch_id'])) {
                    $query->where('branch_id', $validated['branch_id']);
                } elseif ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                }
                $inventory = $query->lockForUpdate()->first();

                if (!$inventory) {
                    return response()->json(['message' => 'Inventory record not found.'], 404);
                }

                $oldStock = (int) $inventory->current_stock;
                $qty      = (int) $validated['quantity'];

                $newStock = match ($validated['adjustment_type']) {
                    'add'      => $oldStock + $qty,
                    'subtract' => max(0, $oldStock - $qty),
                    'set', 'correction' => $qty,
                };

                if ($validated['adjustment_type'] === 'subtract' && $qty > $oldStock) {
                    return response()->json([
                        'message' => "Cannot subtract {$qty} — current stock is only {$oldStock}.",
                    ], 422);
                }

                $inventory->update([
                    'current_stock' => $newStock,
                    'cost_per_unit' => $validated['cost_per_unit'] ?? $inventory->cost_per_unit,
                ]);

                // Record audit trail
                InventoryMovement::record(
                    productId:   $inventory->product_id,
                    branchId:    $inventory->branch_id,
                    type:        'adjustment',
                    quantity:    $newStock - $oldStock, // signed delta
                    stockBefore: $oldStock,
                    stockAfter:  $newStock,
                    refType:     'adjustment',
                    notes:       $validated['reason'] ?? null,
                    userId:      $user->id,
                );

                Log::info('Stock adjusted', [
                    'product_id'  => $inventory->product_id,
                    'branch_id'   => $inventory->branch_id,
                    'type'        => $validated['adjustment_type'],
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'delta'       => $newStock - $oldStock,
                    'reason'      => $validated['reason'] ?? null,
                    'user_id'     => $user->id,
                ]);

                // Broadcast low stock alert if below minimum
                if ($newStock < (int) $inventory->minimum_stock) {
                    event(new LowStockAlert(
                        $inventory->product_id,
                        $inventory->product->name ?? 'Unknown',
                        $newStock,
                        (int) $inventory->minimum_stock
                    ));
                }

                return response()->json([
                    'message'    => 'Stock adjusted successfully.',
                    'old_stock'  => $oldStock,
                    'new_stock'  => $newStock,
                    'delta'      => $newStock - $oldStock,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Stock adjustment failed', [
                'product_id' => $validated['product_id'],
                'user_id'    => $user->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Stock adjustment failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Perform a stock audit. Records a snapshot of current stock levels.
     */
    public function audit(Request $request)
    {
        Gate::authorize('manage_inventory');

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $query = Inventory::with('product');

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $inventories = $query->get();

        $audit = \App\Models\StockAudit::create([
            'user_id'   => $user->id,
            'branch_id' => $user->branch_id,
            'notes'     => $validated['notes'] ?? 'Audit stok rutin',
            'status'    => 'in_progress',
        ]);

        foreach ($inventories as $inv) {
            $audit->items()->create([
                'product_id'      => $inv->product_id,
                'expected_qty'    => $inv->current_stock,
                'actual_qty'      => $inv->current_stock,
                'cost_per_unit'   => $inv->cost_per_unit,
            ]);
        }

        return redirect()->route('stock_audits.show', $audit)
            ->with('success', 'Audit stok berhasil dimulai dengan ' . $inventories->count() . ' item.');
    }

    /**
     * Get inventory alerts as JSON for dashboard.
     */
    public function getAlerts()
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        $query = Inventory::with('product');

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $lowStock = (clone $query)->whereColumn('current_stock', '<', 'minimum_stock')
            ->where('current_stock', '>', 0)->get();
        $outOfStock = (clone $query)->where('current_stock', '<=', 0)->get();
        $expiringSoon = (clone $query)->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])->get();

        return response()->json([
            'low_stock'    => $lowStock->count(),
            'out_of_stock' => $outOfStock->count(),
            'expiring'     => $expiringSoon->count(),
            'items'        => [
                'low_stock'    => $lowStock->map(fn($i) => ['product' => $i->product?->name ?? 'Unknown', 'stock' => $i->current_stock, 'minimum' => $i->minimum_stock]),
                'out_of_stock' => $outOfStock->map(fn($i) => ['product' => $i->product?->name ?? 'Unknown']),
                'expiring'     => $expiringSoon->map(fn($i) => ['product' => $i->product?->name ?? 'Unknown', 'expires' => $i->expiration_date?->format('Y-m-d') ?? 'N/A']),
            ],
        ]);
    }
}
