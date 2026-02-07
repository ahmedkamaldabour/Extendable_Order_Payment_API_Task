<?php

use App\Exceptions\BusinessException;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Handle Authentication Exceptions (not logged in)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return ApiResponse::error(
                'Unauthenticated',
                ['auth' => 'You must be logged in to access this resource'],
                401
            );
        });

        // Handle Business Exceptions (our custom exceptions)
        $exceptions->render(function (BusinessException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    $e->getMessage(),
                    $e->getErrors(),
                    $e->getStatusCode()
                );
            }
        });

        // Handle Authorization Exceptions (Policy failures)
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    'Unauthorized',
                    ['auth' => 'You do not have permission to perform this action'],
                    403
                );
            }
        });

        // Handle Not Found (Model not found or route not found)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $message = str_contains($e->getMessage(), 'model')
                    ? 'Resource not found'
                    : 'Endpoint not found';

                return ApiResponse::error(
                    $message,
                    ['resource' => 'The requested resource was not found'],
                    404
                );
            }
        });

        // Handle Validation Exceptions
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    'Validation failed',
                    $e->errors(),
                    422
                );
            }
        });

        // Handle all other exceptions
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    'Internal Server Error',
                    config('app.debug') ? ['message' => $e->getMessage()] : [],
                    500
                );
            }
        });
    })->create();
