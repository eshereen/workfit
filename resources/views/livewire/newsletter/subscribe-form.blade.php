{{-- resources/views/livewire/newsletter/subscribe-form.blade.php --}}
<div class="max-w-md">
    @if($submitted)
        <div id="newsletter-success" class="rounded-md bg-green-50 p-3 text-sm text-green-700 animate-fade-in">
            Thanks! Please check your email to confirm your subscription.
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-3">
            {{-- Honeypot --}}
            <input type="text" wire:model="bot_field" class="hidden" tabindex="-1" autocomplete="off" />

            <div class="flex gap-2">
                <input type="email" wire:model.defer="email"
                       class="flex-grow px-4 py-2 border border-gray-300 focus:outline-none focus:border-red-600 text-gray-200"
                       placeholder="Enter your email"
                       required>

                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </form>
    @endif
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

