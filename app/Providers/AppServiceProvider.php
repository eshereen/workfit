<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Category;
use App\Events\OrderPlaced;
use App\Events\PaymentStatusChanged;
use App\Listeners\AwardLoyaltyPoints;
use App\Listeners\HandlePaymentStatusChange;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            AwardLoyaltyPoints::class,
        ],
        PaymentStatusChanged::class => [
            HandlePaymentStatusChange::class,
        ],
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Schema::defaultStringLength(191);
        // Temporarily disabled to debug 502 on checkout (emails in observer)
         Order::observe(OrderObserver::class);

        // Share categories with all views
        View::composer('*', function ($view) {
            $view->with('categories', Category::where('active', true)->orderBy('name')->take(4)->get());
        });
    }
}
