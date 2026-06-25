<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        Gate::authorize('manage_customers');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:customers,nik,' . $customerId,
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|unique:customers,phone,' . $customerId,
            'email' => 'nullable|email|unique:customers,email,' . $customerId,
            'type' => 'required|in:retail,wholesale,vip',
            'address' => 'nullable|string',
            'aroma_preferences' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
