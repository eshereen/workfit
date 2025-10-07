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

                <div class="grid gap-6">
                    {{-- Name Field --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model="name"
                                id="name"
                                required
                                maxlength="255"
                            >
                                <x-slot name="label">
                                    Full Name
                                </x-slot>
                            </x-filament::input>
                        </x-filament::input.wrapper>

                        @error('name')
                            <x-filament::input.wrapper.error-message>
                                {{ $message }}
                            </x-filament::input.wrapper.error-message>
                        @enderror
                    </div>

                    {{-- Email Field --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="email"
                                wire:model="email"
                                id="email"
                                required
                            >
                                <x-slot name="label">
                                    Email Address
                                </x-slot>
                            </x-filament::input>
                        </x-filament::input.wrapper>

                        @error('email')
                            <x-filament::input.wrapper.error-message>
                                {{ $message }}
                            </x-filament::input.wrapper.error-message>
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

                <div class="grid gap-6">
                    {{-- Current Password --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="password"
                                wire:model="current_password"
                                id="current_password"
                                autocomplete="current-password"
                            >
                                <x-slot name="label">
                                    Current Password
                                </x-slot>
                            </x-filament::input>
                        </x-filament::input.wrapper>

                        @error('current_password')
                            <x-filament::input.wrapper.error-message>
                                {{ $message }}
                            </x-filament::input.wrapper.error-message>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="password"
                                wire:model="password"
                                id="password"
                                autocomplete="new-password"
                            >
                                <x-slot name="label">
                                    New Password
                                </x-slot>
                            </x-filament::input>
                        </x-filament::input.wrapper>

                        @error('password')
                            <x-filament::input.wrapper.error-message>
                                {{ $message }}
                            </x-filament::input.wrapper.error-message>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="password"
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                autocomplete="new-password"
                            >
                                <x-slot name="label">
                                    Confirm New Password
                                </x-slot>
                            </x-filament::input>
                        </x-filament::input.wrapper>

                        @error('password_confirmation')
                            <x-filament::input.wrapper.error-message>
                                {{ $message }}
                            </x-filament::input.wrapper.error-message>
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
                    <x-filament::loading-indicator wire:loading wire:target="save" class="h-5 w-5" />

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
