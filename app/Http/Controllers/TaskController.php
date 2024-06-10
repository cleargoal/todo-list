<?php

namespace App\Http\Controllers;

use App\Dto\TaskCreateDto;
use App\Dto\TaskUpdateDto;
use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskShowResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{

    private TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the tasks
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TaskIndexResource::collection($this->service->getTasksByAuthor(request()->user()->id));
    }

    /**
     * Store a newly created Task in storage/DB
     * @param StoreTaskRequest $request
     * @return TaskShowResource
     */
    public function store(StoreTaskRequest $request): TaskShowResource
    {
        $validated = $request->validated();
        $taskDto = new TaskCreateDto(
            $request->user()->id,
            $validated['parent_id'],
            $validated['title'],
            $validated['description'],
            StatusEnum::from($validated['status']),
            PriorityEnum::from($validated['priority']),
        );
        return new TaskShowResource($this->service->create($taskDto));
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
     * @return JsonResponse|TaskShowResource
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse|TaskShowResource
    {
        $validated = $request->validated();
        $taskDto = new TaskUpdateDto(
            $validated['parent_id'] ?? null,
            $validated['title'] ?? null,
            $validated['description'] ?? null,
            PriorityEnum::from($validated['priority']) ?? null,
        );
        return new TaskShowResource($this->service->update($task, $taskDto));
    }

    /**
     * Remove the specified Task from DB
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        return response()->json($this->service->delete($task));
    }

    /**
     * Mark Task as 'done'
     * @param Task $task
     * @return TaskShowResource|JsonResponse
     */
    public function markTaskDone(Task $task): TaskShowResource|JsonResponse
    {
        $response = $this->service->markTaskDone($task);
        if (gettype($response) === 'array') {
            return response()->json($response);
        }
        return new TaskShowResource($response);
    }

    /**
     * Get tasks collection by filters
     * @param
     * @queryParam filter[language] Filter the books to a specific language. filter[language]=en
     * @queryParam filter[pages] Filter the books to those with a certain amount of pages. filter[pages]=1000
     * @queryParam filter[published_at] Filter the books to those published on a certain date. filter[published_at]=12-12-1992
     */
    public function getFilteredCollection($filters)
    {

    }

    /**
     * Get the tree of one of user's task by ID
     * @param Task $task
     * @return JsonResponse
     */
    public function getUserTaskTree(Task $task): JsonResponse
    {
        return response()->json($this->service->getTaskTree($task->id));
    }
}
