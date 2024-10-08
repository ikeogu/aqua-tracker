<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFarmRequest;
use App\Http\Requests\UpdateFarmRequest;
use App\Http\Resources\FarmResource;
use App\Jobs\DeleteFarmJob;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FarmController extends Controller
{
    //

    public function store(CreateFarmRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $tenant = $request->user()->tenant;


        $farm = $tenant->farms()->create($request->validated());

        $farm->users()->attach($request->user(), [
            'role' => Role::FARM_TEAM_OWNER->value,
            'status' => 'active',
        ]);


        return $this->success(
            message: 'Farm created successfully',
            code: HttpStatusCode::CREATED->value,
            data: new FarmResource($farm)
        );
    }

    public function index(Request $request)
    {

        $tenants = $request->user()->tenants;

        $organizations = $tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'type' => 'tenant',
                'attributes' => [
                    'tenant_id' => $tenant->id,
                    'organization_name' => $tenant->organization_name,
                    'no_of_farms_owned' => $tenant->no_of_farms_owned,

                    //  'capital' => $tenant->capital
                ],
                'farms' => $tenant->farms->map(function ($farm) {
                    return [
                        'id' => $farm->id,
                        'name' => $farm->name,
                    ];
                }),
            ];
        });


        return $this->success(
            message: 'Farms retrieved successfully',
            data: [
                'organizations' => $organizations
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdateFarmRequest $request, Farm $farm): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $authorization = Gate::inspect('update', $farm);

        if ($authorization->denied()) {
            return $this->error(
                message: $authorization->message(),
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $farm->update($request->validated());

        return $this->success(
            message: 'Farm updated successfully',
            code: HttpStatusCode::SUCCESSFUL->value,
            data: new FarmResource($farm)
        );
    }

    public function destroy(Farm $farm): JsonResponse
    {

        /** @var User $user */
        $user = auth()->user();

        if (!$user->isFarmOwner($farm)) {
            return $this->error(
                message: 'Unauthorized',
                code: HttpStatusCode::BAD_REQUEST->value
            );
        }

        DeleteFarmJob::dispatch($farm);
        return $this->success(
            message: 'Farm deletion in progress',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
