<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use App\Http\Requests\StoreTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\StockUpdated;
use App\Events\DashboardUpdated;
use App\Events\DebtSubmitted;
use App\Models\InventoryMovement;
use Illuminate\Support\Str;
use App\Models\TransactionDetail;

class TransactionController extends Controller
{
    /**
     * Display paginated transaction list with branch scoping.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_transactions');

        $query = Transaction::with(['customer', 'user', 'branch', 'details.product']);

        $user = auth()->user();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        $transactions = $query->latest()->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the POS transaction form.
     */
    public function create()
    {
        Gate::authorize('manage_transactions');

        $user = auth()->user();
        $branchId = $user->branch_id;

        $products = Product::with([
            'inventories' => function ($q) use ($branchId) {
                if ($branchId) {
                    $q->where(function($query) use ($branchId) {
                        $query->where('branch_id', $branchId)
                              ->orWhereNull('branch_id');
                    });
                } else {
                    $q->whereNull('branch_id');
                }
            },
            'category',
        ])->where('is_active', true)->get();
        $customers = Customer::where('is_active', true)
            ->whereIn('type', ['retail', 'vip'])
            ->when($user->branch_id && !$user->isOwner() && !$user->isAdmin(), function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->orderBy('name')->get();
        $categories = \App\Models\ProductCategory::all();

        // Harga tetap per tier per ukuran (dari settings)
        $tierPrices = [
            '30ml'  => [
                'premium' => (int) \App\Models\Setting::getValue('tier_price_30ml_premium',  63000),
                'sedang'  => (int) \App\Models\Setting::getValue('tier_price_30ml_sedang',   50000),
                'biasa'   => (int) \App\Models\Setting::getValue('tier_price_30ml_biasa',    35000),
            ],
            '50ml'  => [
                'premium' => (int) \App\Models\Setting::getValue('tier_price_50ml_premium',  125000),
                'sedang'  => (int) \App\Models\Setting::getValue('tier_price_50ml_sedang',   100000),
                'biasa'   => (int) \App\Models\Setting::getValue('tier_price_50ml_biasa',    70000),
            ],
            '100ml' => [
                'premium' => (int) \App\Models\Setting::getValue('tier_price_100ml_premium', 250000),
                'sedang'  => (int) \App\Models\Setting::getValue('tier_price_100ml_sedang',  200000),
                'biasa'   => (int) \App\Models\Setting::getValue('tier_price_100ml_biasa',   140000),
            ],
        ];

        return view('transactions.create', compact('products', 'customers', 'categories', 'tierPrices'));
    }

    /**
     * Store a new transaction with atomic stock deduction.
     */
    public function store(StoreTransactionRequest $request)
    {
        Gate::authorize('manage_transactions');

        // Approval guard for large nominal (cashier only).
        // Use validated() data — not raw $request->items — so the check operates on
        // the same figures the transaction will actually be created with.
        $approvalThreshold = config('business.approval_threshold', 5000000);
        $validatedItems    = $request->validated()['items'] ?? [];
        $estimatedTotal    = collect($validatedItems)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 0));

        if ($estimatedTotal >= $approvalThreshold && auth()->user()->isCashier()) {
            return response()->json([
                'message'           => 'Transactions above Rp ' . number_format($approvalThreshold, 0, ',', '.') . ' require Admin/Manager approval.',
                'requires_approval' => true,
                'amount'            => $estimatedTotal,
            ], 403);
        }

