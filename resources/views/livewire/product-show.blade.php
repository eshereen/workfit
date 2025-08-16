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

    <!-- Debug Info -->
    @if(false && config('app.debug'))
    <!-- Debug information is available but hidden -->
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Product Images -->
        <div class="lg:w-1/2">
            <div class="mb-4">
                <img src="{{ $product->getFirstMediaUrl('main_image', 'large') }}"
                     alt="{{ $product->name }}"
                     class="w-full rounded-lg shadow-md">
            </div>
            <div class="grid grid-cols-4 gap-2">
                @foreach($product->getMedia('product_images') as $image)
                <div class="border rounded overflow-hidden">
                    <img src="{{ $image->getUrl('medium') }}"
                         alt="{{ $product->name }}"
                         class="w-full h-24 object-cover cursor-pointer hover:opacity-80">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Product Info -->
        <div class="lg:w-1/2">
            @if($currencyCode !== 'USD')
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-blue-800 text-center">
                    @if($isAutoDetected)
                        Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
                    @else
                        Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
                    @endif
                </div>
            </div>
            @endif

            <div class="flex items-start justify-between mb-4">
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                @auth
                <button wire:click="toggleWishlist"
                        wire:loading.attr="disabled"
                        wire:target="toggleWishlist"
                        class="wishlist-btn p-2 transition-colors {{ $isInWishlist ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}"
                        title="{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                    <svg class="w-8 h-8" fill="{{ $isInWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span wire:loading wire:target="toggleWishlist" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                @endauth
            </div>

            <div class="flex items-center mb-4">
                @if($product->compare_price > 0)
                <span class="text-2xl font-bold text-red-600 mr-3">
                    {{ $currencySymbol }}{{ number_format($product->converted_price ?? $product->price, 2) }}
                </span>
                <span class="text-lg text-gray-500 line-through">
                    {{ $currencySymbol }}{{ number_format($product->converted_compare_price ?? $product->compare_price, 2) }}
                </span>
                <span class="ml-3 bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
                    Save {{ $product->discount_percentage }}%
                </span>
                @else
                <span class="text-2xl font-bold">
                    {{ $currencySymbol }}{{ number_format($product->converted_price ?? $product->price, 2) }}
                </span>
                @endif
            </div>

            <div class="mb-6">
                <div class="flex items-center mb-2">
                    <span class="text-gray-700 mr-2">Availability:</span>
                    @if($selectedVariant && $selectedVariant->stock > 0)
                    <span class="text-green-600 font-medium">In Stock ({{ $selectedVariant->stock }} available)</span>
                    @elseif($product->variants->isEmpty() && $product->quantity > 0)
                    <span class="text-green-600 font-medium">In Stock</span>
                    @else
                    <span class="text-red-600 font-medium">Out of Stock</span>
                    @endif
                </div>

                @if($product->variants->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Select Options</h3>

                    <!-- Color Selection -->
                    @php
                        $colors = $product->variants->unique('color')->pluck('color');
                    @endphp
                    @if($colors->count() > 1)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Color</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $color)
                            @php
                                $variantForColor = $product->variants->where('color', $color)->first();
                                $colorCode = $this->getColorCode($color);
                            @endphp
                            <button wire:click="selectVariant('{{ $variantForColor->id }}')"
                                    class="px-4 py-2 border rounded-md text-sm transition-all duration-200 {{ $selectedVariant && $selectedVariant->color === $color ? 'ring-2 ring-gray-900 ring-offset-2' : 'hover:scale-105' }}"
                                    @if($selectedVariant && $selectedVariant->color === $color) aria-pressed="true" @endif
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
                            @foreach($product->variants->where('color', $selectedVariant->color) as $variant)
                            <button type="button"
                                    wire:click="selectVariant('{{ $variant->id }}')"
                                    class="w-12 h-10 flex items-center justify-center border rounded-md text-sm {{ $selectedVariantId == $variant->id ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                                    @if($selectedVariantId == $variant->id) aria-pressed="true" @endif>
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
                    <div class="flex items-center border rounded-md overflow-hidden">
                        <button type="button"
                                wire:click="decrementQty"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $quantity <= 1 ? 'disabled' : '' }}
                                title="Decrease quantity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number"
                               wire:model.defer="quantity"
                               min="1"
                               max="{{ $selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10) }}"
                               class="w-16 text-center border-0 focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                               id="quantity-input">
                        <button type="button"
                                wire:click="incrementQty"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors {{ $quantity >= ($selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10)) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $quantity >= ($selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10)) ? 'disabled' : '' }}
                                title="Increase quantity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                    @if(config('app.debug'))
                    <div class="text-xs text-gray-500 mt-1">
                        <p>Current Quantity: {{ $quantity }}</p>
                        <p>Max Quantity: {{ $selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10) }}</p>
                        <p>Has Variant: {{ $selectedVariant ? 'Yes' : 'No' }}</p>
                    </div>
                    @endif
                </div>

                <!-- Selected Variant Info -->
                @if($selectedVariant)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-600">Selected:</span>
                            <span class="ml-2 font-medium">{{ $selectedVariant->color }} - {{ $selectedVariant->size }}</span>
                        </div>
                        <span class="font-bold text-lg">{{ $currencySymbol }}{{ number_format($selectedVariant->converted_price ?? $selectedVariant->price ?? $product->converted_price ?? $product->price, 2) }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Stock: {{ $selectedVariant->stock }} available
                    </div>
                </div>
                @endif

                <!-- Add to Cart Button -->
                <button wire:click="addToCart"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-not-allowed"
                        wire:target="addToCart"
                        class="w-full bg-red-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                    <span wire:loading wire:target="addToCart">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    </span>
                </button>

                @if(config('app.debug'))
                <!-- Debug Buttons -->
                <div class="mt-4 space-y-2">
                    <button wire:click="testQuantityIncrement"
                            class="w-full px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                        Test Quantity Increment
                    </button>
                    <button wire:click="testQuantityDecrement"
                            class="w-full px-4 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                        Test Quantity Decrement
                    </button>
                    <button wire:click="testAddToCart"
                            class="w-full px-4 py-2 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600">
                        Test Add to Cart (Current Qty: {{ $quantity }})
                    </button>
                    <button wire:click="setQuantity(5)"
                            class="w-full px-4 py-2 bg-purple-500 text-white text-sm rounded hover:bg-purple-600">
                        Set Quantity to 5
                    </button>
                    <button wire:click="setQuantity(10)"
                            class="w-full px-4 py-2 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">
                        Set Quantity to 10
                    </button>
                    <button wire:click="refreshComponent"
                            class="w-full px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                        Refresh Component
                    </button>
                    <button onclick="updateQuantityInput()"
                            class="w-full px-4 py-2 bg-pink-500 text-white text-sm rounded hover:bg-pink-600">
                        Force Update Input (JS)
                    </button>
                </div>
                @endif
            </div>

            <div class="border-t pt-6">
                <h3 class="font-semibold text-lg mb-3">Description</h3>
                <div class="prose max-w-none">
                    {!! $product->description !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('product.show', $relatedProduct->slug) }}">
                    <img src="{{ $relatedProduct->getFirstMediaUrl('main_image', 'medium') }}"
                         alt="{{ $relatedProduct->name }}"
                         class="w-full h-64 object-cover">
                </a>
                <div class="p-4">
                    <a href="{{ route('product.show', $relatedProduct->slug) }}"
                       class="font-semibold text-lg hover:text-red-600 block mb-1">
                        {{ $relatedProduct->name }}
                    </a>
                    <span class="font-bold">{{ $currencySymbol }}{{ number_format($relatedProduct->converted_price ?? $relatedProduct->price, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if(config('app.debug'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product show page loaded, setting up debug listeners...');

    const quantityInput = document.getElementById('quantity-input');
    if (quantityInput) {
        console.log('Quantity input found:', quantityInput);
        console.log('Initial value:', quantityInput.value);

        // Monitor value changes
        quantityInput.addEventListener('input', function(e) {
            console.log('Quantity input changed:', e.target.value);
        });

        // Monitor Livewire updates
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    console.log('Quantity input value attribute changed:', quantityInput.value);
                }
            });
        });

        observer.observe(quantityInput, {
            attributes: true,
            attributeFilter: ['value']
        });
    }

    // Listen for Livewire events
    window.addEventListener('livewire:init', () => {
        console.log('Livewire initialized on product show');
    });

    window.addEventListener('livewire:load', () => {
        console.log('Livewire loaded on product show');
    });

    // Listen for custom quantity updated event
    window.addEventListener('quantityUpdated', function(e) {
        console.log('Quantity updated event received:', e.detail);

        // Manually update the input field     value
        if (quantityInput && e.detail && e.detail.quantity) {
            quantityInput.value = e.detail.quantity;
            console.log('Manually updated input value to:', e.detail.quantity);
        }
    });

    // Watch for changes in the debug text and update input accordingly
    function updateQuantityInput() {
        const debugSection = document.querySelector('.text-xs.text-gray-500');
        if (debugSection) {
            const quantityText = debugSection.querySelector('p:first-child');
            if (quantityText) {
                const match = quantityText.textContent.match(/Current Quantity: (\d+)/);
                if (match) {
                    const newQuantity = match[1];
                    const input = document.getElementById('quantity-input');
                    if (input && input.val    ue !== newQuantity) {
                        input.value = newQuantity;
                        console.log('Updated input value to:', newQuantity);
                    }
                }
            }
        }
    }

    // Set up observer to watch for changes in the debug section
    const debugObserver = new MutationObserver(function    (mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                updateQuantityInput();
            }
        });
    });

    // Start observing the debug section
    const debugSection = docume    nt.querySelector('.text-xs.text-gray-500');
    if (debugSection) {
        debugObserver.observe(debugSection, {
            childList: true,
            characterData: true,
            subtree: true
        });
    }

    // Also update when Livewire processes messages
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint.name === 'product-show') {
            console.log('Product show component updated, checking for quantity changes');
            setTimeout(updateQuantityInput, 50);
        }
    });

    // Listen for Livewire refresh events
    window.addEventListener('livewire:load', () => {
        console.log('Livewire loaded on product show');

        // Set up a listener for when Livewire updates the DOM
        Livewire.hook('message.processed', (message, component) => {
            if (component.fingerprint.name === 'product-show') {
                console.log('Product show component updated, checking quantity input');
                const updatedInput = document.getElementById('quantity-input');
                if (updatedInput) {
                    console.log('Updated input value:', updatedInput.value);
                }
            }
        });
    });
});
</script>
@endif
