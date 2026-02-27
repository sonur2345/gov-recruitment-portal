<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'session.timeout' => \App\Http\Middleware\EnsureSessionIsActive::class,
            'otp.verified' => \App\Http\Middleware\EnsureOtpIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $exception): void {
            $request = app()->bound('request') ? app('request') : null;
            $context = [
                'message' => $exception->getMessage(),
                'class' => $exception::class,
                'url' => $request?->fullUrl(),
                'ip_address' => $request?->ip(),
            ];

            if (app()->bound('log')) {
                app('log')->error('Unhandled exception captured', $context);
                return;
            }

            error_log('Unhandled exception captured: ' . $exception->getMessage());
        });
    })->create();
