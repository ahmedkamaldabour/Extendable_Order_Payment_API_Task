<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->input('method');

        $rules = [
            'method' => ['required', Rule::in(array_keys(config('payment.gateways', [])))],
        ];

        // Method-specific validation
        if ($method === 'credit_card' || $method === 'stripe') {
            $rules['card_number'] = 'required|string|size:16';
            $rules['expiry_month'] = 'required|integer|between:1,12';
            $rules['expiry_year'] = 'required|integer|min:' . date('Y');
            $rules['cvv'] = 'required|string|min:3|max:4';
        }

        if ($method === 'paypal') {
            $rules['email'] = 'required|email';
        }

        if ($method === 'stripe' && $this->has('token')) {
            // If token provided NO card details needed (working without Card)
            $rules = [
                'method' => ['required', Rule::in(array_keys(config('payment.gateways', [])))],
                'token' => 'required|string',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'method.required' => 'Payment method is required',
            'method.in' => 'Invalid payment method',
            'card_number.required' => 'Card number is required',
            'card_number.size' => 'Card number must be 16 digits',
        ];
    }
}
