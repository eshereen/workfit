<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderCreated;
use App\Mail\OrderShipped;
use App\Events\PaymentStatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderObserver
{

    public function created(Order $order): void
    {
        if ($order->coupon_id) {
            $order->coupon->increment('used_count');
        }
        //Send email to customer after order created
        // Temporarily disabled for debugging
        try {
            Mail::to($order->email)->later(now()->addSeconds(5), new OrderCreated($order));
        } catch (Exception $e) {
            Log::error('Email sending failed but continuing with order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
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

        // Restore stock when order is cancelled
        if ($order->wasChanged('status') && $order->status->value === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                        Log::info('Stock restored for cancelled order', [
                            'order_id' => $order->id,
                            'variant_id' => $variant->id,
                            'restored_quantity' => $item->quantity,
                            'new_stock' => $variant->fresh()->stock
                        ]);
                    }
                }
            }
        }

        //Send Mail to customer after shipping
        //if($order->wasChanged('status') && $order->status == 'shipped'){
           // Mail::to($order->email)->queue(new OrderShipped($order));
        }
    }

