<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePondRequest extends FormRequest
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
            "name" => ["nullable", "string"],
            "type" => ["nullable", "string"],
            "holding_capacity" => ["nullable", "numeric"],
            "unit" => ["nullable", "numeric"],
            "size" => ["nullable", "numeric"],
            "feed_size" => ["nullable", "numeric"],
            "mortality_rate " => ["nullable", "numeric"],
            "batch_id" => ["", "exists:batches,id"],
            "amount_paid" => ['nullable', 'numeric'],
            "to_balance" => ['nullable', 'numeric']

        ];
    }
}