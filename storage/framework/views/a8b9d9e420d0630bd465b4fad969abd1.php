
<div class="max-w-md">
    <!--[if BLOCK]><![endif]--><?php if($submitted): ?>
        <div id="newsletter-success" class="rounded-md bg-green-50 p-3 text-sm text-green-700 animate-fade-in">
            Thanks! Please check your email to confirm your subscription.
        </div>
    <?php else: ?>
        <form wire:submit.prevent="submit" class="space-y-3">
            
            <input type="text" wire:model="bot_field" class="hidden" tabindex="-1" autocomplete="off" />

            <div class="flex gap-2">
                <input type="email" wire:model.defer="email"
                       class="flex-grow px-4 py-2 border border-gray-300 focus:outline-none focus:border-red-600 text-gray-900"
                       placeholder="Enter your email"
                       required>

                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </form>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide newsletter success message after 3 seconds
    const successMessage = document.getElementById('newsletter-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease-out';
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.remove();
            }, 500);
        }, 3000);
    }
});

// Listen for Livewire events to handle dynamic content
document.addEventListener('livewire:init', () => {
    Livewire.on('newsletter-subscribed', () => {
        // Auto-hide success message after 3 seconds when triggered by Livewire
        setTimeout(() => {
            const successMessage = document.getElementById('newsletter-success');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(() => {
                    successMessage.remove();
                }, 500);
            }
        }, 3000);
    });
});
</script>

<?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/livewire/newsletter/subscribe-form.blade.php ENDPATH**/ ?>