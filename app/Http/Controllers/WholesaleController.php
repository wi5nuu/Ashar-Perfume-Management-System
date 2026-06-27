<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WholesaleOrder;
use App\Models\WholesaleOrderDetail;
use App\Models\WholesaleProduct;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use App\Services\WholesaleLoyaltyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\NewWholesaleOrder;
use App\Notifications\WholesaleOrderNotification;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('wholesale.view');
        $query = WholesaleOrder::with(['user', 'customer', 'handler']);

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

        return view('wholesale.index', compact('orders', 'statuses'));
    }

    public function create()
    {
        Gate::authorize('wholesale.manage');
        $wholesaleProducts = WholesaleProduct::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('inventories')->orderBy('name')->get(['id', 'name', 'selling_price', 'wholesale_price', 'size']);
        $customers = Customer::where('is_active', true)
            ->when(auth()->user()->branch_id && !auth()->user()->isOwner() && !auth()->user()->isAdminPusat(), function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'type']);
        $handlers = User::where('can_login', true)->whereIn('role', ['owner', 'admin', 'admin_pusat', 'manager', 'supervisor', 'warehouse'])->orderBy('name')->get(['id', 'name', 'role']);

        return view('wholesale.create', compact('wholesaleProducts', 'products', 'customers', 'handlers'));
    }

    public function store(Request $request)
    {
        Gate::authorize('wholesale.manage');
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
            'items' => 'required|array|min:1',
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
        $handlers = User::where('can_login', true)->whereIn('role', ['owner', 'admin', 'admin_pusat', 'manager', 'supervisor', 'warehouse'])->orderBy('name')->get(['id', 'name', 'role']);
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
        $handlers = User::where('can_login', true)->whereIn('role', ['owner', 'admin', 'admin_pusat', 'manager', 'supervisor', 'warehouse'])->orderBy('name')->get(['id', 'name', 'role']);
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
            'items' => 'required|array|min:1',
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
        $order->details()->delete();
        $order->delete();
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

            $branchId = $order->branch_id ?? Auth::user()->branch_id;

            foreach ($order->details as $detail) {
                if (!$detail->product_id) continue; // skip non-inventory items (e.g. fragrance oils)

                $query = \App\Models\Inventory::where('product_id', $detail->product_id);
                if ($branchId) $query->where('branch_id', $branchId);
                $inventory = $query->lockForUpdate()->first();

                if (!$inventory) {
                    // Try without branch filter
                    $inventory = \App\Models\Inventory::where('product_id', $detail->product_id)
                        ->lockForUpdate()
                        ->first();
                }

                if (!$inventory) {
                    throw new \Exception("Stok tidak ditemukan untuk produk: {$detail->product_name}.");
                }

                if ($inventory->current_stock < $detail->quantity) {
                    throw new \Exception(
                        "Stok tidak cukup untuk '{$detail->product_name}'. " .
                        "Dibutuhkan: {$detail->quantity}, Tersedia: {$inventory->current_stock}"
                    );
                }

                $inventory->update([
                    'current_stock' => $inventory->current_stock - $detail->quantity,
                    'stock_out' => $inventory->stock_out + $detail->quantity,
                ]);
            }

            $order->update([
                'status' => 'reviewed',
                'confirmed_at' => Carbon::now(),
            ]);

            DB::commit();
            $this->notifyCustomer($order, 'reviewed');
            return back()->with('success', 'Pesanan dikonfirmasi dan stok gudang otomatis terpotong!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi: ' . $e->getMessage());
        }
    }

    private function notifyCustomer(WholesaleOrder $order, string $status): void
    {
        $customerUser = User::where('role', 'wholesale_customer')
            ->where(function ($q) use ($order) {
                $q->where('phone', $order->recipient_phone)
                  ->orWhere('email', $order->customer?->email)
                  ->orWhere('email', $order->recipient_phone . '@email.com');
            })->first();
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
        $order->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);
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

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

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
