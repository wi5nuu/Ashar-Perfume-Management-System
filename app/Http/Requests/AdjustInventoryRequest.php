<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        Gate::authorize('manage_inventory');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:add,subtract,set,correction',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required',
            'cost_per_unit' => 'nullable|numeric|min:0'
        ];

        if ($this->user()?->isOwner()) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        return $rules;
    }
}
