<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
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

            'quantity' => 'nullable|numeric',
            'pieces' => 'nullable|numeric',
            'price_per_unit' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'status' => 'nullable|string',
            'size' => 'nullable|numeric',

        ];
    }
}
