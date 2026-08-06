<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('goods_receipts.create');
    }

    public function rules(): array
    {
        return [
            'purchase_order_id'         => ['required', 'integer', 'exists:purchase_orders,id'],
            'received_date'             => ['required', 'date', 'before_or_equal:today'],
            'notes'                     => ['nullable', 'string', 'max:1000'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.received_quantity' => ['required', 'integer', 'min:0'],
            'items.*.batch_number'      => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date'       => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_order_id.exists'                       => 'Purchase order tidak ditemukan.',
            'items.required'                                 => 'Minimal satu item harus diterima.',
            'items.*.received_quantity.min'                  => 'Jumlah diterima tidak boleh negatif.',
            'items.*.purchase_order_item_id.exists'          => 'Item purchase order pada baris :position tidak valid.',
            'items.*.expiry_date.after'                      => 'Tanggal kadaluarsa harus setelah hari ini.',
        ];
    }
}
