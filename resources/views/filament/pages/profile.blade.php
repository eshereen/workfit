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
                            class="fi-input block w-full border-2 border-gray-300 focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm px-3 py-2 sm:text-sm mt-2 transition-colors"
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
                            class="fi-input block w-full border-2 border-gray-300 focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm px-3 py-2 sm:text-sm mt-2 transition-colors"
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

                <div class="space-y-6 ">
                    {{-- Current Password --}}
                    <div x-data="{ showCurrentPassword: false }">
                        <label for="current_password" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Current Password
                            </span>
                        </label>

                        <div class="relative mt-2">
                            <input
                                :type="showCurrentPassword ? 'text' : 'password'"
                                wire:model="current_password"
                                id="current_password"
                                autocomplete="current-password"
                                class="fi-input block w-full border-2 border-gray-300 focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm px-3 py-2 pr-10 sm:text-sm transition-colors"
                            />

                            <button
                                type="button"
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <svg x-show="!showCurrentPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showCurrentPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        @error('current_password')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div x-data="{ showNewPassword: false }">
                        <label for="password" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                New Password
                            </span>
                        </label>

                        <div class="relative mt-2">
                            <input
                                :type="showNewPassword ? 'text' : 'password'"
                                wire:model="password"
                                id="password"
                                autocomplete="new-password"
                                class="fi-input block w-full border-2 border-gray-300 focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm px-3 py-2 pr-10 sm:text-sm transition-colors"
                            />

                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <svg x-show="!showNewPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showNewPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Confirm New Password
                            </span>
                        </label>

                        <div class="relative mt-2">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                autocomplete="new-password"
                                class="fi-input block w-full border-2 border-gray-300 focus:border-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm px-3 py-2 pr-10 sm:text-sm transition-colors"
                            />

                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400 mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-filament::section>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-x-3 mt-8">
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
