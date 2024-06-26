<?php

namespace App\Http\Controllers\Auth;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SigninController extends Controller
{
    //

    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) { //@phpstan-ignore-line
            throw ValidationException::withMessages([
                'password' => ['Password mismatched'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        LoginLog::where('user_id',$user->id)->whereNull('logout_at')->update(['logout_at' => now()]);

        LoginLog::create([
            'user_id' => $user->id,
            'login_at' => now(),
        ]);

        return $this->success(
            message: 'User signed in successfully',
            code: HttpStatusCode::SUCCESSFUL->value,
            data: [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        );
    }
}
