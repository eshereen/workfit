<?php

namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandlePaymentStatusChange implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentStatusChanged $event)
    {
        $order = $event->order;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        Log::info('Payment status changed', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $order->user_id
        ]);

        // Handle specific status changes
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            // Payment was just confirmed - you can add additional logic here
            // For example: send confirmation emails, update inventory, etc.
            Log::info('Payment confirmed for order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'amount' => $order->total_amount
            ]);
        }

        if ($newStatus === 'failed' && $oldStatus !== 'failed') {
            // Payment failed - you can add logic here
            Log::info('Payment failed for order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);
        }
    }
}
