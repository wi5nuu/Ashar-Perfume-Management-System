<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchase_orders.create');
    }

    public function rules(): array
    {
        return [
            'supplier_id'            => ['required', 'integer', 'exists:suppliers,id'],
            'branch_id'              => ['required', 'integer', 'exists:branches,id'],
            'order_date'             => ['required', 'date', 'before_or_equal:today'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1', 'max:99999'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'items.*.discount'       => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Minimal satu item produk harus ditambahkan.',
            'items.*.product_id.exists'   => 'Produk pada baris :position tidak ditemukan.',
            'items.*.quantity.min'        => 'Jumlah produk pada baris :position harus lebih dari 0.',
            'items.*.unit_price.min'      => 'Harga satuan tidak boleh negatif.',
        ];
    }
}
