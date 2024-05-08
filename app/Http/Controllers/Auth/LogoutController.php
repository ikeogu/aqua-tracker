<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    //

    public function __invoke(Request $request) : JsonResponse
    {
        $user = auth()->user();
        //continue from here
        $user->
        $request->user()->tokens()->delete();


        return response()->json(['message' => 'Logged out successfully']);
    }
}
