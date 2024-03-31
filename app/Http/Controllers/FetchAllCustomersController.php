<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Http\Resources\CustomerResource;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FetchAllCustomersController extends Controller
{
    //

    public function __invoke(Request $request, Farm $farm) : JsonResponse
    {

        $customers = $farm->harvestcustomers()->
             when($request->search, function ($query) use ($request) {
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
}
