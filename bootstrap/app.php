<?php

use App\Http\Middleware\CheckPermission;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('exchange-rates:sync')->hourly();
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->redirectGuestsTo(function (Request $request) {
            abort(response()->json([
                'message' => 'Unauthenticated',
            ], 401));
        });

        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
         * Validation errors
         *
         * Keep the existing Laravel validation contract:
         * HTTP 422 + message + errors.
         */
        $exceptions->render(function (
            ValidationException $exception,
            Request $request
        ) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $exception->getMessage() ?: 'البيانات المدخلة غير صحيحة.',
                'errors' => $exception->errors(),
            ], 422);
        });

        /*
         * Resource not found
         */
        $exceptions->render(function (
            ModelNotFoundException $exception,
            Request $request
        ) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'العنصر المطلوب غير موجود.',
            ], 404);
        });

        /*
         * Database errors
         *
         * Technical details are logged internally only.
         * Never expose SQLSTATE, SQL query, host, port or database name.
         */
        $exceptions->render(function (
            QueryException $exception,
            Request $request
        ) {
            if (!$request->expectsJson()) {
                return null;
            }

            Log::error('API database exception', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'code' => 'SERVER_ERROR',
                'message' => 'حدث خطأ في النظام، يرجى المحاولة لاحقًا.',
            ], 500);
        });

        /*
         * Unexpected exceptions
         *
         * Final security layer for API requests.
         */
        $exceptions->render(function (
            Throwable $exception,
            Request $request
        ) {
            if (!$request->expectsJson()) {
                return null;
            }

            Log::error('API unexpected exception', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'code' => 'SERVER_ERROR',
                'message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقًا.',
            ], 500);
        });

    })
    ->create();
