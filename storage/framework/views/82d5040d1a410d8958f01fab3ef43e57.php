<div>
    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Customer Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" wire:model.live="firstName" id="first_name" name="customer[first_name]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['firstName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                <input type="text" wire:model.live="lastName" id="last_name" name="customer[last_name]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['lastName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input type="email" wire:model.live="email" id="email" name="customer[email]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                <input type="tel" wire:model.live="phoneNumber" id="phone_number" name="customer[phone_number]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['phoneNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <!-- Billing Address -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Address</h2>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="useBillingForShipping" class="mr-2 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <span class="text-sm text-gray-700">Use billing address for shipping</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                <select wire:model.live="billingCountry" id="billing_country" name="billing_address[country]" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">Select a country</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['billingCountry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="md:col-span-2">
                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                <input type="text" wire:model.live="billingAddress" id="billing_address" name="billing_address[address]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['billingAddress'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                <input type="text" wire:model.live="billingState" id="billing_state" name="billing_address[state]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['billingState'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div>
                <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                <input type="text" wire:model.live="billingCity" id="billing_city" name="billing_address[city]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['billingCity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <div class="md:col-span-2">
                <label for="billing_building_number" class="block text-sm font-medium text-gray-700 mb-1">Building Number (Optional)</label>
                <input type="text" wire:model.live="billingBuildingNumber" id="billing_building_number" name="billing_address[building_number]"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['billingBuildingNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <!--[if BLOCK]><![endif]--><?php if(!$useBillingForShipping): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Address</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                    <select wire:model.live="shippingCountry" id="shipping_country" name="shipping_address[country]" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Select a country</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shippingCountry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="md:col-span-2">
                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                    <input type="text" wire:model.live="shippingAddress" id="shipping_address" name="shipping_address[address]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shippingAddress'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                    <input type="text" wire:model.live="shippingState" id="shipping_state" name="shipping_address[state]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shippingState'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                    <input type="text" wire:model.live="shippingCity" id="shipping_city" name="shipping_address[city]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shippingCity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="md:col-span-2">
                    <label for="shipping_building_number" class="block text-sm font-medium text-gray-700 mb-1">Building Number (Optional)</label>
                    <input type="text" wire:model.live="shippingBuildingNumber" id="shipping_building_number" name="shipping_address[building_number]"
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shippingBuildingNumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Payment Methods Selector -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('payment-methods-selector');

$__html = app('livewire')->mount($__name, $__params, 'lw-399471460-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>


        <!-- Submit Button -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <button type="button"
                wire:click="submitForm"
                wire:loading.attr="disabled"
                wire:target="submitForm"
                class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="submitForm">
                <?php echo e(Auth::check() ? 'Place Order' : 'Place Order as Guest'); ?>

            </span>
            <span wire:loading wire:target="submitForm" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </div>

    <!-- Loading and Error Messages -->
    <div wire:loading wire:target="submitForm" class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing your order...
        </div>
    </div>



    <!-- Hidden Form for Submission -->
    <form id="checkout-form" method="POST" action="<?php echo e(route('checkout.process')); ?>" style="display: none;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="first_name" value="<?php echo e($firstName); ?>">
        <input type="hidden" name="last_name" value="<?php echo e($lastName); ?>">
        <input type="hidden" name="email" value="<?php echo e($email); ?>">
        <input type="hidden" name="phone_number" value="<?php echo e($phoneNumber); ?>">
        <input type="hidden" name="billing_country_id" value="<?php echo e($billingCountry); ?>">
        <input type="hidden" name="billing_state" value="<?php echo e($billingState); ?>">
        <input type="hidden" name="billing_city" value="<?php echo e($billingCity); ?>">
        <input type="hidden" name="billing_address" value="<?php echo e($billingAddress); ?>">
        <input type="hidden" name="billing_building_number" value="<?php echo e($billingBuildingNumber ?: 'N/A'); ?>">
        <input type="hidden" name="shipping_country_id" value="<?php echo e($useBillingForShipping ? $billingCountry : ($shippingCountry ?: $billingCountry)); ?>">
        <input type="hidden" name="shipping_state" value="<?php echo e($useBillingForShipping ? $billingState : ($shippingState ?: $billingState)); ?>">
        <input type="hidden" name="shipping_city" value="<?php echo e($useBillingForShipping ? $billingCity : ($shippingCity ?: $billingCity)); ?>">
        <input type="hidden" name="shipping_address" value="<?php echo e($useBillingForShipping ? $billingAddress : ($shippingAddress ?: $billingAddress)); ?>">
        <input type="hidden" name="shipping_building_number" value="<?php echo e($useBillingForShipping ? ($billingBuildingNumber ?: 'N/A') : ($shippingBuildingNumber ?: 'N/A')); ?>">
        <input type="hidden" name="use_billing_for_shipping" value="<?php echo e($useBillingForShipping ? '1' : '0'); ?>">
        <input type="hidden" name="payment_method" value="<?php echo e($selectedPaymentMethod); ?>">
                        <input type="hidden" name="paypal_payment_type" value="credit_card">
        <input type="hidden" name="currency" value="<?php echo e($currentCurrency); ?>">
    </form>

</div>

<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/checkout-form.blade.php ENDPATH**/ ?>