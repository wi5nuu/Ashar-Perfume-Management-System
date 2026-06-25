<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SalesReturnController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage_transactions');

        $query = SalesReturn::with(['transaction', 'user', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $user = auth()->user();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $returns = $query->latest()->paginate(15);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show create form for a specific transaction.
     */
    public function create(Transaction $transaction)
    {
        Gate::authorize('manage_transactions');

        $transaction->load(['details.product', 'customer']);

        return view('returns.create', compact('transaction'));
    }

    /**
     * Store a new return request.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_transactions');

        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reason'         => 'required|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.detail_id' => 'required|exists:transaction_details,id',
            'items.*.quantity'  => 'required|integer|min:1',
        ]);

        try {
            $return = DB::transaction(function () use ($validated) {
                $user = auth()->user();
                $transaction = Transaction::findOrFail($validated['transaction_id']);

                $return = SalesReturn::create([
                    'return_number'  => SalesReturn::generateReturnNumber(),
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'branch_id'      => $user->branch_id,
                    'reason'         => $validated['reason'],
                    'status'         => 'pending',
                ]);

                $totalRefund = 0;
                foreach ($validated['items'] as $item) {
                    $detail = TransactionDetail::findOrFail($item['detail_id']);

                    // Ensure return qty doesn't exceed original
                    $qty = min((int) $item['quantity'], (int) $detail->quantity);
                    $subtotal = $qty * $detail->price;
                    $totalRefund += $subtotal;

                    $return->items()->create([
                        'transaction_detail_id' => $detail->id,
                        'product_id'            => $detail->product_id,
                        'quantity'              => $qty,
                        'unit_price'            => $detail->price,
                        'subtotal'              => $subtotal,
                    ]);
                }

                $return->update(['total_refund' => $totalRefund]);

                return $return;
            });

            Log::info('Sales return created', [
                'return_id' => $return->id,
                'user_id'   => auth()->id(),
            ]);

            return redirect()->route('returns.show', $return)
                ->with('success', 'Retur penjualan berhasil dibuat: ' . $return->return_number);
        } catch (\Exception $e) {
            Log::error('Return creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal membuat retur: ' . $e->getMessage());
        }
    }

    public function show(SalesReturn $return)
    {
        Gate::authorize('manage_transactions');

        $return->load(['transaction.customer', 'items.product', 'user', 'branch', 'approver']);

        return view('returns.show', compact('return'));
    }

    /**
     * Approve a pending return (manager/owner only).
     */
    public function approve(SalesReturn $return)
    {
        Gate::authorize('manage_employees');

        if ($return->status !== 'pending') {
            return back()->with('error', 'Hanya retur pending yang bisa diapprove.');
        }

        $return->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Retur telah diapprove.');
    }

    /**
     * Complete return — restore stock atomically.
     */
    public function complete(SalesReturn $return)
    {
        Gate::authorize('manage_employees');

        if ($return->status !== 'approved') {
            return back()->with('error', 'Retur harus diapprove terlebih dahulu.');
        }

        $user = auth()->user();
        $branchId = $return->branch_id ?? $user->branch_id;

        try {
            DB::transaction(function () use ($return, $branchId, $user) {
                foreach ($return->items as $item) {
                    $query = Inventory::where('product_id', $item->product_id);
                    if ($branchId) $query->where('branch_id', $branchId);
                    $inventory = $query->lockForUpdate()->first();

                    if ($inventory) {
                        $oldStock = (int) $inventory->current_stock;
                        $newStock = $oldStock + $item->quantity;

                        $inventory->update(['current_stock' => $newStock]);

                        InventoryMovement::record(
                            productId:   $item->product_id,
                            branchId:    $branchId,
                            type:        'return',
                            quantity:    $item->quantity,
                            stockBefore: $oldStock,
                            stockAfter:  $newStock,
                            refType:     'sales_return',
                            refId:       $return->id,
                            notes:       'Return: ' . $return->return_number,
                            userId:      $user->id,
                        );
                    }
                }

                $return->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);
            });

            Log::info('Sales return completed', [
                'return_id' => $return->id,
                'user_id'   => $user->id,
            ]);

            return redirect()->route('returns.show', $return)
                ->with('success', 'Retur selesai. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            Log::error('Return completion failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menyelesaikan retur: ' . $e->getMessage());
        }
    }
}
