<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Supports\ApiError;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ===========================
        // API: VALIDATION (422)
        // ===========================
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(
                    ApiError::make(
                        message: 'Validation failed',
                        status: 422,
                        code: 'VALIDATION_ERROR',
                        errors: $e->errors()
                    ),
                    422
                );
            }
        });

        // ===========================
        // API: DOMAIN EXCEPTION (422)
        // ===========================
        $exceptions->render(function (\DomainException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(
                    ApiError::make(
                        message: $e->getMessage(),
                        status: 422,
                        code: 'DOMAIN_ERROR'
                    ),
                    422
                );
            }
        });

        // ===========================
        // API: AUTH (401)
        // ===========================
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(
                    ApiError::make(
                        message: 'Unauthenticated',
                        status: 401,
                        code: 'UNAUTHENTICATED'
                    ),
                    401
                );
            }
        });

        // ===========================
        // API: MODEL NOT FOUND (404)
        // ===========================
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(
                    ApiError::make(
                        message: 'Resource not found',
                        status: 404,
                        code: 'NOT_FOUND'
                    ),
                    404
                );
            }
        });

        // ===========================
        // API: HTTP EXCEPTION (404/403/...)
        // ===========================
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = $e->getStatusCode();

                $message = match ($status) {
                    404 => 'Not found',
                    403 => 'Forbidden',
                    429 => 'Too many requests',
                    default => $e->getMessage() ?: 'Request error',
                };

                return response()->json(
                    ApiError::make(
                        message: $message,
                        status: $status
                    ),
                    $status
                );
            }
        });

        // ===========================
        // API: FALLBACK (500)
        // ===========================
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $isLocal = app()->environment('local');

                return response()->json(
                    ApiError::make(
                        message: $isLocal ? $e->getMessage() : 'Internal server error',
                        status: 500,
                        code: 'SERVER_ERROR',
                        errors: $isLocal ? ['trace' => [$e->getFile().':'.$e->getLine()]] : []
                    ),
                    500
                );
            }
        });
    })
    ->withProviders([
        App\Infrastructure\Providers\CQRSServiceProvider::class,
    ])
    ->create();
