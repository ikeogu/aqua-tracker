<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\createHarvestRequest;
use App\Http\Requests\UpdateHarvestRequest;
use App\Http\Resources\HarvestResource;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Inventory;
use App\Models\Purchase;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HarvestController extends Controller
{
    //
    public function index(Request $request, Farm $farm): JsonResponse
    {
        $harvests = $farm->harvests()->when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('consultant', 'like', '%' . $request->search . '%')
                ->orWhereHas('batch', function (Builder $query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
        })
            ->paginate($request->per_page ?? 20)
            ->through(function ($harvest) {
                return [
                    'id' => $harvest->id,
                    'date' => $harvest->created_at->format('d/m/Y'),
                    'name' => $harvest->name,
                    'consultant' => $harvest->consultant,
                    'batch' => [
                        'id' => $harvest->batch_id,
                        'name' => $harvest->batch->name,
                    ],
                    'total_sales' => number_format($harvest->purchases()->sum('amount'), 2),

                ];
            });


        $totalHarvest = Purchase::whereIn('harvest_id', $farm->harvests()->pluck('harvests.id')->toArray())->sum('amount');
        $batch_ids = $farm->harvests()->pluck('batch_id')->unique()->toArray();

        $inventories = $farm->inventories()->whereIn('batch_id', $batch_ids)->sum('amount');

        $expenses = [];

        foreach ($batch_ids as $batch) {
            $expenses[] = Expense::where('farm_id', $farm->id)
                ->whereJsonContains('splitted_for_batch', ['batch_id' => $batch])
                ->get()
                ->pluck('splitted_for_batch')
                ->flatten()
                ->toArray();
        }
        $expenses = Arr::flatten($expenses);

        $totalExpenses = array_sum(array_filter($expenses, function ($item) {
            return is_numeric($item);
        }));


        $amount_spent = Batch::whereId($batch_ids)->sum('amount_spent');

        $data = [
            'total_harvest' => intval($totalHarvest),
            'total_capital' => $inventories + $totalExpenses + $amount_spent,
            'total_profit' =>  $totalHarvest - ($inventories + $totalExpenses + $amount_spent),
            'expenses' => $totalExpenses,
            'harvests' => $harvests,

        ];

        return $this->success(
            message: 'Harvests retrieved successfully',
            data: $data,
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(Farm $farm, Harvest $harvest): JsonResponse
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

    public function store(createHarvestRequest $request, Farm $farm): JsonResponse
    {
        $harvest = $farm->harvests()->create($request->validated());

        $batch = $farm->batches()->find($request->batch_id);
        $batch->update(['status' => 'sold out']);

        $farm->inventories()->where('batch_id', $request->batch_id)->update(['status' => Status::SOLDOUT->value]);


        return $this->success(
            message: 'Harvest created successfully',
            data: new HarvestResource($harvest),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdateHarvestRequest $request, Farm $farm, Harvest $harvest): JsonResponse
    {

        $harvest->update($request->validated());

        return $this->success(
            message: 'Harvest updated successfully',
            data: new HarvestResource($harvest),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
