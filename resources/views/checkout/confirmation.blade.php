@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 my-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmation</h1>
                <p class="text-lg text-gray-600">Order #{{ $order->order_number }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-4 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                @if($item->product)
                                    <img src="{{ $item->product->getFirstMediaUrl('main_image', 'thumb') }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-16 h-16 object-cover rounded mr-4">
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item->product->name ?? 'Product' }}</h3>
                                    @if($item->variant)
                                        <p class="text-sm text-gray-600">{{ $item->variant->color }}, {{ $item->variant->size }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-900">Qty: {{ $item->quantity }}</p>
                                <p class="font-medium text-gray-900">{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($item->price, 2) }}</p>
                                <p class="text-sm text-gray-600">Total: {{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Order Details -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Order Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Number:</span>
                                        <span class="text-gray-900">{{ $order->order_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Date:</span>
                                        <span class="text-gray-900">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($order->status->value) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Payment Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($order->payment_status->value) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Payment Method:</span>
                                        <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Customer Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="text-gray-900">{{ $order->first_name }} {{ $order->last_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="text-gray-900">{{ $order->email }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="text-gray-900">{{ $order->phone_number }}</span>
                                    </div>
                                    @if($order->notes)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Notes:</span>
                                        <span class="text-gray-900">{{ $order->notes }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Addresses</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Shipping Address</h3>
                                @php
                                    $shipping = $order->shipping_address;
                                @endphp
                                <div class="text-sm text-gray-600">
                                    <p>{{ $shipping['name'] ?? $order->first_name . ' ' . $order->last_name }}</p>
                                    <p>{{ $shipping['address'] ?? '' }}</p>
                                    <p>{{ $shipping['city'] ?? $order->city }}, {{ $shipping['state'] ?? $order->state }}</p>
                                    <p>{{ $shipping['postal_code'] ?? '' }}</p>
                                    <p>{{ $shipping['country'] ?? '' }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Billing Address</h3>
                                @php
                                    $billing = $order->billing_address;
                                @endphp
                                <div class="text-sm text-gray-600">
                                    <p>{{ $billing['name'] ?? $order->first_name . ' ' . $order->last_name }}</p>
                                    <p>{{ $billing['address'] ?? '' }}</p>
                                    <p>{{ $billing['city'] ?? $order->city }}, {{ $billing['state'] ?? $order->state }}</p>
                                    <p>{{ $billing['postal_code'] ?? '' }}</p>
                                    <p>{{ $billing['country'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Totals</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900">{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if($order->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900">{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->shipping_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="text-gray-900">{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->discount_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-gray-900">-{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif

                            <hr class="border-gray-200">

                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">{{ $currencyInfo['currency_symbol'] ?? '$' }}{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>

                        @if($currencyInfo['currency_code'] !== 'USD')
                        <div class="mt-4 text-sm text-gray-500 text-center p-2 bg-blue-50 rounded">
                            @if($currencyInfo['is_auto_detected'])
                                Prices automatically converted to {{ $currencyInfo['currency_code'] }} ({{ $currencyInfo['currency_symbol'] }}) based on your location
                            @else
                                Prices converted to {{ $currencyInfo['currency_code'] }} ({{ $currencyInfo['currency_symbol'] }})
                            @endif
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-3">
                            <a href="{{ route('home') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                                Continue Shopping
                            </a>

                            @if($order->is_guest)
                                <a href="{{ route('thankyou', $order) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Back to Thank You
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

