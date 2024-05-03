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

class FarmController extends Controller
{
    //

    public function store(CreateFarmRequest $request) : JsonResponse
    {
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

    public function update(UpdateFarmRequest $request, Farm $farm) : JsonResponse
    {
        $farm->update($request->validated());

        return $this->success(
            message: 'Farm updated successfully',
            code: HttpStatusCode::SUCCESSFUL->value,
            data: new FarmResource($farm)
        );
    }

    public function destroy(Farm $farm) : JsonResponse
    {

        DeleteFarmJob::dispatch($farm);
        return $this->success(
            message: 'Farm deletion in progress',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}

