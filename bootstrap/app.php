<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\GlobalRateLimitCheck::class,
            \App\Http\Middleware\CheckAuthenticationLocks::class,
        ]);

        // API middleware should use sanctum authentication
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Register progressive rate limiter
        $middleware->alias([
            'progressive_throttle' => \App\Http\Middleware\ProgressiveRateLimiter::class,
            'auth_throttle' => \App\Http\Middleware\AuthRateLimiter::class,
            'check_auth_locks' => \App\Http\Middleware\CheckAuthenticationLocks::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;
            $message = $e->getMessage();
            
            // Check if this is a lock message
            $isLocked = str_contains($message, 'locked') || str_contains($message, 'administrator');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $isLocked ? $message : 'Too Many Requests',
                    'retry_after' => $retryAfter,
                    'is_locked' => $isLocked,
                ], 429);
            }
            
            return response()->view('errors.429', [
                'retryAfter' => $retryAfter,
                'exception' => $e,
                'isLocked' => $isLocked,
                'message' => $message,
                'originalUrl' => $request->fullUrl()
            ], 429);
        });
    })->create();
