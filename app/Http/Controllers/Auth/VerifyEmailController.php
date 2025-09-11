<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Otp;
use App\Enums\HttpStatusCode;
use App\Events\EmailVerified;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function resend(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = User::where('email', $request->email)->first();

        if ($user->canRequestNewOtpFor(Otp::EMAIL_VERIFICATION)) {
            $user->sendEmailVerificationOtp();
        }

        return $this->success(
            message: 'Email verification code has been sent to your email address',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        //$user = User::where('email', $request->email)->first();
        $user = Auth::user();

        $user->verifyOtpFor(Otp::EMAIL_VERIFICATION, $request->validated()['code']);

        event(new EmailVerified($user));

        return $this->success(
            message: 'Email has been verified successfully.',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
