<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderPing extends Component
{
    public ?int $latestOrderId = null;
    public bool $hasNewOrder = false;

    public function mount(): void
    {
        $this->latestOrderId = Order::max('id');
    }

    public function checkForNewOrders(): void
    {
        $currentLatest = Order::max('id');

        if ($currentLatest && (!$this->latestOrderId || $currentLatest > $this->latestOrderId)) {
            $order = Order::find($currentLatest);
            $this->latestOrderId = $currentLatest;
            $this->hasNewOrder = true;

            // Dispatch to browser
            $this->dispatch('new-order-notification', [
                'orderId' => $order?->id,
                'orderNumber' => $order?->id,
                'message' => "New order #" . $order?->id . " received!",
            ]);
        }
    }

    public function markAsRead(): void
    {
        $this->hasNewOrder = false;
    }

    public function render()
    {
        return view('livewire.admin.order-ping');
    }
}
