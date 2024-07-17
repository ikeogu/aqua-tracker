<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Exports\PurchaseExport;
use App\Http\Resources\CustomerResource;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FetchAllCustomersController extends Controller
{
    //

    public function __invoke(Request $request, Farm $farm) : JsonResponse|BinaryFileResponse
    {

        $customers = $farm->harvestcustomers()->
             when($request->search, function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });

           // dd($request->all());
            if ($request->has('export') && $request->export !== null) {
                try {
                    return Excel::download(new PurchaseExport($customers->get()), 'customers-export-' . date('Ymdhis') . '.csv');
                    //@codeCoverageIgnoreStart
                } catch (\Throwable $exception) {
                    Log::debug($exception->getMessage());
                    return $this->error(Response::HTTP_SERVICE_UNAVAILABLE);
                }
                //@codeCoverageIgnoreEnd
            }
            $customers = $customers->paginate($request->per_page ?? 10);

            return $this->success(
                message: 'Customers retrieved successfully',
                data: CustomerResource::collection($customers)->response()->getData(true),
                code: HttpStatusCode::SUCCESSFUL->value
            );
    }
}
