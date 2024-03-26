<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Closure;
class SignupRequest extends FormRequest
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
            'email' => ['required', 'email', function (string $attribute, string $value, Closure $fail) {
                $user = User::where('email', $value)->first();

                if ($user?->isCreator() && $user?->fully_onboarded) {
                    $fail('An account already exists with this email address');
                }
            }],
            'password' => 'required|string|min:8|confirmed',

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
             'email.required' => 'Enter email address',
             'email.email' => 'Email address is invalid',
         ];
     }
}
