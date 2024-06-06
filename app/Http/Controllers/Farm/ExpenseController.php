<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    //

    public function store(CreateExpenseRequest $request, Farm $farm): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }

        $expense = $farm->expenses()->create(array_merge($request->validated(), ['splitted_for_batch' => $request->splitted_for_batch]));

        return $this->success(
            message: 'Expense created successfully',
            data: new ExpenseResource($expense),
            code: HttpStatusCode::CREATED->value
        );
    }

    public function update(UpdateExpenseRequest $request, Farm $farm, Expense $expense): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $expense = $farm->expenses()->findOrFail($expense->id);
        $expense->update(array_merge($request->validated(), ['splitted_for_batch' => json_encode($request->splitted_for_batch)]));

        return $this->success(
            message: 'Expense updated successfully',
            data: new ExpenseResource($expense),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(Farm $farm, Expense $expense): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $expense = $farm->expenses()->findOrFail($expense->id);
        $expense->delete();

        return $this->success(
            message: 'Expense deleted successfully',
            data: new ExpenseResource($expense),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function index(Request $request, Farm $farm): JsonResponse
    {

        $expenses = $farm->expenses()->when($request->search, function ($query) use ($request) {
            return $query->where('description', 'like', '%' . $request->search . '%')
                ->orWhere('total_amount', 'like', '%' . $request->search . '%');
        })->latest()->paginate($request->per_page ?? 20);

        return $this->success(
            message: 'Expenses retrieved successfully',
            data: ExpenseResource::collection($expenses)->response()->getData(),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
