<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Contracts\PaymentGateway;

class PaymobGateway implements PaymentGatewayInterface{
    public function charge(array $data): array
    {
        // Call Paymob API here...
        // For now just simulate success:
        $order = $data['order'] ?? null;
        if ($order) {
            $order->update(['status' => 'paid']);
        }
        return ['success' => true];
    }
}
