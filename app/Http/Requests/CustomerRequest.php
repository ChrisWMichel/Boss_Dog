<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'status' => ['required', new Enum(\App\Enums\CustomerStatus::class)],
            
            // Make shipping address fields nullable
            'shipping.address1' => ['nullable', 'string', 'max:255'],
            'shipping.address2' => ['nullable', 'string', 'max:255'],
            'shipping.city' => ['nullable', 'string', 'max:255'],
            'shipping.state' => ['nullable', 'string', 'max:255'],
            'shipping.zip_code' => ['nullable', 'string', 'max:255'],
            'shipping.country_code' => ['nullable', 'exists:countries,code'],
    
            // Make billing address fields nullable
            'billing.address1' => ['nullable', 'string', 'max:255'],
            'billing.address2' => ['nullable', 'string', 'max:255'],
            'billing.city' => ['nullable', 'string', 'max:255'],
            'billing.state' => ['nullable', 'string', 'max:255'],
            'billing.zip_code' => ['nullable', 'string', 'max:255'],
            'billing.country_code' => ['nullable', 'exists:countries,code'],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'status' => 'Status',
            'billing.address1' => 'Billing Address 1',
            'billing.city' => 'Billing City',
            'billing.state' => 'Billing State',
            'billing.zip_code' => 'Billing Zip Code',
            'billing.country_code' => 'Billing Country Code',
            'shipping.address1' => 'Shipping Address 1',
            'shipping.city' => 'Shipping City',
            'shipping.state' => 'Shipping State',
            'shipping.zip_code' => 'Shipping Zip Code',
            'shipping.country_code' => 'Shipping Country Code',
        ];
    }
}
