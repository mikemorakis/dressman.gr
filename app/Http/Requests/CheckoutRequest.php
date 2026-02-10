<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255'],

            'shipping_first_name' => ['required', 'string', 'max:100'],
            'shipping_last_name' => ['required', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_address2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'size:2'],
            'shipping_phone' => ['nullable', 'string', 'max:30'],

            'billing_same_as_shipping' => ['sometimes', 'boolean'],

            'payment_method' => ['required', 'string', 'in:stripe,bank_transfer,store_pickup'],

            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if (! $this->boolean('billing_same_as_shipping')) {
            $rules['billing_first_name'] = ['required', 'string', 'max:100'];
            $rules['billing_last_name'] = ['required', 'string', 'max:100'];
            $rules['billing_address'] = ['required', 'string', 'max:255'];
            $rules['billing_address2'] = ['nullable', 'string', 'max:255'];
            $rules['billing_city'] = ['required', 'string', 'max:100'];
            $rules['billing_state'] = ['nullable', 'string', 'max:100'];
            $rules['billing_postal_code'] = ['required', 'string', 'max:20'];
            $rules['billing_country'] = ['required', 'string', 'size:2'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'shipping_first_name' => 'first name',
            'shipping_last_name' => 'last name',
            'shipping_address' => 'street address',
            'shipping_address2' => 'address line 2',
            'shipping_city' => 'city',
            'shipping_state' => 'state/region',
            'shipping_postal_code' => 'postal code',
            'shipping_country' => 'country',
            'shipping_phone' => 'phone number',
            'billing_first_name' => 'billing first name',
            'billing_last_name' => 'billing last name',
            'billing_address' => 'billing street address',
            'billing_address2' => 'billing address line 2',
            'billing_city' => 'billing city',
            'billing_state' => 'billing state/region',
            'billing_postal_code' => 'billing postal code',
            'billing_country' => 'billing country',
            'payment_method' => 'payment method',
            'notes' => 'order notes',
        ];
    }
}
