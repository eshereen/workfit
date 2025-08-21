<?php

use App\Models\User;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayManager;

test('egypt customers see paymob and cod', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'billing_country' => 'EG']);

    $availableMethods = app(PaymentGatewayManager::class)->getAvailableGateways($order);

    expect($availableMethods)->toContain('paymob', 'cod');
    expect($availableMethods)->not()->toContain('paypal');
});

test('gulf customers see paypal', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'billing_country' => 'SA']);

    $availableMethods = app(PaymentGatewayManager::class)->getAvailableGateways($order);

    expect($availableMethods)->toContain('paypal');
    expect($availableMethods)->not()->toContain('cod'); // No COD outside Egypt
});

test('international customers see paypal', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'billing_country' => 'US']);

    $availableMethods = app(PaymentGatewayManager::class)->getAvailableGateways($order);

    expect($availableMethods)->toContain('paypal');
});
