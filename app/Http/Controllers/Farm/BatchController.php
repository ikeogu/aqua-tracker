<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Models\Farm;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BatchController extends Controller
{
    //
    public function store(CreateBatchRequest $request, Farm $farm) : JsonResponse
    {

        /** @var Batch $batch */
        $batch = $farm->batches()->create($request->validated());

        return $this->success(
            message:"Batch Created Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }

    public function index(Request $request, Farm $farm): JsonResponse
    {

        $batches = QueryBuilder::for(Batch::class)
        ->where('farm_id', $farm->id)
        ->allowedFilters(['fish_specie', 'fish_type', 'date','status'])
        ->when($request->search && !empty($request->search), function(Builder $query) use($request){
            return $query->where('name', 'like', '%'. $request->search . '%')
                ->orWhere('unit_purchase', 'like', '%'. $request->search . '%')
                ->orWhere('price_per_unit', 'like', '%'. $request->search . '%')
                ->orWhere('amount_spent', 'like', '%'. $request->search . '%')
                ->orWhere('vendor', 'like', '%'. $request->search . '%')
                ->orWhere('status', 'like', '%'. $request->search . '%')
                ->orWhere('date', 'like', '%'. $request->search . '%');

        })->paginate($request->per_page ?? 20);

        return $this->success(
            message:"Batch returned Successfully",
            data: BatchResource::collection($batches),
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }




    public function update(UpdateBatchRequest $request,Farm $farm,  Batch $batch) : JsonResponse
    {
        $batch = $farm->batches()->find($batch->id);

        $batch->update($request->validated());

        return $this->success(
            message:"Batch Updated Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(Farm $farm, Batch $batch) : JsonResponse
    {
        $batch = $farm->batches()->find($batch->id);
        return $this->success(
            message:"Batch fetched Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy (Farm $farm, Batch $batch) : JsonResponse
    {
        $batch = $farm->batches()->find($batch->id);
        $batch->delete();

        return $this->success(
            message:"Batch deleted Successfully",
            data: new BatchResource($batch),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

}
