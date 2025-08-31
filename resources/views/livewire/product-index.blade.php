<div>
<div class="container mx-auto px-4 py-8 my-20">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
@if(request()->routeIs('products.index'))
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-red-600">Shop</h1>
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Search products..."
                       class="border rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <!-- Sort -->
            <select wire:model.live="sortBy" class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <option value="newest">Newest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
            </select>
        </div>
    </div>

    @endif
    @if(!request()->routeIs('home'))
    @if($currencyCode !== 'USD')
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="text-sm text-green-800 text-center">
            @if($isAutoDetected)
                Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
            @else
                Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
            @endif
        </div>
    </div>
    @endif
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition mb-20"  x-data="{ hover: false }"
        @mouseenter="hover = true"
        @mouseleave="hover = false">
            <div class="relative">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $product->getFirstMediaUrl('main_image', 'medium') }}"
                         alt="{{ $product->name }}"
                         class="w-full h-64 object-cover"
                         :class="hover ? 'opacity-100' : 'opacity-0'">
                    <img src="{{ $product->getFirstMediaUrl('product_images', 'medium') }}"
                    loading="lazy"
                         alt="{{ $product->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                          :class="hover ? 'opacity-0' : 'opacity-100'">
                </a>
                <!-- Wishlist Button -->
                @auth
                <div class="">
                    <button wire:click="toggleWishlist({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="toggleWishlist({{ $product->id }})"
                            class="absolute top-1 right-2 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors z-10"
                            data-product-id="{{ $product->id }}"
                            title="{{ in_array($product->id, $wishlistProductIds) ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                        <svg class="w-5 h-5 {{ in_array($product->id, $wishlistProductIds) ? 'text-red-500 fill-current' : 'text-gray-600' }}"
                             fill="{{ in_array($product->id, $wishlistProductIds) ? 'currentColor' : 'none' }}"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    <span wire:loading wire:target="toggleWishlist({{ $product->id }})" class="absolute top-2 right-2 p-2">
                        <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
                @else
                <a href="{{ route('login') }}" class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors z-10" title="Login to add to wishlist">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
                @endauth
            </div>
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <a href="{{ route('product.show', $product->slug) }}"
                           class="font-semibold text-lg hover:text-red-600">
                            {{ $product->name }}
                        </a>
                        <p class="text-gray-600 text-sm">{{ $product->category->name }}</p>
                    </div>
                    @if($product->compare_price > 0)
                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                        -{{ $product->discount_percentage }}%
                    </span>
                    @endif
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-lg">{{ $currencySymbol }}{{ number_format($product->converted_price ?? $product->price, 2) }}</span>
                        @if($product->compare_price > 0)
                        <span class="text-sm text-gray-500 line-through ml-2">
                            {{ $currencySymbol }}{{ number_format($product->converted_compare_price ?? $product->compare_price, 2) }}
                        </span>
                        @endif
                    </div>

                    @if($product->variants->isNotEmpty())
                    <button wire:click="openVariantModal({{ $product->id }})"
                            class="add-to-cart bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                    </button>
                    @else
                    <button wire:click="addSimpleProductToCart({{ $product->id }}, 1)"
                            class="add-to-cart bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if(request()->routeIs('products.index'))
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>

