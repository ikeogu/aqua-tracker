<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    //

    public function store(Request $request, Farm $farm) : JsonResponse
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->hasRole(Role::VIEW_FARMS->value)) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        // Create a new beneficiary
        $request->validate([
            'harvest_customer_id' => 'required|exists:harvest_customers,id',
        ]);

        if($farm->beneficiaries()->where('harvest_customer_id', $request->harvest_customer_id)->exists()){
            $farm->beneficiaries()->delete($request->harvest_customer_id);
            return $this->error(
                message: 'Beneficiary removed',
                code: HttpStatusCode::BAD_REQUEST->value
            );
        }

        $beneficiary = $farm->beneficiaries()->create($request->all());

        return $this->success(
            message: 'Beneficiary created successfully',
            data :  new CustomerResource($beneficiary->harvestCustomer),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }


    public function index(Request $request, Farm $farm) : JsonResponse
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->cannot('view')) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        $beneficiaries = $farm->beneficiaries()->
        when($request->search, function ($query) use ($request) {
            return $query->where('harvest_customer_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('harvestCustomer', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
        })->latest()->get()->map(function ($beneficiary) {
            return [
                'id' => $beneficiary->harvestCustomer->id,
                'name' => $beneficiary->harvestCustomer->name,
                'phone_number' => $beneficiary->harvestCustomer->phone_number,
                'email' => $beneficiary->harvestCustomer->email,

            ];
        });
        return $this->success(
            message: 'Beneficiaries retrieved successfully',
            data: $beneficiaries,
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }


    public function destroy(Farm $farm,string $beneficiary) : JsonResponse
    {
         /** @var User $user */
         $user = auth()->user();
         if ($user->cannot('delete')) {
             return $this->error(
                 message: "Your current role does not permit this action, kindly contact the Admin.",
                 code: HttpStatusCode::FORBIDDEN->value
             );
         }
        $beneficiary = $farm->beneficiaries()->where('harvest_customer_id', $beneficiary)->first();
        if(!$beneficiary){
            return $this->error(
                message: 'Beneficiary not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }
        $beneficiary->delete();
        return $this->success(
            message: 'Beneficiary deleted successfully',
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
