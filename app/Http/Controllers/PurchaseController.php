<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Requests\CreatePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Purchase;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    //

    public function store(Request $request, Farm $farm, Harvest $harvest) : JsonResponse
    {
         /** @var User $user */
         $user = Auth::user();
         if ($user->hasRole(Role::VIEW_FARMS->value)) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
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

    public function update(Request $request, Farm $farm, Harvest $harvest) : JsonResponse
    {
         /** @var User $user */
         $user = Auth::user();
         if ($user->hasRole(Role::VIEW_FARMS->value)) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }

        $harvest = $farm->harvests()->find($harvest->id);
        $purchases =  Arr::map($request->data, function($purchase) use($harvest){
          return  Purchase::whereId($purchase['id'])->update(
                array_merge($purchase, ['harvest_id' => $harvest->id])
            );
        });


        return $this->success(
            message: 'Purchase updated successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }

    public function updatePurchase(Request $request, Purchase $purchase){

        $request->validate([
            'amount_paid' => ['required', 'numeric'],
            'to_balance' => ['required', 'numeric'],
            'status' => ['nullable']
        ]);

        $purchase->update([
            'amount_paid' => $request->amount_paid,
            'to_balance' => $request->to_balance,
            'status' => $request->status
        ]);

        return $this->success(
            message: 'Purchase updated successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy( Purchase $purchase) : JsonResponse
    {
         /** @var User $user */
         $user = Auth::user();
         if ($user->hasRole(Role::VIEW_FARMS->value)) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }

        $purchase->delete();

        return $this->success(
            message: 'Purchase deleted successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
