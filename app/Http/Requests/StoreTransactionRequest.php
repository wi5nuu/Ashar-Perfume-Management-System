<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        Gate::authorize('manage_transactions');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id'          => 'nullable|exists:customers,id',
            'customer_type'        => 'required|in:retail,wholesale',
            'items'                => 'required|array',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.size'         => 'nullable|string|max:50',
            'items.*.price'        => 'required|numeric|min:0',
            'items.*.bonus_quantity' => 'nullable|integer|min:0',
            'items.*.bonus_note'   => 'nullable|string|max:255',
            'items.*.refill_volume_ml' => 'nullable|numeric|min:0',
            'items.*.is_bonus_item' => 'nullable|boolean',
            'items.*.bonus_ml'     => 'nullable|numeric|min:0',
            'items.*.tier'         => 'nullable|in:premium,sedang,biasa',
            'discount_amount'      => 'nullable|numeric|min:0',
            'discount_type'        => 'nullable|in:fixed,percent',
            'discount_percent'     => 'nullable|numeric|min:0|max:100',
            'payment_method'       => 'required|in:cash,qris,transfer,ewallet,debit_card,credit_card',
            'ewallet_type'         => 'nullable|string|max:50',
            'tax_enabled'          => 'nullable|boolean',
            'paid_amount'          => 'required|numeric|min:0',
            'receipt_visibility'   => 'nullable|in:public,private',
            'notes'                => 'nullable|string',
            'coupon_code'          => 'nullable|exists:coupons,code'
        ];
    }

    // BUG-16 FIX: Validasi stok di layer Request
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                if ($validator->errors()->isNotEmpty()) return; // Skip jika ada error sebelumnya
                
                $user = auth()->user();
                $branchId = $user->branch_id;
                $items = $this->input('items', []);

                // Semua item (reguler, bonus, refill) pakai bulk_stock_ml
                // Aggregate ml yang dibutuhkan per produk
                $bulkMlNeeded = [];
                foreach ($items as $item) {
                    $pid = $item['product_id'];
                    if (!empty($item['refill_volume_ml'])) {
                        // Isi ulang: volume custom dari user
                        $bulkMlNeeded[$pid] = ($bulkMlNeeded[$pid] ?? 0) + (float)$item['refill_volume_ml'];
                    } elseif (!empty($item['is_bonus_item'])) {
                        // Bonus gratis: 20ml per unit
                        $bonusMl = (float)($item['bonus_ml'] ?? 20);
                        $bulkMlNeeded[$pid] = ($bulkMlNeeded[$pid] ?? 0) + ($bonusMl * (int)$item['quantity']);
                    } else {
                        // Produk reguler: validasi stok bibit sesuai standar racikan per tier
                        // Premium: 30ml=20ml, 50ml=33ml, 100ml=65ml
                        // Sedang:  30ml=15ml, 50ml=25ml, 100ml=50ml
                        // Biasa:   30ml=10ml, 50ml=17ml, 100ml=33ml
                        $product = \App\Models\Product::find($pid);
                        $rawSize = strtolower(preg_replace('/\s+/', '', $product->size ?? '30ml'));
                        $tier    = $item['tier'] ?? 'biasa';
                        $porsiMl = match(true) {
                            str_contains($rawSize, '100') => match($tier) {
                                'premium' => 65, 'sedang' => 50, default => 33
                            },
                            str_contains($rawSize, '50') => match($tier) {
                                'premium' => 33, 'sedang' => 25, default => 17
                            },
                            default => match($tier) {
                                'premium' => 20, 'sedang' => 15, default => 10
                            },
                        };
                        $bulkMlNeeded[$pid] = ($bulkMlNeeded[$pid] ?? 0) + ($porsiMl * (int)$item['quantity']);
                    }
                }

                foreach ($bulkMlNeeded as $productId => $totalMl) {
                    $inventory = \App\Models\Inventory::with('product')
                        ->where('product_id', $productId)
                        ->when(is_null($branchId), fn($q) => $q->whereNull('branch_id'), fn($q) => $q->where('branch_id', $branchId))
                        ->first();

                    if (!$inventory) {
                        $validator->errors()->add(
                            'items',
                            "Inventory tidak ditemukan untuk product ID: {$productId}" . ($branchId ? " di cabang ini." : ".")
                        );
                        continue;
                    }

                    // Controller (adjustRefillStock) membaca current_stock, bukan bulk_stock_ml
                    $available = (float)($inventory->current_stock ?? 0);
                    if ($available < $totalMl) {
                        $name = $inventory->product->name ?? "Produk #{$productId}";
                        $validator->errors()->add(
                            'items',
                            "Stok '{$name}' tidak mencukupi. Dibutuhkan: {$totalMl}ml, Tersedia: {$available}ml."
                        );
                    }
                }


            }
        ];
    }
}
