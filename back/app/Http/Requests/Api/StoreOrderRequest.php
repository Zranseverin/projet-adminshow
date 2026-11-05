<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer' => ['required', 'array'],
            'customer.first_name' => ['required', 'string', 'max:80'],
            'customer.last_name' => ['required', 'string', 'max:80'],
            'customer.email' => ['nullable', 'email', 'max:180'],
            'customer.phone' => ['nullable', 'string', 'max:60'],

            'shipping' => ['required', 'array'],
            'shipping.address' => ['required', 'string', 'max:255'],
            'shipping.address_extra' => ['nullable', 'string', 'max:255'],
            'shipping.postal_code' => ['nullable', 'string', 'max:30'],
            'shipping.city' => ['required', 'string', 'max:100'],
            'shipping.country' => ['required', 'string', 'max:100'],
            'shipping.method' => ['nullable', 'string', 'max:80'],
            'shipping.total' => ['nullable', 'numeric', 'min:0'],

            'payment' => ['required', 'array'],
            'payment.method' => ['required', 'string', 'max:80'],
            'payment.reference' => ['nullable', 'string', 'max:120'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'integer', 'exists:books,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.format' => ['nullable', 'string', 'max:80'],
        ];
    }
}
