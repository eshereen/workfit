<div>
    <h2 class="text-xl font-semibold text-gray-900 my-20">Order Summary</h2>

    @if(config('app.debug'))
        <div class="text-xs text-gray-500 mb-2">
            Currency: {{ $currencyCode }} ({{ $currencySymbol }}) | Items: {{ count($cartItems) }}
        </div>
    @endif

    @if(empty($cartItems))
        <div class="text-center py-8">
            <p class="text-gray-500">Your cart is empty</p>
        </div>
    @else
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
                <p class="font-medium text-gray-900">{{ $currencySymbol }}{{ number_format($item['converted_price'], 2) }}</p>
            </div>
        </div>
        @endforeach

        <!-- Order Totals -->
        <div class="mt-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</span>
            </div>

            @if($taxAmount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($taxAmount, 2) }}</span>
            </div>
            @endif

            @if($shippingAmount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping:</span>
                <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($shippingAmount, 2) }}</span>
            </div>
            @endif

            @if($loyaltyDiscount > 0)
            <div class="flex justify-between text-green-600">
                <span>Loyalty Points Discount:</span>
                <span>-{{ $currencySymbol }}{{ number_format($loyaltyDiscount, 2) }}</span>
            </div>
            @endif

            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                @if($loyaltyDiscount > 0)
                    <span class="text-gray-900">Final Total:</span>
                    <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($finalTotal, 2) }}</span>
                @else
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900">{{ $currencySymbol }}{{ number_format($total, 2) }}</span>
                @endif
            </div>

            @if($loyaltyDiscount > 0)
            <div class="text-sm text-gray-500 text-center">
                <p>You saved {{ $currencySymbol }}{{ number_format($loyaltyDiscount, 2) }} with loyalty points!</p>
            </div>
            @endif
        </div>
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
                console.log('✅ OrderSummary refreshed manually');
            }
        }
    }
};
</script>
@endif
