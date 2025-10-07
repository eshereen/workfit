<x-filament-panels::page>
    <form wire:submit="save">
        <div class="grid gap-6">
            {{-- Profile Information Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    Profile Information
                </x-slot>

                <x-slot name="description">
                    Update your account's profile information and email address.
                </x-slot>

                <div class="space-y-6">
                    {{-- Name Field --}}
                    <div>
                        <label for="name" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Full Name
                                <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                            </span>
                        </label>

                        <input
                            type="text"
                            wire:model="name"
                            id="name"
                            required
                            maxlength="255"
                            class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white rounded-lg shadow-sm sm:text-sm mt-2"
                        />

                        @error('name')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email Field --}}
                    <div>
                        <label for="email" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Email Address
                                <sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
                            </span>
                        </label>

                        <input
                            type="email"
                            wire:model="email"
                            id="email"
                            required
                            class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white rounded-lg shadow-sm sm:text-sm mt-2"
                        />

                        @error('email')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-filament::section>

            {{-- Update Password Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    Update Password
                </x-slot>

                <x-slot name="description">
                    Ensure your account is using a long, random password to stay secure. Leave blank to keep your current password.
                </x-slot>

                <div class="space-y-6">
                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Current Password
                            </span>
                        </label>

                        <input
                            type="password"
                            wire:model="current_password"
                            id="current_password"
                            autocomplete="current-password"
                            class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white rounded-lg shadow-sm sm:text-sm mt-2"
                        />

                        @error('current_password')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                New Password
                            </span>
                        </label>

                        <input
                            type="password"
                            wire:model="password"
                            id="password"
                            autocomplete="new-password"
                            class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white rounded-lg shadow-sm sm:text-sm mt-2"
                        />

                        @error('password')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Confirm New Password
                            </span>
                        </label>

                        <input
                            type="password"
                            wire:model="password_confirmation"
                            id="password_confirmation"
                            autocomplete="new-password"
                            class="fi-input block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white rounded-lg shadow-sm sm:text-sm mt-2"
                        />

                        @error('password_confirmation')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-filament::section>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-filament::button
                    type="submit"
                    size="lg"
                >
                    <svg wire:loading wire:target="save" class="animate-spin -ms-1 me-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span wire:loading.remove wire:target="save">
                        Save Changes
                    </span>

                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
