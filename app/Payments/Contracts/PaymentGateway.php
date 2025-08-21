<?php

namespace App\Payments\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGateway
{
    public function charge(array $data): array;
    public function isAvailableForCountry(string $countryCode): bool;
    public function initiate(Order $order, Payment $payment): array; // ['payment'=>Payment,'redirect_url'=>?string]
    public function handleReturn(array $query): Payment;
    public function handleWebhook(array $payload, ?string $signature = null): void;
}

