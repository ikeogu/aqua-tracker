<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Requests\UpdateOrganazationRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OrganizationController extends Controller
{
    //

    public function __invoke(UpdateOrganazationRequest $request, Tenant $tenant) : JsonResponse

    {
        /** @var User $user */
        $user = auth()->user();

        if(!$user->hasRole(Role::ORGANIZATION_OWNER->value) && $user->tenant !== $tenant->id){
            $this->error(
                message:"unathorized action.",
                code:403
            );
        }

       $tenant->update($request->validated());

       return $this->success(
            message: 'Organization updated successfully',
            data: $tenant,
            code: HttpStatusCode::SUCCESSFUL->value
        );


    }
}
