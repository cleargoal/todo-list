<?php

declare(strict_types = 1);

use App\Exceptions\TaskAlreadyDoneException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Exception $e): Response {
            Log::info('Denied', ['instance' => get_class($e), 'exception' => $e->getMessage(), $e->getCode()]);
            Log::info('Denied.code', [$e->getCode()]);
            $statusCode = Response::HTTP_FORBIDDEN;
            if ($e instanceof TaskAlreadyDoneException) {
                $statusCode = Response::HTTP_CONFLICT;
            }
            return response()->json(['error' => $e->getMessage(),], $statusCode);
        });
    })->create();
