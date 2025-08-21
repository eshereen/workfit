<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\AwardLoyaltyPoints;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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
            ]);
        }
    }
}
