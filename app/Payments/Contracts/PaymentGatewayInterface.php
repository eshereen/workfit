<?php
namespace App\Payments\Contracts;

interface PaymentGatewayInterface
{
    public function charge(array $data): array;
}
