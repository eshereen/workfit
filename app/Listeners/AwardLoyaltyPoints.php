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

    public function handle(OrderPlaced $event)
    {
        $order = $event->order;

        // Only award points if the order has an associated user (not guest orders)
        if (!$order->user) {
            return;
        }

        // Award points immediately when order is placed
        // This provides better user experience - users see points right away

        // Calculate points based on dollar amount (convert cents to dollars)
        $dollarAmount = $order->total_amount / 100;
        $points = $dollarAmount * config('loyalty.rules.purchase.points_per_dollar', 1);

        $this->loyaltyService->addPoints(
            $order->user,
            (int) $points,
            'purchase',
            $order
        );
    }
}
