   <!-- Header -->
   @if(request()->routeIs('home'))
   @if($sale)
 <div class="relative z-[1100] bg-red-600 text-white py-3 px-4 h-auto w-full text-center transition-all duration-300 ">
 <p> <span >{{ $sale->description }}</span> <a href="{{ route('collection.show', $sale->slug) }}" class="px-2 font-bold text-gray-800 underline hover:text-white">Shop Now</a></p>
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
  searchModalOpen: false,
  searchQuery: '',
   isHome: {{ request()->routeIs('home') ? 'true' : 'false' }},
   init() {
     window.addEventListener('scroll', () => {
       this.scrolled = window.scrollY > 10;
     });
   },
   openSearchModal() {
     this.searchModalOpen = true;
     this.$nextTick(() => {
       this.$refs.searchInput.focus();
     });
   },
   closeSearchModal() {
     this.searchModalOpen = false;
     this.searchQuery = '';
   },
   performSearch() {
     if (this.searchQuery.trim()) {
       window.location.href = '{{ route('products.search') }}?q=' + encodeURIComponent(this.searchQuery.trim());
     }
   }
 }"
 :class="{
   'fixed top-0 left-0 right-0 bg-white text-gray-900 shadow-md': (isHome ? scrolled : true),
   'relative bg-transparent text-gray-900 caret-gray-100' : isHome && !scrolled
 }"
 class="z-[1100] transition-all duration-300 py-3 mb-10 font-semibold max-h-30"
