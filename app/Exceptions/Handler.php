<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $e): Response
    {
        if ($e instanceof TaskAlreadyDoneException) {
            return response()->json(['error' => $e->getMessage(), ], $e->getCode());
        }

        if ($e instanceof TaskDeletionException) {
            return response()->json(['error' => $e->getMessage(), ], $e->getCode());
        }

        if ($e instanceof ValidationException) {
            $validationErrors = $e->validator->errors()->getMessages();
            return response()->json(['errors' => $validationErrors], 422);
        }

        if ($e instanceof QueryException) {
            return response()->json(['error' => $e->getMessage(), ], $e->getCode());
        }

        return parent::render($request, $e);
    }

}
