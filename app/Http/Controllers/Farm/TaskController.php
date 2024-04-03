<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Farm;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(CreateTaskRequest $request, Farm $farm) : JsonResponse
    {

        $task =  $farm->tasks()->create($request->validated());

        return $this->success(
            message: 'Task created successfully',
            data: new TaskResource($task),
            code: 201
        );
    }

    public function update(Request $request, Farm $farm, Task $task) : JsonResponse
    {
        $farm->tasks()->findOrFail($task->id);
        $task->update($request->all());

        return $this->success(
            message: 'Task updated successfully',
            data: new TaskResource($task),
            code: 200
        );
    }

    public function destroy(Farm $farm, Task $task) : JsonResponse
    {
        $farm->tasks()->findOrFail($task->id);
        $task->delete();

        return $this->success(
            message: 'Task deleted successfully',
            data: new TaskResource($task),
            code: 200
        );
    }
}
