<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionPlanRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'unique:subscription_plans,title'],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['nullable', 'numeric'],
            'duration' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string', 'in:free,paid'],
            'discount' => ['nullable', 'numeric'],
            'limited_to' => ['nullable']
        ];
    }
}
