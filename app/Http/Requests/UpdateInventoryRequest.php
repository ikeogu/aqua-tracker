<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
            'name' => 'nullable|string',
            'quantity' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'vendor' => 'nullable|string',
            'batch_id' => 'nullable|exists:batches,id',
            'size' => 'nullable|numeric',
        ];
    }
}
