<div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg border border-green-400">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="mb-8 text-3xl font-bold">Your Shopping Cart</h1>


    <div class="flex flex-col gap-8 lg:flex-row">
        <!-- Cart Items -->
        <div class="lg:w-2/3">
            <div class="overflow-x-auto">
                @if($cartCount > 0)
                    <table class="w-full">
                        <thead class="border-b border-gray-200">
                            <tr class="text-sm text-left text-gray-500">
                                <th class="pb-4 font-medium">Product</th>
                                <th class="pb-4 font-medium">Price</th>
                                <th class="pb-4 font-medium">Quantity</th>
                                <th class="pb-4 font-medium">Total</th>
                                <th class="pb-4 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-6">
                                        <div class="flex items-center">
                                            <div class="overflow-hidden mr-4 w-20 h-20 bg-gray-50 rounded-md">
                                                @if(isset($item['attributes']['image']))
                                                    <img src="{{ $item['attributes']['image'] }}" class="object-cover w-full h-full">
                                                @else
                                                    <div class="flex justify-center items-center w-full h-full bg-gray-200">
                                                        <span class="text-gray-400">No Image</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-medium">{{ $item['name'] }}</h3>
                                                @if(isset($item['attributes']['color']) && isset($item['attributes']['size']))
                                                    <div class="flex items-center mt-1 space-x-2">
                                                        @if(isset($item['attributes']['color']))
                                                            @php
                                                                $colorCode = $this->getColorCode($item['attributes']['color']);
                                                            @endphp
                                                            <div class="flex items-center space-x-2">
                                                                <div class="w-4 h-4 rounded-full border border-gray-300"
                                                                     style="background-color: {{ $colorCode }};"
                                                                     title="{{ $item['attributes']['color'] }}"></div>
                                                                <span class="text-sm text-gray-500">{{ $item['attributes']['color'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(isset($item['attributes']['size']))
                                                            <span class="text-sm text-gray-500">/ {{ $item['attributes']['size'] }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6">

                                    </td>
                                    <td class="py-6">
                                        <div class="flex w-24 rounded-md border">
                                            <button
                                                wire:click="decreaseQuantity('{{ $item['rowId'] }}')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 {{ $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                                title="Decrease quantity"
                                            >-</button>
                                            <span class="px-2 py-1 text-center border-x">
                                                <span wire:loading.remove>{{ $item['quantity'] }}</span>
                                                <span wire:loading class="text-gray-400">...</span>
                                            </span>
                                            <button
                                                wire:click="increaseQuantity('{{ $item['rowId'] }}')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100"
                                                title="Increase quantity"
                                            >+</button>
                                        </div>

                                    </td>
                                    <td class="py-6">
                                        @if(isset($item['converted_price']))
                                            <span class="text-green-600">{{ $currencySymbol }}{{ number_format($item['converted_price'] * $item['quantity'], 2) }}</span>
                                            <span class="block text-xs text-gray-500">Original: ${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                        @else
                                            <span>{{ $currencySymbol }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-6">
                                        <button
                                            wire:click="removeItem('{{ $item['rowId'] }}')"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="text-gray-400 transition-colors hover:text-pink-500"
                                            title="Remove {{ $item['name'] }} from cart"
                                        >
                                            <span wire:loading.remove>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </span>
                                            <span wire:loading>
                                                <svg class="w-5 h-5 text-pink-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center bg-white rounded-lg shadow-md">
                        <h2 class="mb-4 text-2xl font-semibold">Your cart is empty</h2>
                        <p class="mb-6">Looks like you haven't added any items to your cart yet.</p>
                        <a href="{{ route('products.index') }}"
                           class="inline-block px-6 py-2 text-white bg-red-600 rounded-lg transition hover:bg-red-700">
                            Start Shopping
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="p-6 bg-gray-50 rounded-lg">
                <h2 class="mb-6 text-xl font-bold">Order Summary</h2>

                <div class="mb-6 space-y-4">
                    <!-- Coupon Form -->
                    <div class="p-3 bg-white rounded-md border">
                        @if($appliedCouponCode)
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-sm text-gray-600">Applied coupon:</span>
                                    <span class="ml-2 font-semibold">{{ $appliedCouponCode }}</span>
                                </div>
                                <button wire:click="removeCoupon" class="text-sm text-red-600 hover:underline">Remove</button>
                            </div>
                            @if($couponDiscount > 0)
                                <div class="mt-2 text-sm text-green-700">
                                    Discount: -{{ number_format($couponDiscount, 2) }} {{ $currencySymbol }}
                                </div>
                            @endif
                        @else
                            <form wire:submit.prevent="applyCoupon" class="flex items-center space-x-2">
                                <input
                                    type="text"
                                    wire:model.defer="couponCode"
                                    placeholder="Enter coupon code"
                                    class="flex-1 px-3 py-2 text-sm rounded-md border focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                >
                                <button type="submit" class="px-3 py-2 text-sm text-white bg-red-500 rounded-md hover:bg-red-600">Apply</button>
                            </form>
                            @error('couponCode')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>
                            @if($currencyCode !== 'USD')
                                <span class="text-green-600">{{ number_format($subtotal, 2) }} {{ $currencySymbol }}</span>
                                <span class="block text-xs text-gray-500">Original: ${{ number_format(app(\App\Services\CartService::class)->getSubtotal(), 2) }}</span>
                            @else
                                {{ number_format($subtotal, 2) }} {{ $currencySymbol }}
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span>
                            @if($currencyCode !== 'USD')
                                <span class="text-green-600">{{ number_format($shipping, 2) }} {{ $currencySymbol }}</span>
                                <span class="block text-xs text-gray-500">Original: ${{ number_format(app(\App\Services\CartService::class)->getShippingCost(), 2) }}</span>
                            @else
                                {{ number_format($shipping, 2) }} {{ $currencySymbol }}
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span>
                            @if($currencyCode !== 'USD')
                                <span class="text-green-600">{{ number_format($tax, 2) }} {{ $currencySymbol }}</span>
                                <span class="block text-xs text-gray-500">Original: ${{ number_format(app(\App\Services\CartService::class)->getTaxAmount(), 2) }}</span>
                            @endif
                        </span>
                    </div>

                    @if($couponDiscount > 0)
                    <div class="flex justify-between text-red-700">
                        <span>Coupon Discount</span>
                        <span>-{{ number_format($couponDiscount, 2) }} {{ $currencySymbol }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between pt-4 font-bold border-t border-gray-200">
                        <span>Total</span>
                        <span>
                            @if($currencyCode !== 'USD')
                                <span class="text-green-600">{{ $currencySymbol }}{{ number_format($total, 2) }}</span>
                                <span class="block text-xs text-gray-500">Original: ${{ number_format(app(\App\Services\CartService::class)->getTotal(), 2) }}</span>
                            @else
                                {{ number_format($total, 2) }} {{ $currencySymbol }}
                            @endif
                        </span>
                    </div>
                </div>

                @if($currencyCode !== 'USD')
                <div class="p-2 mb-4 text-sm text-center text-gray-500 bg-blue-50 rounded">
                    @if($isAutoDetected)
                        Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
                    @else
                        Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
                    @endif
                </div>
                @endif

                <div class="space-y-3">
                    <a href="{{ route('checkout') }}" class="block py-3 w-full font-medium text-center text-white bg-red-500 rounded-md transition hover:bg-red-600">
                        Proceed to Checkout
                    </a>
                    <a href="{{ route('products.index') }}" class="block py-3 w-full font-medium text-center text-white rounded-md border transition bg-gray-950 hover:bg-gray-50">
                        Continue Shopping
                    </a>
                    @if($cartCount > 0)
                        <button
                            wire:click="clearCart"
                            class="py-3 w-full font-medium border border-gray-900 transition rounded-m-d text-gray-950 hover:bg-gray-950"
                        >
                            Clear Cart
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>



