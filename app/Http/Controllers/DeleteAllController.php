<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Services\DeleteAllService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteAllController extends Controller
{
    //

    public function __construct(
        public DeleteAllService $deleteAllService
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "unathourized area.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $request->validate([
            'model' => 'required|string|in:tasks,ponds,farms,batches,customers,expenses,employees,inventories,harvests',
            'ids' => 'required|array',
        ]);



        $this->deleteAllService->execute($request->model, $request->ids);

        return $this->success(
            message: $request->model . " deleted successfully",
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }
}
