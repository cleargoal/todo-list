<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Dto\TaskCreateDto;
use App\Dto\TaskFiltersDto;
use App\Dto\TaskUpdateDto;
use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use App\Exceptions\TaskAlreadyDoneException;
use App\Exceptions\TaskDeletionException;
use App\Exceptions\TaskHasTodoChildrenException;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskIndexResource;
use App\Http\Resources\TaskShowResource;
use App\Models\Task;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $service)
    {
    }

    /**
     * Display a listing of the tasks
     * @return AnonymousResourceCollection
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
        return response()->json($this->service->getTaskTree($task->id));
    }

    /**
     * Store a newly created Task in DB
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
        try {
            $this->service->delete($task);
            return response()->json(['message' => 'Delete successful!']);
        } catch (TaskAlreadyDoneException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (TaskDeletionException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark Task as 'done'
     * @param Task $task
     * @return TaskShowResource|JsonResponse
     * @throws TaskHasTodoChildrenException
     */
    public function markTaskDone(Task $task): TaskShowResource|JsonResponse
    {
        try{
            $response = $this->service->markTaskDone($task);
            return new TaskShowResource($response);
        }
        catch(TaskAlreadyDoneException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
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
    public function getFilteredCollection(Request $request): AnonymousResourceCollection
    {
        // Query parameters
        $request->validate([
            'title' => 'sometimes|nullable|string|max:255|required_without_all:priority,description,status',
            'description' => 'sometimes|nullable|string|max:10000|required_without_all:priority,title,status',
            'priority' => ['sometimes', 'nullable', 'int', Rule::enum(PriorityEnum::class), 'required_without_all:title,description,status'],
            'status' => ['sometimes', 'nullable', Rule::enum(StatusEnum::class), 'required_without_all:title,description,priority'],
        ]);

        $taskDto = new TaskFiltersDto(
            $request->query('title') ?? null,
            $request->query('description') ?? null,
                $request->query('priority') ? PriorityEnum::from($request->query('priority')) : null,
                $request->query('status') ? StatusEnum::from($request->query('status')) : null,
        );
        return TaskIndexResource::collection($this->service->getFiltered(request()->user()->id, $taskDto));
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

        return TaskIndexResource::collection($this->service->getSorted(request()->user()->id, $validated));
    }

}
