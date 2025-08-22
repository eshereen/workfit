<?php

namespace App\Payments\Gateways;

use RuntimeException;
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
            throw new RuntimeException('Paymob requires EGP (Egypt).');
        }

        // 1) auth token
        $auth = Http::post(config('paymob.auth_endpoint'), [
            'api_key' => config('paymob.api_key'),
        ])->json();

        Log::info('Paymob auth response', ['auth' => $auth]);

        if (!isset($auth['token'])) {
            $errorMessage = 'Paymob authentication failed';
            if (isset($auth['detail'])) {
                $errorMessage .= ': ' . $auth['detail'];
            } else {
                $errorMessage .= ': ' . json_encode($auth);
            }
            Log::error('Paymob authentication failed', ['response' => $auth]);
            throw new RuntimeException($errorMessage);
        }

        // 2) order registration
        $orderRegistrationData = [
            'auth_token' => $auth['token'],
            'delivery_needed' => false,
            'amount_cents' => $payment->amount_minor, // Paymob uses cents
            'currency' => 'EGP',
            'merchant_order_id' => $payment->order->order_number . '-' . time(), // Add timestamp to prevent duplicates
            'items' => [],
        ];

        Log::info('Paymob order registration request', [
            'merchant_order_id' => $payment->order->order_number,
            'payment_id' => $payment->id,
            'order_id' => $payment->order->id,
            'amount_cents' => $payment->amount_minor
        ]);

        $reg = Http::post(config('paymob.order_endpoint'), $orderRegistrationData)->json();

        Log::info('Paymob order registration response', ['reg' => $reg]);

        if (!isset($reg['id'])) {
            $errorMessage = 'Paymob order registration failed';
            if (isset($reg['message'])) {
                $errorMessage .= ': ' . $reg['message'];
            } else {
                $errorMessage .= ': ' . json_encode($reg);
            }
            Log::error('Paymob order registration failed', ['response' => $reg]);
            throw new RuntimeException($errorMessage);
        }

        // Get billing data from new database structure
        Log::info('Order billing data', [
            'billing_address' => $order->billing_address,
            'state' => $order->state,
            'city' => $order->city,
            'billing_building_number' => $order->billing_building_number
        ]);

        // 3) payment key
        $billingData = [
            'first_name' => $order->first_name ?: 'Customer',
            'last_name' => $order->last_name ?: 'Name',
            'email' => $order->email ?: 'customer@example.com',
            'phone_number' => $order->phone_number ?: '+201234567890',
            'street' => $order->billing_address ?: 'Default Street',
            'building' => $order->billing_building_number ?: '1',
            'city' => $order->city ?: 'Cairo',
            'country' => 'EG',
            'apartment' => 'Apt',
            'floor' => '1',
            'postal_code' => '11511', // Default postal code for Cairo
            'state' => $order->state ?: 'Cairo',
        ];

        // Ensure all required fields have valid values
        $billingData = array_map(function($value) {
            return $value ?: 'N/A';
        }, $billingData);

        Log::info('Paymob billing data prepared', ['billing_data' => $billingData]);

                        // Build callback URLs - using the new Paymob callback route
        $successUrl = route('paymob.callback');
        $failureUrl = route('paymob.callback');
        
        Log::info('Paymob callback URLs', [
            'success_url' => $successUrl,
            'failure_url' => $failureUrl,
            'order_id' => $order->id,
            'note' => 'Using new Paymob callback route: /api/paymob/callback'
        ]);
        
        $key = Http::post(config('paymob.payment_key_endpoint'), [
            'auth_token' => $auth['token'],
            'amount_cents' => $payment->amount_minor,
            'expiration' => 3600,
            'order_id' => $reg['id'],
            'billing_data' => $billingData,
            'currency' => 'EGP',
            'integration_id' => config('paymob.integration_id_card'),
            'success_url' => $successUrl,
            'failure_url' => $failureUrl,
            'merchant_order_id' => $order->id, // Add this to help identify the order in callbacks
        ])->json();

        Log::info('Paymob payment key response', ['key' => $key]);

        if (!isset($key['token'])) {
            $errorMessage = 'Paymob payment key failed';
            if (isset($key['billing_data'])) {
                $errorMessage .= ': ' . json_encode($key['billing_data']);
            } else {
                $errorMessage .= ': ' . json_encode($key);
            }
            Log::error('Paymob payment key failed', ['response' => $key]);
            throw new RuntimeException($errorMessage);
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
