<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Models\Farm;
use Illuminate\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Spatie\QueryBuilder\QueryBuilder;

class BatchController extends Controller
{
    //
    public function store(CreateBatchRequest $request, Farm $farm): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        /** @var Batch $batch */
        $batch = $farm->batches()->create($request->validated());

        return $this->success(
            message: "Batch Created Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function index(Request $request, Farm $farm): JsonResponse
    {

        $batches = QueryBuilder::for(Batch::class)
            ->where('farm_id', $farm->id)
            ->allowedFilters(['fish_specie', 'fish_type', 'date_purchased', 'status'])
            ->when($request->search && !empty($request->search), function (Builder $query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('unit_purchase', 'like', '%' . $request->search . '%')
                    ->orWhere('price_per_unit', 'like', '%' . $request->search . '%')
                    ->orWhere('amount_spent', 'like', '%' . $request->search . '%')
                    ->orWhere('vendor', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('date_purchased', 'like', '%' . $request->search . '%');
            })->
            where('status', Status::INSTOCK->value)
            ->latest()->get();

        return $this->success(
            message: "batches retrived",
            data: BatchResource::collection($batches),
            code: 200
        );
    }

    public function getBatches(Request $request, Farm $farm): JsonResponse
    {

        $batches = QueryBuilder::for(Batch::class)
            ->where('farm_id', $farm->id)
            ->allowedFilters(['fish_specie', 'fish_type', 'date_purchased', 'status'])
            ->when($request->search && !empty($request->search), function (Builder $query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('unit_purchase', 'like', '%' . $request->search . '%')
                    ->orWhere('price_per_unit', 'like', '%' . $request->search . '%')
                    ->orWhere('amount_spent', 'like', '%' . $request->search . '%')
                    ->orWhere('vendor', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('date_purchased', 'like', '%' . $request->search . '%');
            })->latest()->paginate($request->per_page ?? 10);

        $response = BatchResource::collection($batches)->response()->getData(true);
        $data = $response['data'];
        $links = $response['links'];
        $meta = $response['meta'];

        return response()->json([
            'message' => "Batches retrived successfully",
            'status' => 200,
            'data' => $data,
            'links' => $links,
            'meta' => $meta,
        ]);
    }


    public function update(UpdateBatchRequest $request, Farm $farm,  Batch $batch): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

       $authorization = FacadesGate::inspect('update', $batch);

        if ($authorization->denied()) {
            return $this->error(
                message: $authorization->message(),
                code: HttpStatusCode::FORBIDDEN->value
            );
        }


        $batch = $farm->batches()->find($batch->id);

        $batch->update($request->validated());

        if($batch->status === Status::SOLDOUT->value){
            $batch->inventories->update(['status' => Status::SOLDOUT->value]);
        }

        return $this->success(
            message: "Batch Updated Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(Farm $farm, Batch $batch): JsonResponse
    {

        $batch = $farm->batches()->find($batch->id);
        return $this->success(
            message: "Batch fetched Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(Farm $farm, Batch $batch): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $batch = $farm->batches()->find($batch->id);
        $batch->delete();

        return $this->success(
            message: "Batch deleted Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}