<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;

class PaymentReturnController extends Controller
{
    public function handle(Request $request)
    {
        // PayPal: token; Paymob: merchant_order_id (optional)
        // For simplicity, redirect to thankyou using order id from payment
        $payment = Payment::latest()->whereNotNull('order_id')->first(); // or resolve by query keys
        return redirect()->route('thankyou', ['order' => $payment->order_id])->with('success', 'Payment processed.');
    }

    public function cancel(Request $request)
    {
        $payment = Payment::latest()->whereNotNull('order_id')->first();
        if ($payment) {
            $payment->update(['status' => 'canceled']);
            $payment->order->update(['payment_status' => PaymentStatus::FAILED, 'status' => OrderStatus::PENDING]);
            return redirect()->route('thankyou', ['order' => $payment->order_id])
                ->with('error', 'Payment canceled.');
        }
        return redirect()->route('cart.index')->with('error', 'Payment canceled.');
    }
}
