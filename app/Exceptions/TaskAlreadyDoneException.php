<?php
declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskAlreadyDoneException extends Exception
{
    protected $message = 'The task has already been completed.';
    protected $code = Response::HTTP_CONFLICT; // 409

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        parent::__construct($message ?: $this->message, $code ?: $this->code, $previous);
    }
}
