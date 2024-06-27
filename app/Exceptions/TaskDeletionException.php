<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskDeletionException extends Exception
{
    protected $message;
    protected $code;

    public function __construct($message = 'Task deletion error', $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $code);
    }
}
