<?php

namespace App\Exceptions;

use Exception;

class TaskDeletionException extends Exception
{
    protected $message;
    protected $code;

    public function __construct($message = 'Task deletion error', $code = 409)
    {
        parent::__construct($message, $code);
    }
}
