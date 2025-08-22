<?php

namespace App\Payments\Gateways;

use Exception;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
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
        // Debug: Log what we're doing
        Log::info('CodGateway: Processing COD payment', [
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'payment_method' => $order->payment_method
        ]);

        $payment->update(['status' => 'succeeded', 'provider_reference' => 'COD-'.now()->timestamp]);
        $order->update(['payment_status' => 'pending', 'status' => 'pending']); // collect on delivery
        
        $result = ['payment' => $payment, 'redirect_url' => null];
        
        // Debug: Log what we're returning
        \Log::info('CodGateway: Returning result', [
            'result' => $result,
            'has_redirect_url' => !empty($result['redirect_url']),
            'redirect_url' => $result['redirect_url']
        ]);
        
        return $result;
    }
    public function handleReturn(array $request): Payment {
        throw new Exception('COD does not support return handling');
    }
    public function handleWebhook(array $payload, ?string $signature = null): void { /* no-op */ }
}
