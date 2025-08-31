<div class="bg-white rounded-lg shadow-md p-6 w-2/3">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Loyalty Points</h3>
        <div class="text-2xl font-bold text-blue-600">
            <?php echo e(number_format($loyaltyBalance)); ?> pts
        </div>
    </div>

    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>How it works:</strong> Earn 1 point for every $1 spent. Redeem 100 points for $1 off your next purchase.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($loyaltyBalance >= 100): ?>
        <div class="border-t pt-6">
            <button
                wire:click="toggleRedeemForm"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
            >
                <?php echo e($showRedeemForm ? 'Cancel Redemption' : 'Redeem Points'); ?>

            </button>

            <!--[if BLOCK]><![endif]--><?php if($showRedeemForm): ?>
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <form wire:submit.prevent="redeemPoints">
                        <div class="mb-4">
                            <label for="pointsToRedeem" class="block text-sm font-medium text-gray-700 mb-2">
                                Points to Redeem (Minimum: 100)
                            </label>
                            <input
                                type="number"
                                id="pointsToRedeem"
                                wire:model="pointsToRedeem"
                                min="100"
                                step="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pointsToRedeem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($redemptionValue > 0): ?>
                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-700">
                                    <strong>Redemption Value:</strong> $<?php echo e(number_format($redemptionValue, 2)); ?>

                                </p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
                        >
                            Redeem Points
                        </button>
                    </form>

                    <!--[if BLOCK]><![endif]--><?php if($redemptionMessage): ?>
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700"><?php echo e($redemptionMessage); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($redemptionError): ?>
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-700"><?php echo e($redemptionError); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php else: ?>
        <div class="border-t pt-6">
            <div class="text-center py-4">
                <p class="text-gray-500">You need at least 100 points to start redeeming.</p>
                <p class="text-sm text-gray-400 mt-1">Keep shopping to earn more points!</p>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($loyaltyBalance > 0): ?>
        <div class="border-t pt-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Transactions</h4>
            <div class="space-y-2">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = Auth::user()->loyaltyTransactions()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600"><?php echo e($transaction->description); ?></span>
                        <span class="font-medium <?php echo e($transaction->points > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                            <?php echo e($transaction->points > 0 ? '+' : ''); ?><?php echo e($transaction->points); ?> pts
                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/loyalty-points.blade.php ENDPATH**/ ?>