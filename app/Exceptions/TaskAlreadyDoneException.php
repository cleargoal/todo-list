<?php

namespace App\Exceptions;

use Exception;

class TaskAlreadyDoneException extends Exception
{
    public function __construct()
    {
        parent::__construct('Action impossible! Task is done.', 400);
    }
}