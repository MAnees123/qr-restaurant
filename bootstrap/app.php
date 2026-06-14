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
            'role'          => \App\Http\Middleware\CheckRole::class,
            'guest.tracker' => \App\Http\Middleware\GuestTrackerMiddleware::class,
            'superadmin'    => \App\Http\Middleware\SuperAdmin::class,
            'feature'       => \App\Http\Middleware\CheckFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
