<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InviteTeamMemberController extends Controller
{
    //

   // public function __invoke(Request $request, Farm $farm) : JsonResponse
    //{
      /*   $request->validate([
            'email' => 'required|email',
            'role' => 'required|string',
            'farm_id' => 'required|exists:farms,id',
        ]);

        $farm = auth()->user()->farms()->find($request->farm_id);

        if (!$farm) {
            return response()->json([
                'message' => 'Farm not found',
                'code' => 404
            ], 404);
        }

        $user = $farm->users()->where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'message' => 'User already exists in farm',
                'code' => 400
            ], 400);
        }

        $farm->users()->attach($request->farm_id, [
            'email' => $request->email,
            'role' => $request->role,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'User invited successfully',
            'code' => 200
        ], 200);
    } */
}
