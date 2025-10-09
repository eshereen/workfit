{{-- Promotional Modal --}}
<div x-data="{
        open: false,
        init() {
            // Check if user has seen modal in this session
            if (!sessionStorage.getItem('promo_modal_seen')) {
                // Show modal after 1 second delay
                setTimeout(() => {
                    this.open = true;
                    document.body.style.overflow = 'hidden';
                }, 2000);
            }
            this.$watch('open', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        },
        closeModal() {
            this.open = false;
            sessionStorage.setItem('promo_modal_seen', 'true');
        }
     }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] overflow-y-auto"
     style="display: none;"
     @keydown.escape.window="closeModal()">

    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"
         @click="closeModal()"></div>

    <!-- Modal container -->
    <div class="flex justify-center items-center p-4 min-h-screen">
        <div class="overflow-hidden relative mx-4 w-full max-w-4xl bg-white rounded-lg shadow-2xl"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop>

            <!-- Close button -->
            <button @click="closeModal()"
                    class="absolute top-4 right-4 z-10 p-2 text-gray-500 bg-white rounded-full shadow-lg transition-colors hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 min-h-[500px]">
                <!-- Left section - Registration CTA -->
                   <!-- Right section - Image -->
                   <div class="relative h-64 md:h-auto">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=600&auto=format&fit=crop"
                         alt="WorkFit Activewear Collection"
                         class="object-cover w-full h-full">

                    <!-- Image overlay with branding -->
                    <div class="absolute inset-0 bg-gradient-to-t to-transparent from-black/50">
                        <div class="absolute bottom-6 left-6 text-white">
                            <h3 class="mb-2 text-2xl font-bold">WorkFit</h3>
                            <p class="text-sm opacity-90">Premium Activewear</p>
                        </div>
                    </div>
                </div>
                <!-- Right section - Email Capture CTA -->
                <div class="flex flex-col justify-center p-8 bg-gray-50 md:p-12"
                     x-data="{ showSuccess: false, showError: false, message: '' }">
                    <div class="text-center md:text-left">
                        <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">
                            Get Your <span class="text-red-600">10% OFF</span> Coupon!
                        </h2>

                        <!-- Inline Success Notification -->
                        <div x-show="showSuccess"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="message" class="font-semibold"></span>
                            </div>
                        </div>

                        <!-- Inline Error Notification -->
                        <div x-show="showError"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="message" class="font-semibold"></span>
                            </div>
                        </div>

                        <!-- Email Form -->
                        <form wire:submit.prevent="submitEmail" class="space-y-4"
                              @submit="showSuccess = false; showError = false;">
                            <div>
                                <input type="email"
                                       wire:model.live="email"
                                       id="promo-email-input"
                                       placeholder="Enter your email address"
                                       class="px-3 py-2 w-full rounded-lg border border-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       required
                                       @show-promo-success.window="$el.value = ''"
                                       @show-promo-error.window="setTimeout(() => $el.select(), 100)">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="submitEmail"
                                    class="px-4 py-2 w-full text-lg font-semibold text-center text-white bg-red-600 rounded-lg shadow-lg transition-all transition-colors duration-200 transform hover:bg-red-700 hover:shadow-xl hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="submitEmail">GET 10% OFF COUPON</span>
                                <span wire:loading wire:target="submitEmail">
                                    <svg class="inline-block mr-2 -ml-1 w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </form>

                        <p class="mt-4 text-xs text-gray-500">
                            One coupon per email. Valid for 30 days.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
// Listen for promo coupon success
$wire.on('promoCouponSuccess', (data) => {
    const event = new CustomEvent('show-promo-success', {
        detail: { message: data[0]?.message || data.message || 'Coupon sent successfully!' }
    });
    window.dispatchEvent(event);
});

// Listen for promo coupon error
$wire.on('promoCouponError', (data) => {
    const event = new CustomEvent('show-promo-error', {
        detail: { message: data[0]?.message || data.message || 'An error occurred' }
    });
    window.dispatchEvent(event);
});

// Handle inline notifications
window.addEventListener('show-promo-success', (event) => {
    const alpineComponent = Alpine.$data(document.querySelector('[x-data*="showSuccess"]'));
    if (alpineComponent) {
        alpineComponent.message = event.detail.message;
        alpineComponent.showSuccess = true;
        alpineComponent.showError = false;

        // Auto hide after 5 seconds
        setTimeout(() => {
            alpineComponent.showSuccess = false;
        }, 5000);
    }
});

window.addEventListener('show-promo-error', (event) => {
    const alpineComponent = Alpine.$data(document.querySelector('[x-data*="showSuccess"]'));
    if (alpineComponent) {
        alpineComponent.message = event.detail.message;
        alpineComponent.showError = true;
        alpineComponent.showSuccess = false;

        // Auto hide after 5 seconds
        setTimeout(() => {
            alpineComponent.showError = false;
        }, 5000);
    }
});
</script>
@endscript
