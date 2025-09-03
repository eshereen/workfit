   <!-- Header -->
   <?php if(request()->routeIs('home')): ?>
 <header class="relative max-h-28  z-[1100] transition-all duration-300 py-3 mb-10 bg-transparent hover:bg-white  text-white font-semibold hover:text-gray-900 group">
    <?php else: ?>
    <header class="fixed top-0 left-0 right-0 z-[1100] max-h-28   transition-all duration-300 py-3 mb-10 bg-white hover:bg-white  text-gray-900 font-semibold group">
    <?php endif; ?>
<div class="container mx-auto px-4">
   <div class="flex items-center justify-between">


       <!-- Desktop Navigation -->
       <nav class="hidden md:flex space-x-8 flex-1 ">
           <a href="<?php echo e(route('categories.index', 'women')); ?>" class="font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">WOMEN</a>
           <a href="<?php echo e(route('categories.index', 'men')); ?>" class="font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">MEN</a>
                       <a href="<?php echo e(route('categories.all')); ?>" class="font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">CATEGORIES</a>
            <a href="<?php echo e(route('collections.index')); ?>" class="font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">COLLECTIONS</a>
       </nav>
           <!-- Logo -->
     <?php if(request()->routeIs('home')): ?>
            <div class="flex items-center flex-1 justify-center">

       <img src="/imgs/workfit_logo_white.png" alt="logo" class="w-20 block group-hover:hidden">
                <!-- Black logo (only visible on hover) -->
                <img src="/imgs/workfit_logo_black.png" alt="logo" class="w-20 hidden group-hover:block">
                </div>
       <?php else: ?>
       <div class="flex items-center flex-1 justify-center">

           <a href="<?php echo e(route('home')); ?>" class="text-2xl font-bold"> <img src="/imgs/workfit_logo_black.png" alt="logo" class="w-20"></a>
       </div>
       <?php endif; ?>

       <!-- Icons -->
       <div class="flex items-center flex-1 space-x-4 justify-end relative z-[1001]">
           <a href="<?php echo e(route('location')); ?>" class=" hidden lg:block font-xs uppercase hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">Location</a>

           <!-- Currency Selector -->
           <?php echo $__env->make('components.currency-selector', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

           <a href="<?php echo e(route('login')); ?>" class=" hidden lg:block font-xs hover:text-red-600 uppercase transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-900'); ?>">Account</a>

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
               <i class="fas fa-bars text-xl"></i>
           </button>
       </div>
   </div>
</div>

</header>

<!-- Mobile Menu (moved outside header) -->
<div id="mobileMenu" class="md:hidden fixed top-16 left-0 right-0 z-[1090] bg-white py-4 px-4 shadow-lg hidden">
   <nav class="flex flex-col space-y-4">
    <a href="<?php echo e(route('home')); ?>" class="font-medium hover:text-red-600 transition-colors uppercase">Home</a>
       <a href="<?php echo e(route('categories.index', 'women')); ?>" class="font-medium hover:text-red-600 transition-colors uppercase">Women</a>

       <a href="<?php echo e(route('categories.index', 'men')); ?>" class="font-medium hover:text-red-600 transition-colors">MEN</a>
       <a href="<?php echo e(route('categories.all')); ?>" class="font-medium hover:text-red-600 transition-colors">CATEGORIES</a>
       <a href="<?php echo e(route('collections.index')); ?>" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>
            <a href="<?php echo e(route('location')); ?>" class="font-medium hover:text-red-600 transition-colors uppercase<?php echo e(request()->routeIs('home') ? 'text-white uppercase group-hover:text-gray-900' : 'text-gray-900'); ?>">Location</a>
              <a href="<?php echo e(route('login')); ?>" class="font-medium hover:text-red-600 transition-colors uppercase">Account</a>

   </nav>
</div>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/layouts/navbar.blade.php ENDPATH**/ ?>