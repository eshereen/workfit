<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Payments\Gateways\{PaymobGateway, PaypalGateway, CodGateway};

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymobGateway::class);

        $this->app->singleton(PaypalGateway::class);
        $this->app->singleton(CodGateway::class);
    }
}

