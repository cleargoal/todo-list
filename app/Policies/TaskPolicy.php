<?php

declare(strict_types = 1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

}
