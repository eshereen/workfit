   <!-- Header -->
 <header class="relative max-h-28  z-50 transition-all duration-300 py-3 mb-10 bg-transparent hover:bg-white  text-white font-semibold hover:text-gray-900">
<div class="container mx-auto px-4">
   <div class="flex items-center justify-between">


       <!-- Desktop Navigation -->
       <nav class="hidden md:flex space-x-8 flex-1 ">
           <a href="#" class="font-xs hover:text-red-600 transition-colors">WOMEN</a>
           <a href="#" class="font-xs hover:text-red-600 transition-colors">MEN</a>
           <a href="#" class="font-xs hover:text-red-600 transition-colors">COLLECTIONS</a>
           <a href="#" class="font-xs hover:text-red-600 transition-colors">SALE</a>
       </nav>
           <!-- Logo -->
       <div class="flex items-center flex-1 justify-center">

           <a href="#" class="text-2xl font-bold"> <img src="/imgs/workfit.png" alt="logo" class="w-20"></a>
       </div>

       <!-- Icons -->
       <div class="flex items-center flex-1 space-x-4 justify-end">
           <a href="#" class="font-xs hover:text-red-600 transition-colors">Location</a>

           <!-- Currency Selector -->
           @include('components.currency-selector')

           <a href="#" class="font-xs hover:text-red-600 transition-colors">Account</a>

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
       <a href="#" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>
       <a href="#" class="font-medium hover:text-red-600 transition-colors">SALE</a>
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
