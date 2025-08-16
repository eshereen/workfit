<div class="flex items-center space-x-4">
    <!-- Wishlist Link -->
    @auth
    <a href="{{ route('wishlist.index') }}" class="relative font-xs hover:text-red-600 transition-colors">
        <i class="fas fa-heart text-gray-800 text-xl"></i>
        @if($wishlistCount > 0)
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            {{ $wishlistCount }}
        </span>
        @endif
    </a>
    @endauth

    <!-- Cart Link -->
    <a href="{{ route('cart.index') }}" class="relative font-xs hover:text-red-600 transition-colors">
        <i class="fas fa-shopping-bag text-gray-800 text-xl"></i>
        @if($cartCount > 0)
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            {{ $cartCount }}
        </span>
        @else
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
            0
        </span>
        @endif
    </a>
</div>
