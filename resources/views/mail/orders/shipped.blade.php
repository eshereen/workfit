<x-mail::message>
Hello {{ $order->customer->full_name }}

Your order has been shipped successfully.
Order ID: {{ $order->id }}
Order Date: {{ $order->created_at->format('d-m-Y') }}
Order Status: {{ $order->status }}
Order Total: {{ $order->total_amount }}
@foreach ($order->items as $item)
    {{ $item->product->name }} - {{ $item->quantity }} - {{ $item->price }}
@endforeach
<x-mail::button :url="config('app.url')">
Visit our website
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
