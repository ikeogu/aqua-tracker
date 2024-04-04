<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
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

            'description' => 'required|string',
            'total_amount' => 'required|numeric',
            'splitted_for_batch' => 'required|array',
            'splitted_for_batch.*batch_id ' => 'required|exists:batches,id',
            'splitted_for_batch.*amount' => 'required|numeric',
        ];
    }
}
