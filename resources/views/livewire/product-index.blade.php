<div>
<div class="container px-4 py-8 mx-auto my-16">
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
    @if(!request()->routeIs('home') && (request()->routeIs('products.index') || request()->routeIs('categories.index') || $category || $categoryModel))
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-lg md:text-xl lg:text-2xl  font-bold text-gray-950">
            @if(request()->routeIs('products.index'))
                WorkfitStore
            @elseif($subcategoryModel && $categoryModel)
                 {{ $categoryModel->name }} <span class="text-gray-400 mx-1 font-normal">/</span> {{ $subcategoryModel->name }}
            @elseif($categoryModel)
                {{ $categoryModel->name }}
            @else
                Products
            @endif
        </h1>

        <div class="flex items-center gap-2" x-data="{ showSearch: false }">
            <!-- Search Icon & Input -->
            <div class="relative">
                <!-- Search Icon Button -->
                <button
                    @click="showSearch = !showSearch"
                    class="p-1 rounded-lg hover:bg-gray-100 transition-colors"
                    type="button"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                <!-- Search Input (toggleable) -->
                <div
                    x-show="showSearch"
                    x-cloak
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                   class="absolute right-0 top-12 z-50 w-80 min-w-max"
                    @click.away="showSearch = false"
                >
                    <div class="relative">
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Search products..."
                            class="px-1 py-2 pl-10 w-full rounded-lg border border-gray-300 shadow-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            x-ref="searchInput"
                        >
                        <svg class="absolute top-2.5 left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sort Filter Icon & Dropdown -->
            <div class="relative" x-data="{ showSort: false }">
                <!-- Filter Icon Button -->
                <button
                    @click="showSort = !showSort"
                    class="p-1 rounded-lg hover:bg-gray-100 transition-colors"
                    type="button"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                    </svg>
                </button>

                <!-- Sort Dropdown -->
                <div
                    x-show="showSort"
                    x-cloak
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 top-12 z-50 w-80 min-w-max bg-white rounded-lg shadow-lg border border-gray-200"
                    @click.away="showSort = false"
                >
                    <div class="p-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort by:</label>
                        <select
                            wire:model.live="sortBy"
                            class="w-full px-2 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            @change="showSort = false"
                        >
                            <option value="newest">Newest</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
    @if(!request()->routeIs('home'))
    @if($currencyCode !== 'USD')
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-4 right-4 z-50 p-4 bg-white rounded-lg shadow-lg border-l-4 border-green-500 max-w-sm"
         style="display: none;">
        <div class="flex items-center">
            <div class="flex-shrink-0 text-green-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-800">
                @if($isAutoDetected)
                    Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
                @else
                    Currency set to {{ $currencyCode }} ({{ $currencySymbol }})
                @endif
            </div>
            <button @click="show = false" class="ml-auto pl-3 text-gray-400 hover:text-gray-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif
    @endif

    {{-- Preload first product image for better LCP --}}
    @if($products && $products->count() > 0)
        @php
            $firstProduct = $products->first();
            $firstImageUrl = $firstProduct->getFirstMediaUrl('main_image', 'medium_webp');
            if (!$firstImageUrl) {
                $firstImageUrl = $firstProduct->getFirstMediaUrl('main_image');
            }
        @endphp
        @if($firstImageUrl)
            @push('head')
                <link rel="preload" as="image" href="{{ $firstImageUrl }}" fetchpriority="high">
            @endpush
        @endif
    @endif

    <div class="grid grid-cols-2 gap-x-4 gap-y-8 md:grid-cols-3 lg:grid-cols-4">
        @if($products && $products->count() > 0)
            @foreach($products as $product)
        <div wire:key="product-{{ $product->id }}" class="overflow-hidden bg-white transition">
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
                    <span class="px-2 py-[2px] ml-2 text-[8px] font-bold text-white uppercase rounded-sm bg-gray-500">
                         Sale
                    </span>
                    @endif
                </div>

                <div class="block relative w-full h-full">
                    {{-- Main image --}}
                    @php
                        $mainImage = $product->getFirstMediaUrl('main_image', 'medium_webp');
                        // Fallback to original if conversion doesn't exist
                        if (!$mainImage) {
                            $mainImage = $product->getFirstMediaUrl('main_image');
                        }
                        
                        // Prevent src="" loop
                        if (empty($mainImage)) {
                             $mainImage = 'https://via.placeholder.com/400x400?text=No+Image';
                        }
                    @endphp
                    <img src="{{ $mainImage }}"
                         alt="{{ $product->name }}"
                         class="object-cover w-full h-full {{ $loop->index >= 4 ? 'transition-opacity duration-500' : '' }} main-image"
                         style="opacity: 1; {{ $loop->index >= 4 ? 'transition: opacity 0.5s ease;' : '' }} object-position: top;"
                         width="400"
                         height="400"
                         @if($loop->index === 0)
                             fetchpriority="high"
                             loading="eager"
                             decoding="sync"
                         @elseif($loop->index < 4)
                             loading="eager"
                             fetchpriority="high"
                             decoding="async"
                         @else
                             loading="lazy"
                             decoding="async"
                         @endif
                    >

                    {{-- Gallery image (if exists) - Optimized with WebP --}}
                    @php
                        $galleryImages = $product->getMedia('product_images');
                        $galleryImage = null;
                        foreach($galleryImages as $img) {
                            $url = '';
                            try {
                                // Check if conversion exists before accessing
                                if ($img->hasGeneratedConversion('medium_webp')) {
                                    $url = $img->getUrl('medium_webp');
                                } else {
                                    $url = $img->getUrl();
                                }
                            } catch (\Exception $e) {
                                $url = $img->getUrl();
                            }
                            
                            if($url && $url !== $mainImage) {
                                $galleryImage = $url;
                                break;
                            }
                        }
                    @endphp
                    @if($galleryImage)
                        <img src="{{ $galleryImage }}"
                             alt="{{ $product->name }}"
                             class="object-cover w-full h-full gallery-image"
                             style="opacity: 0; z-index: 2; position: absolute; top: 0; left: 0; width: 100%; height: 100%; transition: opacity 0.5s ease;"
                             width="400"
                             height="400"
                             loading="lazy"
                             decoding="async">
                    @endif
                </div>

                <!-- Wishlist Button -->
                @auth
                <div class="absolute top-2 right-2 z-20">
                    <button wire:click="toggleWishlist({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="toggleWishlist({{ $product->id }})"
                            class="p-1 bg-white rounded-full shadow-md transition-colors hover:bg-gray-50"
                            data-product-id="{{ $product->id }}"
                            title="{{ in_array($product->id, $wishlistProductIds) ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                        <svg class="w-3 h-3 {{ in_array($product->id, $wishlistProductIds) ? 'text-yellow-900 fill-current' : 'text-gray-600' }}"
                             fill="{{ in_array($product->id, $wishlistProductIds) ? 'currentColor' : 'none' }}"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                    <span wire:loading wire:target="toggleWishlist({{ $product->id }})" class="absolute top-2 right-2 p-1">
                        <svg class="w-3 h-3 text-yellow-900 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </div>
                @else
                <a href="{{ route('login') }}" class="absolute top-2 right-2 z-20 p-1 bg-white rounded-full shadow-md transition-colors hover:bg-gray-50" title="Login to add to wishlist">
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
                @endauth
            </div>
            <div>
                <div class="text-center p-2">
                    <a href="{{ route('product.show', $product->slug) }}"
                       class="text-[11px] md:text-xs lg:text-sm text-gray-600 font-semibold hover:text-red-600 block leading-tight">
                        {{ $product->name }}
                    </a>
                </div>
                <div class="flex justify-between text-xs items-center mx-2">
                    <div>
                        @php
                            $displayPrice = $product->converted_price ?? $product->price ?? 0;
                        @endphp
                        <span class="text-xs font-bold">{{ number_format($displayPrice, 2) }} {{ $currencySymbol }}</span>
                    </div>
                    @if($product->compare_price > 0)
                    <div class="flex gap-2 items-center">
                        @php
                            $displayComparePrice = $product->converted_compare_price ?? $product->compare_price ?? 0;
                        @endphp
                        <span class="text-xs text-gray-500 line-through">
                            {{ number_format($displayComparePrice, 2) }} {{ $currencySymbol }}
                        </span>

                      {{-- -  <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">
                            -{{ $product->discount_percentage }}%
                        </span>
--}}
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
    @if($products && method_exists($products, 'links'))
    <div class="my-6 py-6">
        {{ $products->links('vendor.pagination.tailwind') }}
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
