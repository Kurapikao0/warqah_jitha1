<?php

use App\Http\Middleware\CheckPermission;
use Illuminate\Auth\AuthenticationException;        // â†گ ط³ط·ط± ط¬ط¯ظٹط¯ طھط¶ظٹظپظ‡ ظ‡ظ†ط§
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('exchange-rates:sync')->hourly();
        $schedule->command('stock:clean-expired')->everyMinute();
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
         * Authentication errors
         *
         * Keep the existing Laravel auth contract:
         * HTTP 401 for unauthenticated API requests.
         */
        $exceptions->render(function (// â†گ ط§ظ„ط¨ظ„ظˆظƒ ط§ظ„ط¬ط¯ظٹط¯ ظƒط§ظ…ظ„طŒ طھط­ط·ظ‡ ظ‡ظ†ط§
            AuthenticationException $exception,             //   ظ‚ط¨ظ„ ط¨ظ„ظˆظƒ ValidationException ط§ظ„ظ‚ط¯ظٹظ…
            Request $request
        ) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        });

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
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $exception->getMessage() ?: 'ط§ظ„ط¨ظٹط§ظ†ط§طھ ط§ظ„ظ…ط¯ط®ظ„ط© ط؛ظٹط± طµط­ظٹط­ط©.',
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
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'ط§ظ„ط¹ظ†طµط± ط§ظ„ظ…ط·ظ„ظˆط¨ ط؛ظٹط± ظ…ظˆط¬ظˆط¯.',
            ], 404);
        });

        /*
        * Route / model binding not found
        */
        $exceptions->render(function (
            NotFoundHttpException $exception,
            Request $request
        ) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'ط§ظ„ط¹ظ†طµط± ط§ظ„ظ…ط·ظ„ظˆط¨ ط؛ظٹط± ظ…ظˆط¬ظˆط¯.',
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
            if (! $request->expectsJson()) {
                return null;
            }

            Log::error('API database exception', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'code' => 'SERVER_ERROR',
                'message' => 'ط­ط¯ط« ط®ط·ط£ ظپظٹ ط§ظ„ظ†ط¸ط§ظ…طŒ ظٹط±ط¬ظ‰ ط§ظ„ظ…ط­ط§ظˆظ„ط© ظ„ط§ط­ظ‚ظ‹ط§.',
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
            if (! $request->expectsJson()) {
                return null;
            }

            Log::error('API unexpected exception', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'code' => 'SERVER_ERROR',
                'message' => 'ط­ط¯ط« ط®ط·ط£ ط؛ظٹط± ظ…طھظˆظ‚ط¹طŒ ظٹط±ط¬ظ‰ ط§ظ„ظ…ط­ط§ظˆظ„ط© ظ„ط§ط­ظ‚ظ‹ط§.',
            ], 500);
        });

    })
    ->create();

