<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Exceptions\TaskAlreadyDoneException;
use App\Exceptions\TaskDeletionException;
use App\Exceptions\TaskHasUncompletedChildrenException;
use App\Http\Requests\FiltersTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskShowResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(
 *     title="Tasks Management API",
 *     version="0.1"
 * )
 */

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $service)
    {
    }

    /**
     * @return AnonymousResourceCollection
     * @OA\Get(
     *     path='/tasks',
     *     summary='Display user's tasks list',
     *     tags={'task'},
     *     description='Tasks',
     *     operationId='index',
     *     @OA\Parameter(
     *         name='tags',
     *         in='query',
     *         description='Tags to filter by',
     *         required=true,
     *         @OA\Schema(
     *             type='array',
     *             @OA\Items(type='string'),
     *         ),
     *         style='form'
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description='successful operation',
     *         @OA\Schema(
     *             type='array',
     *             @OA\Items(ref='#/components/schemas/Task')
     *         ),
     *     ),
     *     @OA\Response(
     *         response='400',
     *         description='Invalid tag value',
     *     ),
/     *     deprecated=true
     *  )
     */
    public function index(): AnonymousResourceCollection
    {
        return TaskIndexResource::collection($this->service->getTasksByUserId(request()->user()->id));
    }

    /**
     * Get the tree of one of user's task by ID
     * @param Task $task
     * @return JsonResponse
     */
    public function getUserTaskTree(Task $task): JsonResponse
    {
        return response()->json($this->service->getTaskTree(request()->user()->id, $task->id));
    }

    /**
     * Store a newly created Task in DB
     * @param StoreTaskRequest $request
     * @return TaskShowResource
     */
    public function store(StoreTaskRequest $request): TaskShowResource
    {
        return new TaskShowResource($this->service->create($request->toDto()));
    }

    /**
     * Display the specified Task
     * @param Task $task
     * @return TaskShowResource
     */
    public function show(Task $task): TaskShowResource
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
        return new TaskShowResource($this->service->update($task, $request->toDto()));
    }

    /**
     * Remove the specified Task from DB
     * @param Task $task
     * @return JsonResponse
     * @throws TaskAlreadyDoneException|TaskDeletionException
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->service->delete($task);
        return response()->json(['message' => 'Delete successful!']);
    }

    /**
     * Set Task's status as 'done'
     * @param Task $task
     * @return TaskShowResource|JsonResponse
     * @throws TaskHasUncompletedChildrenException|TaskAlreadyDoneException
     */
    public function markTaskDone(Task $task): TaskShowResource|JsonResponse
    {
        $response = $this->service->markTaskDone($task);
        return new TaskShowResource($response);
    }

    /**
     * Get tasks collection by filters
     * @param Request $request
     * @queryParam title string Filter the tasks by a specific part of the title. Example: something
     * @queryParam description string Filter the tasks by a specific part of the description. Example: good
     * @queryParam priority int Filter the tasks by a priority. Example: 4
     * @queryParam status string Filter the tasks by status. Example: todo
     * @return AnonymousResourceCollection
     */
    public function getFilteredCollection(FiltersTaskRequest $request): AnonymousResourceCollection
    {
        return TaskIndexResource::collection($this->service->getFiltered($request->user()->id, $request->toDto()));
    }

    /**
     * Sorting tasks
     * @param Request $request
     * @queryParam priority string Sorting the tasks by priority. Example: desc
     * @queryParam created_at string Sorting the tasks by a created_at. Example: asc
     * @queryParam completed_at string Sorting the tasks by a completed_at. Example: asc
     * @return AnonymousResourceCollection
     */
    public function getSortedCollection(Request $request): AnonymousResourceCollection
    {
        // Query parameters
         $validated = $request->validate([
            'priority' => 'sometimes|nullable|string|in:asc,desc|required_without_all:created_at,completed_at',
            'created_at' => 'sometimes|nullable|string|in:asc,desc|required_without_all:priority, completed_at',
            'completed_at' => 'sometimes|nullable|string|in:asc,desc|required_without_all:priority, created_at',
        ]);

        return TaskIndexResource::collection($this->service->getSorted($request->user()->id, $validated));
    }

}
