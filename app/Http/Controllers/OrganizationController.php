<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Requests\UpdateOrganazationRequest;
use App\Models\Farm;
use App\Models\SubscribedPlan;
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
         if ($user->cannot('edit')) {
             return $this->error(
                 message: "unathourized area.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
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

    public function checkSubscription(Farm $farm) : JsonResponse
    {
        $response = SubscribedPlan::where('tenant_id', $farm->tenant->id)->where('status', 'active')->exists();

        return $this->success(
            message: 'Organization subscription status',
            data: [
                'active_subscription' => $response
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
