<?php

namespace App\Services;

use App\Dto\TaskCreateDto;
use App\Dto\TaskUpdateDto;
use App\Enums\StatusEnum;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Carbon\Carbon;

class TaskService
{

    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * All user's tasks
     *
     * @param int $userId
     * @return mixed
     */
    public function getTasksByAuthor(int $userId): mixed
    {
        return $this->repository->getTasksByAuthor($userId);
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
     * @return array
     */
    public function delete(Task $task): array
    {
        if ($task->status === StatusEnum::DONE) {
            return ['message' => 'Delete impossible! Task is done.', 200];
        }
        return $this->repository->delete($task) ? ['message' => 'Delete successful!', 200] : ['Unsuccessful', 404];
    }

    /**
     * Build tasks Tree
     * @param int|null $taskId
     * @return array
     */
    public function getTaskTree(int $taskId = null)
    {
        return Task::where('id', $taskId)->with('children')->get();
//        $taskTree = $this->buildTree($rootTasks);

//        return $taskTree;
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

}
