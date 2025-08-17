<?php

namespace App\Providers;

use App\Services\LoyaltyService;
use Illuminate\Support\ServiceProvider;

class LoyaltyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(LoyaltyService::class, function ($app) {
            return new LoyaltyService();
        });
    }

    public function boot()
    {
        //
    }
}
