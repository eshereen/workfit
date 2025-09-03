<?php

namespace App\Payments\Gateways;

use RuntimeException;
use Exception;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;

class PaypalGateway
{
    protected $clientId;
    protected $clientSecret;
    protected $isSandbox;
    protected $baseUrl;
    protected $webhookUrl;

    public function __construct()
    {
        $this->clientId = config('paypal.sandbox.client_id');
        $this->clientSecret = config('paypal.sandbox.client_secret');
        $this->isSandbox = config('paypal.mode') === 'sandbox';
        $this->baseUrl = $this->isSandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
        $this->webhookUrl = config('paypal.webhook_url');
    }

    public function initiate(Order $order, Payment $payment, bool $useCreditCard = false): array
    {
        try {
            Log::info('PayPal gateway: Starting payment initiation', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount_minor,
                'currency' => $order->currency,
                'payment_type' => $useCreditCard ? 'credit_card' : 'paypal_account'
            ]);

            // Validate currency support
            if (!$this->isCurrencySupported($order->currency)) {
                throw new RuntimeException("PayPal does not support {$order->currency} currency. Please use USD, EUR, GBP, CAD, AUD, JPY, CHF, SGD, HKD, or NZD.");
            }

            if ($useCreditCard) {
                Log::info('PayPal gateway: Initiating credit card payment', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'use_credit_card' => $useCreditCard
                ]);
                return $this->initiateCreditCardPayment($order, $payment);
            } else {
                Log::info('PayPal gateway: Initiating PayPal account payment', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'use_credit_card' => $useCreditCard
                ]);
                return $this->initiatePayPalAccountPayment($order, $payment);
            }

        } catch (Exception $e) {
            Log::error('PayPal gateway: Error in initiate method: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'PayPal payment initiation failed: ' . $e->getMessage()
            ];
        }
    }

    protected function initiateCreditCardPayment(Order $order, Payment $payment): array
    {
        // For credit card payments, create a payment page that uses PayPal Smart Payment Buttons
        $payment->update([
            'meta' => [
                'paypal_order_id' => null, // Will be created by frontend
                'payment_type' => 'credit_card',
                'amount' => $payment->amount_minor / 100,
                'currency' => $order->currency,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'return_url' => null, // Credit card payments don't redirect back from PayPal
                'cancel_url' => $payment->cancel_url,
                'webhook_url' => $this->webhookUrl,
            ]
        ]);

        Log::info('PayPal gateway: Credit card payment prepared for frontend', [
            'payment_id' => $payment->id,
            'payment_type' => 'credit_card',
            'amount' => $payment->amount_minor / 100,
            'currency' => $order->currency
        ]);

        // Return a special response indicating frontend should handle this
        return [
            'success' => true,
            'payment_id' => $payment->id,
            'requires_frontend_processing' => true,
            'payment_type' => 'credit_card',
            'redirect_url' => route('checkout.paypal.credit-card', ['payment' => $payment->id])
        ];
    }

    protected function initiatePayPalAccountPayment(Order $order, Payment $payment): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            throw new RuntimeException('Failed to obtain PayPal access token');
        }

        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($order->currency),
                    'value' => number_format($payment->amount_minor / 100, 2, '.', ''),
                ],
                'custom_id' => (string)$payment->id,
                'description' => 'Order #' . $order->id,
                'invoice_id' => 'INV-' . $order->id,
            ]],
            'application_context' => [
                'return_url' => $payment->return_url,
                'cancel_url' => $payment->cancel_url,
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
                'brand_name' => config('app.name', 'Your Store'),
                'landing_page' => 'LOGIN',
                'locale' => 'en-US',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                ],
            ],
        ];

        // Add webhook if configured
        if ($this->webhookUrl) {
            $orderData['application_context']['webhook_url'] = $this->webhookUrl;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'PayPal-Partner-Attribution-Id' => '1234567890', // PayPal partner ID
        ])->post($this->baseUrl . '/v2/checkout/orders', $orderData);

        $responseData = $response->json();
        Log::info('PayPal gateway: Order creation response', ['response' => $responseData]);

        if (isset($responseData['id'])) {
            // Update payment with PayPal order ID
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paypal_order_id' => $responseData['id'],
                    'paypal_status' => $responseData['status'],
                    'created_time' => $responseData['create_time'] ?? now()->toISOString(),
                ])
            ]);

            // Get approval URL
            $approvalUrl = null;
            foreach ($responseData['links'] ?? [] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if ($approvalUrl) {
                Log::info('PayPal gateway: Payment initiated successfully', [
                    'order_id' => $responseData['id'],
                    'approval_url' => $approvalUrl,
                    'payment_type' => 'paypal_account'
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $approvalUrl
                ];
            } else {
                throw new RuntimeException('PayPal approval URL not found');
            }
        } else {
            $errorMessage = $this->extractPayPalErrorMessage($responseData);
            throw new RuntimeException('Failed to create PayPal order: ' . $errorMessage);
        }
    }

    public function getAccessToken(): ?string
    {
        try {
            Log::info('PayPal gateway: Attempting to get access token', [
                'base_url' => $this->baseUrl,
                'client_id_length' => strlen($this->clientId),
                'client_secret_length' => strlen($this->clientSecret)
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            $data = $response->json();
            Log::info('PayPal gateway: OAuth response', ['response' => $data]);

            if (isset($data['access_token'])) {
                Log::info('PayPal gateway: Access token obtained successfully');
                return $data['access_token'];
            } else {
                Log::error('PayPal gateway: Failed to get access token', ['response' => $data]);
                return null;
            }
        } catch (Exception $e) {
            Log::error('PayPal gateway: Error getting access token: ' . $e->getMessage());
            return null;
        }
    }

    protected function isCurrencySupported(string $currency): bool
    {
        $supportedCurrencies = config('paypal.supported_currencies', []);
        return in_array(strtoupper($currency), $supportedCurrencies);
    }

    protected function extractPayPalErrorMessage(array $response): string
    {
        if (isset($response['error']['details'][0]['issue'])) {
            $issue = $response['error']['details'][0]['issue'];
            $description = $response['error']['details'][0]['description'] ?? '';

            switch ($issue) {
                case 'CURRENCY_NOT_SUPPORTED':
                    return "PayPal does not support this currency. Please try a different payment method.";
                case 'INVALID_CURRENCY_CODE':
                    return "Invalid currency code. Please contact support.";
                case 'INVALID_AMOUNT':
                    return "Invalid payment amount. Please check your order total.";
                case 'PAYER_ACCOUNT_LOCKED_OR_CLOSED':
                    return "PayPal account is locked or closed. Please use a different payment method.";
                default:
                    return $description ?: "PayPal error: {$issue}";
            }
        }

        return $response['error']['message'] ?? 'Unknown PayPal error';
    }

    public function captureOrder(Payment $payment, string $orderId): array
    {
        try {
            Log::info('PayPal gateway: Capturing order', [
                'payment_id' => $payment->id,
                'paypal_order_id' => $orderId
            ]);

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new RuntimeException('Failed to obtain PayPal access token');
            }

            $url = $this->baseUrl . '/v2/checkout/orders/' . $orderId . '/capture';

            Log::info('PayPal gateway: Capture request details', [
                'url' => $url,
                'method' => 'POST',
                'headers' => [
                    'Authorization' => 'Bearer ' . substr($accessToken, 0, 20) . '...',
                    'Content-Type' => 'application/json'
                ],
                'body' => 'NO_BODY'
            ]);

            // Use Guzzle directly to ensure no body is sent
            $client = new Client();
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            Log::info('PayPal gateway: Capture response', ['response' => $responseData]);

            if (isset($responseData['status']) && $responseData['status'] === 'COMPLETED') {
                // Update payment with capture details
                $payment->update([
                    'status' => 'succeeded',
                    'meta' => array_merge($payment->meta ?? [], [
                        'capture_id' => $responseData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                        'capture_status' => $responseData['status'],
                        'capture_time' => now()->toISOString(),
                        'capture_completed_at' => now()->toISOString(),
                        'capture_result' => [
                            'status' => $responseData['status'],
                            'success' => true,
                            'transaction_id' => $responseData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null
                        ]
                    ])
                ]);

                // Update the associated order status
                if ($payment->order) {
                    $payment->order->update([
                        'status' => OrderStatus::PROCESSING,
                        'payment_status' => PaymentStatus::PAID
                    ]);

                    Log::info('PayPal gateway: Order status updated after successful capture', [
                        'order_id' => $payment->order->id,
                        'new_status' => OrderStatus::PROCESSING->value,
                        'payment_status' => PaymentStatus::PAID->value
                    ]);
                }

                return [
                    'success' => true,
                    'transaction_id' => $responseData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                    'status' => $responseData['status']
                ];
            } else {
                $errorMessage = $this->extractPayPalErrorMessage($responseData);
                return [
                    'success' => false,
                    'message' => 'Payment capture failed: ' . $errorMessage,
                    'response' => $responseData
                ];
            }

        } catch (Exception $e) {
            Log::error('PayPal gateway: Error capturing order: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error capturing payment: ' . $e->getMessage()
            ];
        }
    }

    public function getOrderDetails(string $orderId): ?array
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/v2/checkout/orders/' . $orderId);

            return $response->json();
        } catch (Exception $e) {
            Log::error('PayPal gateway: Error getting order details: ' . $e->getMessage());
            return null;
        }
    }

    // Public method for testing access token (debugging only)
    public function testAccessToken(): array
    {
        try {
            $accessToken = $this->getAccessToken();
            return [
                'success' => true,
                'has_token' => !empty($accessToken),
                'token_length' => strlen($accessToken ?? ''),
                'base_url' => $this->baseUrl,
                'is_sandbox' => str_contains($this->baseUrl, 'sandbox'),
                'client_id' => $this->clientId,
                'client_secret_length' => strlen($this->clientSecret),
                'client_id_length' => strlen($this->clientId)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle return from PayPal payment
     */
    public function handleReturn(array $query): Payment
    {
        Log::info('PayPal gateway: Handling return', ['query' => $query]);

        // For PayPal account payments, the token is the PayPal order ID
        $paypalOrderId = $query['token'] ?? null;

        // For credit card payments, we might have payment_id
        $paymentId = $query['payment_id'] ?? null;

        // Find the payment - either by PayPal order ID or payment ID
        $payment = null;
        if ($paypalOrderId) {
            // Find payment by PayPal order ID
            $payment = Payment::where('meta->paypal_order_id', $paypalOrderId)->first();
            Log::info('PayPal gateway: Found payment by PayPal order ID', [
                'paypal_order_id' => $paypalOrderId,
                'payment_id' => $payment ? $payment->id : 'not found'
            ]);
        } elseif ($paymentId) {
            // Find payment by payment ID
            $payment = Payment::find($paymentId);
            Log::info('PayPal gateway: Found payment by payment ID', [
                'payment_id' => $paymentId,
                'found' => $payment ? 'yes' : 'no'
            ]);
        }

        if (!$payment) {
            throw new RuntimeException('Payment not found for PayPal order ID: ' . $paypalOrderId . ' or payment ID: ' . $paymentId);
        }

        // Check if this is a credit card payment that needs frontend processing
        if (isset($payment->meta['payment_type']) && $payment->meta['payment_type'] === 'credit_card') {
            // For credit card payments, redirect to the credit card page
            // The frontend will handle the capture process
            Log::info('PayPal gateway: Credit card payment detected, redirecting to frontend processing');
            return $payment;
        }

        // For PayPal account payments, check the order status and capture
        if ($paypalOrderId) {
            try {
                Log::info('PayPal gateway: Processing PayPal account payment return', [
                    'payment_id' => $payment->id,
                    'paypal_order_id' => $paypalOrderId
                ]);

                // Get order details from PayPal
                $orderDetails = $this->getOrderDetails($paypalOrderId);
                Log::info('PayPal gateway: PayPal order details', ['order_details' => $orderDetails]);

                if (isset($orderDetails['status']) && $orderDetails['status'] === 'APPROVED') {
                    // Order is approved, capture the payment
                    Log::info('PayPal gateway: Order approved, capturing payment');
                    $captureResult = $this->captureOrder($payment, $paypalOrderId);

                    if ($captureResult['success']) {
                        Log::info('PayPal gateway: Payment captured successfully', [
                            'payment_id' => $payment->id,
                            'capture_result' => $captureResult
                        ]);
                        return $payment;
                    } else {
                        Log::error('PayPal gateway: Payment capture failed', [
                            'payment_id' => $payment->id,
                            'capture_result' => $captureResult
                        ]);
                    }
                } else {
                    Log::warning('PayPal gateway: Order not approved', [
                        'payment_id' => $payment->id,
                        'order_status' => $orderDetails['status'] ?? 'unknown'
                    ]);
                }
            } catch (Exception $e) {
                Log::error('PayPal gateway: Error handling return', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // If we can't process automatically, return the payment as is
        Log::warning('PayPal gateway: Could not process payment automatically', [
            'payment_id' => $payment->id,
            'paypal_order_id' => $paypalOrderId
        ]);
        return $payment;
    }

    public function testConnectivity(): array
    {
        try {
            $token = $this->getAccessToken();
            return [
                'success' => true,
                'access_token' => $token ? 'Token obtained successfully' : 'No token obtained',
                'token_length' => $token ? strlen($token) : 0,
                'gateway_class' => get_class($this),
                'base_url' => $this->baseUrl,
                'is_sandbox' => $this->isSandbox
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
}
