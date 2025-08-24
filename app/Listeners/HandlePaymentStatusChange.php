<?php

namespace App\Listeners;

use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Log;
use App\Events\PaymentStatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePaymentStatusChange implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentStatusChanged $event)
    {
        $order = $event->order;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

      

        // Handle specific status changes
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            // Payment was just confirmed - you can add additional logic here
            // For example: send confirmation emails, update inventory, etc.
            Mail::to($order->email)->queue(new OrderCreated($order));
          
        }

        if ($newStatus === 'failed' && $oldStatus !== 'failed') {
            // Payment failed - you can add logic here
           
        }
    }
}
