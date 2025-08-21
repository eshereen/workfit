<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;

class CodGateway implements PaymentGateway
{
    public function charge(array $data): array
    {
        $order = $data['order'] ?? null;
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Validate currency for Egypt
        if (strtoupper($order->currency ?? '') !== 'EGP') {
            return ['success' => false, 'error' => 'COD requires EGP currency'];
        }

        // COD logic for Egypt
        $order->update(['status' => 'pending_payment']);
        return ['success' => true, 'gateway' => 'cod'];
    }

    public function isAvailableForCountry(string $countryCode): bool
    {
        return $countryCode === 'EG';
    }

    public function initiate(Order $order, Payment $payment): array
    {
        $payment->update(['status' => 'succeeded', 'provider_reference' => 'COD-'.now()->timestamp]);
        $order->update(['payment_status' => 'pending', 'status' => 'pending']); // collect on delivery
        return ['payment' => $payment, 'redirect_url' => null];
    }
    public function handleReturn(array $request): Payment {
        throw new \Exception('COD does not support return handling');
    }
    public function handleWebhook(array $payload, ?string $signature = null): void { /* no-op */ }
}
