<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseRequest extends FormRequest
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
            //

            'harvest_customer_id' => 'required|string|exists:harvest_customers,id',
            'quantity' => 'required|numeric',
            'pieces' => 'required|numeric',
            'price_per_unit' => 'required|numeric',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'size' => 'required|numeric',

        ];
    }
}
