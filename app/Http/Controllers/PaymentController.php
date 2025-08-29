<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle payment return from gateway
     */
    public function handleReturn(Request $request, Order $order)
    {
        try {
            Log::info('Payment return callback received', [
                'order_id' => $order->id,
                'request_data' => $request->all(),
                'route_name' => 'payments.return'
            ]);

            $payment = $order->payments()->latest()->first();

            if (!$payment) {
                throw new Exception('Payment not found for this order');
            }

            // Special handling for Paymob payments
            if ($payment->provider === 'paymob') {
                Log::info('Paymob payment return detected', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id
                ]);

                // For Paymob, we assume success if we reach this point
                $payment->update(['status' => 'succeeded']);
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);

                Log::info('Paymob payment marked as successful', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id
                ]);
            } else {
                // Handle other payment gateways
                $gateway = $this->paymentService->gateway(PaymentMethod::from($payment->provider));
                $result = $gateway->handleReturn($request->all());

                // Update payment status
                $payment->update(['status' => 'succeeded']);

                // Update order status
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
            }

            return redirect()->route('thankyou', ['order' => $order->id])
                           ->with('success', 'Payment completed successfully!');

        } catch (Exception $e) {
            Log::error('Payment return error: ' . $e->getMessage());

            return redirect()->route('thankyou', ['order' => $order->id])
                           ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Handle payment cancellation
     */
    public function handleCancel(Request $request, Order $order)
    {
        $payment = $order->payments()->latest()->first();

        if ($payment) {
            $payment->update(['status' => 'cancelled']);
        }

        $order->update([
            'payment_status' => 'failed',
            'status' => 'cancelled'
        ]);

        return redirect()->route('checkout')
                       ->with('error', 'Payment was cancelled. Please try again.');
    }

    /**
     * Handle webhook from payment gateway
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        try {
            $gatewayInstance = $this->paymentService->gateway(PaymentMethod::from($gateway));
            $gatewayInstance->handleWebhook($request->all(), $request->header('Signature'));

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            Log::error("Webhook error for {$gateway}: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

        // Paymob callbacks now handled by standard methods:
    // - handleWebhook() for /payments/webhook/paymob
    // - handleReturn() for /payments/return/{order_id}

    /**
     * Handle Paymob callback - simplified and improved version
     */
    public function handlePaymobCallback(Request $request)
    {
        try {
            Log::info('Paymob callback received', [
                'query_params' => $request->query(),
                'all_params' => $request->all(),
                'method' => $request->method()
            ]);

            // First try to get order_id from query parameters (our custom parameter)
            $orderId = $request->query('order_id') ?? $request->input('order_id');

            // If not found, try extracting from Paymob's callback data
            if (!$orderId) {
                $orderId = $request->input('merchant_order_id') ??
                           $request->input('order') ??
                           $request->input('id');
            }

            Log::info('Paymob callback: Order ID extraction', [
                'extracted_order_id' => $orderId,
                'from_query' => $request->query('order_id'),
                'status_param' => $request->query('status')
            ]);

            if (!$orderId) {
                Log::error('Paymob callback: No order ID found', ['request' => $request->all()]);
                return redirect()->route('checkout')->with('error', 'Invalid payment callback');
            }

            // Find the order
            $order = Order::find($orderId);

            // If not found by ID, try by order number
            if (!$order) {
                $order = Order::where('order_number', $orderId)->first();

                // If still not found and we have merchant_order_id, try removing timestamp suffix
                if (!$order && $request->input('merchant_order_id')) {
                    $merchantOrderId = $request->input('merchant_order_id');
                    $baseOrderNumber = preg_replace('/-\d+$/', '', $merchantOrderId);
                    $order = Order::where('order_number', $baseOrderNumber)->first();
                }
            }

            if (!$order) {
                Log::error('Paymob callback: Order not found', [
                    'searched_order_id' => $orderId,
                    'merchant_order_id' => $request->input('merchant_order_id')
                ]);
                return redirect()->route('checkout')->with('error', 'Order not found');
            }

            Log::info('Paymob callback: Order found', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Determine payment success based on multiple indicators
            $statusParam = $request->query('status'); // Our custom status parameter
            $success = $request->input('success', false);
            $errorOccurred = $request->input('error_occured', false);

            // Convert to proper booleans
            $success = filter_var($success, FILTER_VALIDATE_BOOLEAN);
            $errorOccurred = filter_var($errorOccurred, FILTER_VALIDATE_BOOLEAN);

            // Payment is successful if:
            // 1. status=success in query params, OR
            // 2. success=true and error_occured=false from Paymob
            $paymentSuccessful = ($statusParam === 'success') || ($success && !$errorOccurred);

            Log::info('Paymob callback: Payment status evaluation', [
                'status_param' => $statusParam,
                'paymob_success' => $success,
                'paymob_error_occurred' => $errorOccurred,
                'final_success' => $paymentSuccessful
            ]);

            if ($paymentSuccessful) {
                // Update payment status
                $payment = $order->payments()->latest()->first();
                if ($payment) {
                    $payment->update(['status' => 'succeeded']);
                }

                // Update order status
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);

                Log::info('Paymob payment completed successfully', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id ?? 'none'
                ]);

                return redirect()->route('thankyou', ['order' => $order->id])
                               ->with('success', 'Payment completed successfully!');
            } else {
                // Payment failed
                $payment = $order->payments()->latest()->first();
                if ($payment) {
                    $payment->update(['status' => 'failed']);
                }

                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'pending'
                ]);

                Log::warning('Paymob payment failed', [
                    'order_id' => $order->id,
                    'status_param' => $statusParam,
                    'paymob_success' => $success,
                    'paymob_error_occurred' => $errorOccurred
                ]);

                return redirect()->route('checkout')->with('error', 'Payment failed. Please try again.');
            }

        } catch (Exception $e) {
            Log::error('Paymob callback error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return redirect()->route('checkout')->with('error', 'Payment verification failed');
        }
    }


}
