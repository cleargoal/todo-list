<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskAlreadyDoneException extends Exception
{
    public function __construct()
    {
        parent::__construct('Action impossible! Task is done.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
