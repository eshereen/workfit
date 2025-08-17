<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\AwardLoyaltyPoints;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    protected $listen = [
        OrderPlaced::class => [
            AwardLoyaltyPoints::class,
        ],
        // Other events...
    ];
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
