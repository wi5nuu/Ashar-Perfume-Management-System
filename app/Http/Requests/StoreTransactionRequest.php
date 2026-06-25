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

                // Aggregate quantity per product (menangani produk duplikat)
                $aggregated = [];
                $refillVolumes = [];
                foreach ($items as $item) {
                    $pid = $item['product_id'];
                    if (!empty($item['refill_volume_ml'])) {
                        $refillVolumes[$pid] = ($refillVolumes[$pid] ?? 0) + $item['refill_volume_ml'];
                    } else {
                        $aggregated[$pid] = ($aggregated[$pid] ?? 0) + $item['quantity'];
                    }
                }

                foreach ($aggregated as $productId => $totalQty) {
                    $query = \App\Models\Inventory::with('product')
                        ->where('product_id', $productId);
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                    $inventory = $query->first();

                    if (!$inventory) {
                        $validator->errors()->add(
                            'items',
                            "Inventory tidak ditemukan untuk product ID: {$productId}" . ($branchId ? " di cabang ini." : ".")
                        );
                        continue;
                    }

                    if ($inventory->current_stock < $totalQty) {
                        $validator->errors()->add(
                            'items',
                            "Stok produk '{$inventory->product->name}' tidak mencukupi. Diminta: {$totalQty}, Tersedia: {$inventory->current_stock}."
                        );
                    }
                }

                // Validate refill stock
                foreach ($refillVolumes as $productId => $totalMl) {
                    $query = \App\Models\Inventory::with('product')
                        ->where('product_id', $productId);
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                    $inventory = $query->first();

                    if (!$inventory) {
                        $validator->errors()->add(
                            'items',
                            "Inventory tidak ditemukan untuk product ID: {$productId}" . ($branchId ? " di cabang ini." : ".")
                        );
                        continue;
                    }

                    $bulkStock = (float) ($inventory->bulk_stock_ml ?? 0);
                    if ($bulkStock < $totalMl) {
                        $validator->errors()->add(
                            'items',
                            "Stok isi ulang '{$inventory->product->name}' tidak mencukupi. Diminta: {$totalMl}ml, Tersedia: {$bulkStock}ml."
                        );
                    }
                }
            }
        ];
    }
}
