<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBatchRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'unit_purchase' => ['required', 'numeric'],
            'price_per_unit' => ['required', 'numeric'],
            'amount_spent' => ['required', 'numeric'],
            'fish_specie' => ['required','string'],
            'fish_type' => ['required', 'required'],
            'vendor' => ['nullable', 'string'],
            'status' => ['required', 'string','in:sold out,in stock'],
            'date_purchased' => ['required', 'date']
        ];
    }
}
