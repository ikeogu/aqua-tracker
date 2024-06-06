<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Exports\PurchaseExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\HarvestCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HarvestCustomerController extends Controller
{

    public function index(Request $request, Farm $farm, Harvest $harvest): mixed
    {

        $harvest = $farm->harvests()->find($harvest->id);
        $customers = $harvest->customers()->when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone_number', 'like', '%' . $request->search . '%');
        });


        if ($request->has('export') && $request->export !== null) {
            try {
                return Excel::download(new PurchaseExport($customers), 'customers-export-' . date('Ymdhis') . '.csv');
                //@codeCoverageIgnoreStart
            } catch (\Throwable $exception) {
                Log::error($exception->getMessage());
                return $this->error(Response::HTTP_SERVICE_UNAVAILABLE)->respondWithError($exception->getMessage());
            }
            //@codeCoverageIgnoreEnd
        }
        $customers = $customers->latest()->paginate($request->per_page ?? 10);

        return $this->success(
            message: 'Customers retrieved successfully',
            data: CustomerResource::collection($customers)->response()->getData(),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(Farm $farm, Harvest $harvest, HarvestCustomer $customer): JsonResponse
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

    public function store(CreateCustomerRequest $request, Farm $farm, Harvest $harvest): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
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

    public function update(UpdateCustomerRequest $request, Farm $farm, Harvest $harvest, HarvestCustomer $customer): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
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
