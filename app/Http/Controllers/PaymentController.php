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
            $payment = $order->payments()->latest()->first();

            if (!$payment) {
                throw new \Exception('Payment not found for this order');
            }

            $gateway = $this->paymentService->gateway(PaymentMethod::from($payment->provider));
            $result = $gateway->handleReturn($request->all());

            // Update payment status
            $payment->update(['status' => 'succeeded']);

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);

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

        return redirect()->route('checkout.index')
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


}