<!-- Variant Selection Modal -->
@if($showVariantModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" wire:key="variant-modal-{{ $selectedProduct?->id ?? 'none' }}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Select Options</h3>
            <button wire:click="$set('showVariantModal', false)" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        @if($selectedProduct)
        <!-- Variant Options -->
        @if($selectedProduct->variants->isNotEmpty())
        <div class="mb-4">
            @php
                $colors = $selectedProduct->variants->unique('color')->pluck('color');
            @endphp
            @if($colors->count() > 1)
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Color</h4>
                                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $color)
                            @php
                                $variantForColor = $selectedProduct->variants->where('color', $color)->first();
                                $colorCode = $this->getColorCode($color);
                            @endphp
                            <button wire:click="selectVariant('{{ $variantForColor->id }}')"
                                    class="px-4 py-2 border rounded-md text-sm transition-all duration-200 {{ $selectedVariant && $selectedVariant->color === $color ? 'ring-2 ring-gray-900 ring-offset-2' : 'hover:scale-105' }}"
                                    style="background-color: {{ $colorCode }}; color: {{ $this->getContrastColor($colorCode) }}; border-color: {{ $colorCode }};">
                                {{ $color }}
                            </button>
                            @endforeach
                        </div>
            </div>
            @endif

            <!-- Size Selection -->
            @if($selectedVariant)
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Size</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($selectedProduct->variants->where('color', $selectedVariant->color) as $variant)
                    <button type="button"
                            wire:click="selectVariant('{{ $variant->id }}')"
                            class="w-12 h-10 flex items-center justify-center border rounded-md text-sm {{ $selectedVariantId == $variant->id ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        {{ $variant->size }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Quantity Selector -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
            <div class="flex items-center border rounded-lg w-32">
                <button type="button"
                        onclick="decrementModalQuantity()"
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors rounded-l-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <input type="number"
                       wire:model.defer="quantity"
                       min="1"
                       max="10"
                       class="w-16 text-center border-0 focus:ring-0 focus:outline-none"
                       id="modal-quantity-input"
                       onchange="updateQuantityFromInput(this.value)">
                <button type="button"
                        onclick="incrementModalQuantity()"
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors rounded-r-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Selected Variant Info -->
        @if($selectedVariant)
        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-600">Selected:</span>
                    <span class="ml-2 font-medium">{{ $selectedVariant->color }} - {{ $selectedVariant->size }}</span>
                </div>
                <span class="font-bold text-lg">{{ $currencySymbol }}{{ number_format($selectedVariant->converted_price ?? $selectedVariant->price ?? $selectedProduct->converted_price ?? $selectedProduct->price, 2) }}</span>
            </div>
            <div class="text-sm text-gray-600 mt-1">
                Stock: {{ $selectedVariant->stock }} available
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button wire:click="$set('showVariantModal', false)"
                    class="px-4 py-2 border rounded hover:bg-gray-100 transition-colors">
                Cancel
            </button>
            <button wire:click="addToCart"
                    wire:loading.attr="disabled"
                    wire:target="addToCart"
                    class="px-4 py-2 text-white rounded transition-colors {{ $selectedVariant && $quantity > 0 && $quantity <= $selectedVariant->stock ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400 cursor-not-allowed' }}"
                    {{ !$selectedVariant || $quantity <= 0 || $quantity > ($selectedVariant ? $selectedVariant->stock : 0) ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                <span wire:loading wire:target="addToCart">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Adding...
                </span>
            </button>
        </div>
        @endif
    </div>
</div>
@endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product index page loaded, setting up quantity button listeners...');

    // Function to update quantity input value
    function updateModalQuantityInput() {
        const quantityInput = document.getElementById('modal-quantity-input');
        if (quantityInput) {
            // Get the current quantity from Livewire
            const currentQuantity = @this.quantity || 1;
            if (quantityInput.value !== currentQuantity.toString()) {
                quantityInput.value = currentQuantity;
                console.log('Updated modal quantity input to:', currentQuantity);
            }
        }
    }

    // Listen for Livewire events
    window.addEventListener('livewire:init', () => {
        console.log('Livewire initialized on product index');
    });

    // Update input when Livewire processes messages
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint.name === 'product-index') {
            console.log('Product index component updated, checking for quantity changes');
            setTimeout(updateModalQuantityInput, 50);
        }
    });

    // Also update when the modal opens
    window.addEventListener('modal-opened', function() {
        setTimeout(updateModalQuantityInput, 100);
    });

    // Listen for currency change events
    window.addEventListener('currency-changed', function(e) {
        console.log('Currency change event received:', e.detail);
        // Trigger the Livewire method to refresh currency
        @this.call('handleCurrencyChange', e.detail);
    });

    // Listen for Livewire updates to showVariantModal
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint.name === 'product-index') {
            // Check if modal just opened
            if (@this.showVariantModal) {
                console.log('Variant modal opened, initializing quantity');
                setTimeout(() => {
                    initializeModalQuantity();
                }, 100);
            }
        }
    });
});

// Global functions for quantity buttons
function incrementModalQuantity() {
    console.log('Incrementing modal quantity');

    // Get current quantity from input
    const quantityInput = document.getElementById('modal-quantity-input');
    if (!quantityInput) {
        console.error('Quantity input not found');
        return;
    }

    let currentQty = parseInt(quantityInput.value) || 1;
    const maxQty = 10; // You can make this dynamic based on stock if needed

    if (currentQty < maxQty) {
        currentQty++;
        quantityInput.value = currentQty;

        // Update Livewire component
        @this.set('quantity', currentQty);

        console.log('Quantity incremented to:', currentQty);
    } else {
        console.log('Quantity already at maximum:', maxQty);
    }
}

function decrementModalQuantity() {
    console.log('Decrementing modal quantity');

    // Get current quantity from input
    const quantityInput = document.getElementById('modal-quantity-input');
    if (!quantityInput) {
        console.error('Quantity input not found');
        return;
    }

    let currentQty = parseInt(quantityInput.value) || 1;

    if (currentQty > 1) {
        currentQty--;
        quantityInput.value = currentQty;

        // Update Livewire component
        @this.set('quantity', currentQty);

        console.log('Quantity decremented to:', currentQty);
    } else {
        console.log('Quantity already at minimum: 1');
    }
}

function updateQuantityFromInput(value) {
    console.log('Quantity input changed to:', value);

    let newQty = parseInt(value) || 1;

    // Validate range
    if (newQty < 1) {
        newQty = 1;
    } else if (newQty > 10) {
        newQty = 10;
    }

    // Update input if value was corrected
    const quantityInput = document.getElementById('modal-quantity-input');
    if (quantityInput) {
        quantityInput.value = newQty;
    }

    // Update Livewire component
    @this.set('quantity', newQty);

    console.log('Quantity updated to:', newQty);
}

function initializeModalQuantity() {
    console.log('Initializing modal quantity');

    const quantityInput = document.getElementById('modal-quantity-input');
    if (quantityInput) {
        // Get current quantity from Livewire
        const currentQty = @this.quantity || 1;
        quantityInput.value = currentQty;
        console.log('Modal quantity initialized to:', currentQty);
    }
}
</script>
