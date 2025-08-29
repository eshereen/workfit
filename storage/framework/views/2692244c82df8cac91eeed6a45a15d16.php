<div class="flex items-center space-x-4">
    <!-- Wishlist Link -->
    <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
    <a href="<?php echo e(route('wishlist.index')); ?>" class="relative font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-400'); ?>">
        <i class="fas fa-heart text-xl"></i>
        <!--[if BLOCK]><![endif]--><?php if($wishlistCount > 0): ?>
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <?php echo e($wishlistCount); ?>

        </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </a>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Cart Link -->
    <a href="<?php echo e(route('cart.index')); ?>" class="relative font-xs hover:text-red-600 transition-colors <?php echo e(request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-800'); ?>">
        <i class="fas fa-shopping-bag text-xl"></i>
        <!--[if BLOCK]><![endif]--><?php if($cartCount > 0): ?>
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            <?php echo e($cartCount); ?>

        </span>
        <?php else: ?>
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
            0
        </span>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </a>
</div>
<?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/livewire/cart-wishlist-counts.blade.php ENDPATH**/ ?>