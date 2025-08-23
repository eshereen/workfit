   <!-- Header -->
   @if(request()->routeIs('home'))
 <header class="relative max-h-28  z-50 transition-all duration-300 py-3 mb-10 bg-transparent hover:bg-white  text-white font-semibold hover:text-gray-900 group">
    @else
    <header class="fixed top-0 left-0 right-0 z-50 max-h-28   transition-all duration-300 py-3 mb-10 bg-white hover:bg-white  text-gray-900 font-semibold group">
    @endif
<div class="container mx-auto px-4">
   <div class="flex items-center justify-between">


       <!-- Desktop Navigation -->
       <nav class="hidden md:flex space-x-8 flex-1 ">
           <a href="{{route('categories.index', 'women')}}" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">WOMEN</a>
           <a href="{{route('categories.index', 'men')}}" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">MEN</a>
                       <a href="{{ route('categories.all') }}" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">CATEGORIES</a>
            <a href="{{ route('collections.index') }}" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">COLLECTIONS</a>
       </nav>
           <!-- Logo -->
       <div class="flex items-center flex-1 justify-center">

           <a href="{{ route('home') }}" class="text-2xl font-bold"> <img src="/imgs/workfit.png" alt="logo" class="w-20"></a>
       </div>

       <!-- Icons -->
       <div class="flex items-center flex-1 space-x-4 justify-end">
           <a href="#" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">Location</a>

           <!-- Currency Selector -->
           @include('components.currency-selector')

           <a href="/login" class="font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900' }}">Account</a>

            <!-- Cart and Wishlist Counts -->
            @livewire('cart-wishlist-counts')

           <!-- Mobile Menu Button -->
           <button class="md:hidden" onclick="toggleMobileMenu()">
               <i class="fas fa-bars text-xl"></i>
           </button>
       </div>
   </div>
</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="md:hidden bg-transparent hover:bg-white py-4 px-4 shadow-lg hidden">
   <nav class="flex flex-col space-y-4">
       <a href="#" class="font-medium hover:text-red-600 transition-colors">NEW ARRIVALS</a>
       <a href="#" class="font-medium hover:text-red-600 transition-colors">WOMEN</a>
       <a href="#" class="font-medium hover:text-red-600 transition-colors">MEN</a>
       <a href="{{ route('categories.all') }}" class="font-medium hover:text-red-600 transition-colors">CATEGORIES</a>
       <a href="{{ route('collections.index') }}" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>

   </nav>
</div>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}
</script>
</header>
