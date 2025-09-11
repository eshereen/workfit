<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Payments\Gateways\PaypalGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PayPalCreditCardController extends Controller
{
    protected $paypalGateway;

    public function __construct(PaypalGateway $paypalGateway)
    {
        $this->paypalGateway = $paypalGateway;
    }

    /**
     * Show PayPal credit card payment form
     */
    public function showForm(Payment $payment)
    {
        try {
            // Verify this is a PayPal credit card payment
            if ($payment->provider !== 'paypal' ||
                ($payment->meta['payment_type'] ?? '') !== 'credit_card') {
                Log::error('PayPal Credit Card Controller: Invalid payment type', [
                    'payment_id' => $payment->id,
                    'provider' => $payment->provider,
                    'payment_type' => $payment->meta['payment_type'] ?? 'unknown'
                ]);
                abort(404, 'Invalid payment type');
            }

            $order = $payment->order;
            if (!$order) {
                Log::error('PayPal Credit Card Controller: Order not found', [
                    'payment_id' => $payment->id
                ]);
                abort(404, 'Order not found');
            }

            Log::info('PayPal Credit Card Controller: Showing payment form', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'amount' => $payment->amount_minor
            ]);

            return view('checkout.paypal-credit-card', compact('payment', 'order'));

        } catch (\Exception $e) {
            Log::error('PayPal Credit Card Controller: Error showing form', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'An error occurred while loading the payment form');
        }
    }

    /**
     * Capture PayPal credit card payment
     */
    public function captureOrder(Request $request, Payment $payment)
    {
        try {
            $validated = $request->validate([
                'paypal_order_id' => 'required|string',
            ]);

            $paypalOrderId = $validated['paypal_order_id'];

            Log::info('PayPal Credit Card Controller: Capturing order', [
                'payment_id' => $payment->id,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Update payment with PayPal order ID
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paypal_order_id' => $paypalOrderId,
                    'capture_attempted_at' => now()->toISOString()
                ])
            ]);

            // Use the existing gateway to capture the order
            $result = $this->paypalGateway->captureOrder($payment, $paypalOrderId);

            if ($result['success']) {
                Log::info('PayPal Credit Card Controller: Payment captured successfully', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $result['transaction_id'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment captured successfully',
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'redirect_url' => route('checkout.paypal.credit-card.success', ['payment' => $payment->id])
                ]);
            } else {
                Log::error('PayPal Credit Card Controller: Payment capture failed', [
                    'payment_id' => $payment->id,
                    'capture_result' => $result
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to capture payment'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayPal Credit Card Controller: Error capturing order', [
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
     * Handle successful PayPal credit card payment
     */
    public function success(Request $request, Payment $payment)
    {
        try {
            Log::info('PayPal Credit Card Controller: Handling success', [
                'payment_id' => $payment->id,
                'query_params' => $request->query()
            ]);

            // Verify payment is successful
            if ($payment->status !== 'succeeded') {
                Log::warning('PayPal Credit Card Controller: Payment not succeeded', [
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);

                return redirect()->route('checkout')
                    ->with('error', 'Payment was not completed successfully. Please contact support.');
            }

            $order = $payment->order;
            if (!$order) {
                Log::error('PayPal Credit Card Controller: Order not found for successful payment', [
                    'payment_id' => $payment->id
                ]);
                return redirect()->route('checkout')
                    ->with('error', 'Order not found. Please contact support.');
            }

            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Payment completed successfully!');

        } catch (\Exception $e) {
            Log::error('PayPal Credit Card Controller: Error in success handler', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    /**
     * Handle cancelled PayPal credit card payment
     */
    public function cancel(Request $request, Payment $payment)
    {
        try {
            Log::info('PayPal Credit Card Controller: Handling cancel', [
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
            Log::error('PayPal Credit Card Controller: Error in cancel handler', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout')
                ->with('error', 'An error occurred while processing your cancellation. Please contact support.');
        }
    }
}
