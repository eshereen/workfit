<div>

    <!-- Success Message -->
    <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <h1 class="text-3xl font-bold mb-8">Your Shopping Cart</h1>


    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="lg:w-2/3">
            <div class="overflow-x-auto">
                <!--[if BLOCK]><![endif]--><?php if($cartCount > 0): ?>
                    <table class="w-full">
                        <thead class="border-b border-gray-200">
                            <tr class="text-left text-sm text-gray-500">
                                <th class="pb-4 font-medium">Product</th>
                                <th class="pb-4 font-medium">Price</th>
                                <th class="pb-4 font-medium">Quantity</th>
                                <th class="pb-4 font-medium">Total</th>
                                <th class="pb-4 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-gray-100">
                                    <td class="py-6">
                                        <div class="flex items-center">
                                            <div class="w-20 h-20 bg-gray-50 rounded-md overflow-hidden mr-4">
                                                <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['image'])): ?>
                                                    <img src="<?php echo e($item['attributes']['image']); ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-gray-400">No Image</span>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <div>
                                                <h3 class="font-medium"><?php echo e($item['name']); ?></h3>
                                                <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['color']) && isset($item['attributes']['size'])): ?>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <?php if(isset($item['attributes']['color'])): ?>
                                                            <?php
                                                                $colorCode = $this->getColorCode($item['attributes']['color']);
                                                            ?>
                                                            <div class="flex items-center space-x-2">
                                                                <div class="w-4 h-4 rounded-full border border-gray-300"
                                                                     style="background-color: <?php echo e($colorCode); ?>;"
                                                                     title="<?php echo e($item['attributes']['color']); ?>"></div>
                                                                <span class="text-sm text-gray-500"><?php echo e($item['attributes']['color']); ?></span>
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['size'])): ?>
                                                            <span class="text-sm text-gray-500">/ <?php echo e($item['attributes']['size']); ?></span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6">

                                    </td>
                                    <td class="py-6">
                                        <div class="flex border rounded-md w-24">
                                            <button
                                                wire:click="decreaseQuantity('<?php echo e($item['rowId']); ?>')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 <?php echo e($item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                                                <?php echo e($item['quantity'] <= 1 ? 'disabled' : ''); ?>

                                                title="Decrease quantity"
                                            >-</button>
                                            <span class="px-2 py-1 border-x text-center">
                                                <span wire:loading.remove><?php echo e($item['quantity']); ?></span>
                                                <span wire:loading class="text-gray-400">...</span>
                                            </span>
                                            <button
                                                wire:click="increaseQuantity('<?php echo e($item['rowId']); ?>')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-50 cursor-not-allowed"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100"
                                                title="Increase quantity"
                                            >+</button>
                                        </div>

                                    </td>
                                    <td class="py-6">
                                        <!--[if BLOCK]><![endif]--><?php if(isset($item['converted_price'])): ?>
                                            <span class="text-green-600"><?php echo e($currencySymbol); ?><?php echo e(number_format($item['converted_price'] * $item['quantity'], 2)); ?></span>
                                            <span class="text-xs text-gray-500 block">Original: $<?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?></span>
                                        <?php else: ?>
                                            <span><?php echo e($currencySymbol); ?><?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="py-6">
                                        <button
                                            wire:click="removeItem('<?php echo e($item['rowId']); ?>')"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                            class="text-gray-400 hover:text-pink-500 transition-colors"
                                            title="Remove <?php echo e($item['name']); ?> from cart"
                                        >
                                            <span wire:loading.remove>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </span>
                                            <span wire:loading>
                                                <svg class="animate-spin h-5 w-5 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <h2 class="text-2xl font-semibold mb-4">Your cart is empty</h2>
                        <p class="mb-6">Looks like you haven't added any items to your cart yet.</p>
                        <a href="<?php echo e(route('home')); ?>"
                           class="bg-red-600 text-white py-2 px-6 rounded-lg inline-block hover:bg-red-700 transition">
                            Start Shopping
                        </a>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-xl font-bold mb-6">Order Summary</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>
                            <!--[if BLOCK]><![endif]--><?php if($currencyCode !== 'USD'): ?>
                                <span class="text-green-600"><?php echo e($currencySymbol); ?><?php echo e(number_format($subtotal, 2)); ?></span>
                                <span class="text-xs text-gray-500 block">Original: $<?php echo e(number_format(app(\App\Services\CartService::class)->getSubtotal(), 2)); ?></span>
                            <?php else: ?>
                                <?php echo e($currencySymbol); ?><?php echo e(number_format($subtotal, 2)); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span>
                            <!--[if BLOCK]><![endif]--><?php if($currencyCode !== 'USD'): ?>
                                <span class="text-green-600"><?php echo e($currencySymbol); ?><?php echo e(number_format($shipping, 2)); ?></span>
                                <span class="text-xs text-gray-500 block">Original: $<?php echo e(number_format(app(\App\Services\CartService::class)->getShippingCost(), 2)); ?></span>
                            <?php else: ?>
                                <?php echo e($currencySymbol); ?><?php echo e(number_format($shipping, 2)); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span>
                            <!--[if BLOCK]><![endif]--><?php if($currencyCode !== 'USD'): ?>
                                <span class="text-green-600"><?php echo e($currencySymbol); ?><?php echo e(number_format($tax, 2)); ?></span>
                                <span class="text-xs text-gray-500 block">Original: $<?php echo e(number_format(app(\App\Services\CartService::class)->getTaxAmount(), 2)); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </span>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between font-bold">
                        <span>Total</span>
                        <span>
                            <!--[if BLOCK]><![endif]--><?php if($currencyCode !== 'USD'): ?>
                                <span class="text-green-600"><?php echo e($currencySymbol); ?><?php echo e(number_format($total, 2)); ?></span>
                                <span class="text-xs text-gray-500 block">Original: $<?php echo e(number_format(app(\App\Services\CartService::class)->getTotal(), 2)); ?></span>
                            <?php else: ?>
                                <?php echo e($currencySymbol); ?><?php echo e(number_format($total, 2)); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </span>
                    </div>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($currencyCode !== 'USD'): ?>
                <div class="text-sm text-gray-500 text-center mb-4 p-2 bg-blue-50 rounded">
                    <!--[if BLOCK]><![endif]--><?php if($isAutoDetected): ?>
                        Prices automatically converted to <?php echo e($currencyCode); ?> (<?php echo e($currencySymbol); ?>) based on your location
                    <?php else: ?>
                        Prices converted to <?php echo e($currencyCode); ?> (<?php echo e($currencySymbol); ?>)
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="space-y-3">
                    <a href="<?php echo e(route('checkout')); ?>" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-md font-medium transition text-center block">
                        Proceed to Checkout
                    </a>
                    <a href="<?php echo e(route('home')); ?>" class="w-full border border-gray-300 hover:bg-gray-50 py-3 rounded-md font-medium transition text-center block">
                        Continue Shopping
                    </a>
                    <!--[if BLOCK]><![endif]--><?php if($cartCount > 0): ?>
                        <button
                            wire:click="clearCart"
                            class="w-full border border-red-300 text-red-600 hover:bg-red-50 py-3 rounded-md font-medium transition"
                        >
                            Clear Cart
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

</div>



<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/cart-index.blade.php ENDPATH**/ ?>