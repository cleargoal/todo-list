<?php
declare(strict_types = 1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskDeletionException extends Exception
{
    protected $message = 'Task deletion error!';

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        parent::__construct($message ?: $this->message, $code ?: $this->code, $previous);
    }
}
