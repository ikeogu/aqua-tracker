<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role as EnumsRole;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    //

    public function index(Request $request) : JsonResponse
    {
        $request->validate(
            [
                'type' => 'required|in:employee,team'
            ]
        );

        $roles = match($request->type) {
            'team' => Role::with('permissions')->whereIn('name',
                [ EnumsRole::FARM_ADMIN->value, EnumsRole::EDIT_FARMS->value, EnumsRole::VIEW_FARMS->value]
            )->get(),
            'employee' =>  Role::whereIn('name', [ EnumsRole::FARM_EMPLOYEE->value])->get(),
            default => ''
        };

        return $this->success(
            message:"Roles retrived",
            data: RoleResource::collection($roles),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
