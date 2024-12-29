<?php

namespace App\Http\Controllers\Farm;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Farm;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index(Request $request, Farm $farm): JsonResponse
    {

        $tasks = match ($request->type) {
            'active' => $farm->tasks()
                ->where('status', Status::ACTIVE->value)
                ->latest()->paginate($request->per_page ?? 10),
            'due' => $farm->tasks()->where('status', Status::DUE->value)
                ->orWhere('status', Status::OVERDUE->value)
                ->latest()->paginate($request->per_page ?? 10),
            'completed'  => $farm->tasks()
                ->where('status', Status::COMPLETED->value)
                ->latest()->paginate($request->per_page ?? 10),
            'pending'  => $farm->tasks()
                ->where('status', Status::PENDING->value)
                ->latest()->paginate($request->per_page ?? 10),
            default  => $farm->tasks()->latest()->paginate($request->per_page ?? 10),
        };

        $tasks = TaskResource::collection($tasks)->response()->getData(true);

        return $this->success(
            message: 'Tasks retrieved successfully',
            data: $tasks,
            code: 200
        );
    }
    public function store(CreateTaskRequest $request, Farm $farm): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }

        $data = $request->validated();
        $data['status'] = Status::PENDING->value;
        $task =  $farm->tasks()->create($data);

        return $this->success(
            message: 'Task created successfully',
            data: new TaskResource($task),
            code: 201
        );
    }

    public function update(Request $request, Farm $farm, Task $task): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $farm->tasks()->findOrFail($task->id);
        $task->update($request->all());

        return $this->success(
            message: 'Task updated successfully',
            data: new TaskResource($task),
            code: 200
        );
    }

    public function destroy(Farm $farm, Task $task): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        $farm->tasks()->findOrFail($task->id);
        $task->delete();

        return $this->success(
            message: 'Task deleted successfully',
            data: new TaskResource($task),
            code: 200
        );
    }
}
