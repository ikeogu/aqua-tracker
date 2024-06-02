<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Requests\ProfileSettingRequest;
use App\Http\Resources\UserResource;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //

    public function __invoke(ProfileSettingRequest $request) : JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data = $request->validated();

        $user->update([
            'first_name' => $data['first_name'] ?? $user->first_name,
            'last_name' => $data['last_name'] ?? $user->last_name,
            'email' => $data['email'] ?? $user->email,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
        ]);

        if ($request->hasFile('profile_picture')) {

            $user->addMediaFromRequest('profile_picture')->toMediaCollection('profile_picture');
        }

        return $this->success(
            message: 'Profile updated successfully',
            data: new UserResource($user),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

}
