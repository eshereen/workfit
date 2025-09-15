
<div x-data="{ open: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?> }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] overflow-y-auto"
     style="display: none;"
     @keydown.escape.window="$wire.closeModal()"
     <?php if($showModal): ?> x-init="setTimeout(() => open && document.body.style.overflow = 'hidden', 100)" <?php endif; ?>>

    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
         @click="$wire.closeModal()"></div>

    <!-- Modal container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-2xl max-w-4xl w-full mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop>

            <!-- Close button -->
            <button wire:click="closeModal"
                    class="absolute top-4 right-4 z-10 text-gray-500 hover:text-gray-700 bg-white rounded-full p-2 shadow-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 min-h-[500px]">
                <!-- Left section - Registration CTA -->
                <div class="p-8 md:p-12 flex flex-col justify-center bg-gray-50">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            Join WorkFit & Get <span class="text-red-600">10% OFF</span>
                        </h2>
                        <p class="text-lg text-gray-600 mb-8">
                            Create an account and unlock exclusive benefits, member-only discounts, and early access to new collections!
                        </p>

                        <!-- Benefits List -->
                        <div class="space-y-3 mb-8 text-left">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">10% off your first order</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">Exclusive member discounts</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">Early access to new collections</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">Fast & easy checkout</span>
                            </div>
                        </div>

                        <!-- Register Button -->
                        <button wire:click="redirectToRegister"
                                class="w-full bg-red-600 text-white py-4 px-6 rounded-lg hover:bg-red-700 transition-colors font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                           GET 10% OFF
                        </button>

                        <p class="text-xs text-gray-500 mt-6 text-center">
                            Join thousands of satisfied customers who love WorkFit's premium activewear.
                        </p>
                    </div>
                </div>

                <!-- Right section - Image -->
                <div class="relative h-64 md:h-auto">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=600&auto=format&fit=crop"
                         alt="WorkFit Activewear Collection"
                         class="w-full h-full object-cover">

                    <!-- Image overlay with branding -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent">
                        <div class="absolute bottom-6 left-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">WorkFit</h3>
                            <p class="text-sm opacity-90">Premium Activewear</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Reset body overflow when modal closes
document.addEventListener('livewire:updated', () => {
    if (!window.Livewire.find('<?php echo e($_instance->getId()); ?>').showModal) {
        document.body.style.overflow = '';
    }
});
</script>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/promo-modal.blade.php ENDPATH**/ ?>