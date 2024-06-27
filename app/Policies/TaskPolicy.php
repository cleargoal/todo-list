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
    public function own(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}
