<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'cashier', 'manager', 'owner']);
    }

    public function rules(): array
    {
        return [
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,qris,transfer,ewallet,debit_card,credit_card',
            'notes'          => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'           => 'Payment amount must be at least 1.',
            'payment_method.in'    => 'Invalid payment method. Allowed: cash, qris, transfer, ewallet, debit_card, credit_card.',
        ];
    }
}
