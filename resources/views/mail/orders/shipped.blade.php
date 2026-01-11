<x-mail::message>
# Order Status Update

Hello **{{ $order->first_name }} {{ $order->last_name }}**,

Your order status has been updated to: **{{ $order->status->value }}**

## Order Details

**Order Number:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('F j, Y') }}  
**Payment Method:** {{ $order->payment_method }}  
**Payment Status:** {{ $order->payment_status->value }}

---

## Shipping Information

**Address:** {{ $order->shipping_address ?? $order->billing_address }}  
@if($order->shipping_building_number || $order->billing_building_number)
**Building Number:** {{ $order->shipping_building_number ?? $order->billing_building_number }}  
@endif
**City:** {{ $order->city }}  
**State:** {{ $order->state }}  
**Country:** {{ $order->country?->name ?? 'N/A' }}

---

## Order Items

@foreach ($order->items as $item)
**{{ $item->product->name }}**  
@if($item->variant)
- Color: {{ $item->variant->color }}  
- Size: {{ $item->variant->size }}  
- SKU: {{ $item->variant->sku }}  
@endif
- Quantity: {{ $item->quantity }}  
- Price: {{ $order->currency }} {{ \App\Models\Product::formatPrice($item->price) }}  
- Subtotal: {{ $order->currency }} {{ \App\Models\Product::formatPrice($item->price * $item->quantity) }}

---
@endforeach

## Order Summary

| Item | Amount |
|:-----|-------:|
| Subtotal | {{ $order->currency }} {{ \App\Models\Product::formatPrice($order->subtotal) }} |
| Shipping | {{ $order->currency }} {{ \App\Models\Product::formatPrice($order->shipping_amount) }} |
| Tax | {{ $order->currency }} {{ \App\Models\Product::formatPrice($order->tax_amount) }} |
@if($order->discount_amount > 0)
| Discount | -{{ $order->currency }} {{ \App\Models\Product::formatPrice($order->discount_amount) }} |
@endif
| **Total** | **{{ $order->currency }} {{ \App\Models\Product::formatPrice($order->total_amount) }}** |

---

@if($order->notes)
## Order Notes
{{ $order->notes }}

---
@endif

<x-mail::button :url="config('app.url')">
Visit Our Store
</x-mail::button>

Thank you for your order!

Best regards,  
{{ config('app.name') }}
</x-mail::message>
