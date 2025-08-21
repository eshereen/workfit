<?php

namespace App\Services\Payments\Contracts;

use App\Models\Order;

interface PaymentGateway
{
    public function charge(Order $order, float $amount): bool;
}
