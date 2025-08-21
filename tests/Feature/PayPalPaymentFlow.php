<?php

use App\Models\Order;
use PaypalGateway;
test('paypal payment flow marks order as paid', function () {
    $order = Order::factory()->create(['country_id' => 3, 'status' => 'pending']);

    $paypal = Mockery::mock([PaypalGateway::class]);
    $paypal->shouldReceive('charge')->once()->andReturn(true);

    app()->instance(PaypalGateway::class, $paypal);

    $result = app(PaypalGateway::class)->charge($order, 100);

    expect($result)->toBeTrue();
    $order->refresh();
    $order->status = 'paid';
});
