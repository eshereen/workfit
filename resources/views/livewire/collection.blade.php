<div class="py-8">
    @if($collection)
        <!-- Collection Header -->
        <div class="container mx-auto px-4 mb-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $collection->name }}</h1>
                @if($collection->description)
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">{{ $collection->description }}</p>
                @endif
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
          @livewire('product-index', ['products' => $products])
        @else
            <!-- No Products Found -->
            <div class="container mx-auto px-4 text-center py-12">
                <div class="text-gray-500">
                    <i class="fas fa-search text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">No products found in {{ $collection->name }}</h3>
                    <p class="text-gray-600">
                        @if($search)
                            No products match your search "{{ $search }}" in this collection.
                        @else
                            This collection doesn't have any products yet.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    @else
        <!-- Collection Not Found -->
        <div class="container mx-auto px-4 text-center py-12">
            <div class="text-gray-500">
                <i class="fas fa-exclamation-triangle text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Collection not found</h3>
                <p class="text-gray-600">The collection you're looking for doesn't exist or is not active.</p>
            </div>
        </div>
    @endif

    <!-- Variant Selection Modal -->
    @if($showVariantModal && $selectedProduct)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ $selectedProduct->name }}</h3>
                        <button
                            wire:click="$set('showVariantModal', false)"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Product Image -->
                    @if($selectedProduct->media->count() > 0)
                        <img
                            src="{{ $selectedProduct->media->first()->getUrl('medium') }}"
                            alt="{{ $selectedProduct->name }}"
                            class="w-full h-48 object-cover rounded-lg mb-4"
                        >
                    @endif

                    <!-- Variant Selection -->
                    @if($selectedProduct->variants->count() > 0)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Variant:</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($selectedProduct->variants as $variant)
                                    <button
                                        wire:click="selectVariant({{ $variant->id }})"
                                        class="p-3 border rounded-lg text-left hover:border-red-500 transition-colors {{ $selectedVariantId == $variant->id ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                                    >
                                        <div class="flex items-center gap-2">
                                            @if($variant->color)
                                                <div
                                                    class="w-4 h-4 rounded-full border border-gray-300"
                                                    style="background-color: {{ $this->getColorCode($variant->color) }}"
                                                ></div>
                                            @endif
                                            <span class="text-sm font-medium">{{ $variant->name ?: 'Default' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            @if($variant->converted_price)
                                                {{ $currencySymbol }}{{ number_format($variant->converted_price, 2) }}
                                            @else
                                                {{ $currencySymbol }}{{ number_format($variant->price, 2) }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Stock: {{ $variant->stock }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="decrementQty"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                {{ $quantity <= 1 ? 'disabled' : '' }}
                            >
                                <i class="fas fa-minus text-sm"></i>
                            </button>
                            <span class="text-lg font-medium w-12 text-center">{{ $quantity }}</span>
                            <button
                                wire:click="incrementQty"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                {{ $quantity >= ($selectedVariant ? $selectedVariant->stock : 10) ? 'disabled' : '' }}
                            >
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <button
                        wire:click="addToCart"
                        class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ !$selectedVariant ? 'disabled' : '' }}
                    >
                        @if($selectedVariant)
                            Add to Cart - {{ $currencySymbol }}{{ number_format($selectedVariant->converted_price ?? $selectedVariant->price, 2) }}
                        @else
                            Select a variant first
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
