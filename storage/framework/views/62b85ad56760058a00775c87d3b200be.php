<div>
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

    <div class="space-y-4">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
                <input type="radio"
                       name="payment_method_selector"
                       value="<?php echo e($method); ?>"
                       wire:model.live="selectedMethod"
                       class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
                <div class="flex-1">
                    <div class="flex items-center">
                        <!--[if BLOCK]><![endif]--><?php if($method === 'paypal'): ?>
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.067 8.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478zM20.067 12.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478z"/>
                            </svg>
                        <?php elseif($method === 'paymob'): ?>
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>

                        <?php elseif($method === 'cash_on_delivery'): ?>
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <span class="font-medium text-gray-900"><?php echo e(ucfirst(str_replace('_',' ', $method))); ?></span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        <!--[if BLOCK]><![endif]--><?php if($method === 'paypal'): ?>
                            Pay with your PayPal account
                        <?php elseif($method === 'paymob'): ?>
                            Local payment solution for Egypt and MENA region

                        <?php elseif($method === 'cash_on_delivery'): ?>
                            Pay with cash when your order is delivered
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </p>

                    <!--[if BLOCK]><![endif]--><?php if($method === 'paypal'): ?>
                        <div class="mt-3">
                            <div class="text-xs text-gray-500">
                                <p>• Pay with PayPal account or credit/debit card</p>
                                <p>• Secure payment processing by PayPal</p>
                                <p>• Choose payment method on PayPal's secure page</p>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-500 text-sm mt-2"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Hidden input for PayPal payment type - REMOVED to prevent form submission issues -->
    <!-- <input type="hidden" name="paypal_payment_type" value="<?php echo e($paypalPaymentType); ?>"> -->

    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-gray-600">
                Your payment information is secure and encrypted. We never store your payment details.
            </p>
        </div>
    </div>

</div>

<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/payment-methods-selector.blade.php ENDPATH**/ ?>