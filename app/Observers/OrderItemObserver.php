<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        Log::info('OrderItem created, decreasing stock', [
            'order_item_id' => $orderItem->id,
            'product_variant_id' => $orderItem->product_variant_id,
            'quantity' => $orderItem->quantity
        ]);

        // Decrease variant stock if this order item has a variant
        if ($orderItem->product_variant_id) {
            $variant = ProductVariant::find($orderItem->product_variant_id);
            if ($variant) {
                $variant->decrement('stock', $orderItem->quantity);
                Log::info('Stock decreased for variant', [
                    'variant_id' => $variant->id,
                    'decreased_by' => $orderItem->quantity,
                    'new_stock' => $variant->fresh()->stock
                ]);
            }
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     * This restores stock when an order item is deleted (e.g., order cancellation)
     */
    public function deleted(OrderItem $orderItem): void
    {
        Log::info('OrderItem deleted, restoring stock', [
            'order_item_id' => $orderItem->id,
            'product_variant_id' => $orderItem->product_variant_id,
            'quantity' => $orderItem->quantity
        ]);

        // Restore variant stock if this order item had a variant
        if ($orderItem->product_variant_id) {
            $variant = ProductVariant::find($orderItem->product_variant_id);
            if ($variant) {
                $variant->increment('stock', $orderItem->quantity);
                Log::info('Stock restored for variant', [
                    'variant_id' => $variant->id,
                    'restored_by' => $orderItem->quantity,
                    'new_stock' => $variant->fresh()->stock
                ]);
            }
        }
    }
}
