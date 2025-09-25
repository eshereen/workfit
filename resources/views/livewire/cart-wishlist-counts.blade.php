<div class="flex items-center space-x-4">
    <!-- Search Component (hidden on mobile, visible on md+) -->
    <div class="hidden md:block">
        @livewire('product-search')
    </div>

    <!-- Wishlist Link -->
    @auth
    <a href="{{ route('wishlist.index') }}" class="hidden relative transition-colors md:inline-block font-xs hover:text-red-600"
       :class="isHome && !scrolled ? 'text-white' : 'text-gray-950'"
       x-data="{ isHome: {{ request()->routeIs('home') ? 'true' : 'false' }}, scrolled: false }"
       x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })">
        <i class="text-xl fas fa-heart"></i>
        @if($wishlistCount > 0)
        <span class="flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white bg-red-500 rounded-full">
            {{ $wishlistCount }}
        </span>
        @endif
    </a>
    @endauth

    <!-- Cart Link -->
    <a href="{{ route('cart.index') }}" class="relative transition-colors font-xs hover:text-red-600"
       :class="isHome && !scrolled ? 'text-white' : 'text-gray-950'"
       x-data="{ isHome: {{ request()->routeIs('home') ? 'true' : 'false' }}, scrolled: false }"
       x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })">
        <i class="text-xl fas fa-shopping-bag"></i>
        @if($cartCount > 0)
        <span id="cart-count" class="flex absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white bg-red-500 rounded-full">
            {{ $cartCount }}
        </span>
        @else
        <span id="cart-count" class="hidden absolute -top-2 -right-2 justify-center items-center w-5 h-5 text-xs text-white bg-red-500 rounded-full">
            0
        </span>
        @endif
    </a>
</div>
