<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\PaymentStatusChanged;
use App\Listeners\AwardLoyaltyPoints;
use App\Listeners\HandlePaymentStatusChange;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{

    protected $listen = [
        OrderPlaced::class => [
            AwardLoyaltyPoints::class,
        ],
        PaymentStatusChanged::class => [
            HandlePaymentStatusChange::class,
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
        // Register Order observer for payment status changes
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);

        // Manually register Livewire components if auto-discovery fails
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('checkout-form', \App\Livewire\CheckoutForm::class);
            \Livewire\Livewire::component('payment-methods-selector', \App\Livewire\PaymentMethodsSelector::class);

            // Also try registering with full namespace
            \Livewire\Livewire::component('App\\Livewire\\CheckoutForm', \App\Livewire\CheckoutForm::class);
            \Livewire\Livewire::component('App\\Livewire\\PaymentMethodsSelector', \App\Livewire\PaymentMethodsSelector::class);

            // Debug: Log that components are being registered
            Log::info('Livewire components registration attempted', [
                'checkout-form' => 'App\\Livewire\\CheckoutForm',
                'payment-methods-selector' => 'App\\Livewire\\PaymentMethodsSelector',
                'loyalty-points' => 'App\\Livewire\\LoyaltyPoints',
            ]);

            // Register loyalty-points component
            \Livewire\Livewire::component('loyalty-points', \App\Livewire\LoyaltyPoints::class);
        }
    }
}
