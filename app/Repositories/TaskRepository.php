<?php

namespace App\Repositories;
use App\Dto\TaskCreateDto;
use App\Dto\TaskUpdateDto;
use App\Models\Task;
use Carbon\Carbon;
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
     * Get tree structure
     */
    public function getTree(Task $task)
    {
        $structure = Task::where()->get();
    }

}
