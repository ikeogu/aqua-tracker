<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Farm;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class InventoryController extends Controller
{
    //
    public function store(CreateInventoryRequest $request, Farm $farm) : JsonResponse
    {
        // Create a new inventory
        $inventory = $farm->inventories()->create(
            array_merge($request->validated(), ['status' => Status::INSTOCK->value ])
        );

        return $this->success(
            message: 'Inventory created successfully',
            data :  new InventoryResource($inventory),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function index(Request $request, Farm $farm) : JsonResponse
    {
        $inventories = $farm->inventories()->when($request->search, function (Builder $query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->orWhere('quantity', 'like', '%' . $request->search . '%')
                ->orWhere('price', 'like', '%' . $request->search . '%')
                ->orWhere('vendor', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%');

        })->paginate($request->per_page ?? 20);

        return $this->success(
            message: 'Inventories retrieved successfully',
            data: InventoryResource::collection($inventories)->response()->getData(true),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(Farm $farm, Inventory $inventory) : JsonResponse
    {
        $inventory = $farm->inventories()->find($inventory->id);

        if (!$inventory) {
            return $this->error(
                message: 'Inventory not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        return $this->success(
            message: 'Inventory retrieved successfully',
            data: new InventoryResource($inventory),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdateInventoryRequest $request, Farm $farm, Inventory $inventory) : JsonResponse
    {
        $inventory = $farm->inventories()->find($inventory->id);

        if (!$inventory) {
            return $this->error(
                message: 'Inventory not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        $inventory->update($request->validated());

        return $this->success(
            message: 'Inventory updated successfully',
            data: new InventoryResource($inventory),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(Farm $farm, Inventory $inventory) : JsonResponse
    {
        $inventory = $farm->inventories()->find($inventory->id);

        if (!$inventory) {
            return $this->error(
                message: 'Inventory not found',
                code: HttpStatusCode::NOT_FOUND->value
            );
        }

        $inventory->delete();

        return $this->success(
            message: 'Inventory deleted successfully',
            data: new InventoryResource($inventory),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

}
