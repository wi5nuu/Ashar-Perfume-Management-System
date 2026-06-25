<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        Gate::authorize('manage_coupons');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $couponId = $this->route('coupon') ? $this->route('coupon')->id : null;

        return [
            'code' => 'required|string|unique:coupons,code,' . $couponId,
            'type' => 'required|in:discount,bonus,cashback,other',
            'value' => 'required|numeric|min:0',
            'is_percentage' => 'boolean',
            'expiration_date' => 'required|date',
            'max_usage' => 'required|integer|min:1',
        ];
    }
}
