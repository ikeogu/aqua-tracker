<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    //

    public function __invoke(Request $request)
    {
        $request->validate([

            'password' => 'required|confirmed|min:8',
        ]);

        /** @var User $user */
        $user = Auth::user();


        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->success(
            message: 'Password reset successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
