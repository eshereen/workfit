<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Livewire\Livewire;
use App\Livewire\CheckoutForm;
use App\Livewire\PaymentMethodsSelector;
use App\Livewire\LoyaltyPoints;
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
        Order::observe(OrderObserver::class);

        // Manually register Livewire components if auto-discovery fails
        if (class_exists(Livewire::class)) {
            Livewire::component('checkout-form', CheckoutForm::class);
            Livewire::component('payment-methods-selector', PaymentMethodsSelector::class);

            // Also try registering with full namespace
            Livewire::component('App\\Livewire\\CheckoutForm', CheckoutForm::class);
            Livewire::component('App\\Livewire\\PaymentMethodsSelector', PaymentMethodsSelector::class);

            // Debug: Log that components are being registered
            Log::info('Livewire components registration attempted', [
                'checkout-form' => 'App\\Livewire\\CheckoutForm',
                'payment-methods-selector' => 'App\\Livewire\\PaymentMethodsSelector',
                'loyalty-points' => 'App\\Livewire\\LoyaltyPoints',
            ]);

            // Register loyalty-points component
            Livewire::component('loyalty-points', LoyaltyPoints::class);
        }
    }
}
