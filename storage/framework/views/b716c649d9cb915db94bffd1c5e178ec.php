   <!-- Header -->
   <?php if(request()->routeIs('home')): ?>
   <?php if($sale): ?>
 <div class="relative z-[1100] bg-red-600 text-white py-3 px-4 h-12 w-full text-center transition-all duration-300">
 <p> <span ><?php echo e($sale->description); ?></span> <a href="<?php echo e(route('collection.show', $sale->slug)); ?>" class="px-2 text-gray-800 font-bold underline hover:text-white">Shop Now</a></p>
    </div>
    <?php endif; ?>
    <?php endif; ?>
 </div>
 <header
 x-data="{
   scrolled: false,
   isHome: <?php echo e(request()->routeIs('home') ? 'true' : 'false'); ?>,
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
          <a href="<?php echo e(route('categories.index', 'women')); ?>" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">WOMEN</a>
          <a href="<?php echo e(route('categories.index', 'men')); ?>" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">MEN</a>
          <a href="<?php echo e(route('categories.all')); ?>" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">CATEGORIES</a>
          <a href="<?php echo e(route('collections.index')); ?>" class="font-xs hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">COLLECTIONS</a>
      </nav>

          <!-- Logo -->
      <div class="flex items-center flex-1 justify-center">
          <!-- White logo (home page, not scrolled) -->
          <img x-show="isHome && !scrolled" src="/imgs/workfit_logo_white.png" alt="logo" class="w-20">
          <!-- Black logo (home page scrolled or non-home page) -->
          <img x-show="!isHome || (isHome && scrolled)" src="/imgs/workfit_logo_black.png" alt="logo" class="w-20">
      </div>

      <!-- Icons -->
      <div class="flex items-center flex-1 space-x-4 justify-end relative z-[1001]">
          <a href="<?php echo e(route('location')); ?>" class="hidden lg:block font-xs uppercase hover:text-red-600 transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Location</a>

          <!-- Currency Selector -->
          <!-- (your currency selector code here) -->

          <a href="<?php echo e(route('login')); ?>" class="hidden lg:block font-xs hover:text-red-600 uppercase transition-colors" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">Account</a>

           <!-- Cart and Wishlist Counts -->
           <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cart-wishlist-counts');

$__html = app('livewire')->mount($__name, $__params, 'lw-3488617085-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

          <!-- Mobile Menu Button -->
          <button id="mobile-menu-button" class="md:hidden" type="button" aria-controls="mobileMenu" aria-expanded="false" onclick="toggleMobileMenu()">
              <i class="fas fa-bars text-xl" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'"></i>
          </button>
      </div>
  </div>
</div>
</header>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/layouts/navbar.blade.php ENDPATH**/ ?>