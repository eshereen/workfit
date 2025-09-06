<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Payments\Gateways\PaymobGateway;

class PaymobController extends Controller
{
    protected $paymobGateway;

    public function __construct(PaymobGateway $paymobGateway)
    {
        $this->paymobGateway = $paymobGateway;
    }

    /**
     * Handle PayMob callback (success/failure)
     */
    public function callback(Request $request)
    {
        Log::info('PayMob callback received', [
            'all_params' => $request->all(),
            'query_params' => $request->query(),
            'order_id' => $request->get('order_id'),
            'status' => $request->get('status'),
            'hmac' => $request->get('hmac'),
            'obj' => $request->get('obj'),
            'method' => $request->method()
        ]);

        try {
            // Try to get order_id from different sources
            $orderId = $request->get('order_id') 
                      ?? $request->query('order_id')
                      ?? $request->input('order_id');
            
            // If no order_id in query params, try to extract from PayMob obj data
            if (!$orderId && $request->has('obj')) {
                $objData = json_decode($request->get('obj'), true);
                if ($objData && isset($objData['merchant_order_id'])) {
                    $orderId = $objData['merchant_order_id'];
                }
            }
            
            // If still no order_id, try to find by payment reference
            if (!$orderId) {
                // Look for payment by provider_reference in case PayMob sends order ID differently
                $payment = Payment::where('provider', 'paymob')
                    ->where('status', 'pending_redirect')
                    ->latest()
                    ->first();
                
                if ($payment) {
                    $orderId = $payment->order_id;
                    Log::info('PayMob callback: Found order by payment reference', ['order_id' => $orderId]);
                }
            }
            
            if (!$orderId) {
                Log::error('PayMob callback: No order_id found in any format', [
                    'request_data' => $request->all(),
                    'query_data' => $request->query()
                ]);
                return response()->json(['error' => 'Order ID required'], 400);
            }

            $status = $request->get('status') ?? $request->query('status');

            $order = Order::find($orderId);
            if (!$order) {
                Log::error('PayMob callback: Order not found', ['order_id' => $orderId]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            $payment = $order->payments()->where('provider', 'paymob')->latest()->first();
            if (!$payment) {
                Log::error('PayMob callback: Payment not found', ['order_id' => $orderId]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Determine if payment was successful
            // PayMob sends success/failure in different ways, so we need to check multiple sources
            $isSuccess = false;
            
            // Check explicit status parameter
            if ($status === 'success') {
                $isSuccess = true;
            }
            
            // Check PayMob obj data for success indicators
            if ($request->has('obj')) {
                $objData = json_decode($request->get('obj'), true);
                if ($objData) {
                    // PayMob success indicators
                    if (isset($objData['success']) && $objData['success'] === true) {
                        $isSuccess = true;
                    }
                    if (isset($objData['data']) && isset($objData['data']['success']) && $objData['data']['success'] === true) {
                        $isSuccess = true;
                    }
                }
            }
            
            // If no explicit success/failure, assume success if we reach the callback
            // (PayMob typically only redirects to callback on success)
            if ($status === null && !$request->has('obj')) {
                $isSuccess = true;
                Log::info('PayMob callback: Assuming success (no explicit status)', ['order_id' => $orderId]);
            }

            Log::info('PayMob callback: Status determination', [
                'order_id' => $orderId,
                'status_param' => $status,
                'has_obj' => $request->has('obj'),
                'is_success' => $isSuccess
            ]);

            // Handle success/failure based on determined status
            if ($isSuccess) {
                // Verify HMAC if provided
                if ($request->has('hmac') && $request->has('obj')) {
                    $this->verifyHmac($request);
                }

                // Update payment and order status
                DB::transaction(function () use ($payment, $order) {
                    $payment->update([
                        'status' => 'succeeded',
                        'meta' => array_merge($payment->meta ?? [], [
                            'callback_received' => now()->toISOString(),
                            'callback_status' => 'success'
                        ])
                    ]);

                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);
                });

                Log::info('PayMob callback: Payment succeeded', [
                    'order_id' => $orderId,
                    'payment_id' => $payment->id
                ]);

                // Redirect to success page
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('success', 'Payment completed successfully!');

            } else {
                // Payment failed
                DB::transaction(function () use ($payment, $order) {
                    $payment->update([
                        'status' => 'failed',
                        'meta' => array_merge($payment->meta ?? [], [
                            'callback_received' => now()->toISOString(),
                            'callback_status' => 'failure'
                        ])
                    ]);

                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'pending'
                    ]);
                });

                Log::info('PayMob callback: Payment failed', [
                    'order_id' => $orderId,
                    'payment_id' => $payment->id
                ]);

                // Redirect to failure page
                return redirect()->route('checkout')
                    ->with('error', 'Payment failed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('PayMob callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => 'Callback processing failed'], 500);
        }
    }

    /**
     * Verify PayMob HMAC signature
     */
    private function verifyHmac(Request $request)
    {
        $hmac = $request->get('hmac');
        $obj = $request->get('obj');
        $expectedHmac = config('paymob.hmac');

        if (!$expectedHmac) {
            Log::warning('PayMob HMAC key not configured');
            return;
        }

        $calculatedHmac = hash_hmac('sha512', $obj, $expectedHmac);

        if (!hash_equals($calculatedHmac, $hmac)) {
            Log::error('PayMob HMAC verification failed', [
                'received_hmac' => $hmac,
                'calculated_hmac' => $calculatedHmac
            ]);
            throw new \Exception('HMAC verification failed');
        }

        Log::info('PayMob HMAC verification successful');
    }

    /**
     * Handle PayMob webhook
     */
    public function webhook(Request $request)
    {
        Log::info('PayMob webhook received', ['payload' => $request->all()]);
        
        try {
            $this->paymobGateway->handleWebhook($request->all(), $request->header('X-HMAC'));
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('PayMob webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Create payment (placeholder)
     */
    public function createPayment(Request $request)
    {
        // This method can be implemented if needed for direct payment creation
        return response()->json(['message' => 'Use checkout process instead']);
    }

    /**
     * Success page
     */
    public function success(Payment $payment)
    {
        return redirect()->route('thankyou', ['order' => $payment->order_id])
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Cancel page
     */
    public function cancel(Payment $payment)
    {
        return redirect()->route('checkout')
            ->with('error', 'Payment was cancelled. Please try again.');
    }
}
