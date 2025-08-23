<?php

use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\AppServiceProvider::class,

        EventServiceProvider::class,
        RouteServiceProvider::class,
        App\Providers\LoyaltyServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            // Temporarily disabled to test if it's causing 502 errors
            // \App\Http\Middleware\CurrencyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
