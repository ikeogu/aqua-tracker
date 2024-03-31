<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
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
            'name' => ['nullable', 'string'],
            'unit_purchase' => ['nullable', 'numeric'],
            'price_per_unit' => ['nullable', 'numeric'],
            'amount_spent' => ['nullable', 'numeric'],
            'fish_specie' => ['nullable','string'],
            'fish_type' => ['nullable', 'nullable'],
            'vendor' => ['nullable', 'string'],
            'status' => ['nullable', 'string','in:sold out, in stock'],
            'date_purchased' => ['nullable', 'date']
        ];
    }
}
