<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePondRequest;
use App\Http\Requests\UpdatePondRequest;
use App\Http\Resources\PondResource;
use App\Models\Farm;
use App\Models\Pond;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PondController extends Controller
{
    //

    public function index(Request $request, Farm $farm): JsonResponse
    {
        
        $ponds = $farm->ponds()->latest()
            ->latest()->paginate($request->per_page ?? 20);

        return $this->success(
            message: "Ponds fetched Successfully",
            data: PondResource::collection($ponds)->response()->getData(true),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function store(CreatePondRequest $request, Farm $farm): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "unathourized area.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $pond = $farm->ponds()->create($request->validated());

        return $this->success(
            message: "Pond Created Successfully",
            data: new PondResource($pond),
            code: HttpStatusCode::CREATED->value
        );
    }

    public function show(Pond $pond, Farm $farm): JsonResponse
    {

        $pond = $farm->ponds()->find($pond->id);

        return $this->success(
            message: "Pond fetched Successfully",
            data: new PondResource($pond),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdatePondRequest $request, Farm $farm, Pond $pond): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "unathourized area.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }

        $farm->ponds()->find($pond->id);

        $pond->update($request->validated());

        return $this->success(
            message: "Pond Updated Successfully",
            data: new PondResource($pond),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(Farm $farm, Pond $pond): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "unathourized area.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $farm->ponds()->find($pond->id);
        $pond->delete();

        return $this->success(
            message: "Pond Deleted Successfully",
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }


    public function farmStatictics(Farm $farm): JsonResponse
    {

        $ponds = $farm->ponds()->get();

        $totalPonds = $ponds->count();

        $totalPondsCapacity = $ponds->sum('holding_capacity');

        $totalPondsStocked = $ponds->sum('unit');

        $totalPondsMortality = $ponds->sum('mortality_rate');
        $totalFeedSize = $ponds->sum('feed_size');


        return $this->success(
            message: "Farm Statistics fetched Successfully",
            data: [
                'total_ponds' => $totalPonds,
                'total_capacity' => $totalPondsCapacity,
                'total_stocked' => $totalPondsStocked,
                'total_mortality' => $totalPondsMortality,
                'total_feed_size' => $totalFeedSize,
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
