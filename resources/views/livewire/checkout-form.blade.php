<div>
    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Customer Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" wire:model.live="firstName" id="first_name" name="customer[first_name]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('firstName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                <input type="text" wire:model.live="lastName" id="last_name" name="customer[last_name]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('lastName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input type="email" wire:model.live="email" id="email" name="customer[email]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                <input type="tel" wire:model.live="phoneNumber" id="phone_number" name="customer[phone_number]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('phoneNumber') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Billing Address -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Address</h2>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="useBillingForShipping" class="mr-2 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <span class="text-sm text-gray-700">Use billing address for shipping</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                <select wire:model.live="billingCountry" id="billing_country" name="billing_address[country]" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">Select a country</option>
                    @foreach($this->countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('billingCountry') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                <input type="text" wire:model.live="billingAddress" id="billing_address" name="billing_address[address]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billingAddress') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                <input type="text" wire:model.live="billingState" id="billing_state" name="billing_address[state]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billingState') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                <input type="text" wire:model.live="billingCity" id="billing_city" name="billing_address[city]" required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billingCity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="billing_building_number" class="block text-sm font-medium text-gray-700 mb-1">Building Number (Optional)</label>
                <input type="text" wire:model.live="billingBuildingNumber" id="billing_building_number" name="billing_address[building_number]"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billingBuildingNumber') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <div x-data="{ useBillingForShipping: @entangle('useBillingForShipping') }"
         x-show="!useBillingForShipping"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Address</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                    <select wire:model.live="shippingCountry" id="shipping_country" name="shipping_address[country]" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Select a country</option>
                        @foreach($this->countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('shippingCountry') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                    <input type="text" wire:model.live="shippingAddress" id="shipping_address" name="shipping_address[address]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('shippingAddress') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                    <input type="text" wire:model.live="shippingState" id="shipping_state" name="shipping_address[state]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('shippingState') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                    <input type="text" wire:model.live="shippingCity" id="shipping_city" name="shipping_address[city]" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('shippingCity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="shipping_building_number" class="block text-sm font-medium text-gray-700 mb-1">Building Number (Optional)</label>
                    <input type="text" wire:model.live="shippingBuildingNumber" id="shipping_building_number" name="shipping_address[building_number]"
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('shippingBuildingNumber') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Methods Selector -->
    @livewire('payment-methods-selector')


        <!-- Submit Button -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <button type="button"
                wire:click="submitForm"
                wire:loading.attr="disabled"
                wire:target="submitForm"
                class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="submitForm">
                {{ Auth::check() ? 'Place Order' : 'Place Order as Guest' }}
            </span>
            <span wire:loading wire:target="submitForm" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </div>

    <!-- Loading and Error Messages -->
    <div wire:loading wire:target="submitForm" class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing your order...
        </div>
    </div>



    <!-- Hidden Form for Submission -->
    <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}" style="display: none;">
        @csrf
        <input type="hidden" name="first_name" value="{{ $firstName }}">
        <input type="hidden" name="last_name" value="{{ $lastName }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="phone_number" value="{{ $phoneNumber }}">
        <input type="hidden" name="billing_country_id" value="{{ $billingCountry }}">
        <input type="hidden" name="billing_state" value="{{ $billingState }}">
        <input type="hidden" name="billing_city" value="{{ $billingCity }}">
        <input type="hidden" name="billing_address" value="{{ $billingAddress }}">
        <input type="hidden" name="billing_building_number" value="{{ $billingBuildingNumber ?: 'N/A' }}">
        <input type="hidden" name="shipping_country_id" value="{{ $useBillingForShipping ? $billingCountry : ($shippingCountry ?: $billingCountry) }}">
        <input type="hidden" name="shipping_state" value="{{ $useBillingForShipping ? $billingState : ($shippingState ?: $billingState) }}">
        <input type="hidden" name="shipping_city" value="{{ $useBillingForShipping ? $billingCity : ($shippingCity ?: $billingCity) }}">
        <input type="hidden" name="shipping_address" value="{{ $useBillingForShipping ? $billingAddress : ($shippingAddress ?: $billingAddress) }}">
        <input type="hidden" name="shipping_building_number" value="{{ $useBillingForShipping ? ($billingBuildingNumber ?: 'N/A') : ($shippingBuildingNumber ?: 'N/A') }}">
        <input type="hidden" name="use_billing_for_shipping" value="{{ $useBillingForShipping ? '1' : '0' }}">
        <input type="hidden" name="payment_method" value="{{ $selectedPaymentMethod }}">
                        <input type="hidden" name="paypal_payment_type" value="credit_card">
        <input type="hidden" name="currency" value="{{ $currentCurrency }}">
    </form>

</div>

