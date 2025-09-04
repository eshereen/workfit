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
        // Replace default CSRF middleware with our enhanced Livewire-aware version
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, \App\Http\Middleware\LivewireCSRFMiddleware::class);
        
        // Append our currency middleware to the web group in a supported way
        $middleware->appendToGroup('web', \App\Http\Middleware\CurrencyMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CacheControlStatic::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
