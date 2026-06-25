<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'manager', 'owner']);
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:expense_categories,id',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'vendor'      => 'nullable|string|max:255',
            'type'        => 'required|in:operational,marketing,utility,salary,other',
            'date'        => 'required|date|before_or_equal:today',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Expense category is required.',
            'amount.min'           => 'Amount must be at least 1.',
            'date.before_or_equal' => 'Date cannot be in the future.',
            'proof_image.max'      => 'Proof image must not exceed 2MB.',
        ];
    }
}
