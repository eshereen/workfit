<div>
    <h2 class="text-xl font-semibold text-gray-900 my-20">Order Summary</h2>

    <!--[if BLOCK]><![endif]--><?php if(config('app.debug')): ?>
        <div class="text-xs text-gray-500 mb-2">
            Currency: <?php echo e($currencyCode); ?> (<?php echo e($currencySymbol); ?>) | Items: <?php echo e(count($cartItems)); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(empty($cartItems)): ?>
        <div class="text-center py-8">
            <p class="text-gray-500">Your cart is empty</p>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
            <div class="flex items-center">
                <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['image'])): ?>
                    <img src="<?php echo e($item['attributes']['image']); ?>"
                         alt="<?php echo e($item['name']); ?>"
                         class="w-12 h-12 object-cover rounded mr-3">
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div>
                    <h3 class="font-medium text-gray-900"><?php echo e($item['name']); ?></h3>
                    <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['size']) || isset($item['attributes']['color'])): ?>
                        <p class="text-sm text-gray-600">
                            <?php if(isset($item['attributes']['size'])): ?><?php echo e($item['attributes']['size']); ?><?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php if(isset($item['attributes']['size']) && isset($item['attributes']['color'])): ?>, <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if(isset($item['attributes']['color'])): ?><?php echo e($item['attributes']['color']); ?><?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
            <div class="text-right">
                <p class="text-gray-900">Qty: <?php echo e($item['quantity']); ?></p>
                <p class="font-medium text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($item['converted_price'], 2)); ?></p>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Order Totals -->
        <div class="mt-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($subtotal, 2)); ?></span>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($taxAmount > 0): ?>
            <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span class="text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($taxAmount, 2)); ?></span>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($shippingAmount > 0): ?>
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping:</span>
                <span class="text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($shippingAmount, 2)); ?></span>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($loyaltyDiscount > 0): ?>
            <div class="flex justify-between text-green-600">
                <span>Loyalty Points Discount:</span>
                <span>-<?php echo e($currencySymbol); ?><?php echo e(number_format($loyaltyDiscount, 2)); ?></span>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                <!--[if BLOCK]><![endif]--><?php if($loyaltyDiscount > 0): ?>
                    <span class="text-gray-900">Final Total:</span>
                    <span class="text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($finalTotal, 2)); ?></span>
                <?php else: ?>
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($total, 2)); ?></span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($loyaltyDiscount > 0): ?>
            <div class="text-sm text-gray-500 text-center">
                <p>You saved <?php echo e($currencySymbol); ?><?php echo e(number_format($loyaltyDiscount, 2)); ?> with loyalty points!</p>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<!-- OrderSummary now uses pure Livewire events - no custom JavaScript needed -->
<!--[if BLOCK]><![endif]--><?php if(config('app.debug')): ?>
<script>
console.log('💰 OrderSummary: Using pure Livewire events (Alpine.js approach)');

// Simple test function for debugging
window.testOrderSummaryEvent = function() {
    console.log('🧪 Testing OrderSummary refresh...');
    if (window.Livewire) {
        const orderSummary = document.querySelector('[wire\\:id*="order-summary"]');
        if (orderSummary) {
            const wireId = orderSummary.getAttribute('wire:id');
            const component = window.Livewire.find(wireId);
            if (component) {
                component.$refresh();
                console.log('✅ OrderSummary refreshed manually');
            }
        }
    }
};
</script>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/order-summary.blade.php ENDPATH**/ ?>