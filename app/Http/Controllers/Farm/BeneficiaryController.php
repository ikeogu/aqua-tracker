<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
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
        // Create a new beneficiary
        $request->validate([
            'harvest_customer_id' => 'required|exists:harvest_customers,id',
        ]);

        if($farm->beneficiaries()->where('harvest_customer_id', $request->harvest_customer_id)->exists()){
            return $this->error(
                message: 'Beneficiary already exists',
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

        $beneficiaries = $farm->beneficiaries()->
        when($request->search, function ($query) use ($request) {
            return $query->where('harvest_customer_id', 'like', '%' . $request->search . '%')
                ->orWhereHas('harvestCustomer', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
        })->get()->map(function ($beneficiary) {
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
}
