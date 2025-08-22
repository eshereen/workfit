<?php

namespace App\Services\Payments;
use App\Models\Order;
use InvalidArgumentException;
use App\Payments\Gateways\CodGateway;
use App\Payments\Gateways\PaymobGateway;
use App\Payments\Gateways\PaypalGateway;
use App\Payments\Contracts\PaymentGatewayInterface;

class PaymentGatewayManager
{
    protected array $gateways;

    public function __construct(
        $paymob = null,
        $paypal = null,
        $cod = null
    ) {
        $this->gateways = [
            'paymob' => $paymob ?? app(PaymobGateway::class),
            'paypal' => $paypal ?? app(PaypalGateway::class),
            'cod'    => $cod ?? app(CodGateway::class),
        ];
    }

    public function gateway(string $name): PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? throw new InvalidArgumentException("Gateway {$name} not supported");
    }

    /**
     * Get available gateways based on order country
     */
    public function getAvailableGateways(Order $order): array
    {
        $countryCode = strtoupper($order->country->code ?? 'EG');

        return collect($this->gateways)
            ->filter(fn($gateway) => $gateway->isAvailableForCountry($countryCode))
            ->keys()
            ->toArray();
    }

    /**
     * Resolve gateway instance
     */


    /**
     * Charge order
     */
    public function charge(Order $order, string $gateway, float $amount): array
    {
        $instance = $this->gateway($gateway);

        return $instance->charge(['order' => $order, 'amount' => $amount]);
    }
}
