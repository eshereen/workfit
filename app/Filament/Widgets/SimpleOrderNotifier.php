<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;

class SimpleOrderNotifier extends Widget
{
    protected string $view = 'filament.widgets.simple-order-notifier';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public ?int $latestOrderId = null;
    public bool $hasNewOrder = false;
    public ?Order $newOrder = null;

    public function mount(): void
    {
        $this->latestOrderId = Order::max('id');
    }

    public function checkForNewOrders(): void
    {
        $currentLatest = Order::max('id');

        if ($currentLatest && (!$this->latestOrderId || $currentLatest > $this->latestOrderId)) {
            $this->newOrder = Order::find($currentLatest);
            $this->latestOrderId = $currentLatest;
            $this->hasNewOrder = true;

            // Trigger browser event for sound and notification
            $this->dispatch('new-order-created', [
                'orderId' => $this->newOrder?->id,
                'message' => "New order #{$this->newOrder?->id} received!"
            ]);
        }
    }

    public function dismissNotification(): void
    {
        $this->hasNewOrder = false;
        $this->newOrder = null;
    }
}
