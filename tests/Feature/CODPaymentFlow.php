<?php

use App\Models\Order;
use CodGateway;

test('cod payment flow sets order as pending payment', function () {
    $order = Order::factory()->create(['country_id' => 1, 'status' => 'pending']);

    $cod = app(CodGateway::class);

    $result = $cod->charge($order, 300);

    expect($result)->toBeTrue();
    $order->refresh();
    expect($order->status)->toBe('pending_payment');
});
