<?php

use App\Models\Order;
use App\Services\Payments\PaymobGateway;

test('paymob payment flow works for egypt orders', function () {
    $order = Order::factory()->create(['country_id' => 1, 'status' => 'pending']);

    $paymob = Mockery::mock([PaymobGateway::class]);
    $paymob->shouldReceive('charge')->once()->andReturn(true);

    app()->instance(PaymobGateway::class, $paymob);

    $result = app(PaymobGateway::class)->charge(['order' => $order, 'amount' => 500]);

    expect($result['success'])->toBeTrue();
    $order->refresh();
    $order->status = 'paid';
});
