<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Payments\Gateways\PaypalGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PayPalController extends Controller
{
    protected $paypalGateway;

    public function __construct(PaypalGateway $paypalGateway)
    {
        $this->paypalGateway = $paypalGateway;
    }

    /**
     * Create PayPal order for account payments
     */
    public function createOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_id' => 'required|exists:payments,id',
            ]);

            $payment = Payment::findOrFail($validated['payment_id']);
            $order = $payment->order;

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found for this payment'
                ], 404);
            }

            // Verify this is a PayPal account payment
            if ($payment->provider !== 'paypal' ||
                ($payment->meta['payment_type'] ?? '') !== 'paypal_account') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment type for PayPal account payment'
                ], 400);
            }

            Log::info('PayPal Controller: Creating order for PayPal account payment', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'amount' => $payment->amount_minor
            ]);

            // Use the existing gateway to initiate payment
            $result = $this->paypalGateway->initiate($order, $payment, false);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $result['redirect_url']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to create PayPal order'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayPal Controller: Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the PayPal order'
            ], 500);
        }
    }

    /**
     * Capture PayPal order after user approval
     */
    public function captureOrder(Request $request, Payment $payment)
    {
        try {
            $validated = $request->validate([
                'paypal_order_id' => 'required|string',
            ]);

            $paypalOrderId = $validated['paypal_order_id'];

            Log::info('PayPal Controller: Capturing order', [
                'payment_id' => $payment->id,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Use the existing gateway to capture the order
            $result = $this->paypalGateway->captureOrder($payment, $paypalOrderId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment captured successfully',
                    'transaction_id' => $result['transaction_id'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to capture payment'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayPal Controller: Error capturing order', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while capturing the payment'
            ], 500);
        }
    }

    /**
     * Handle successful PayPal payment return
     */
    public function success(Request $request, Payment $payment)
    {
        try {
            Log::info('PayPal Controller: Handling success return', [
                'payment_id' => $payment->id,
                'query_params' => $request->query()
            ]);

            // Get PayPal order ID from query parameters
            $paypalOrderId = $request->query('token');

            if (!$paypalOrderId) {
                Log::error('PayPal Controller: No PayPal order ID in success return', [
                    'payment_id' => $payment->id,
                    'query_params' => $request->query()
                ]);

                return redirect()->route('checkout.failure')
                    ->with('error', 'Invalid payment return. Please contact support.');
            }

            // Update payment with PayPal order ID if not already set
            if (!isset($payment->meta['paypal_order_id'])) {
                $payment->update([
                    'meta' => array_merge($payment->meta ?? [], [
                        'paypal_order_id' => $paypalOrderId
                    ])
                ]);
            }

            // Capture the order
            $captureResult = $this->paypalGateway->captureOrder($payment, $paypalOrderId);

            if ($captureResult['success']) {
                Log::info('PayPal Controller: Payment captured successfully', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $captureResult['transaction_id'] ?? null
                ]);

                return redirect()->route('checkout.confirmation', $payment->order->id)
                    ->with('success', 'Payment completed successfully!');
            } else {
                Log::error('PayPal Controller: Payment capture failed', [
                    'payment_id' => $payment->id,
                    'capture_result' => $captureResult
                ]);

            return redirect()->route('checkout')
                ->with('error', 'Payment capture failed: ' . ($captureResult['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('PayPal Controller: Error in success handler', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    /**
     * Handle cancelled PayPal payment
     */
    public function cancel(Request $request, Payment $payment)
    {
        try {
            Log::info('PayPal Controller: Handling cancel return', [
                'payment_id' => $payment->id,
                'query_params' => $request->query()
            ]);

            // Update payment status to cancelled
            $payment->update([
                'status' => 'cancelled',
                'meta' => array_merge($payment->meta ?? [], [
                    'cancelled_at' => now()->toISOString(),
                    'cancelled_reason' => 'User cancelled payment'
                ])
            ]);

            // Update order status
            if ($payment->order) {
                $payment->order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'cancelled'
                ]);
            }

            return redirect()->route('checkout')
                ->with('error', 'Payment was cancelled. You can try again or choose a different payment method.');

        } catch (\Exception $e) {
            Log::error('PayPal Controller: Error in cancel handler', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your cancellation. Please contact support.');
        }
    }
}
