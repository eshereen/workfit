@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                <p class="text-gray-600 mt-2">Complete your order</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    @if(Auth::check())
                        <!-- Authenticated User Checkout -->
                        <form action="{{ route('checkout.authenticated') }}" method="POST" class="space-y-6">
                            @csrf
                            @include('checkout.partials.customer-info')
                            @include('checkout.partials.shipping-address')
                            @include('checkout.partials.billing-address')
                            @include('checkout.partials.payment-method')
                            @include('checkout.partials.order-notes')

                            <div class="bg-white rounded-lg shadow-md p-6">
                                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors">
                                    Place Order
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Guest Checkout -->
                        <form action="{{ route('checkout.guest') }}" method="POST" class="space-y-6">
                            @csrf
                            @include('checkout.partials.guest-info')
                            @include('checkout.partials.shipping-address')
                            @include('checkout.partials.billing-address')
                            @include('checkout.partials.payment-method')
                            @include('checkout.partials.order-notes')

                            <div class="bg-white rounded-lg shadow-md p-6">
                                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors">
                                    Place Order as Guest
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <!-- Cart Items -->
                        @foreach($cartItems as $item)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                @if(isset($item['attributes']['image']))
                                    <img src="{{ $item['attributes']['image'] }}"
                                         alt="{{ $item['name'] }}"
                                         class="w-12 h-12 object-cover rounded mr-3">
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                                    @if(isset($item['attributes']['size']) || isset($item['attributes']['color']))
                                        <p class="text-sm text-gray-600">
                                            @if(isset($item['attributes']['size'])){{ $item['attributes']['size'] }}@endif
                                            @if(isset($item['attributes']['size']) && isset($item['attributes']['color'])), @endif
                                            @if(isset($item['attributes']['color'])){{ $item['attributes']['color'] }}@endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-900">Qty: {{ $item['quantity'] }}</p>
                                <p class="font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($item['price'], 2) }}</p>
                            </div>
                        </div>
                        @endforeach

                        <!-- Order Totals -->
                        <div class="mt-6 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900" data-price="subtotal">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900" data-price="tax">{{ $currencySymbol }}{{ number_format($tax_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($shipping_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="text-gray-900" data-price="shipping">{{ $currencySymbol }}{{ number_format($shipping_amount, 2) }}</span>
                            </div>
                            @endif

                            <hr class="border-gray-200">

                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900" data-price="total">{{ $currencySymbol }}{{ number_format($total, 2) }}</span>
                            </div>

                            @if($currencyCode !== 'USD')
                            <div class="text-sm text-gray-500 text-center" data-currency-info>
                                @if($isAutoDetected)
                                    Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
                                @else
                                    Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Continue Shopping -->
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle country selection changes for currency conversion
    const countrySelects = document.querySelectorAll('select[name*="country"]');

    countrySelects.forEach(select => {
        select.addEventListener('change', function() {
            const countryName = this.value;
            if (countryName) {
                updateCurrency(countryName);
            }
        });
    });

    // Listen for currency change events from the navbar
    window.addEventListener('currencyChanged', function(event) {
        // Reload the page to update all prices with new currency
        window.location.reload();
    });

    function updateCurrency(countryName) {
        // Send AJAX request to get updated currency
        fetch(`/checkout/currency?country=${encodeURIComponent(countryName)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update all price displays
                    updatePriceDisplays(data.currencySymbol, data.prices);
                }
            })
            .catch(error => console.error('Error updating currency:', error));
    }

    function updatePriceDisplays(symbol, prices) {
        // Update subtotal
        const subtotalElement = document.querySelector('[data-price="subtotal"]');
        if (subtotalElement) {
            subtotalElement.textContent = symbol + prices.subtotal.toFixed(2);
        }

        // Update tax
        const taxElement = document.querySelector('[data-price="tax"]');
        if (taxElement && prices.tax_amount > 0) {
            taxElement.textContent = symbol + prices.tax_amount.toFixed(2);
        }

        // Update shipping
        const shippingElement = document.querySelector('[data-price="shipping"]');
        if (shippingElement && prices.shipping_amount > 0) {
            shippingElement.textContent = symbol + prices.shipping_amount.toFixed(2);
        }

        // Update total
        const totalElement = document.querySelector('[data-price="total"]');
        if (totalElement) {
            totalElement.textContent = symbol + prices.total.toFixed(2);
        }

        // Update currency info
        const currencyInfo = document.querySelector('[data-currency-info]');
        if (currencyInfo && prices.currencyCode !== 'USD') {
            currencyInfo.textContent = `Prices converted to ${prices.currencyCode} (${symbol})`;
        }
    }
});
</script>
@endpush
