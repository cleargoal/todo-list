<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;

class TaskHasTodoChildrenException extends Exception
{
    public function __construct()
    {
        parent::__construct('Action impossible! Task has TODO children.', 400);
    }
}
