<div>
    <div class="space-y-6">
        <!-- Search and Sort Controls -->
        <div class="flex flex-col gap-4 justify-between items-center md:flex-row">
            <!-- Search -->
            <div class="w-full md:w-96">
                <input
                    wire:model.live="search"
                    type="text"
                    placeholder="Search products..."
                    class="px-4 py-2 w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
            </div>

            <!-- Sort -->
            <div class="flex gap-2 items-center">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select
                    wire:model.live="sortBy"
                    class="px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
                    <option value="newest">Newest</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-sm font-medium text-gray-700">Showing products for:</span>
                    @foreach($selectedCategories as $categoryId)
                        @php
                            $category = \App\Models\Category::find($categoryId);
                        @endphp
                        @if($category)
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                Category: {{ $category->name }}
                            </span>
                        @endif
                    @endforeach
                    @foreach($selectedSubcategories as $subcategoryId)
                        @php
                            $subcategory = \App\Models\Subcategory::find($subcategoryId);
                        @endphp
                        @if($subcategory)
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                                Subcategory: {{ $subcategory->name }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-3">
                @foreach($products as $product)
                    <div  class="overflow-hidden bg-white rounded-lg shadow-md transition-shadow duration-300 hover:shadow-lg">
                        <!-- Product Image -->
                        <div class="relative group" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <!-- Badges -->
                            <div class="flex absolute top-2 left-2 z-30 flex-col gap-1">
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

                            <a href="{{ route('product.show', $product->slug) }}" class="block relative z-10 w-full h-64">
                                @if($product->media->count() > 0)
                                    {{-- Main image --}}
                                    <picture class="w-full h-full transition-opacity duration-500"
                                             :class="hover ? 'opacity-0' : 'opacity-100'">
                                        {{-- Modern formats first --}}
                                        <source srcset="{{ $product->getFirstMediaUrl('main_image', 'large_avif') }}" type="image/avif">
                                        <source srcset="{{ $product->getFirstMediaUrl('main_image', 'large_webp') }}" type="image/webp">
                                        {{-- Fallback for older browsers --}}
                                        <img src="{{ $product->getFirstMediaUrl('main_image') }}"
                                             alt="{{ $product->name }}"
                                             class="object-cover w-full h-full"
                                             width="800"
                                             height="800"
                                             loading="lazy"
                                             decoding="async"
                                             fetchpriority="high">
                                    </picture>

                                    {{-- Gallery image (if exists) --}}
                                    @php
                                        $galleryImage = $product->getFirstMediaUrl('product_images');
                                    @endphp

                                    @if($galleryImage)
                                        <picture class="absolute top-0 left-0 w-full h-full transition-opacity duration-500"
                                                 :class="hover ? 'opacity-100' : 'opacity-0'">
                                            {{-- Modern formats first --}}
                                            <source srcset="{{ $product->getFirstMediaUrl('product_images', 'zoom_avif') }}" type="image/avif">
                                            <source srcset="{{ $product->getFirstMediaUrl('product_images', 'zoom_webp') }}" type="image/webp">
                                            {{-- Fallback for older browsers --}}
                                            <img src="{{ $galleryImage }}"
                                                 alt="{{ $product->name }}"
                                                 class="object-cover w-full h-full"
                                                 width="800"
                                                 height="800"
                                                 loading="lazy"
                                                 decoding="async">
                                        </picture>
                                    @endif
                                @else
                                    <div class="flex justify-center items-center w-full h-64 bg-gray-200">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Quick Actions Overlay -->
                            <div class="flex absolute inset-0 justify-center items-center bg-black opacity-0 transition-all duration-300 pointer-events-none group-hover:opacity-20 group-hover:pointer-events-auto">
                                <div class="flex gap-2 opacity-0 transition-opacity duration-300 pointer-events-auto group-hover:opacity-100">
                                    @if($product->variants->count() > 0)
                                        <button
                                            wire:click="openVariantModal({{ $product->id }})"
                                            class="px-4 py-2 text-gray-900 bg-white rounded-full transition-colors pointer-events-auto hover:bg-red-600 hover:text-white"
                                        >
                                            <i class="mr-2 fas fa-eye"></i>View Options
                                        </button>
                                    @else
                                        @if($product->quantity > 0)
                                            <button
                                                wire:click="addSimpleProductToCart({{ $product->id }})"
                                                class="px-4 py-2 text-gray-900 bg-white rounded-full transition-colors hover:bg-red-600 hover:text-white"
                                            >
                                                <i class="mr-2 fas fa-shopping-cart"></i>Add to Cart
                                            </button>
                                        @else
                                            <button
                                                disabled
                                                class="px-4 py-2 text-gray-500 bg-gray-300 rounded-full opacity-50 cursor-not-allowed"
                                            >
                                                <i class="mr-2 fas fa-times"></i>Out of Stock
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Wishlist Button -->
                            <button
                                wire:click="toggleWishlist({{ $product->id }})"
                                class="absolute top-3 right-3 p-2 rounded-full bg-white shadow-md hover:bg-red-600 hover:text-white transition-colors {{ in_array($product->id, $wishlistProductIds) ? 'text-red-600' : 'text-gray-400' }}"
                            >
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="mb-2 font-semibold text-gray-900 line-clamp-2">{{ $product->name }}</h3>

                            @if($product->category)
                                <p class="mb-2 text-sm text-gray-600">{{ $product->category->name }}</p>
                            @endif

                            <!-- Price -->
                            <div class="flex gap-2 items-center mb-3">
                                <span class="text-lg font-bold text-gray-900">{{ $currencySymbol }}{{ number_format($this->convertPrice($product->price), 2) }}</span>

                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="text-sm text-gray-500 line-through">{{ $currencySymbol }}{{ number_format($this->convertPrice($product->compare_price), 2) }}</span>
                                @endif
                            </div>

                            <!-- Variant Colors (if any) -->


                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            @if($product->variants->count() > 0)
                                <button
                                    wire:click="openVariantModal({{ $product->id }})"
                                    class="flex-1 px-4 py-2 text-white rounded-lg transition-colors cursor-pointer bg-gray-950 hover:bg-gray-100 hover:text-gray-950"
                                >
                                    View Options
                                </button>
                            @else
                                @if($product->quantity > 0)
                                    <button
                                        wire:click="addSimpleProductToCart({{ $product->id }})"
                                        class="flex-1 px-4 py-2 text-white rounded-lg transition-colors cursor-pointer bg-gray-950 hover:bg-gray-100 hover:text-gray-950"
                                    >
                                        Add to Cart
                                    </button>
                                @else
                                    <button
                                        disabled
                                        class="flex-1 px-4 py-2 text-gray-500 bg-gray-400 rounded-lg opacity-50 cursor-not-allowed"
                                    >
                                        Out of Stock
                                    </button>
                                @endif
                            @endif
                        </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <!-- No Products Found -->
            <div class="py-12 text-center">
                <div class="text-gray-500">
                    <i class="mb-4 text-6xl fas fa-search"></i>
                    <h3 class="mb-2 text-xl font-semibold">No products found</h3>
                    <p class="text-gray-600">
                        @if($search)
                            No products match your search "{{ $search }}"
                        @elseif(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
                            No products found for the selected filters
                        @else
                            No products found in this category.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- Variant Selection Modal -->
        @if($showVariantModal && $selectedProduct)
            <div class="flex fixed inset-0 z-50 justify-center items-center p-4 bg-black/50">
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
                                src="{{ $selectedProduct->getFirstMediaUrl('main_image','medium') }}"
                                alt="{{ $selectedProduct->name }}"
                                class="object-cover mb-4 w-full h-48 rounded-lg"
                            >
                        @endif

                        <!-- Variant Selection -->
                        @if($selectedProduct->variants->count() > 0)
                            <div class="mb-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Select Variant:</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($selectedProduct->variants as $variant)
                                        <button
                                            wire:click="selectVariant({{ $variant->id }})"
                                            class="p-3 border rounded-lg text-left hover:border-red-500 transition-colors {{ $selectedVariantId == $variant->id ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                                        >
                                            <div class="flex gap-2 items-center">
                                                @if($variant->color)
                                                    <div
                                                        class="w-4 h-4 rounded-full border border-gray-300"
                                                        style="background-color: {{ $this->getColorCode($variant->color) }}"
                                                    ></div>
                                                @endif
                                                <span class="text-sm font-medium">
                                                    {{ $variant->color ? ucfirst($variant->color) : '' }}{{ $variant->size ? ' - ' . $variant->size : '' }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600">
                                                @if($variant->price && $variant->price > 0)
                                                       {{ number_format($this->convertPrice($variant->price), 2) }}  {{ $currencySymbol }}
                                                @else
                                                    {{ number_format($this->convertPrice($selectedProduct->price), 2) }} {{ $currencySymbol }}
                                                @endif
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                Stock: {{ $variant->stock }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quantity Selection -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Quantity:</label>
                            <div class="flex gap-3 items-center">
                                <button
                                    wire:click="decrementQty"
                                    class="flex justify-center items-center w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}
                                >
                                    <i class="text-sm fas fa-minus"></i>
                                </button>
                                <span class="w-12 text-lg font-medium text-center">{{ $quantity }}</span>
                                <button
                                    wire:click="incrementQty"
                                    class="flex justify-center items-center w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100"
                                    {{ $quantity >= ($selectedVariant ? $selectedVariant->stock : 10) ? 'disabled' : '' }}
                                >
                                    <i class="text-sm fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button
                            wire:click="addToCart"
                            class="px-4 py-3 w-full text-white bg-red-600 rounded-lg transition-colors hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$selectedVariant ? 'disabled' : '' }}
                        >
                            @if($selectedVariant)
                                Add to Cart -
                                @if($selectedVariant->price && $selectedVariant->price > 0)
                                    {{ $currencySymbol }}{{ number_format($this->convertPrice($selectedVariant->price), 2) }}
                                @else
                                    {{ $currencySymbol }}{{ number_format($this->convertPrice($selectedProduct->price), 2) }}
                                @endif
                            @else
                                Select a variant first
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
