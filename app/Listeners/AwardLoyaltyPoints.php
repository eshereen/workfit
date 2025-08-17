<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\LoyaltyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardLoyaltyPoints implements ShouldQueue
{
    use InteractsWithQueue;

    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

  // app/Listeners/AwardLoyaltyPoints.php
public function handle(OrderPlaced $event)
{
    $order = $event->order;

    // Only award points for paid orders
    if ($order->payment_status !== 'paid') {
        return;
    }

    $points = $order->total_amount * config('loyalty.rules.purchase.points');

    $this->loyaltyService->addPoints(
        $order->user,
        $points,
        'purchase',
        $order
    );
}
}
