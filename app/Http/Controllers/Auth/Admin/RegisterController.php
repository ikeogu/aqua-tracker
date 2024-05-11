<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Enums\HttpStatusCode;
use App\Enums\Role as EnumsRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\AdminInviteMail;
use App\Models\Role;
use App\Enums\Role as EnumRole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as ModelsRole;

class RegisterController extends Controller
{
    //
    public function getRoles() : JsonResponse
    {
        $roles = [
            EnumsRole::ADMIN->value,
            EnumsRole::SUPER_ADMIN->value
        ];

        return $this->success(
            message:'Roles returned',
            data: $roles,
        );


    }
    public function __invoke(CreateAdminUserRequest $request) : JsonResponse
    {

        if(User::where('email',$request->email)->exists()){
            return $this->error(
                message:"email already exist",
                error:null,
                code:HttpStatusCode::BAD_REQUEST->value
            );
        }

        $pwd = Str::random(8);
         /** @var User $user */
         $user = User::create([
            'first_name' => null,
            'last_name' => null,
            'email' => $request->validated()['email'],
            'password' => Hash::make($pwd),

         ]);

         $role =ModelsRole::where('name',$request->validated()['role'] )->first();
         $user->assignRole($role);

        Mail::to($user->email)->send(new AdminInviteMail($pwd,$request->role));

        return $this->success(
            message: 'Admin User created',
            code: HttpStatusCode::CREATED->value,
            data: [
                'user' => new UserResource($user),
            ],
        );
    }
}
