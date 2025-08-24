<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderCreated;
use App\Mail\OrderShipped;
use App\Events\PaymentStatusChanged;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{

    public function created(Order $order): void
    {
        if ($order->coupon_id) {
            $order->coupon->increment('used_count');
        }
        //Send email to customer after order created
       // Mail::to($order->email)->queue(new OrderCreated($order));
    }

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
        //Send Mail to customer after shipping
        //if($order->wasChanged('status') && $order->status == 'shipped'){
           // Mail::to($order->email)->queue(new OrderShipped($order));
        }
    }