>
<div class="container px-8 mx-auto">
  <div class="flex relative justify-between items-center">

      <!-- Mobile Left Side: Menu and Search -->
      <div class="flex flex-shrink-0 items-center space-x-3 md:hidden">
          <!-- Mobile Menu Button (bars) -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="hover:cursor-pointer" type="button" aria-controls="mobileMenu">
              <i class="text-xl fas fa-bars" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'"></i>
          </button>

          <!-- Search Icon -->
          <button @click="openSearchModal()" class="hover:cursor-pointer">
              <i class="text-xl fas fa-search" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'"></i>
          </button>
      </div>

      <!-- Desktop Navigation -->
      <!-- Desktop Navigation -->
      <nav class="hidden relative flex-1 space-x-12 md:flex" x-data="{ openCategory: null }">
          @foreach($categories as $category)
              <div class="relative" 
                   @mouseenter="openCategory = '{{ $category->id }}'" 
                   @mouseleave="openCategory = null">
                  
                  <a href="{{ route('categories.index', $category->slug) }}"
                     class="flex items-center text-sm font-bold uppercase transition-colors hover:text-red-600 mx-2"
                     :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">
                      {{ $category->name }}
                  </a>

                  <!-- Full Width Mega Menu -->
                  @if($category->subcategories->count() > 0)
                      <div x-show="openCategory === '{{ $category->id }}'"
                           x-transition:enter="transition ease-out duration-200"
                           x-transition:enter-start="opacity-0 -translate-y-1"
                           x-transition:enter-end="opacity-100 translate-y-0"
                           x-transition:leave="transition ease-in duration-150"
                           x-transition:leave-start="opacity-100 translate-y-0"
                           x-transition:leave-end="opacity-0 -translate-y-1"
                           class="fixed top-[4.5rem] left-0 right-0 w-full bg-white shadow-lg border-t border-gray-100 z-[1200] pt-6 pb-8"
                           style="display: none;"
                           x-cloak>
                          
                          <div class="container mx-auto p-8">
                              <div class="grid grid-cols-4 gap-8">
                                  <!-- Subcategories with Products -->
                                  @foreach($category->subcategories as $subcategory)
                                      <div class="space-y-3">
                                          <!-- Subcategory Title -->
                                          <a href="{{ route('categories.subcategory', [$category->slug, $subcategory->slug]) }}"
                                             class="block font-bold text-gray-900 uppercase tracking-wider hover:text-red-600">
                                              {{ $subcategory->name }}
                                          </a>

                                          <!-- Top Products List -->
                                          @if($subcategory->products && $subcategory->products->count() > 0)
                                              <ul class="space-y-2">
                                                  @foreach($subcategory->products->take(4) as $product)
                                                      <li>
                                                          <a href="{{ route('product.show', $product->slug) }}" 
                                                             class="block text-xs text-gray-500 hover:text-red-600 truncate transition-colors">
                                                              {{ $product->name }}
                                                          </a>
                                                      </li>
                                                  @endforeach
                                              </ul>
                                          @endif
                                      </div>
                                  @endforeach
                              </div>
                              
                              <div class="my-6 pt-4 border-t border-gray-100 text-center">
                                  <a href="{{ route('categories.index', $category->slug) }}" 
                                     class="inline-block text-sm font-semibold text-red-600 hover:text-red-700">
                                      View All {{ $category->name }} →
                                  </a>
                              </div>
                          </div>
                      </div>
                  @endif
              </div>
          @endforeach
      </nav>

          <!-- Logo (Perfectly Centered) -->
      <a href="{{ route('home') }}" class="flex absolute left-1/2 items-center transform -translate-x-1/2">
          <!-- White logo (home page, not scrolled) -->
          <img x-show="isHome && !scrolled" src="/imgs/workfit_logo_white.svg" alt="logo" class="w-16" width="64" height="64" fetchpriority="high" decoding="async">
          <!-- Black logo (home page scrolled or non-home page) -->
          <img x-show="!isHome || (isHome && scrolled)" src="/imgs/workfit_logo_black.png" alt="logo" class="w-16" width="64" height="64" fetchpriority="high" decoding="async">
      </a>

      <!-- Desktop Right Side Icons -->
      <div class="hidden md:flex items-center space-x-4 justify-end relative z-[1001] flex-shrink-0">
          <a href="{{ route('location') }}" class="hidden text-sm uppercase transition-colors lg:block font-xs hover:text-red-600" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Location</a>

          <!-- Currency Selector -->
          @livewire('currency-selector')

          <a href="{{ route('login') }}" class="hidden uppercase transition-colors lg:block font-xs hover:text-red-600" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'"><i class="fas fa-user"></i></a>

           <!-- Cart and Wishlist Counts -->
           @livewire('cart-wishlist-counts')
      </div>

      <!-- Mobile Right Side: Currency and Cart -->
      <div class="flex flex-shrink-0 items-center space-x-2 md:hidden">
          <!-- Currency Selector -->
          @livewire('currency-selector')

          <!-- Cart Count Only (wishlist/search hidden on mobile inside component) -->
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
    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Menu</h2>
        <button @click="mobileMenuOpen = false" class="p-2 rounded-full transition-colors hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="px-4 py-4 space-y-2">
      @foreach($categories as $category)
        <div x-data="{ open: false }" class="border-b border-gray-100 last:border-0 pb-2">
            <div class="flex justify-between items-center">
                <a href="{{ route('categories.index', $category->slug) }}"
                   class="block py-2 font-semibold text-gray-900 uppercase transition-colors hover:text-red-600"
                   @click="mobileMenuOpen = false">
                    {{ $category->name }}
                </a>
                
                @if($category->subcategories->count() > 0)
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-red-600 focus:outline-none">
                        <!-- Plus Icon -->
                        <svg x-show="!open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <!-- Minus Icon -->
                        <svg x-show="open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    </button>
                @endif
            </div>

            @if($category->subcategories->count() > 0)
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-[800px]"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 max-h-[800px]"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="pl-4 space-y-1 mt-1 overflow-hidden"
                     style="display: none;">
                    @foreach($category->subcategories as $subcategory)
                        <a href="{{ route('categories.subcategory', [$category->slug, $subcategory->slug]) }}"
                           class="block py-1 text-sm text-gray-600 hover:text-red-600 capitalize"
                           @click="mobileMenuOpen = false">
                            {{ $subcategory->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
      @endforeach

      <div class="pt-2 space-y-2 border-t border-gray-200 mt-4">
        <a href="{{ route('location') }}" class="block py-2 font-semibold text-gray-900 transition-colors hover:text-red-600" @click="mobileMenuOpen = false">LOCATION</a>
        <a href="{{ route('login') }}" class="block py-2 font-semibold text-gray-900 transition-colors hover:text-red-600" @click="mobileMenuOpen = false">ACCOUNT</a>
      </div>
    </nav>
  </div>

  <!-- Search Modal -->
  <div x-cloak
       x-show="searchModalOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 transform -translate-y-2"
       x-transition:enter-end="opacity-100 transform translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 transform translate-y-0"
       x-transition:leave-end="opacity-0 transform -translate-y-2"
       class="fixed top-16 left-0 right-0 h-32 bg-white border-b border-gray-200 shadow-lg z-[1400]"
      >
    <!-- Search Modal Content -->
    <div class="container flex items-center px-8 mx-auto h-full" >
      <div class="flex flex-1 items-center space-x-4" style="color: #111827 !important;">
        <div class="relative flex-1">
          <input
            x-ref="searchInput"
            x-model="searchQuery"
            @keydown.enter="performSearch()"
            @keydown.escape="closeSearchModal()"
            type="text"
            placeholder="Search products..."
            class="px-4 py-3 w-full text-lg bg-white border-0 border-b-2 border-gray-300 outline-none focus:ring-0 focus:border-red-500 placeholder-gray-500"
            style="color: #111827 !important; caret-color: #111827 !important; background-color: #ffffff !important; -webkit-text-fill-color: #111827 !important;"
          >
        </div>
        <button @click="performSearch()"
                :disabled="!searchQuery.trim()"
                class="p-3 transition-colors hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                style="color: #4b5563 !important; caret-color: #fcfcfc !important;">
          <i class="text-xl fas fa-search" style="color: #4b5563 !important;"></i>
        </button>
        <button @click="closeSearchModal()"
                class="p-3 transition-colors hover:text-gray-600"
                style="color: #111827 !important;">
          <i class="text-xl fas fa-times" style="color: #111827 !important; care"></i>
        </button>
      </div>
    </div>
  </div>
</div>
</header>

