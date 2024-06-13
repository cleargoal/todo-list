<?php

namespace App\Repositories;

use App\Dto\TaskCreateDto;
use App\Dto\TaskFiltersDto;
use App\Dto\TaskUpdateDto;
use App\Enums\StatusEnum;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class TaskRepository
{
    /**
     * All user's tasks
     * @param int $userId
     * @return mixed
     */
    public function getTasksByAuthor(int $userId): mixed
    {
        return Task::where('user_id', $userId)->get();
    }

    /**
     * Create Task
     * @param TaskCreateDto $data
     * @return Task
     */
    public function create(TaskCreateDto $data): Task
    {
        $model = new Task();
        $model->user_id = $data->user_id;
        $model->parent_id = $data->parent_id;
        $model->title = $data->title;
        $model->description = $data->description;
        $model->status = $data->status;
        $model->priority = $data->priority;

        $model->save();
        return $model->fresh();
    }

    /**
     * Update task
     * @param Task $model
     * @param TaskUpdateDto $data
     * @return Task
     */
    public function update(Task $model, TaskUpdateDto $data): Task
    {
        $model->parent_id = $data->parent_id ?? $model->parent_id;
        $model->title = $data->title ?? $model->title;
        $model->description = $data->description ?? $model->description;
        $model->priority = $data->priority ?? $model->priority;

        $model->save();
        return $model->fresh();
    }

    /**
     * Delete task
     * @param Task $model
     * @return bool
     */
    public function delete(Task $model): bool
    {
        return $model->delete();
    }

    /**
     * Get tree structure of specified task
     * @param $taskId
     * @return mixed
     */
    public function getTree($taskId): mixed
    {
        return Task::where('id', $taskId)->with('children')->get();
    }

    /**
     * Get tree structure of specified task without root (parent)
     * @param $taskId
     * @return mixed
     */
    public function getDescendants($taskId): mixed
    {
        return Task::where('parent_id', $taskId)->with('children')->get();
    }

    /**
     * set task status 'done
     * @param Task $task
     * @return Task|null
     */
    public function setTaskStatusDone(Task $task): ?Task
    {
        $task->status = StatusEnum::DONE;
        $task->save();
        return $task->fresh();
    }

    /**
     * Get filtered user's tasks
     * @param int $userId
     * @param TaskFiltersDto $filters
     * @return Collection<Task>
     */
    public function getFilteredTasks(int $userId, TaskFiltersDto $filters): Collection
    {
        $query = Task::where('user_id', $userId);

        foreach ($filters as $key => $filter) {
            if ($filter !== null) {
                $query->where($key, $filter);
            }
        }

        return $query->get();
    }

    /**
     * Get sorted user's tasks
     *
     * @param int $userId
     * @param array $parameters
     * @return Collection<Task>
     */
    public function getSortedTasks(int $userId, array $parameters): Collection
    {
        $query = Task::where('user_id', $userId);

        foreach ($parameters as $key => $param) {
            if ($param !== null) {
                $query->orderBy($key, $param);
            }
        }

        return $query->get();
    }

}
