<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            Log::info('PayPal webhook received', [
                'event_type' => $request->input('event_type'),
                'resource_type' => $request->input('resource_type'),
                'resource_id' => $request->input('resource.id'),
                'summary' => $request->input('summary')
            ]);

            $eventType = $request->input('event_type');
            $resource = $request->input('resource', []);
            $resourceId = $resource['id'] ?? null;

            if (!$resourceId) {
                Log::error('PayPal webhook: No resource ID found');
                return response()->json(['error' => 'No resource ID'], 400);
            }

            // Find payment by PayPal order ID
            $payment = Payment::where('meta->paypal_order_id', $resourceId)->first();

            if (!$payment) {
                Log::error('PayPal webhook: Payment not found', ['paypal_order_id' => $resourceId]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            Log::info('PayPal webhook: Processing payment', [
                'payment_id' => $payment->id,
                'event_type' => $eventType,
                'resource_id' => $resourceId
            ]);

            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCompleted($payment, $resource);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                    $this->handlePaymentDenied($payment, $resource);
                    break;

                case 'PAYMENT.CAPTURE.PENDING':
                    $this->handlePaymentPending($payment, $resource);
                    break;

                case 'PAYMENT.CAPTURE.REFUNDED':
                    $this->handlePaymentRefunded($payment, $resource);
                    break;

                default:
                    Log::info('PayPal webhook: Unhandled event type', ['event_type' => $eventType]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('PayPal webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    protected function handlePaymentCompleted(Payment $payment, array $resource)
    {
        DB::transaction(function () use ($payment, $resource) {
            // Update payment status
            $payment->update([
                'status' => 'completed',
                'meta' => array_merge($payment->meta ?? [], [
                    'capture_id' => $resource['id'] ?? null,
                    'capture_status' => $resource['status'] ?? null,
                    'capture_amount' => $resource['amount'] ?? null,
                    'capture_time' => now()->toISOString(),
                ])
            ]);

            // Update order status
            $order = $payment->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);

            Log::info('PayPal payment completed', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'capture_id' => $resource['id'] ?? null
            ]);
        });
    }

    protected function handlePaymentDenied(Payment $payment, array $resource)
    {
        DB::transaction(function () use ($payment, $resource) {
            $payment->update([
                'status' => 'failed',
                'meta' => array_merge($payment->meta ?? [], [
                    'denial_reason' => $resource['status_details'] ?? null,
                    'denial_time' => now()->toISOString(),
                ])
            ]);

            $order = $payment->order;
            $order->update([
                'payment_status' => 'failed',
                'status' => 'pending'
            ]);

            Log::info('PayPal payment denied', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'reason' => $resource['status_details'] ?? null
            ]);
        });
    }

    protected function handlePaymentPending(Payment $payment, array $resource)
    {
        $payment->update([
            'status' => 'pending',
            'meta' => array_merge($payment->meta ?? [], [
                'pending_reason' => $resource['status_details'] ?? null,
                'pending_time' => now()->toISOString(),
            ])
        ]);

        Log::info('PayPal payment pending', [
            'payment_id' => $payment->id,
            'reason' => $resource['status_details'] ?? null
        ]);
    }

    protected function handlePaymentRefunded(Payment $payment, array $resource)
    {
        DB::transaction(function () use ($payment, $resource) {
            $payment->update([
                'status' => 'refunded',
                'meta' => array_merge($payment->meta ?? [], [
                    'refund_id' => $resource['id'] ?? null,
                    'refund_time' => now()->toISOString(),
                ])
            ]);

            $order = $payment->order;
            $order->update([
                'payment_status' => 'refunded',
                'status' => 'cancelled'
            ]);

            Log::info('PayPal payment refunded', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'refund_id' => $resource['id'] ?? null
            ]);
        });
    }
}
