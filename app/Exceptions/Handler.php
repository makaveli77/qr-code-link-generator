<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle unique constraint violation for API requests
        if ($exception instanceof QueryException && $request->expectsJson()) {
            if (str_contains($exception->getMessage(), 'UNIQUE constraint failed')) {
                return response()->json([
                    'message' => 'Duplicate entry: a record with the same unique value already exists.'
                ], 422);
            }
        }
        // Handle rate limit exceeded for API requests
        if ($exception instanceof ThrottleRequestsException && $request->expectsJson()) {
            return response()->json([
                'message' => 'Too many requests. Please slow down.',
                'retry_after' => $exception->getHeaders()['Retry-After'] ?? null
            ], 429);
        }
        return parent::render($request, $exception);
    }
}
