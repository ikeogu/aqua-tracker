<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardFarmerRequest extends FormRequest
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
            'organization_name' => 'required|string',
            'no_of_farms_owned' => 'required|integer',
            'capital' => 'required|numeric',
            'team_members' => 'nullable|array',
            'team_members.*' => 'email',
        ];
    }
}
