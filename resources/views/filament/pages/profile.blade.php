<x-filament-panels::page>
    <form wire:submit="save">
        <div class="space-y-8">
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
                            style="display: block; width: 100%; border: 2px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; margin-top: 0.5rem;"
                            class="fi-input focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm transition-colors"
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
                            style="display: block; width: 100%; border: 2px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; margin-top: 0.5rem;"
                            class="fi-input focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm transition-colors"
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
                            style="display: block; width: 100%; border: 2px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; margin-top: 0.5rem;"
                            class="fi-input focus:border-primary-600 focus:ring-2 focus:ring-primary-500 shadow-sm transition-colors"
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
                            style="display: block; width: 100%; border: 2px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; margin-top: 0.5rem;"
                            class="fi-input focus:border-primary-600 focus:ring-2 focus:ring-primary-500 shadow-sm transition-colors"
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
                            style="display: block; width: 100%; border: 2px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; margin-top: 0.5rem;"
                            class="fi-input focus:border-primary-600 focus:ring-2 focus:ring-primary-500 shadow-sm transition-colors"
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
            <div class="flex items-center justify-end gap-x-3" style="margin-top: 2rem;">
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

    <style>
        /* Force spacing between sections */
        .space-y-8 > * + * {
            margin-top: 2rem !important;
        }

        /* Ensure password fields show dots - multiple methods for browser compatibility */
        input[type="password"] {
            font-family: text-security-disc, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            -webkit-text-security: disc !important;
        }

        /* Additional CSS to force borders on all inputs */
        input.fi-input {
            border: 2px solid #d1d5db !important;
        }

        /* Dark mode border - lighter/more visible */
        @media (prefers-color-scheme: dark) {
            input.fi-input {
                border: 2px solid #4b5563 !important;
            }
        }

        /* Make sure icons are visible */
        button svg {
            pointer-events: none;
        }
    </style>
</x-filament-panels::page>
