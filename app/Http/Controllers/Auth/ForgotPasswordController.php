<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    //

    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        /** @var User $user */
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found'],
            ]);
        }

        $user->sendPasswordResetOtp();

        return $this->success(
            message: 'Password reset code has been sent to your email address',

            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([

            'code' => 'required|string',
        ]);

        // get user from otp code
        $otp =  OtpCode::where('otp', $request->code)->first();

        if (!$otp) {
            throw ValidationException::withMessages([
                'code' => ['Invalid code'],
            ]);
        }

        $user = $otp->user;

        $token = $user->createToken('auth_token')->plainTextToken;



        return $this->success(
            message: 'Password reset code has been verified successfully',
            data: [
                'user' => $user,
                'token' => $token,
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
