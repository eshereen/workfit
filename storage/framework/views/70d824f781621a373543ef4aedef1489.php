<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-12 my-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmation</h1>
                <p class="text-lg text-gray-600">Order #<?php echo e($order->order_number); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between py-4 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <?php if($item->product): ?>
                                    <img src="<?php echo e($item->product->getFirstMediaUrl('main_image', 'thumb')); ?>"
                                         alt="<?php echo e($item->product->name); ?>"
                                         class="w-16 h-16 object-cover rounded mr-4">
                                <?php endif; ?>
                                <div>
                                    <h3 class="font-medium text-gray-900"><?php echo e($item->product->name ?? 'Product'); ?></h3>
                                    <?php if($item->variant): ?>
                                        <p class="text-sm text-gray-600"><?php echo e($item->variant->color); ?>, <?php echo e($item->variant->size); ?></p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-500">SKU: <?php echo e($item->product->sku ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-900">Qty: <?php echo e($item->quantity); ?></p>
                                <p class="font-medium text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($item->price, 2)); ?></p>
                                <p class="text-sm text-gray-600">Total: <?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($item->price * $item->quantity, 2)); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Order Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Number:</span>
                                        <span class="text-gray-900"><?php echo e($order->order_number); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Order Date:</span>
                                        <span class="text-gray-900"><?php echo e($order->created_at->format('M d, Y \a\t g:i A')); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <?php echo e($order->getOrderStatusLabel()); ?>

                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Payment Status:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <?php echo e($order->getPaymentStatusLabel()); ?>

                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Payment Method:</span>
                                        <span class="text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $order->payment_method))); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Customer Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="text-gray-900"><?php echo e($order->first_name); ?> <?php echo e($order->last_name); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="text-gray-900"><?php echo e($order->email); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="text-gray-900"><?php echo e($order->phone_number); ?></span>
                                    </div>
                                    <?php if($order->notes): ?>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Notes:</span>
                                        <span class="text-gray-900"><?php echo e($order->notes); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Addresses</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Shipping Address</h3>
                                <?php
                                    $shipping = $order->shipping_address;
                                ?>
                                <div class="text-sm text-gray-600">
                                    <p><?php echo e($shipping['name'] ?? $order->first_name . ' ' . $order->last_name); ?></p>
                                    <p><?php echo e($shipping['address'] ?? ''); ?></p>
                                    <p><?php echo e($shipping['city'] ?? $order->city); ?>, <?php echo e($shipping['state'] ?? $order->state); ?></p>
                                    <p><?php echo e($shipping['postal_code'] ?? ''); ?></p>
                                    <p><?php echo e($shipping['country'] ?? ''); ?></p>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Billing Address</h3>
                                <?php
                                    $billing = $order->billing_address;
                                ?>
                                <div class="text-sm text-gray-600">
                                    <p><?php echo e($billing['name'] ?? $order->first_name . ' ' . $order->last_name); ?></p>
                                    <p><?php echo e($billing['address'] ?? ''); ?></p>
                                    <p><?php echo e($billing['city'] ?? $order->city); ?>, <?php echo e($billing['state'] ?? $order->state); ?></p>
                                    <p><?php echo e($billing['postal_code'] ?? ''); ?></p>
                                    <p><?php echo e($billing['country'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Totals</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->subtotal, 2)); ?></span>
                            </div>

                            <?php if($order->tax_amount > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->tax_amount, 2)); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if($order->shipping_amount > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->shipping_amount, 2)); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if($order->discount_amount > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-gray-900">-<?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->discount_amount, 2)); ?></span>
                            </div>
                            <?php endif; ?>

                            <hr class="border-gray-200">

                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900"><?php echo e($currencyInfo['currency_symbol'] ?? '$'); ?><?php echo e(number_format($order->total_amount, 2)); ?></span>
                            </div>
                        </div>

                        <?php if($currencyInfo['currency_code'] !== 'USD'): ?>
                        <div class="mt-4 text-sm text-gray-500 text-center p-2 bg-blue-50 rounded">
                            <?php if($currencyInfo['is_auto_detected']): ?>
                                Prices automatically converted to <?php echo e($currencyInfo['currency_code']); ?> (<?php echo e($currencyInfo['currency_symbol']); ?>) based on your location
                            <?php else: ?>
                                Prices converted to <?php echo e($currencyInfo['currency_code']); ?> (<?php echo e($currencyInfo['currency_symbol']); ?>)
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-3">
                            <a href="<?php echo e(route('products.index')); ?>"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                                Continue Shopping
                            </a>

                            <?php if($order->is_guest): ?>
                                <a href="<?php echo e(route('thankyou', $order)); ?>"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Back to Thank You
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/checkout/confirmation.blade.php ENDPATH**/ ?>