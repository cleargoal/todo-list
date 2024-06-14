<?php

namespace App\Services;

use App\Dto\TaskCreateDto;
use App\Dto\TaskFiltersDto;
use App\Dto\TaskUpdateDto;
use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use App\Exceptions\TaskAlreadyDoneException;
use App\Exceptions\TaskDeletionException;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TaskService
{

    public function __construct(private TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * All user's tasks
     *
     * @param int $userId
     * @return mixed
     */
    public function getTasksByUserId(int $userId): mixed
    {
        return $this->repository->getTasksByUserId($userId);
    }

    /**
     * Create Task
     * @param TaskCreateDto $data
     * @return Task
     */
    public function create(TaskCreateDto $data): Task
    {
        return $this->repository->create($data);
    }

    /**
     * Create Task
     * @param Task $task
     * @param TaskUpdateDto $data
     * @return Task
     */
    public function update(Task $task, TaskUpdateDto $data): Task
    {
        return $this->repository->update($task, $data);
    }

    /**
     * Delete task
     * @param Task $task
     * @return true
     */
    public function delete(Task $task): true
    {
        if ($task->status === StatusEnum::DONE) {
            throw new TaskAlreadyDoneException();
        }

        if (!$this->repository->delete($task)) {
            throw new TaskDeletionException();
        }

        return true;
    }

    /**
     * Build tasks Tree
     * @param int|null $taskId
     * @return array|Collection
     */
    public function getTaskTree(int $taskId = null): array|Collection
    {
        $taskWithChildren = $this->repository->getTree($taskId);
        return $this->buildTree($taskWithChildren);
    }

    /**
     * Recursive method to build the tree
     * @param $tasks
     * @return mixed
     */
    protected function buildTree($tasks): mixed
    {
        foreach ($tasks as $task) {
            $task->children = $task->children->isNotEmpty() ? $this->buildTree($task->children) : [];
        }

        return $tasks;
    }

    /**
     * Mark task as done
     * @param Task $task
     * @return Task
     * @throws TaskAlreadyDoneException
     */
    public function markTaskDone(Task $task): Task
    {
        $withChildren = $this->repository->getDescendants($task->id);
        $status = StatusEnum::TODO;

        $check = $this->checkChildren($withChildren, $status);

        if($check) {
            throw new TaskAlreadyDoneException();
        }

        return $this->repository->setTaskStatusDone($task);
    }

    /**
     * Check children on 'to do' status
     * @param Collection $children
     * @param StatusEnum $status
     * @param bool $check
     * @return bool
     */
    protected function checkChildren(Collection $children, StatusEnum $status, bool &$check = false): bool
    {
        foreach ($children as $child) {
            if ($child->status === $status) {
                $check = true;
                break;
            }

            if ($child->children) {
                $this->checkChildren($child->children, $status, $check);
            }
        }

        return $check;
    }

    /**
     * Get filtered user's tasks
     * @param int $userId
     * @param TaskFiltersDto $filters
     * @return Collection
     */
    public function getFiltered(int $userId, TaskFiltersDto $filters): Collection
    {
        return $this->repository->getFilteredTasks($userId, $filters);
    }

    /**
     * Get sorted user's tasks
     * @param int $userId
     * @param array $parameters
     * @return Collection
     */
    public function getSorted(int $userId, array $parameters): Collection
    {
        return $this->repository->getSortedTasks($userId, $parameters);
    }

}
