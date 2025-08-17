{{-- resources/views/livewire/newsletter/subscribe-form.blade.php --}}
<div class="max-w-md">
    @if($submitted)
        <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">
            Thanks! Please check your email to confirm your subscription.
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-3">
            {{-- Honeypot --}}
            <input type="text" wire:model="bot_field" class="hidden" tabindex="-1" autocomplete="off" />

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.defer="email"
                       class="mt-1 block w-full rounded border-gray-300 focus:border-red-500 focus:ring-red-500"
                       placeholder="you@example.com">
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                    class="inline-flex items-center rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                Subscribe
            </button>
        </form>
    @endif
</div>

