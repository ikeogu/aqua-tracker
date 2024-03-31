<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\HarvestCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HarvestCustomerController extends Controller
{

    public function index(Request $request, Farm $farm, Harvest $harvest) : JsonResponse
    {
        $harvest = $farm->harvests()->find($harvest->id);
        $customers = $harvest->customers()->when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone_number', 'like', '%' . $request->search . '%');
        })->paginate($request->per_page ?? 20);

        return $this->success(
            message: 'Customers retrieved successfully',
            data: CustomerResource::collection($customers)->response()->getData(),
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }

    public function show(Farm $farm, Harvest $harvest, HarvestCustomer $customer) : JsonResponse
    {
        $harvest = $farm->harvests()->find($harvest->id);
        $customer = $harvest->customers()->find($customer->id);

        if (!$customer) {
            return $this->error(
                message: 'Customer not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        return $this->success(
            message: 'Customer retrieved successfully',
            data: new CustomerResource($customer),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function store(CreateCustomerRequest $request, Farm $farm, Harvest $harvest) : JsonResponse
    {
        $harvest = $farm->harvests()->find($harvest->id);

        $customer = $harvest->customers()->create(
            array_merge($request->validated(), ['farm_id' => $farm->id])
        );

        return $this->success(
            message: 'Customer created successfully',
            data: new CustomerResource($customer),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdateCustomerRequest $request, Farm $farm, Harvest $harvest, HarvestCustomer $customer) : JsonResponse
    {
        $harvest = $farm->harvests()->find($harvest->id);
        $customer = $harvest->customers()->find($customer->id);

        if (!$customer) {
            return $this->error(
                message: 'Customer not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        $customer->update($request->validated());

        return $this->success(
            message: 'Customer updated successfully',
            data: new CustomerResource($customer),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
