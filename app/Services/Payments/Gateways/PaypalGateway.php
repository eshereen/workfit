<?php

use App\Models\Order;
use App\Services\Payments\Contracts\PaymentGateway;

class PaypalGateway implements PaymentGateway
{
    public function charge(Order $order, float $amount): bool
    {
        // PayPal API logic...
        $order->update(['status' => 'paid']);
        return true;
    }
}
