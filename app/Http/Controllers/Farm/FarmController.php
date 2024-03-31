<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFarmRequest;
use App\Http\Resources\FarmResource;
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
        $tenant = $request->user()->tenant;
        $farms = $tenant->farms()->get();

        return $this->success(
            message: 'Farms retrieved successfully',
            data: FarmResource::collection($farms)
        );
    }
}

