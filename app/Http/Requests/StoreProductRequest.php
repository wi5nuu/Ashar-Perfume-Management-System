<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.manage');
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name'                  => ['required', 'string', 'max:255'],
            'sku'                   => ['required', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'barcode'               => ['nullable', 'string', 'max:100', 'unique:products,barcode,' . $productId],
            'category_id'           => ['required', 'integer', 'exists:product_categories,id'],
            'brand'                 => ['nullable', 'string', 'max:100'],
            'size'                  => ['nullable', 'string', 'max:50'],
            'unit'                  => ['required', 'string', 'max:20'],
            'description'           => ['nullable', 'string', 'max:2000'],
            'purchase_price'        => ['required', 'numeric', 'min:0'],
            'selling_price'         => ['required', 'numeric', 'min:0'],
            'wholesale_price'       => ['nullable', 'numeric', 'min:0'],
            'is_refill'             => ['boolean'],
            'refill_price_per_ml'   => ['nullable', 'numeric', 'min:0', 'required_if:is_refill,true'],
            'is_active'             => ['boolean'],
            'image'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique'                      => 'SKU sudah digunakan oleh produk lain.',
            'barcode.unique'                  => 'Barcode sudah digunakan oleh produk lain.',
            'refill_price_per_ml.required_if' => 'Harga per ml wajib diisi jika produk adalah refill.',
        ];
    }
}
