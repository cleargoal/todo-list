<?php

namespace App\Repositories;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskRepository
{

    protected Task $model;

    public function __construct()
    {
    }

    /**
     * All user's tasks
     * @return mixed
     */
    public function getTasksByAuthor(): mixed
    {
        return Task::where('user_id', auth()->user()->id)->get();
    }

    /**
     * Create Task
     * @param
     * @return Task
     */
    public function create($data): Task
    {
        Log::info($data);
        $this->model = new Task();
        $this->model->fill($data);
        $this->model->user_id = auth()->user()->id;
        $this->model->created_at = Carbon::now();
        $this->model->save();

        return $this->model->fresh();
    }

    /**
     * Update task
     * @param $model
     * @param $data
     * @return Task
     */
    public function update($model, $data): Task
    {
        $this->model = $model;
        $this->model->fill($data);
        $this->model->save();
        return $this->model;
    }

    /**
     * Delete task
     * @param
     * @return array
     */
    public function delete($model): array
    {
        $this->model = $model;
        return $this->model->delete() ? ['message' => 'Delete successful!', 200] : ['Unsuccessful', 404];
    }

}
