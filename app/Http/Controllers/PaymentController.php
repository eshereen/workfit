<?php

namespace App\Http\Controllers;

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
                throw new \Exception('Payment not found for this order');
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

        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            Log::error("Webhook error for {$gateway}: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

        // Paymob callbacks now handled by standard methods:
    // - handleWebhook() for /payments/webhook/paymob
    // - handleReturn() for /payments/return/{order_id}
    
    /**
     * Handle Paymob callback with fixed URL (since {order_id} placeholder doesn't work)
     */
    public function handlePaymobCallback(Request $request)
    {
        // Basic debug - this should always appear in logs
        error_log('=== PAYMOB CALLBACK METHOD ENTERED ===');
        error_log('Paymob callback method called!');
        error_log('Request data: ' . json_encode($request->all()));
        
        try {
            Log::info('Paymob callback method called', ['method' => __METHOD__]);
            Log::info('Paymob callback received', ['request' => $request->all()]);
            
            // Extract order ID from the callback data
            $orderId = $request->input('order') ?? 
                       $request->input('merchant_order_id') ?? 
                       $request->input('id');
            
            Log::info('Paymob callback: Extracted order ID', [
                'order_id' => $orderId, 
                'merchant_order_id' => $request->input('merchant_order_id'),
                'order' => $request->input('order'),
                'id' => $request->input('id')
            ]);
            
            if (!$orderId) {
                Log::error('Paymob callback: No order ID found', ['request' => $request->all()]);
                return redirect()->route('checkout')->with('error', 'Invalid callback data');
            }

            // Find the order by merchant_order_id (Paymob sends this)
            // First try to find by merchant_order_id from the callback
            $merchantOrderId = $request->input('merchant_order_id');
            $order = null;
            
            if ($merchantOrderId) {
                // Try exact match first
                $order = Order::where('order_number', $merchantOrderId)->first();
                Log::info('Paymob callback: Searching by exact merchant_order_id', [
                    'merchant_order_id' => $merchantOrderId,
                    'found' => $order ? 'yes' : 'no'
                ]);
                
                
                if (!$order) {
                    // Remove the last part after the last hyphen (Paymob's timestamp suffix)
                    $baseOrderNumber = preg_replace('/-\d+$/', '', $merchantOrderId);
                    Log::info('Paymob callback: Created base order number', [
                        'original' => $merchantOrderId,
                        'base' => $baseOrderNumber
                    ]);
                    
                    $order = Order::where('order_number', $baseOrderNumber)->first();
                    Log::info('Paymob callback: Searching by base order number (removed suffix)', [
                        'base_order_number' => $baseOrderNumber,
                        'original_merchant_order_id' => $merchantOrderId,
                        'found' => $order ? 'yes' : 'no'
                    ]);
                }
                
                // If still not found, try LIKE search
                if (!$order && isset($baseOrderNumber)) {
                    $order = Order::where('order_number', 'LIKE', $baseOrderNumber . '%')->first();
                    Log::info('Paymob callback: Searching with LIKE pattern', [
                        'pattern' => $baseOrderNumber . '%',
                        'found' => $order ? 'yes' : 'no'
                    ]);
                }
            }
            
            // If not found, try by the extracted orderId
            if (!$order && $orderId) {
                $order = Order::where('id', $orderId)
                             ->orWhere('order_number', $orderId)
                             ->first();
                Log::info('Paymob callback: Searching by extracted orderId', [
                    'orderId' => $orderId,
                    'found' => $order ? 'yes' : 'no'
                ]);
            }
            
            if (!$order) {
                Log::error('Paymob callback: Order not found', [
                    'order_id' => $orderId,
                    'merchant_order_id' => $merchantOrderId,
                    'all_request_data' => $request->all()
                ]);
                
                // For debugging, let's show what we're looking for
                $debugInfo = [
                    'searching_for' => [
                        'exact_merchant_order_id' => $merchantOrderId,
                        'base_order_number' => isset($baseOrderNumber) ? $baseOrderNumber : 'not set',
                        'extracted_order_id' => $orderId
                    ],
                    'callback_data' => $request->all()
                ];
                
                Log::error('Paymob callback debug info', $debugInfo);
                
                return redirect()->route('checkout')->with('error', 'Order not found. Please contact support.');
            }

            Log::info('Paymob callback: Order found, processing payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'paymob_order' => $orderId
            ]);

            // Check if payment was successful
            $success = $request->input('success', false);
            $errorOccurred = $request->input('error_occured', false);
            
            // Convert string values to proper booleans
            $success = filter_var($success, FILTER_VALIDATE_BOOLEAN);
            $errorOccurred = filter_var($errorOccurred, FILTER_VALIDATE_BOOLEAN);
            
            if ($success && !$errorOccurred) {
                // Payment successful - update order and payment status
                $payment = $order->payments()->latest()->first();
                if ($payment) {
                    $payment->update(['status' => 'succeeded']);
                }
                
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
                
                Log::info('Paymob payment marked as successful', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id ?? 'none'
                ]);
                
                return redirect()->route('thankyou', ['order' => $order->id])
                               ->with('success', 'Payment completed successfully!');
            } else {
                // Payment failed
                Log::warning('Paymob payment failed', [
                    'order_id' => $order->id,
                    'success' => $success,
                    'error_occured' => $errorOccurred
                ]);
                
                return redirect()->route('checkout')->with('error', 'Payment failed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Paymob callback error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Payment verification failed');
        }
    }


}