        try {
            $transaction = DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $user = Auth::user();
                $subtotal = 0;

                $branchId = $user->branch_id;

                foreach ($validated['items'] as $item) {
                    $subtotal += ($item['price'] * $item['quantity']);
                }

                $discount = $validated['discount_amount'] ?? 0;
                if (($validated['discount_type'] ?? '') === 'percent') {
                    $discount = $subtotal * ($validated['discount_amount'] / 100);
                }

                $taxEnabled = filter_var($validated['tax_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
                $taxRate    = $taxEnabled ? (float) config('business.tax_rate', 0.10) : 0.0;
                $tax        = round(($subtotal - $discount) * $taxRate);
                $total      = $subtotal - $discount + $tax;

                $transaction = Transaction::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'user_id'        => $user->id,
                    'branch_id'      => $branchId,
                    'customer_id'    => $validated['customer_id'] ?? null,
                    'customer_type'  => $validated['customer_type'],
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'tax_amount'     => $tax,
                    'total_amount'   => $total,
                    'final_amount'   => $total,
                    'paid_amount'    => $validated['paid_amount'],
                    'change_amount'  => max(0, round($validated['paid_amount'] - $total)),
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['paid_amount'] >= $total ? 'paid' : 'partial',
                    'debt_amount'    => $validated['paid_amount'] >= $total ? 0 : ($total - $validated['paid_amount']),
                    'notes'          => $validated['notes'] ?? null,
                    'tax_enabled'    => $taxEnabled,
                ]);

                $productIds = collect($validated['items'])->pluck('product_id')->unique();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($validated['items'] as $item) {
                    $product = $products->get($item['product_id']);
                    if (!$product) {
                        throw new \RuntimeException("Product #{$item['product_id']} not found.");
                    }

                    $isRefill = $product->is_refill && !empty($item['refill_volume_ml']);
                    $quantity = $isRefill ? 1 : $item['quantity'];

                    $transaction->details()->create([
                        'product_id'       => $product->id,
                        'quantity'         => $quantity,
                        'price'            => $item['price'],
                        'purchase_price'   => $product->purchase_price,
                        'subtotal'         => $item['price'] * $quantity,
                        'bonus_quantity'   => $item['bonus_quantity'] ?? 0,
                        'bonus_note'       => $item['bonus_note'] ?? null,
                        'refill_volume_ml' => $item['refill_volume_ml'] ?? null,
                        'is_bonus_item'    => !empty($item['is_bonus_item']),
                        'bonus_ml'         => !empty($item['is_bonus_item']) ? (float)($item['bonus_ml'] ?? 20) : null,
                    ]);

                    if ($product->track_inventory) {
                        // Semua produk kurangi bulk_stock_ml (sistem ml)
                        if ($isRefill) {
                            // Isi ulang: volume custom
                            $this->adjustRefillStock($product->id, $branchId, (float)$item['refill_volume_ml'], 'deduct');
                        } elseif (!empty($item['is_bonus_item'])) {
                            // Bonus gratis: botol 20ml kualitas sedang = 10ml bibit
                            // (proporsional dari sedang 30ml=15ml → 20ml=10ml)
                            $bonusMl = 10.0 * (int)$item['quantity'];
                            $this->adjustRefillStock($product->id, $branchId, $bonusMl, 'deduct');
                        } else {
                            // Produk reguler: kurangi stok bibit sesuai standar racikan per tier
                            // Premium: 30ml=20ml, 50ml=33ml, 100ml=65ml
                            // Sedang:  30ml=15ml, 50ml=25ml, 100ml=50ml
                            // Biasa:   30ml=10ml, 50ml=17ml, 100ml=33ml
                            $rawSize = strtolower(preg_replace('/\s+/', '', $product->size ?? '30ml'));
                            $tier    = $item['tier'] ?? 'biasa';
                            $porsiMl = match(true) {
                                str_contains($rawSize, '100') => match($tier) {
                                    'premium' => 65.0, 'sedang' => 50.0, default => 33.0
                                },
                                str_contains($rawSize, '50') => match($tier) {
                                    'premium' => 33.0, 'sedang' => 25.0, default => 17.0
                                },
                                default => match($tier) {
                                    'premium' => 20.0, 'sedang' => 15.0, default => 10.0
                                },
                            };
                            $this->adjustRefillStock($product->id, $branchId, $porsiMl * $quantity, 'deduct');
                        }

                        if (($item['bonus_quantity'] ?? 0) > 0) {
                            $this->handleBonusStock($product->id, $item['bonus_quantity'], 'deduct', $branchId);
                        }
                    }
                }

                // Award loyalty points
                if ($transaction->customer_id) {
                    Customer::where('id', $transaction->customer_id)
                        ->increment('points', (int) floor($total / 10000));
                }

                return $transaction;
            });

            // Broadcast debt notification if partial payment
            if ($transaction->payment_status === 'partial') {
                $transaction->load(['customer', 'user']);
                broadcast(new DebtSubmitted($transaction))->toOthers();
            }

            // Broadcast dashboard update
            $this->dispatchDashboardUpdate();

            Log::info('Transaction created', [
                'id'        => $transaction->id,
                'invoice'   => $transaction->invoice_number,
                'total'     => $transaction->total_amount,
                'user_id'   => auth()->id(),
                'branch_id' => auth()->user()->branch_id,
            ]);

            return response()->json([
                'message'        => 'Transaction successful.',
                'id'             => $transaction->id,
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'change'         => $transaction->change_amount,
                'total'          => $transaction->total_amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Transaction creation failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            // Never expose internal error details to the client
            return response()->json([
                'message' => 'Failed to process transaction. Please try again or contact support.',
            ], 500);
        }
    }

    /**
     * Generate a collision-free invoice number.
     *
     * BEFORE (COLLISION RISK):
     *   'INV-' . date('YmdHis') . strtoupper(Str::random(4))
     *   Under concurrent requests within the same second, date portion is identical
     *   and Str::random(4) has only ~1.7M combinations (birthday paradox).
     *
     * AFTER (COLLISION-FREE):
     *   Uses UUID v4 (122 bits of randomness) â€” collision probability is negligible.
     *   Format: INV-YYYYMMDD-<8 chars of UUID> for readability + uniqueness.
     */
    private function generateInvoiceNumber(): string
    {
        $uuidPart = strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
        return 'INV-' . now()->format('Ymd') . '-' . $uuidPart;
    }

    /**
     * Broadcast live dashboard counters after transaction changes.
     */
    private function dispatchDashboardUpdate(): void
    {
        $today = Carbon::today();

        $totalTransactionsToday = Transaction::whereDate('created_at', $today)->count();
        $totalRevenueToday      = (float) Transaction::whereDate('created_at', $today)->sum('total_amount');
        $lowStockCount          = Inventory::whereColumn('current_stock', '<', 'minimum_stock')->count();
        $pendingDebtsCount      = Transaction::where('payment_status', 'partial')->count();

        broadcast(new DashboardUpdated(
            $totalTransactionsToday,
            $totalRevenueToday,
            $lowStockCount,
            $pendingDebtsCount
        ));
    }

    /**
     * Adjust stock atomically with pessimistic locking.
     */
    private function adjustStock(int $productId, ?int $branchId, int $quantity, string $type): void
    {
        $inventory = Inventory::where('product_id', $productId)
            ->when(is_null($branchId), fn($q) => $q->whereNull('branch_id'), fn($q) => $q->where('branch_id', $branchId))
            ->lockForUpdate()
            ->first();

        if (!$inventory) {
            if ($type === 'deduct') {
                // Do NOT silently create a zero-stock record and then deduct from it —
                // that would immediately produce negative stock. Throw instead so the
                // transaction rolls back with a clear message.
                throw new \RuntimeException(
                    "Inventory record not found for product_id={$productId} at branch_id={$branchId}. " .
                    "Please initialise stock before selling."
                );
            } else {
                Log::warning("Stock restore skipped: no inventory for product_id={$productId} at branch_id={$branchId}");
                return;
            }
        }

        $stockBefore = (int) $inventory->current_stock;

        if ($type === 'deduct' && $stockBefore < $quantity) {
            throw new \RuntimeException("Insufficient stock for product: " . ($inventory->product?->name ?? "Product #{$productId}"));
        }

        $newStockOut = $type === 'deduct'
            ? $inventory->stock_out + $quantity
            : $inventory->stock_out - $quantity;

        $newStock = $type === 'deduct'
            ? $stockBefore - $quantity
            : $stockBefore + $quantity;

        $inventory->update([
            'stock_out'     => $newStockOut,
            'current_stock' => $newStock,
        ]);

        // Record audit trail movement
        $movementType = $type === 'deduct' ? 'sale' : 'void';
        InventoryMovement::record(
            productId:   $productId,
            branchId:    $branchId,
            type:        $movementType,
            quantity:    $type === 'deduct' ? -$quantity : $quantity,
            stockBefore: $stockBefore,
            stockAfter:  $newStock,
            refType:     'transaction',
        );

        event(new StockUpdated($productId, $inventory->product?->name ?? "Product #{$productId}", $newStock));
    }

    /**
     * Handle bonus stock deduction from a related bonus-category product.
     */
    private function handleBonusStock(int $productId, int $qty, string $type, ?int $branchId = null): void
    {
        $product  = Product::findOrFail($productId);
        $branchId = $branchId ?? Auth::user()->branch_id;

        $bonusCategoryId = config('business.bonus_category_id');
        $targetProduct   = null;

        if ($bonusCategoryId) {
            $keyword       = explode(' ', $product->name)[0];
            $targetProduct = Product::where('product_category_id', $bonusCategoryId)
                ->where('name', 'like', '%' . $keyword . '%')
                ->first();
        }

        if (!$targetProduct) {
            Log::warning("Bonus product not found for product_id={$productId}. Bonus skipped.");
            return;
        }

        $this->adjustStock($targetProduct->id, $branchId, $qty, $type);
    }

    /**
     * Adjust refill bulk stock (in ml) atomically with pessimistic locking.
     */
    private function adjustRefillStock(int $productId, ?int $branchId, float $volumeMl, string $type): void
    {
        $inventory = Inventory::where('product_id', $productId)
            ->when(is_null($branchId), fn($q) => $q->whereNull('branch_id'), fn($q) => $q->where('branch_id', $branchId))
            ->lockForUpdate()
            ->first();

        // Fallback ke stok global (branch_id = null) jika stok cabang tidak ada
        if (!$inventory && $branchId) {
            $inventory = Inventory::where('product_id', $productId)
                ->whereNull('branch_id')
                ->lockForUpdate()
                ->first();
        }

        if (!$inventory) {
            if ($type === 'deduct') {
                $inventory = Inventory::create([
                    'product_id'    => $productId,
                    'branch_id'     => $branchId,
                    'current_stock' => 0,
                    'bulk_stock_ml' => 0,
                    'minimum_stock' => 0,
                    'stock_in'      => 0,
                    'stock_out'     => 0,
                ]);
            } else {
                Log::warning("Refill stock restore skipped: no inventory for product_id={$productId} at branch_id={$branchId}");
                return;
            }
        }

        $stockBefore = (float) ($inventory->current_stock ?? 0);

        if ($type === 'deduct' && $stockBefore < $volumeMl) {
            throw new \RuntimeException("Stok tidak cukup untuk produk: " . ($inventory->product?->name ?? "Product #{$productId}") . ". Tersedia: {$stockBefore}ml, Dibutuhkan: {$volumeMl}ml");
        }

        $newStock = $type === 'deduct'
            ? $stockBefore - $volumeMl
            : $stockBefore + $volumeMl;

        $inventory->update([
            'current_stock' => $newStock,
            'stock_out'     => $type === 'deduct'
                ? $inventory->stock_out + (int) $volumeMl
                : max(0, $inventory->stock_out - (int) $volumeMl),
        ]);

        InventoryMovement::record(
            productId:   $productId,
            branchId:    $branchId,
            type:        $type === 'deduct' ? 'sale' : 'void',
            quantity:    $type === 'deduct' ? -(int)$volumeMl : (int)$volumeMl,
            stockBefore: (int)$stockBefore,
            stockAfter:  (int)$newStock,
            refType:     'refill_transaction',
        );

        event(new StockUpdated($productId, $inventory->product?->name ?? "Product #{$productId}", (int)$newStock));
    }

    /**
     * Display a single transaction.
     */
    public function show(Transaction $transaction)
    {
        Gate::authorize('manage_transactions');

        $transaction->load(['customer', 'user', 'branch', 'details.product']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Print / display invoice.
     */
    public function printInvoice(Transaction $transaction)
    {
        Gate::authorize('manage_transactions');

        $transaction->load(['customer', 'user', 'branch', 'details.product']);

        return view('transactions.invoice', compact('transaction'));
    }

    /**
     * Public invoice view for customers (no auth required).
     */
    public function publicInvoice(string $invoiceNumber)
    {
        $transaction = Transaction::where('invoice_number', $invoiceNumber)
            ->with(['customer', 'user', 'branch', 'details.product'])
            ->firstOrFail();

        if (!auth()->check()) {
            $transaction->makeHidden(['customer.nik', 'customer.phone', 'customer.email', 'customer.address']);
        }

        return view('transactions.invoice', compact('transaction'));
    }

    /**
     * Delete (void) a transaction and restore stock.
     *
     * Authorization: Owner can delete any, others must be same branch + admin/manager role.
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);

        return DB::transaction(function () use ($transaction) {
            foreach ($transaction->details as $detail) {
                if (!empty($detail->refill_volume_ml)) {
                    $this->adjustRefillStock($detail->product_id, $transaction->branch_id, $detail->refill_volume_ml, 'restore');
                } elseif (!empty($detail->is_bonus_item)) {
                    // Restore bonus 20ml â€” kembalikan bulk_stock_ml sebesar 20ml
                    $this->adjustRefillStock($detail->product_id, $transaction->branch_id, 20.0, 'restore');
                } else {
                    $this->adjustStock($detail->product_id, $transaction->branch_id, $detail->quantity, 'restore');
                }

                if ($detail->bonus_quantity > 0) {
                    $this->handleBonusStock($detail->product_id, $detail->bonus_quantity, 'restore', $transaction->branch_id);
                }
            }

            if ($transaction->customer_id) {
                Customer::where('id', $transaction->customer_id)
                    ->decrement('points', (int) floor($transaction->total_amount / 10000));
            }

            Log::info('Transaction voided', [
                'id'      => $transaction->id,
                'invoice' => $transaction->invoice_number,
                'user_id' => auth()->id(),
            ]);

            $transaction->delete();

            return back()->with('success', 'Transaction voided successfully.');
        });
    }

    /**
     * Get product info for POS autocomplete.
     */
    public function getProductInfo(int $id)
    {
        Gate::authorize('manage_transactions');
        $branchId = auth()->user()->branch_id;

        $product = Product::with(['inventories' => function ($q) use ($branchId) {
            if (is_null($branchId)) {
                $q->whereNull('branch_id');
            } else {
                $q->where('branch_id', $branchId);
            }
        }])->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Only expose purchase_price to users with manage_products permission.
        // The field is excluded from the safe fields list by default and only
        // added when the Gate check passes — prevents data leak to cashiers.
        $safeFields = ['id', 'name', 'barcode', 'selling_price', 'wholesale_price', 'image', 'is_refill', 'refill_price_per_ml', 'brand', 'size', 'unit', 'minimum_stock', 'inventories'];
        if (Gate::allows('manage_products')) {
            $safeFields[] = 'purchase_price';
        }

        return response()->json($product->only($safeFields));
    }

    /**
     * Get customer info for POS autocomplete.
     */
    public function getCustomerInfo(int $id)
    {
        Gate::authorize('manage_customers');
        $user = auth()->user();

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Restrict by branch for cabang users
        if (!$user->isOwner() && !$user->isAdmin()) {
            if ($customer->branch_id && $customer->branch_id !== $user->branch_id) {
                return response()->json(['error' => 'Customer not found'], 404);
            }
            $customer->makeHidden(['points', 'portal_token']);
        }

        return response()->json($customer->only(['id', 'customer_code', 'name', 'phone', 'type', 'points', 'branch_id', 'loyalty_rank', 'total_credits_earned', 'total_credits_spent', 'gold_points', 'lifetime_spend']));
    }

    /**
     * Show edit form (redirected as transactions are not editable via form).
     */
    public function edit(Transaction $transaction)
    {
        Gate::authorize('update', $transaction);
        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Edit transaksi tidak tersedia langsung.');
    }

    /**
     * Update a transaction (Not available).
     */
    public function update(Request $request, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);
        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Edit transaksi tidak tersedia langsung.');
    }

}

