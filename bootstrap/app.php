<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/quote',
            'api/inquiries',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied: You do not have sufficient privileges to perform this action.',
                ], 403);
            }

            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('customer')) {
                    return redirect()->route('customer.portal')->with('error', 'Access Denied: You do not have permission to access the Executive Dashboard.');
                }
                if ($user->hasRole('technician')) {
                    return redirect()->route('technician.portal')->with('error', 'Access Denied: You do not have permission to access the Executive Dashboard.');
                }
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Access Denied: Administrative credentials are required.',
            ]);
        });
    })->create();
