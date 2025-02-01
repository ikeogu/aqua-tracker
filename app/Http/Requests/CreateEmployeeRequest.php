<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email',
            'role' => 'required|in:'.implode(',', [Role::getRoleTextName(Role::FARM_EMPLOYEE), Role::getRoleTextName(Role::FARM_ADMIN)]),
            'phone_number' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array

    {
        return [
            'role.in' => 'Please select a valid role from the list.' . implode(',', [Role::getRoleTextName(Role::FARM_EMPLOYEE), Role::getRoleTextName(Role::FARM_ADMIN)]),
        ];
    }
}
