<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignupController extends Controller
{
    public function __invoke(SignupRequest $request) : JsonResponse
    {
        
         /** @var User $user */
         $user = User::updateOrCreate(
            $request->validated(),
            $request->validated()
        );

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationOtp();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->success(
            message: 'Email verification OTP sent',
            code: HttpStatusCode::CREATED->value,
            data: new UserResource($user), //@phpstan-ignore-line
        );
    }
}
