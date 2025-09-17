<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;

class OrderPing extends Component
{
    public ?int $latestOrderId = null;

    public function mount(): void
    {
        $this->latestOrderId = Order::max('id');
    }

    public function check(): void
    {
        $currentLatest = Order::max('id');

        if ($currentLatest && (!$this->latestOrderId || $currentLatest > $this->latestOrderId)) {
            $order = Order::find($currentLatest);
            $this->latestOrderId = $currentLatest;

            $this->dispatchBrowserEvent('admin-new-order', [
                'id' => $order?->id,
                'number' => $order?->id,
                'created_at' => optional($order?->created_at)->toDateTimeString(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order-ping');
    }
}
