<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WholesaleOrder;
use App\Models\WholesaleOrderDetail;
use App\Models\WholesaleProduct;
use App\Models\Product;
use App\Models\Accessory;
use App\Models\Customer;
use App\Models\User;
use App\Services\WholesaleLoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\NewWholesaleOrder;
use App\Events\StockUpdated;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Notifications\WholesaleOrderNotification;
use Illuminate\Support\Facades\Log;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('wholesale.view');
        
        $query = WholesaleOrder::with(['user', 'customer', 'handler']);

        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10);
        $statuses = ['pending', 'reviewed', 'on_progress', 'packed', 'shipped', 'delivered', 'completed', 'cancelled'];

        // KPI counts — query all orders (no pagination filter) for accurate totals
        $kpiQuery = WholesaleOrder::query();
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $kpiQuery->where('branch_id', auth()->user()->branch_id);
        }
        $kpiTotal     = $kpiQuery->count();
        $kpiPending   = (clone $kpiQuery)->whereIn('status', ['pending', 'reviewed'])->count();
        $kpiProcess   = (clone $kpiQuery)->whereIn('status', ['shipped', 'on_progress', 'packed'])->count();
        $kpiCompleted = (clone $kpiQuery)->whereIn('status', ['delivered', 'completed'])->count();

        return view('wholesale.index', compact('orders', 'statuses', 'kpiTotal', 'kpiPending', 'kpiProcess', 'kpiCompleted'));
    }

    public function create()
    {
        Gate::authorize('wholesale.manage');
        
        // Load categories untuk filter di view (tanpa is_active karena kolom tidak ada)
        $categories = \App\Models\ProductCategory::orderBy('name')->get();
        
        $wholesaleProducts = WholesaleProduct::where('is_active', true)->orderBy('name')->get();
        
        // Load produk parfum (botol, is_refill=false) dengan relasi category dan inventories
        $products = Product::where('is_active', true)
            ->with(['inventories', 'category'])
            ->orderBy('name')
            ->get();

        // Load aksesori aktif
        $accessories = \App\Models\Accessory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $customers = Customer::where('is_active', true)
            ->where('type', 'wholesale')
            ->when(auth()->user()->branch_id && !auth()->user()->isOwner() && !auth()->user()->isAdmin(), function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'type']);
            
        $handlers = User::where('can_login', true)
            ->whereIn('role', ['owner', 'admin', 'manager', 'supervisor', 'warehouse'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return view('wholesale.create', compact('categories', 'wholesaleProducts', 'products', 'accessories', 'customers', 'handlers'));
    }

    public function store(Request $request)
    {
        Gate::authorize('wholesale.manage');
        $request->validate([
            'package_target_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'handler_id' => 'nullable|exists:users,id',
            'shipping_courier' => 'nullable|string|max:255',
            'delivery_handler' => 'nullable|string|max:255',
            'packing_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1|max:200',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.wholesale_product_id' => 'nullable|exists:wholesale_products,id',
            'items.*.accessory_id' => 'nullable|exists:accessories,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.volume_ml' => 'nullable|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price_per_ml' => 'nullable|numeric|min:0',
        ]);

        // Validasi: setiap item harus punya minimal 1 ID produk
        foreach ($request->items as $idx => $item) {
            if (empty($item['product_id']) && empty($item['wholesale_product_id']) && empty($item['accessory_id'])) {
                return back()->withErrors([
                    'items' => "Item #" . ($idx + 1) . " harus memiliki produk yang valid (parfum, produk grosir, atau aksesori)."
                ])->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Validasi dan potong stok langsung saat order dibuat (pending)
            $branchId = Auth::user()->branch_id;
            foreach ($request->items as $item) {
                // Potong stok parfum (bulk ml)
                if (!empty($item['product_id'])) {
                    $volumeMl = (float) ($item['volume_ml'] ?? 0);
                    $totalMl  = $volumeMl * (int) $item['quantity'];

                    if ($totalMl <= 0) {
                        throw new \Exception("Volume ml tidak valid untuk produk: {$item['product_name']}.");
                    }

                    $this->deductBulkStock($item['product_id'], $branchId, $totalMl, $item['product_name']);
                }

                // Potong stok aksesori (unit pcs/set/dll)
                if (!empty($item['accessory_id'])) {
                    $accessory = Accessory::lockForUpdate()->find($item['accessory_id']);
                    if (!$accessory) {
                        throw new \Exception("Aksesori tidak ditemukan: {$item['product_name']}.");
                    }
                    $accessory->deductStock((int) $item['quantity']);
                }
            }

            $order = WholesaleOrder::create([
                'invoice_number' => 'GROSIR-' . Carbon::now()->format('Ymd') . '-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8)),
                'user_id' => Auth::id(),
                'branch_id' => Auth::user()->branch_id,
                'customer_id' => $request->customer_id,
                'package_target_amount' => $request->package_target_amount,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_courier' => $request->shipping_courier,
                'delivery_handler' => $request->delivery_handler,
                'handler_id' => $request->handler_id,
                'packing_days' => $request->packing_days ?? 1,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                WholesaleOrderDetail::create([
                    'wholesale_order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'wholesale_product_id' => $item['wholesale_product_id'] ?? null,
                    'accessory_id' => $item['accessory_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'volume_ml' => $item['volume_ml'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'price' => $item['price'],
                    'price_per_ml' => $item['price_per_ml'] ?? null,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Handle referral
            $customerUser = User::where('role', 'wholesale_customer')
                ->where(function ($q) use ($order) {
                    $q->where('phone', $order->recipient_phone)
                      ->orWhere('email', $order->customer?->email)
                      ->orWhere('email', $order->recipient_phone . '@email.com');
                })->first();

            if ($customerUser) {
                if (!$customerUser->referral_code) {
                    $code = strtoupper(Str::random(8));
                    while (User::where('referral_code', $code)->exists()) {
                        $code = strtoupper(Str::random(8));
                    }
                    $customerUser->update(['referral_code' => $code]);
                }
                if ($request->filled('referral_code') && !$customerUser->referred_by_id) {
                    $referrer = User::where('role', 'wholesale_customer')
                        ->where('referral_code', $request->referral_code)->first();
                    if ($referrer) {
                        $customerUser->update(['referred_by_id' => $referrer->id]);
                    }
                }
            }

            $customerName = 'Walk-in';
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customerName = $customer?->name ?? 'Customer';
            }
            event(new NewWholesaleOrder($order->id, $order->invoice_number, $customerName, $totalAmount));

            $this->notifyCustomer($order, 'pending');

            return redirect()->route('wholesale.show', $order->id)->with('success', 'Pesanan Grosir berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function show(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.view');
        $order->load(['user', 'customer', 'handler', 'details.wholesaleProduct', 'details.product']);
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->recipient_phone) . "?text=" . $this->generateWhatsAppMessage($order);
        $handlers = User::where('can_login', true)->whereIn('role', ['owner', 'admin', 'manager', 'supervisor', 'warehouse'])->orderBy('name')->get(['id', 'name', 'role']);
        return view('wholesale.show', compact('order', 'whatsappUrl', 'handlers'));
    }

    public function edit(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if (!in_array($order->status, ['pending', 'reviewed'])) {
            return back()->with('error', 'Pesanan sudah diproses, tidak dapat diedit.');
        }
        $wholesaleProducts = WholesaleProduct::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'selling_price', 'wholesale_price', 'size']);
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone', 'type']);
        $handlers = User::where('can_login', true)->whereIn('role', ['owner', 'admin', 'manager', 'supervisor', 'warehouse'])->orderBy('name')->get(['id', 'name', 'role']);
        return view('wholesale.edit', compact('order', 'wholesaleProducts', 'products', 'customers', 'handlers'));
    }

    public function update(Request $request, WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if (!in_array($order->status, ['pending', 'reviewed'])) {
            return back()->with('error', 'Pesanan sudah diproses, tidak dapat diubah.');
        }

        $request->validate([
            'package_target_amount' => 'required|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'handler_id' => 'nullable|exists:users,id',
            'shipping_courier' => 'nullable|string|max:255',
            'delivery_handler' => 'nullable|string|max:255',
            'packing_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1|max:200',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.wholesale_product_id' => 'nullable|exists:wholesale_products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.volume_ml' => 'nullable|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.price_per_ml' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $branchId = $order->branch_id ?? Auth::user()->branch_id;

            // Kembalikan stok lama dari detail order sebelumnya
            $order->load('details');
            foreach ($order->details as $oldDetail) {
                if (!$oldDetail->product_id) continue;

                $oldVolumeMl = (float) ($oldDetail->volume_ml ?? 0);
                $oldTotalMl  = $oldVolumeMl * (int) $oldDetail->quantity;
                if ($oldTotalMl <= 0) continue;

                $this->restoreBulkStock($oldDetail->product_id, $branchId, $oldTotalMl, $oldDetail->product_name);
            }

            // Potong stok baru dari item yang diperbarui
            foreach ($request->items as $item) {
                if (!$item['product_id']) continue;

                $volumeMl = (float) ($item['volume_ml'] ?? 0);
                $totalMl  = $volumeMl * (int) $item['quantity'];

                if ($totalMl <= 0) {
                    throw new \Exception("Volume ml tidak valid untuk produk: {$item['product_name']}.");
                }

                $this->deductBulkStock($item['product_id'], $branchId, $totalMl, $item['product_name']);
            }

            $order->update([
                'customer_id' => $request->customer_id,
                'package_target_amount' => $request->package_target_amount,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_courier' => $request->shipping_courier,
                'delivery_handler' => $request->delivery_handler,
                'handler_id' => $request->handler_id,
                'packing_days' => $request->packing_days ?? 1,
                'notes' => $request->notes,
            ]);

            $order->details()->delete();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                WholesaleOrderDetail::create([
                    'wholesale_order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'wholesale_product_id' => $item['wholesale_product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'volume_ml' => $item['volume_ml'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'price' => $item['price'],
                    'price_per_ml' => $item['price_per_ml'] ?? null,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('wholesale.show', $order->id)->with('success', 'Pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pesanan: ' . $e->getMessage());
        }
    }

    public function destroy(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if (!in_array($order->status, ['pending', 'reviewed', 'cancelled'])) {
            return back()->with('error', 'Hanya pesanan pending/reviewed/cancelled yang bisa dihapus.');
        }

        try {
            DB::beginTransaction();

            $branchId = $order->branch_id ?? Auth::user()->branch_id;

            // Kembalikan stok jika order masih pending/reviewed (stok sudah dipotong)
            if (in_array($order->status, ['pending', 'reviewed'])) {
                $order->load('details');
                foreach ($order->details as $detail) {
                    if (!$detail->product_id) continue;

                    $volumeMl = (float) ($detail->volume_ml ?? 0);
                    $totalMl  = $volumeMl * (int) $detail->quantity;
                    if ($totalMl <= 0) continue;

                    $this->restoreBulkStock($detail->product_id, $branchId, $totalMl, $detail->product_name);
                }
            }

            $order->details()->delete();
            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }

        return redirect()->route('wholesale.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function confirm(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');

        try {
            DB::beginTransaction();
            $order = WholesaleOrder::lockForUpdate()->findOrFail($order->id);
            if ($order->status !== 'pending') {
                DB::rollBack();
                return back()->with('error', "Hanya order berstatus 'pending' yang bisa dikonfirmasi.");
            }

            $order->update([
                'status' => 'reviewed',
                'confirmed_at' => Carbon::now(),
            ]);

            DB::commit();
            $this->notifyCustomer($order, 'reviewed');
            return back()->with('success', 'Pesanan dikonfirmasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi: ' . $e->getMessage());
        }
    }

    /**
     * Kurangi current_stock dari inventory untuk transaksi grosir.
     * total_ml = quantity (botol) × volume_ml (ukuran botol: 30/50/100ml)
     * Dipanggil saat order berstatus completed.
     */
    private function deductBulkStock(int $productId, ?int $branchId, float $totalMl, string $productName): void
    {
        $inventory = Inventory::where('product_id', $productId)
            ->when(
                is_null($branchId),
                fn($q) => $q->whereNull('branch_id'),
                fn($q) => $q->where('branch_id', $branchId)
            )
            ->lockForUpdate()
            ->first();

        // Fallback ke stok pusat (branch_id = null) jika stok cabang tidak ada
        if (!$inventory && $branchId) {
            $inventory = Inventory::where('product_id', $productId)
                ->whereNull('branch_id')
                ->lockForUpdate()
                ->first();
        }

        if (!$inventory) {
            throw new \Exception("Stok tidak ditemukan untuk produk: {$productName}.");
        }

        $stockBefore = (float) ($inventory->current_stock ?? 0);

        if ($stockBefore < $totalMl) {
            throw new \Exception(
                "Stok tidak cukup untuk '{$productName}'. " .
                "Dibutuhkan: {$totalMl}ml, Tersedia: {$stockBefore}ml"
            );
        }

        $newStock = $stockBefore - $totalMl;

        $inventory->update([
            'current_stock' => $newStock,
            'stock_out'     => $inventory->stock_out + (int) $totalMl,
        ]);

        InventoryMovement::record(
            productId:   $productId,
            branchId:    $branchId,
            type:        'sale',
            quantity:    -(int) $totalMl,
            stockBefore: (int) $stockBefore,
            stockAfter:  (int) $newStock,
            refType:     'wholesale_order',
        );

        event(new StockUpdated(
            $productId,
            $inventory->product?->name ?? "Product #{$productId}",
            (int) $newStock
        ));
    }

    private function restoreBulkStock(int $productId, ?int $branchId, float $totalMl, string $productName): void
    {
        $inventory = Inventory::where('product_id', $productId)
            ->when(
                is_null($branchId),
                fn($q) => $q->whereNull('branch_id'),
                fn($q) => $q->where('branch_id', $branchId)
            )
            ->lockForUpdate()
            ->first();

        // Fallback ke stok pusat (branch_id = null) jika stok cabang tidak ada
        if (!$inventory && $branchId) {
            $inventory = Inventory::where('product_id', $productId)
                ->whereNull('branch_id')
                ->lockForUpdate()
                ->first();
        }

        if (!$inventory) {
            throw new \Exception("Stok tidak ditemukan untuk produk: {$productName}.");
        }

        $stockBefore = (float) ($inventory->current_stock ?? 0);
        $newStock    = $stockBefore + $totalMl;

        $inventory->update([
            'current_stock' => $newStock,
            'stock_out'     => max(0, $inventory->stock_out - (int) $totalMl),
        ]);

        InventoryMovement::record(
            productId:   $productId,
            branchId:    $branchId,
            type:        'return',
            quantity:    (int) $totalMl,
            stockBefore: (int) $stockBefore,
            stockAfter:  (int) $newStock,
            refType:     'wholesale_order',
        );

        event(new StockUpdated(
            $productId,
            $inventory->product?->name ?? "Product #{$productId}",
            (int) $newStock
        ));
    }

    private function notifyCustomer(WholesaleOrder $order, string $status): void
    {
        // Only look up by verified identifiers — never fabricate an email address
        // from a phone number (e.g. phone@email.com leaks info and matches nothing real).
        $customerUser = null;

        if ($order->customer?->email) {
            $customerUser = User::where('role', 'wholesale_customer')
                ->where('email', $order->customer->email)
                ->first();
        }

        if (!$customerUser && $order->recipient_phone) {
            $customerUser = User::where('role', 'wholesale_customer')
                ->where('phone', $order->recipient_phone)
                ->first();
        }

        if ($customerUser) {
            $customerUser->notify(new WholesaleOrderNotification($order, $status));
        }
    }

    public function process(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if ($order->status !== 'reviewed') {
            return back()->with('error', 'Order harus dalam status reviewed.');
        }
        $order->update(['status' => 'on_progress']);
        $this->notifyCustomer($order, 'on_progress');
        return back()->with('success', 'Pesanan sedang diproses.');
    }

    public function markPacked(Request $request, WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if ($order->status !== 'on_progress') {
            return back()->with('error', 'Order harus dalam status on_progress.');
        }

        $order->update([
            'status' => 'packed',
            'packed_at' => Carbon::now(),
            'handler_id' => $request->handler_id ?? $order->handler_id,
            'barcode' => 'SHP-' . $order->id . '-' . time(),
        ]);

        $this->notifyCustomer($order, 'packed');
        return back()->with('success', 'Pesanan sudah di-packing dan siap dikirim!');
    }

    public function markShipped(Request $request, WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if ($order->status !== 'packed') {
            return back()->with('error', 'Order harus dalam status packed.');
        }

        $order->update([
            'status' => 'shipped',
            'shipped_at' => Carbon::now(),
            'shipping_courier' => $request->shipping_courier ?? $order->shipping_courier,
            'shipping_cost' => $request->shipping_cost ?? $order->shipping_cost,
            'tracking_number' => $request->tracking_number ?? $order->tracking_number,
        ]);

        $this->notifyCustomer($order, 'shipped');
        return back()->with('success', 'Pesanan sudah dikirim!');
    }

    public function markDelivered(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Order harus dalam status shipped.');
        }
        $order->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now(),
        ]);
        $this->notifyCustomer($order, 'delivered');
        return back()->with('success', 'Pesanan sudah diterima oleh pelanggan.');
    }

    public function complete(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if (!in_array($order->status, ['delivered', 'shipped', 'packed'])) {
            return back()->with('error', 'Order harus dalam status delivered/shipped/packed untuk diselesaikan.');
        }

        try {
            DB::beginTransaction();

            $order = WholesaleOrder::lockForUpdate()->findOrFail($order->id);

            // Stok sudah dipotong saat order dibuat (pending), tidak perlu potong lagi di sini
            $order->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan order: ' . $e->getMessage());
        }

        $this->notifyCustomer($order, 'completed');

        // Earn loyalty credits
        if ($order->customer && $order->total_amount > 0) {
            try {
                app(WholesaleLoyaltyService::class)->earnCredits(
                    $order->customer,
                    (float) $order->total_amount,
                    'order',
                    $order->id
                );
            } catch (\Throwable $e) {
                \Log::warning('Loyalty credit earning failed for order #' . $order->id . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Pesanan telah selesai!');
    }

    public function cancel(Request $request, WholesaleOrder $order)
    {
        Gate::authorize('wholesale.manage');
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan sudah selesai/dibatalkan.');
        }

        $request->validate(['cancellation_reason' => 'required|string|max:500']);

        try {
            DB::beginTransaction();

            $branchId = $order->branch_id ?? Auth::user()->branch_id;

            // Kembalikan stok jika order masih pending/reviewed (stok sudah dipotong)
            if (in_array($order->status, ['pending', 'reviewed'])) {
                $order->load('details');
                foreach ($order->details as $detail) {
                    if (!$detail->product_id) continue;

                    $volumeMl = (float) ($detail->volume_ml ?? 0);
                    $totalMl  = $volumeMl * (int) $detail->quantity;
                    if ($totalMl <= 0) continue;

                    $this->restoreBulkStock($detail->product_id, $branchId, $totalMl, $detail->product_name);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }

        $this->notifyCustomer($order, 'cancelled');
        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function print(WholesaleOrder $order)
    {
        Gate::authorize('wholesale.view');
        $order->load(['user', 'customer', 'handler', 'details.wholesaleProduct', 'details.product']);
        return view('wholesale.invoice', compact('order'));
    }

    private function generateWhatsAppMessage(WholesaleOrder $order): string
    {
        $statusLabels = [
            'pending' => 'Menunggu Review',
            'reviewed' => 'Dikonfirmasi',
            'on_progress' => 'Diproses',
            'packed' => 'Di-packing',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $message = "*NOTA PESANAN GROSIR - AL'ASHAR PARFUM*\n";
        $message .= "------------------------------------------------\n";
        $message .= "No. Invoice: *" . $order->invoice_number . "*\n";
        $message .= "Tanggal: " . ($order->created_at ? $order->created_at->format('d/m/Y') : '-') . "\n";
        $message .= "Status: " . strtoupper($statusLabels[$order->status] ?? $order->status) . "\n";
        $message .= "------------------------------------------------\n";
        $message .= "*Detail Pesanan:*\n";

        foreach ($order->details as $detail) {
            $message .= "- " . $detail->product_name;
            if ($detail->volume_ml) $message .= " (" . $detail->volume_ml . "ml)";
            $message .= " x" . $detail->quantity . " : Rp " . number_format($detail->subtotal, 0, ',', '.') . "\n";
        }

        $message .= "------------------------------------------------\n";
        $message .= "TOTAL NILAI: *Rp " . number_format($order->total_amount, 0, ',', '.') . "*\n";
        $message .= "Biaya Kirim: Rp " . number_format($order->shipping_cost, 0, ',', '.') . "\n";
        $message .= "Grand Total: *Rp " . number_format($order->total_amount + $order->shipping_cost, 0, ',', '.') . "*\n";

        if ($order->notes) {
            $message .= "------------------------------------------------\n";
            $message .= "Catatan: " . $order->notes . "\n";
        }

        $message .= "------------------------------------------------\n";
        $message .= "*Informasi Pengiriman:*\n";
        $message .= "Penerima: " . $order->recipient_name . "\n";
        $message .= "Alamat: " . $order->shipping_address . "\n";
        if ($order->shipping_courier) $message .= "Kurir: " . $order->shipping_courier . "\n";
        if ($order->estimated_arrival) $message .= "Estimasi Sampai: " . $order->estimated_arrival->format('d/m/Y') . "\n";

        $message .= "------------------------------------------------\n";
        $message .= "Lihat Invoice Digital:\n";
        $message .= route('wholesale.print', ['order' => $order->id]) . "\n\n";
        $message .= "Terima kasih telah memesan di *Al'Ashar Parfum*!\n";
        $message .= "_Sistem dikelola oleh APMS_";

        return rawurlencode($message);
    }
}
