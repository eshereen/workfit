<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-40">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Thank You Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You for Your Order!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Your order has been successfully placed. We've sent a confirmation email to <strong><?php echo e($order->email); ?></strong>
            </p>

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium text-gray-900"><?php echo e($order->order_number); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Order Date:</span>
                        <span class="font-medium text-gray-900"><?php echo e($order->created_at->format('M d, Y')); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->total_amount, 2)); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $order->payment_method))); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <?php echo e(ucfirst($order->status)); ?>

                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Payment Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <?php echo e(ucfirst($order->payment_status)); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>
                


                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center">
                        <?php if($item->product): ?>
                            <img src="<?php echo e($item->product->getFirstMediaUrl('main_image', 'thumb')); ?>"
                                 alt="<?php echo e($item->product->name); ?>"
                                 class="w-12 h-12 object-cover rounded mr-3">
                        <?php endif; ?>
                        <div>
                            <h3 class="font-medium text-gray-900"><?php echo e($item->product->name ?? 'Product'); ?></h3>
                            <?php if($item->variant): ?>
                                <p class="text-sm text-gray-600"><?php echo e($item->variant->color); ?>, <?php echo e($item->variant->size); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-900">Qty: <?php echo e($item->quantity); ?></p>
                        <p class="font-medium text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($item->price, 2)); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('products.index')); ?>"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                    Continue Shopping
                </a>

                <?php if($order->is_guest): ?>
                    <a href="<?php echo e(route('checkout.confirmation', $order)); ?>?token=<?php echo e($order->guest_token); ?>"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        View Order Details
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('checkout.confirmation', $order)); ?>"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        View Order Details
                    </a>
                <?php endif; ?>
            </div>

            <?php if($currencyInfo['currency_code'] !== 'USD'): ?>
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="text-sm text-blue-800 text-center">
                    <?php if($currencyInfo['is_auto_detected']): ?>
                        Prices automatically converted to <?php echo e($currencyInfo['currency_code']); ?> (<?php echo e($currencyInfo['currency_symbol']); ?>) based on your location
                    <?php else: ?>
                        Prices converted to <?php echo e($currencyInfo['currency_code']); ?> (<?php echo e($currencyInfo['currency_symbol']); ?>)
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional Info -->
            <div class="mt-8 text-sm text-gray-500">
                <p>If you have any questions about your order, please contact our customer support.</p>
                <p class="mt-2">Email: support@workfit.com | Phone: +1 (555) 123-4567</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/checkout/thank-you.blade.php ENDPATH**/ ?>