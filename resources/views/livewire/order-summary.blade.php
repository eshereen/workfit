<div>
    <h2 class="my-5 text-xl font-semibold text-gray-900">Order Summary</h2>

    @if(config('app.debug'))
        <div class="mb-2 text-xs text-gray-500">
            Currency: {{ $currencyCode }} ({{ $currencySymbol }}) | Items: {{ count($cartItems) }}
        </div>
    @endif

    @if(empty($cartItems))
        <div class="py-8 text-center">
            <p class="text-gray-500">Your cart is empty</p>
        </div>
    @else
        <!-- Cart Items -->
        @foreach($cartItems as $item)
        <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
            <div class="flex items-center">
                @if(isset($item['attributes']['image']))
                    <img src="{{ $item['attributes']['image'] }}"
                         alt="{{ $item['name'] }}"
                         class="object-cover mr-3 w-12 h-12 rounded">
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
                <p class="font-medium text-gray-900">{{ number_format($item['converted_price'], 2) }} {{ $currencySymbol }}</p>
            </div>
        </div>
        @endforeach

        <!-- Order Totals -->
        <div class="mt-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900">{{ number_format($subtotal, 2) }} {{ $currencySymbol }}</span>
            </div>

            @if($taxAmount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span class="text-gray-900">{{ number_format($taxAmount, 2) }} {{ $currencySymbol }}</span>
            </div>
            @endif

            <div class="flex justify-between">
                <span class="text-gray-600">Shipping (calculated after country selection):</span>
                <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($shippingAmount, 2) }}</span>
            </div>

            @if($couponDiscount > 0)
            <div class="flex justify-between text-red-600">
                <span>Coupon Discount:</span>
                <span>-{{ $currencySymbol }}{{ number_format($couponDiscount, 2) }}</span>
            </div>
            @endif

            @if($loyaltyDiscount > 0)
            <div class="flex justify-between text-green-600">
                <span>Loyalty Points Discount:</span>
                <span>-{{ $currencySymbol }}{{ number_format($loyaltyDiscount, 2) }}</span>
            </div>
            @endif

            <div class="flex justify-between pt-3 text-lg font-bold border-t border-gray-200">
                <span class="text-gray-900">Total:</span>
                <span class="text-gray-900">{{ number_format($finalTotal, 2) }} {{ $currencySymbol }}</span>
            </div>

            @if($loyaltyDiscount > 0)
            <div class="text-sm text-center text-gray-500">
                <p>You saved {{ number_format($loyaltyDiscount, 2) }} {{ $currencySymbol }} with loyalty points!</p>
            </div>
            @endif
        </div>

        <!-- Currency Toast Notification -->
        @if($currencyCode !== 'USD')
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-4 right-4 z-50 p-4 bg-green-50 rounded-lg border border-green-200 shadow-lg max-w-sm"
             style="display: none;">
            <div class="flex items-center">
                <div class="flex-shrink-0 text-green-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 text-sm font-medium text-green-800">
                    Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
                </div>
                <button @click="show = false" class="ml-auto pl-3 text-green-500 hover:text-green-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        @endif
    @endif
</div>

<!-- OrderSummary now uses pure Livewire events - no custom JavaScript needed -->
@if(config('app.debug'))
<script>
console.log('💰 OrderSummary: Using pure Livewire events (Alpine.js approach)');

// Simple test function for debugging
window.testOrderSummaryEvent = function() {
    console.log('🧪 Testing OrderSummary refresh...');
    if (window.Livewire) {
        const orderSummary = document.querySelector('[wire\\:id*="order-summary"]');
        if (orderSummary) {
            const wireId = orderSummary.getAttribute('wire:id');
            const component = window.Livewire.find(wireId);
            if (component) {
                component.$refresh();
                console.log('OrderSummary refreshed manually');
            }
        }
    }
};
</script>
@endif
