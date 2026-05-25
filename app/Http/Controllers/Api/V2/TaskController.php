<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Task::class);

        // return TaskResource::collection(Task::all());
        return request()->user()
            ->tasks()
            ->handleSort(request()->query('sort_by') ?? 'time')
            ->with('priority')
            ->get()
            ->toResourceCollection();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        Gate::authorize('create', Task::class);
        
        $task = request()->user()->tasks()->create($request->validated());
        $task->load('priority');
        // return new TaskResource($task);
        return $task->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, Request $request)
    {
        Gate::authorize('view', $task);

        $task->load('priority');
        // all three syntax are valid
        // return new TaskResource($task);
        // return TaskResource::make($task);
        return $task->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());
        $task->load('priority');
        return $task->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, Request $request)
    {
        Gate::authorize('delete', $task);
        
        $task->delete();
        return response()->noContent();
    }
}
