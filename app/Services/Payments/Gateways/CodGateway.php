<?php

use App\Models\Order;
use App\Services\Payments\Contracts\PaymentGateway;

class CodGateway implements PaymentGateway
{
    public function charge(Order $order, float $amount): bool
    {
        $order->update(['status' => 'pending_payment']);
        return true;
    }
}
