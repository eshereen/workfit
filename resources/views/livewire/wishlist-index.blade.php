<div class="container mx-auto px-4 py-8">
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

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-red-600">My Wishlist</h1>
        @if($wishlistItems->count() > 0)
        <button wire:click="clearWishlist"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
            Clear Wishlist
        </button>
        @endif
    </div>

    @if($currencyCode !== 'USD')
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="text-sm text-blue-800 text-center">
            @if($isAutoDetected)
                Prices automatically converted to {{ $currencyCode }} ({{ $currencySymbol }}) based on your location
            @else
                Prices converted to {{ $currencyCode }} ({{ $currencySymbol }})
            @endif
        </div>
    </div>
    @endif

    @if($wishlistItems->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $wishlistItem)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <a href="{{ route('product.show', $wishlistItem->product->slug) }}">
                    <img src="{{ $wishlistItem->product->getFirstMediaUrl('main_image', 'medium') }}"
                         alt="{{ $wishlistItem->product->name }}"
                         class="w-full h-64 object-cover">
                </a>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <a href="{{ route('product.show', $wishlistItem->product->slug) }}"
                               class="font-semibold text-lg hover:text-red-600">
                                {{ $wishlistItem->product->name }}
                            </a>
                            <p class="text-gray-600 text-sm">{{ $wishlistItem->product->category->name }}</p>
                        </div>
                        <button wire:click="removeFromWishlist({{ $wishlistItem->id }})"
                                class="text-red-500 hover:text-red-700 transition-colors"
                                title="Remove from wishlist">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="font-bold text-lg">{{ $currencySymbol }}{{ number_format($wishlistItem->product->converted_price ?? $wishlistItem->product->price, 2) }}</span>
                            @if($wishlistItem->product->compare_price > 0)
                            <span class="text-sm text-gray-500 line-through ml-2">
                                {{ $currencySymbol }}{{ number_format($wishlistItem->product->converted_compare_price ?? $wishlistItem->product->compare_price, 2) }}
                            </span>
                            @endif
                        </div>
                        @if($wishlistItem->product->compare_price > 0)
                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                            -{{ $wishlistItem->product->discount_percentage }}%
                        </span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="addToCart({{ $wishlistItem->product->id }}, {{ $wishlistItem->product->variants->isNotEmpty() ? 'true' : 'false' }})"
                                class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-sm">
                            Add to Cart
                        </button>
                        <button wire:click="removeFromWishlist({{ $wishlistItem->id }})"
                                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-gray-400 mb-4">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-500 mb-6">Start adding products you love to your wishlist!</p>
            <a href="{{ route('products.index') }}"
               class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                Browse Products
            </a>
        </div>
    @endif
</div>
