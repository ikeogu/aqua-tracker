<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePondRequest extends FormRequest
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

            "name" => ["required", "string"],
            "type" => ["required", "string"],
            "holding_capacity" => ["required", "numeric"],
            "unit" => ["required", "numeric"],
            "size" => ["required", "numeric"],
            "feed_size" => ["required", "numeric"],
            "mortality_rate" => ["required", "numeric"],
            "batch_id" => ["required", "exists:batches,id"],
    
        ];
    }
}
