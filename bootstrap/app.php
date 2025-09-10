<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Agregar CORS global (usa config/cors.php)
        $middleware->append(HandleCors::class);

        // Si en algún momento necesitás alias o grupos:
        // $middleware->alias([
        //     // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // ]);
        //
        // Para APIs con cookies (SPA), se usaría:
        // use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
        // $middleware->prependToGroup('web', EnsureFrontendRequestsAreStateful::class);
        // (No es necesario para tokens personales con Bearer)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();