<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(\App\Http\Middleware\SetLocale::class);
        $middleware->web(\App\Http\Middleware\CompressResponse::class);
        $middleware->web(\App\Http\Middleware\LogLivewireRequests::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'log.organization' => \App\Http\Middleware\LogOrganizationAccess::class,
            'api-docs.password' => \App\Http\Middleware\ApiDocsPasswordGate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API routes have no session/login page to redirect to - always
        // return JSON instead of the default HTML-redirect-to-login
        // behavior, which kicked in for any request without an explicit
        // Accept: application/json header (e.g. opening the URL in a browser).
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
        });
    })->create();
