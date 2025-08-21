<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Payments\Contracts\PaymentGateway;

class PaymobGateway implements PaymentGateway
{
    public function charge(array $data): array
    {
        $order = $data['order'] ?? null;
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Validate currency for Egypt
        if (strtoupper($order->currency ?? '') !== 'EGP') {
            return ['success' => false, 'error' => 'Paymob requires EGP currency'];
        }

        // Call Paymob API here...
        // For now just simulate success:
        $order->update(['status' => 'paid']);
        return ['success' => true, 'gateway' => 'paymob'];
    }

    public function isAvailableForCountry(string $countryCode): bool
    {
        return $countryCode === 'EG';
    }

    public function initiate(Order $order, Payment $payment): array
    {
        // Ensure EGP
        if (strtoupper($order->currency) !== 'EGP') {
            throw new \RuntimeException('Paymob requires EGP (Egypt).');
        }

        // 1) auth token
        $auth = Http::post(config('paymob.auth_endpoint'), [
            'api_key' => config('paymob.api_key'),
        ])->json();

        Log::info('Paymob auth response', ['auth' => $auth]);

        if (!isset($auth['token'])) {
            throw new \RuntimeException('Paymob auth failed: ' . json_encode($auth));
        }

        // 2) order registration
        $reg = Http::post(config('paymob.order_endpoint'), [
            'auth_token' => $auth['token'],
            'delivery_needed' => false,
            'amount_cents' => $payment->amount_minor, // Paymob uses cents
            'currency' => 'EGP',
            'merchant_order_id' => $payment->id,
            'items' => [],
        ])->json();

        Log::info('Paymob order registration response', ['reg' => $reg]);

        if (!isset($reg['id'])) {
            throw new \RuntimeException('Paymob order registration failed: ' . json_encode($reg));
        }

        // Helper function to safely get address data
        $getAddressField = function($field) use ($order) {
            Log::info('Billing address type and content', [
                'type' => gettype($order->billing_address),
                'content' => $order->billing_address
            ]);
            
            if (is_string($order->billing_address)) {
                $decoded = json_decode($order->billing_address, true);
                return $decoded[$field] ?? '';
            }
            return $order->billing_address[$field] ?? '';
        };

        // 3) payment key
        $key = Http::post(config('paymob.payment_key_endpoint'), [
            'auth_token' => $auth['token'],
            'amount_cents' => $payment->amount_minor,
            'expiration' => 3600,
            'order_id' => $reg['id'],
            'billing_data' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'phone_number' => $order->phone_number,
                'street' => $getAddressField('address'),
                'building' => 'Building',
                'city' => $getAddressField('city'),
                'country' => 'EG',
                'apartment' => 'Apt',
                'floor' => '1',
                'postal_code' => $getAddressField('postal_code'),
                'state' => $getAddressField('state'),
            ],
            'currency' => 'EGP',
            'integration_id' => config('paymob.integration_id_card'),
        ])->json();

        Log::info('Paymob payment key response', ['key' => $key]);

        if (!isset($key['token'])) {
            throw new \RuntimeException('Paymob payment key failed: ' . json_encode($key));
        }

        $iframeUrl = sprintf('https://accept.paymob.com/api/acceptance/iframes/441229?payment_token=%s', $key['token']);

        $payment->update([
            'provider_reference' => (string)$reg['id'],
            'status' => 'pending_redirect',
            'meta' => ['payment_key' => $key['token']],
        ]);

        return ['payment' => $payment, 'redirect_url' => $iframeUrl];
    }

    public function handleReturn(array $request): Payment
    {
        // Paymob usually completes via webhook; return flow can confirm visually.
        // You could lookup by merchant_order_id if provided.
        return Payment::where('id', $request['merchant_order_id'] ?? 0)->firstOrFail();
    }

    public function handleWebhook(array $payload, ?string $signature = null): void
    {
        // Verify HMAC per Paymob docs.
        // If valid, find payment by provider_reference or merchant_order_id and update.
        DB::transaction(function () use ($payload) {
            $payment = Payment::where('provider_reference', $payload['order']['id'] ?? null)->lockForUpdate()->first();
            if (!$payment) return;

            $success = (bool)($payload['success'] ?? false);
            $payment->update(['status' => $success ? 'succeeded' : 'failed']);

            $payment->order->update([
                'payment_status' => $success ? 'paid' : 'failed',
                'status' => $success ? 'processing' : 'pending',
            ]);
        });
    }
}
