<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    //

    public function __invoke(Request $request) : JsonResponse
    {
        $user = auth()->user();
        //continue from here
        LoginLog::where('user_id', $user->id)->latest()->first()->update(['login_at' => now()]);

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
