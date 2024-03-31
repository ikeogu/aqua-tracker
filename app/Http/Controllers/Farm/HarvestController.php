<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\createHarvestRequest;
use App\Http\Resources\HarvestResource;
use App\Models\Farm;
use App\Models\Harvest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    //
    public function index(Request $request, Farm $farm) : JsonResponse
    {
        $harvests = $farm->harvests()->when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('consultant', 'like', '%' . $request->search . '%')
                ->orWhere('batch_id', 'like', '%' . $request->search . '%');
        })->paginate($request->per_page ?? 20);

        return $this->success(
            message: 'Harvests retrieved successfully',
            data: HarvestResource::collection($harvests)->response()->getData(),
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }

    public function show(Farm $farm, Harvest $harvest) : JsonResponse
    {
        $harvest = $farm->harvests()->find($harvest->id);

        if (!$harvest) {
            return $this->error(
                message: 'Harvest not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        return $this->success(
            message: 'Harvest retrieved successfully',
            data: new HarvestResource($harvest),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function store(createHarvestRequest $request, Farm $farm) : JsonResponse
    {
        $harvest = $farm->harvests()->create($request->validated());

        return $this->success(
            message: 'Harvest created successfully',
            data: new HarvestResource($harvest),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }


}
