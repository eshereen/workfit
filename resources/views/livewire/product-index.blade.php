<div>
<div class="container px-4 py-8 mx-auto my-20">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg border border-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg border border-red-400">
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
                       class="px-4 py-2 pl-10 rounded-lg border focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <svg class="absolute top-2.5 left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <!-- Sort -->
            <select wire:model.live="sortBy" class="px-3 py-2 rounded-lg border focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <option value="newest">Newest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
            </select>
        </div>
    </div>

    @endif
    @if(!request()->routeIs('home'))
    @if($currencyCode !== 'USD')
    <div class="p-4 mb-6 bg-green-50 rounded-lg border border-green-200">
        <div class="text-sm text-center text-green-800">
            @if($isAutoDetected)
                Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
            @else
                Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
            @endif
        </div>
    </div>
    @endif
    @endif

    <div class="grid grid-cols-2 gap-x-4 gap-y-8 md:grid-cols-3 lg:grid-cols-4">
        @if($products && $products->count() > 0)
            @foreach($products as $product)
        <div class="overflow-hidden bg-white transition">
            <div class="relative overflow-hidden aspect-[4/5] product-image-container"
                 style="cursor: pointer;"
                 onmouseenter="this.querySelector('.main-image').style.opacity='0'; this.querySelector('.gallery-image').style.opacity='1';"
                 onmouseleave="this.querySelector('.main-image').style.opacity='1'; this.querySelector('.gallery-image').style.opacity='0';"
                 onclick="window.location.href='{{ route('product.show', $product->slug) }}'">

                <!-- Badges -->
                <div class="flex absolute left-0 top-2 z-30 flex-col gap-1">
                    <!-- Best Seller Badge -->
                    @if($this->isBestSeller($product->id))
                    <span class="px-2 py-1 text-xs font-bold text-white uppercase bg-green-600 rounded">
                        Best Seller
                    </span>
                    @endif

                    <!-- Flash Sale Badge -->
                    @if($product->compare_price > 0)
                    <span class="px-2 py-1 font-bold text-white uppercase bg-red-600 rounded opacity-80">
                         Sale
                    </span>
                    @endif
                </div>

                <div class="block relative w-full h-full">
                    {{-- Main image --}}
                    @php
                        $mainImage = $product->getFirstMediaUrl('main_image') ;
                    @endphp
                    <img src="{{ $mainImage }}"
                         alt="{{ $product->name }}"
                         class="object-cover w-full h-full transition-opacity duration-500 main-image"
                         style="opacity: 1; transition: opacity 0.5s ease;"
                         width="400"
                         height="400"
                         loading="lazy">

                    {{-- Gallery image (if exists) --}}
                    @php
                        $galleryImages = $product->getMedia('product_images');
                        $galleryImage = null;
                        foreach($galleryImages as $img) {
                            if($img->getUrl() !== $mainImage) {
                                $galleryImage = $img->getUrl();
                                break;
                            }
                        }
                    @endphp
                    @if($galleryImage)
                        <img src="{{ $galleryImage }}"
                             alt="{{ $product->name }}"
                             class="object-cover absolute top-0 left-0 w-full h-full transition-opacity duration-500 gallery-image"
                             style="opacity: 0; z-index: 2; transition: opacity 0.5s ease;"
                             width="300"
                             height="300"
                             loading="lazy">
                    @endif
                </div>

                <!-- Wishlist Button -->
                @auth
                <div class="absolute top-2 right-2 z-20">
                    <button wire:click="toggleWishlist({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="toggleWishlist({{ $product->id }})"
                            class="p-2 bg-white rounded-full shadow-md transition-colors hover:bg-gray-50"
                            data-product-id="{{ $product->id }}"
                            title="{{ in_array($product->id, $wishlistProductIds) ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                        <svg class="w-5 h-5 {{ in_array($product->id, $wishlistProductIds) ? 'text-red-500 fill-current' : 'text-gray-600' }}"
                             fill="{{ in_array($product->id, $wishlistProductIds) ? 'currentColor' : 'none' }}"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    <span wire:loading wire:target="toggleWishlist({{ $product->id }})" class="absolute top-2 right-2 p-1">
                        <svg class="w-5 h-5 text-red-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
                @else
                <a href="{{ route('login') }}" class="absolute top-2 right-2 z-20 p-1 bg-white rounded-full shadow-md transition-colors hover:bg-gray-50" title="Login to add to wishlist">
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
                           class="text-base font-semibold hover:text-red-600">
                            {{ $product->name }}
                        </a>
                        @if($product->category)
                            <p class="pt-3 text-sm text-gray-600">{{ $product->category->name }}</p>
                        @endif
                    </div>
                    @if($product->compare_price > 0)
                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">
                        -{{ $product->discount_percentage }}%
                    </span>
                    @endif
                </div>
                <div class="flex justify-between items-center mt-2">
                    <div>
                        <span class="text-base font-bold">{{ number_format($product->converted_price ?? $product->price, 2) }} {{ $currencySymbol }}</span>
                    </div>
                    @if($product->compare_price > 0)
                    <div>
                        <span class="text-sm text-gray-500 line-through">
                            {{ number_format($product->converted_compare_price ?? $product->compare_price, 2) }} {{ $currencySymbol }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
            @endforeach
        @else
            <div class="col-span-full py-12 text-center">
                <div class="text-gray-500">
                    <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        @endif
    </div>
    @if(request()->routeIs('products.index') && $products && method_exists($products, 'links'))
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>

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
