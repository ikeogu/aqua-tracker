<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Http\Requests\CreatePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Purchase;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PurchaseController extends Controller
{
    //

    public function store(Request $request, Farm $farm, Harvest $harvest) : JsonResponse
    {

        $harvest = $farm->harvests()->find($harvest->id);
        $purchases =  Arr::map($request->data, function($purchase) use($harvest, $farm){
          return  Purchase::create(
                array_merge($purchase, ['harvest_id' => $harvest->id, 'farm_id' => $farm->id])
            );
        });


        return $this->success(
            message: 'Purchase created successfully',
            data:  PurchaseResource::collection($purchases),
            code: HttpStatusCode::CREATED->value
        );
    }

    public function update(UpdatePurchaseRequest $request, Farm $farm, Harvest $harvest, Purchase $purchase) : JsonResponse
    {
        $harvest = $farm->harvests()->findOrFail($harvest->id);
        $purchase = $harvest->purchases()->findOrFail($purchase->id);

        $purchase->update($request->validated());

        return $this->success(
            message: 'Purchase created successfully',
            data: new PurchaseResource($purchase),
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }

    public function destroy(Farm $farm, Harvest $harvest, Purchase $purchase) : JsonResponse
    {
        $harvest = $farm->harvests()->findOrFail($harvest->id);
        $purchase = $harvest->purchases()->findOrFail($purchase->id);

        $purchase->delete();

        return $this->success(
            message: 'Purchase deleted successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
