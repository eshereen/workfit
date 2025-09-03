<div class="space-y-6">
    <!-- Search and Sort Controls -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Search -->
        <div class="w-full md:w-96">
            <input
                wire:model.live="search"
                type="text"
                placeholder="Search products..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
        </div>

        <!-- Sort -->
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Sort by:</label>
            <select
                wire:model.live="sortBy"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
                <option value="newest">Newest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
            </select>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700">Showing products for:</span>
                @foreach($selectedCategories as $categoryId)
                    @php
                        $category = $this->getCategories()->firstWhere('id', $categoryId);
                    @endphp
                    @if($category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Category: {{ $category->name }}
                        </span>
                    @endif
                @endforeach
                @foreach($selectedSubcategories as $subcategoryId)
                    @php
                        $subcategory = null;
                        foreach($this->getCategories() as $cat) {
                            $sub = $this->getSubcategories($cat->id)->firstWhere('id', $subcategoryId);
                            if($sub) {
                                $subcategory = $sub;
                                break;
                            }
                        }
                    @endphp
                    @if($subcategory)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Subcategory: {{ $subcategory->name }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1  md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Product Image -->
                    <div class="relative group" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
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
                                         class="w-full h-full object-cover"
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
                                             class="w-full h-full object-cover"
                                             width="800"
                                             height="800"
                                             loading="lazy"
                                             decoding="async">
                                    </picture>
                                @endif
                            @else
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </a>

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black/5  group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center pointer-events-none group-hover:pointer-events-auto">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-2 pointer-events-auto">
                                @if($product->variants->count() > 0)
                                    <button
                                        wire:click="openVariantModal({{ $product->id }})"
                                        class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors pointer-events-auto"
                                    >
                                        <i class="fas fa-eye mr-2"></i>View Options
                                    </button>
                                @else
                                    <button
                                        wire:click="addSimpleProductToCart({{ $product->id }})"
                                        class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors pointer-events-auto"
                                    >
                                        <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                    </button>
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
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>

                        @if($product->category)
                            <p class="text-sm text-gray-600 mb-2">{{ $product->category->name }}</p>
                        @endif

                        <!-- Price -->
                        <div class="flex items-center gap-2 mb-3">
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
                                    class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
                                >
                                    View Options
                                </button>
                            @else
                                <button
                                    wire:click="addSimpleProductToCart({{ $product->id }})"
                                    class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
                                >
                                    Add to Cart
                                </button>
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
        <div class="text-center py-12">
            <div class="text-gray-500">
                <i class="fas fa-search text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No products found</h3>
                <p class="text-gray-600">
                    @if($search)
                        No products match your search "{{ $search }}"
                    @elseif(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
                        No products found for the selected filters
                    @else
                        This collection doesn't have any products yet.
                    @endif
                </p>
            </div>
        </div>
    @endif

    <!-- Variant Selection Modal -->
    @if($showVariantModal && $selectedProduct)
        <div class="fixed inset-0 bg-black/50  flex items-center justify-center z-50 p-4">
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
                                            <span class="text-sm font-medium">{{ $variant->name  }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            @if($variant->price && $variant->price > 0)
                                                {{ $currencySymbol }}{{ number_format($this->convertPrice($variant->price), 2) }}
                                            @else
                                                {{ $currencySymbol }}{{ number_format($this->convertPrice($selectedProduct->price), 2) }}
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
