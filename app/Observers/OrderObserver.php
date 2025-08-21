<?php

namespace App\Observers;

use App\Events\PaymentStatusChanged;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if payment_status changed
        if ($order->wasChanged('payment_status')) {
            $oldStatus = $order->getOriginal('payment_status');
            $newStatus = $order->payment_status;

            // Fire event for payment status change
            event(new PaymentStatusChanged($order, $oldStatus, $newStatus));
        }
    }
}
