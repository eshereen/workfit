   <!-- Header -->
   @if(request()->routeIs('home'))
   @if($sale)
 <div class="relative z-[1100] bg-red-600 text-white py-3 px-4 h-auto w-full text-center transition-all duration-300">
 <p> <span >{{ $sale->description }}</span> <a href="{{ route('collection.show', $sale->slug) }}" class="px-2 text-gray-800 font-bold underline hover:text-white">Shop Now</a></p>
    </div>
    @endif
    @endif
 </div>
<header
x-data="{
  scrolled: false,
  mobileMenuOpen: false,
  isHome: {{ request()->routeIs('home') ? 'true' : 'false' }},
  init() {
    window.addEventListener('scroll', () => {
      this.scrolled = window.scrollY > 10;
    });
  }
}"
:class="{
  'fixed top-0 left-0 right-0 bg-white text-gray-900 shadow-md': (isHome ? scrolled : true),
  'relative bg-transparent text-white': isHome && !scrolled
}"
class="z-[1100] transition-all duration-300 py-3 mb-10 font-semibold max-h-28"
>
<div class="container mx-auto px-4">
  <div class="flex items-center justify-between">

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex space-x-8 flex-1 ">
          <a href="{{route('categories.index', 'women')}}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">WOMEN</a>
          <a href="{{route('categories.index', 'men')}}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">MEN</a>
          <a href="{{ route('categories.all') }}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">CATEGORIES</a>
          <a href="{{ route('collections.index') }}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">COLLECTIONS</a>
      </nav>

          <!-- Logo -->
      <a href="/" class="flex items-center flex-1 justify-center">
          <!-- White logo (home page, not scrolled) -->
          <img x-show="isHome && !scrolled" src="/imgs/workfit_logo_white.png" alt="logo" class="w-20">
          <!-- Black logo (home page scrolled or non-home page) -->
          <img x-show="!isHome || (isHome && scrolled)" src="/imgs/workfit_logo_black.png" alt="logo" class="w-20">
      </a>

      <!-- Icons -->
      <div class="flex items-center flex-1 space-x-4 justify-end relative z-[1001]">
          <a href="{{ route('location') }}" class="hidden lg:block font-xs uppercase hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Location</a>

          <!-- Currency Selector -->
          <!-- (your currency selector code here) -->

          <a href="{{ route('login') }}" class="hidden lg:block font-xs hover:text-red-600 uppercase transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Account</a>

           <!-- Cart and Wishlist Counts -->
           @livewire('cart-wishlist-counts')

          <!-- Mobile Menu Button -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden hover:cursor-pointer" type="button" aria-controls="mobileMenu">
              <i class="fas fa-bars text-xl" :class="isHome && !scrolled ? 'text-white' : 'text-gray-950'"></i>
          </button>
      </div>
  </div>

  <!-- Mobile Menu -->
  <div x-show="mobileMenuOpen"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 transform -translate-y-2"
       x-transition:enter-end="opacity-100 transform translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 transform translate-y-0"
       x-transition:leave-end="opacity-0 transform -translate-y-2"
       class="md:hidden absolute top-full left-0 right-0 bg-white shadow-lg border-t border-gray-200 z-[1050] hover:cursor-pointer"
       @click.away="mobileMenuOpen = false">
    <nav class="px-4 py-6 space-y-4">
      <a href="{{route('categories.index', 'women')}}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">WOMEN</a>
      <a href="{{route('categories.index', 'men')}}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">MEN</a>
      <a href="{{ route('categories.all') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">CATEGORIES</a>
      <a href="{{ route('collections.index') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">COLLECTIONS</a>
      <a href="{{ route('location') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">LOCATION</a>
      <a href="{{ route('login') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold" @click="mobileMenuOpen = false">ACCOUNT</a>
    </nav>
  </div>
</div>
</header>
