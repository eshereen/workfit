<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class OrderNotificationWidget extends Widget
{
    protected string $view = 'filament.widgets.order-notification';

    protected static bool $isLazy = false;

    public ?int $latestOrderId = null;

    public function mount(): void
    {
        $this->latestOrderId = Order::max('id');
    }

    public function checkForNewOrders(): void
    {
        $currentLatest = Order::max('id');

        if ($currentLatest && (!$this->latestOrderId || $currentLatest > $this->latestOrderId)) {
            $newOrders = Order::where('id', '>', $this->latestOrderId ?? 0)->get();

            foreach ($newOrders as $order) {
                $this->sendNotification($order);
            }

            $this->latestOrderId = $currentLatest;

            // Dispatch browser event for sound
            $this->dispatch('play-order-sound', [
                'count' => $newOrders->count(),
                'latestOrderId' => $currentLatest
            ]);
        }
    }

    protected function sendNotification(Order $order): void
    {
        Notification::make()
            ->title('New Order Received!')
            ->body("Order #{$order->id} has been placed")
            ->success()
            ->persistent()
            ->send();
    }
}
