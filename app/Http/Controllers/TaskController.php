<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkTaskDoneRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskShowResource;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{

    /**
     * Display a listing of the tasks
     * @param TaskRepository $repository
     * @return AnonymousResourceCollection
     */
    public function index(TaskRepository $repository): AnonymousResourceCollection
    {
        return TaskIndexResource::collection($repository->getTasksByAuthor());
    }

    /**
     * Store a newly created Task in storage/DB
     * @param StoreTaskRequest $request
     * @param TaskRepository $repository
     * @return TaskShowResource
     */
    public function store(StoreTaskRequest $request, TaskRepository $repository): TaskShowResource
    {
        return new TaskShowResource($repository->create($request->validated()));
    }

    /**
     * Display the specified Task
     * @param Task $task
     * @return TaskShowResource|JsonResponse
     */
    public function show(Task $task): TaskShowResource|JsonResponse
    {
        return new TaskShowResource($task);
    }

    /**
     * Update the specified Task in DB
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @param TaskRepository $repository
     * @return JsonResponse|TaskShowResource
     */
    public function update(UpdateTaskRequest $request, Task $task, TaskRepository $repository): JsonResponse|TaskShowResource
    {
        return new TaskShowResource($repository->update($task, $request->validated()));
    }

    /**
     * Remove the specified Task from DB
     * @param Task $task
     * @param TaskRepository $repository
     * @return JsonResponse
     */
    public function destroy(Task $task, TaskRepository $repository): JsonResponse
    {
        return response()->json($repository->delete($task));
    }

    /**
     * Mark Task as 'done'
     * @param MarkTaskDoneRequest $request
     * @param Task $task
     * @param TaskRepository $repository
     * @return TaskShowResource
     */
    public function markTaskDone(MarkTaskDoneRequest $request, Task $task, TaskRepository $repository): TaskShowResource
    {
        return new TaskShowResource($repository->update($task, $request->validated()));
    }

    /**
     * Get tasks collection by filters
     * @param
     */
    public function getFilteredCollection($filters)
    {

    }

}
