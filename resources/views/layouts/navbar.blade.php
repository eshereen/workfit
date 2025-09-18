   <!-- Header -->
   @if(request()->routeIs('home'))
   @if($sale)
 <div class="relative z-[1100] bg-red-600 text-white py-3 px-4 h-auto w-full text-center transition-all duration-300 mb-4">
 <p> <span >{{ $sale->description }}</span> <a href="{{ route('collection.show', $sale->slug) }}" class="px-2 text-gray-500 font-bold underline hover:text-white">Shop Now</a></p>
    </div>
    @endif
    @endif
 </div>
 <header
 x-data="{
   scrolled: false,
  mobileMenuOpen: false,
  categoriesDropdownOpen: false,
  collectionsDropdownOpen: false,
  mobileCategoriesOpen: false,
  mobileCollectionsOpen: false,
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
  <div class="flex items-center justify-between relative">

      <!-- Mobile Menu Button (Left) -->
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden hover:cursor-pointer flex-shrink-0" type="button" aria-controls="mobileMenu">
          <i class="fas fa-bars text-xl" :class="isHome && !scrolled ? 'text-white' : 'text-gray-950'"></i>
      </button>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex space-x-8 flex-1 relative">
          <a href="{{route('categories.index', 'women')}}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">WOMEN</a>
          <a href="{{route('categories.index', 'men')}}" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">MEN</a>

          <!-- Categories Dropdown -->
          <div class="relative"
               @mouseenter="categoriesDropdownOpen = true"
               @mouseleave="categoriesDropdownOpen = false">
              <button class="font-xs hover:text-red-600 transition-colors flex items-center font-light"
                      :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">
                  CATEGORIES
                  <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                  </svg>
              </button>

              <!-- Categories Full-Screen Overlay -->
              <div x-show="categoriesDropdownOpen"
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="opacity-0 transform -translate-y-2"
                   x-transition:enter-end="opacity-100 transform translate-y-0"
                   x-transition:leave="transition ease-in duration-200"
                   x-transition:leave-start="opacity-100 transform translate-y-0"
                   x-transition:leave-end="opacity-0 transform -translate-y-2"
                   class="fixed top-0 left-0 right-0 w-full bg-white shadow-lg border-b border-gray-200 z-[1300]"
                   style="display: none;"
                   @click.away="categoriesDropdownOpen = false">

                  <!-- Header -->
                  <div class="border-b border-gray-100 py-4">
                      <div class="container mx-auto px-4">
                          <div class="flex items-center justify-between">
                              <h2 class="text-2xl font-bold text-gray-900">Browse Categories</h2>
                              <button @click="categoriesDropdownOpen = false"
                                      class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                  <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                  </svg>
                              </button>
                          </div>
                      </div>
                  </div>

                  <!-- Categories Content -->
                  <div class="container mx-auto px-4 py-6">
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                          @foreach($categories as $category)
                          <div class="space-y-3">
                              <!-- Category Header -->
                              <div class="border-b border-gray-200 pb-2">
                                  <a href="{{ route('categories.index', $category->slug) }}"
                                     class="block font-bold text-lg text-gray-900 hover:text-red-600 transition-colors uppercase"
                                     @click="categoriesDropdownOpen = false">
                                      {{ $category->name }}
                                  </a>
                                  <div class="text-xs text-gray-500 mt-1">
                                      {{ $category->products_count ?? 0 }} products
                                  </div>
                              </div>

                              <!-- Subcategories -->
                              @if($category->subcategories && $category->subcategories->count() > 0)
                              <div class="space-y-1">
                                  @foreach($category->subcategories as $subcategory)
                                  <a href="{{ route('categories.subcategory', [$category->slug, $subcategory->slug]) }}"
                                     class="block text-sm text-gray-700 hover:text-red-600 hover:bg-gray-50 py-1 px-2 rounded transition-colors capitalize"
                                     @click="categoriesDropdownOpen = false">
                                      {{ $subcategory->name }}
                                  </a>
                                  @endforeach
                              </div>
                              @endif
                          </div>
                          @endforeach
                      </div>

                      <!-- Footer -->
                      <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                          <a href="{{ route('categories.all') }}"
                             class="inline-flex items-center bg-gray-950 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
                             @click="categoriesDropdownOpen = false">
                              View All Categories
                              <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                              </svg>
                          </a>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Collections Dropdown -->
          <div class="relative"
               @mouseenter="collectionsDropdownOpen = true"
               @mouseleave="collectionsDropdownOpen = false">
              <button class="font-xs hover:text-red-600 transition-colors flex items-center"
                      :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">
                  COLLECTIONS
                  <svg class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                  </svg>
              </button>

              <!-- Collections Full-Screen Overlay -->
              <div x-show="collectionsDropdownOpen"
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="opacity-0 transform -translate-y-2"
                   x-transition:enter-end="opacity-100 transform translate-y-0"
                   x-transition:leave="transition ease-in duration-200"
                   x-transition:leave-start="opacity-100 transform translate-y-0"
                   x-transition:leave-end="opacity-0 transform -translate-y-2"
                   class="fixed top-0 left-0 right-0 w-full bg-white shadow-lg border-b border-gray-200 z-[1300]"
                   style="display: none;"
                   @click.away="collectionsDropdownOpen = false">

                  <!-- Header -->
                  <div class="border-b border-gray-100 py-4">
                      <div class="container mx-auto px-4">
                          <div class="flex items-center justify-between">
                              <h2 class="text-2xl font-bold text-gray-900">Browse Collections</h2>
                              <button @click="collectionsDropdownOpen = false"
                                      class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                  <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                  </svg>
                              </button>
                          </div>
                      </div>
                  </div>

                  <!-- Collections Content -->
                  <div class="container mx-auto px-4 py-6">
                      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                          @foreach($collections as $collection)
                          <a href="{{ route('collection.show', $collection->slug) }}"
                             class="block p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-gray-50 transition-colors group"
                             @click="collectionsDropdownOpen = false">
                              <div class="font-bold text-base text-gray-900 group-hover:text-red-600 transition-colors uppercase mb-1">
                                  {{ $collection->name }}
                              </div>
                              <div class="text-xs text-gray-500">
                                  {{ $collection->products_count }} products
                              </div>
                              <div class="mt-2 text-xs text-red-600 group-hover:text-red-700 font-medium">
                                  Shop Now →
                              </div>
                          </a>
                          @endforeach
                      </div>

                      <!-- Footer -->
                      <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                          <a href="{{ route('collections.index') }}"
                             class="inline-flex items-center bg-gray-950 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
                             @click="collectionsDropdownOpen = false">
                              View All Collections
                              <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                              </svg>
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </nav>

          <!-- Logo (Perfectly Centered) -->
      <a href="{{ route('home') }}" class="absolute left-1/2 transform -translate-x-1/2 flex items-center">
          <!-- White logo (home page, not scrolled) -->
          <img x-show="isHome && !scrolled" src="/imgs/workfit_logo_white.png" alt="logo" class="w-20">
          <!-- Black logo (home page scrolled or non-home page) -->
          <img x-show="!isHome || (isHome && scrolled)" src="/imgs/workfit_logo_black.png" alt="logo" class="w-20">
      </a>

      <!-- Icons (Right) -->
      <div class="flex items-center space-x-4 justify-end relative z-[1001] flex-shrink-0">
          <a href="{{ route('location') }}" class="hidden lg:block font-xs uppercase hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Location</a>

          <!-- Currency Selector -->
          <!-- (your currency selector code here) -->

          <a href="{{ route('login') }}" class="hidden lg:block font-xs hover:text-red-600 uppercase transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Account</a>

           <!-- Cart and Wishlist Counts -->
           @livewire('cart-wishlist-counts')


      </div>
  </div>

  <!-- Mobile Menu Overlay -->
  <div x-show="mobileMenuOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-[1200]"
       @click="mobileMenuOpen = false"
       style="display: none;">
  </div>

  <!-- Mobile Menu Sidebar -->
  <div x-show="mobileMenuOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="transform -translate-x-full"
       x-transition:enter-end="transform translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="transform translate-x-0"
       x-transition:leave-end="transform -translate-x-full"
       class="md:hidden fixed top-0 left-0 h-full w-80 bg-white shadow-xl z-[1300] overflow-y-auto"
       style="display: none;">

    <!-- Mobile Menu Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
        <button @click="mobileMenuOpen = false" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="px-4 py-4 space-y-2">
      <a href="{{route('categories.index', 'women')}}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold py-2" @click="mobileMenuOpen = false">WOMEN</a>
      <a href="{{route('categories.index', 'men')}}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold py-2" @click="mobileMenuOpen = false">MEN</a>

      <!-- Categories Section -->
      <div class="border-t border-gray-200 pt-2">
        <button @click="mobileCategoriesOpen = !mobileCategoriesOpen"
                class="flex items-center justify-between w-full text-left text-gray-900 hover:text-red-600 transition-colors font-semibold py-2">
          <span>CATEGORIES</span>
          <svg class="w-4 h-4 transition-transform duration-200"
               :class="mobileCategoriesOpen ? 'rotate-45' : ''"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>

        <div x-show="mobileCategoriesOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-96"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 max-h-96"
             x-transition:leave-end="opacity-0 max-h-0"
             class="overflow-hidden"
             style="display: none;">
          <div class="pl-4 pt-2 space-y-1">
            <a href="{{ route('categories.all') }}" class="block text-sm text-gray-700 hover:text-red-600 transition-colors py-1" @click="mobileMenuOpen = false">All Categories</a>
            @foreach($categories as $category)
            <div class="space-y-1">
              <a href="{{ route('categories.index', $category->slug) }}" class="block text-sm font-medium text-gray-800 hover:text-red-600 transition-colors py-1" @click="mobileMenuOpen = false">{{ $category->name }}</a>
              @if($category->subcategories && $category->subcategories->count() > 0)
              <div class="pl-3 space-y-1">
                @foreach($category->subcategories as $subcategory)
                <a href="{{ route('categories.subcategory', [$category->slug, $subcategory->slug]) }}" class="block text-xs text-gray-600 hover:text-red-600 transition-colors py-1" @click="mobileMenuOpen = false">{{ $subcategory->name }}</a>
                @endforeach
              </div>
              @endif
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Collections Section -->
      <div class="border-t border-gray-200 pt-2">
        <button @click="mobileCollectionsOpen = !mobileCollectionsOpen"
                class="flex items-center justify-between w-full text-left text-gray-900 hover:text-red-600 transition-colors font-semibold py-2">
          <span>COLLECTIONS</span>
          <svg class="w-4 h-4 transition-transform duration-200"
               :class="mobileCollectionsOpen ? 'rotate-45' : ''"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>

        <div x-show="mobileCollectionsOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-96"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 max-h-96"
             x-transition:leave-end="opacity-0 max-h-0"
             class="overflow-hidden"
             style="display: none;">
          <div class="pl-4 pt-2 space-y-1">
            <a href="{{ route('collections.index') }}" class="block text-sm text-gray-700 hover:text-red-600 transition-colors py-1" @click="mobileMenuOpen = false">All Collections</a>
            @foreach($collections as $collection)
            <a href="{{ route('collection.show', $collection->slug) }}" class="block text-sm text-gray-700 hover:text-red-600 transition-colors py-1" @click="mobileMenuOpen = false">{{ $collection->name }}</a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="border-t border-gray-200 pt-2 space-y-2">
        <a href="{{ route('location') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold py-2" @click="mobileMenuOpen = false">LOCATION</a>
        <a href="{{ route('login') }}" class="block text-gray-900 hover:text-red-600 transition-colors font-semibold py-2" @click="mobileMenuOpen = false">ACCOUNT</a>
      </div>
    </nav>
  </div>
</div>
</header>

