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
    @if(!$useBillingForShipping)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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

    <!-- Debug Info (remove in production) -->
    @if(config('app.debug'))
        <div class="bg-gray-100 p-4 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">Debug Info:</h3>
            <p><strong>Payment Method:</strong> {{ $selectedPaymentMethod ?? 'Not selected' }}</p>
            <p><strong>PayPal Type:</strong> {{ $paypalPaymentType ?? 'Not set' }}</p>
            <p><strong>Billing Country:</strong> {{ $billingCountry ?? 'Not set' }}</p>
            <p><strong>First Name:</strong> {{ $firstName ?? 'Not set' }}</p>
            <p><strong>Email:</strong> {{ $email ?? 'Not set' }}</p>
        </div>

        <!-- Form Data Debug -->
        <div id="debug-info" class="bg-yellow-100 p-4 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">Form Data to be Submitted:</h3>
            <p>Click "Place Order" to see the actual form data</p>
        </div>
    @endif

        <!-- Submit Button -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <button type="button" wire:click="submitForm" class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors">
            {{ Auth::check() ? 'Place Order' : 'Place Order as Guest' }}
        </button>
    </div>

    <!-- Debug: Show current payment method selection -->
    @if(config('app.debug'))
        <div class="bg-blue-100 p-4 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">Current Payment Method Selection:</h3>
            <p><strong>Selected Method:</strong> {{ $selectedPaymentMethod ?? 'Not set' }}</p>
            <p><strong>Is COD:</strong> {{ ($selectedPaymentMethod === 'cash_on_delivery') ? 'Yes' : 'No' }}</p>
            <p><strong>Is Paymob:</strong> {{ ($selectedPaymentMethod === 'paymob') ? 'Yes' : 'No' }}</p>
        </div>
    @endif

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

    <!-- Debug: Show hidden form values -->
    @if(config('app.debug'))
        <div class="bg-yellow-100 p-4 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">Hidden Form Values:</h3>
            <p><strong>Payment Method in Hidden Form:</strong> {{ $selectedPaymentMethod ?? 'Not set' }}</p>
            <p><strong>PayPal Type in Hidden Form:</strong> {{ $paypalPaymentType ?? 'Not set' }}</p>
        </div>
    @endif

    <!-- JavaScript Debug -->
    <script>
        console.log('CheckoutForm Livewire component loaded');

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');

                        // Debug: Check if components are loaded
            setTimeout(() => {
                console.log('🔍 Debug: Checking component state...');

                // Check if PaymentMethodsSelector is loaded
                const paymentSelector = document.querySelector('[wire\\:id*="payment-methods-selector"]');
                console.log('PaymentMethodsSelector found:', !!paymentSelector);

                // Check if CheckoutForm is loaded
                const checkoutForm = document.querySelector('[wire\\:id*="checkout-form"]');
                console.log('CheckoutForm found:', !!checkoutForm);

                // Log all Livewire components
                const allComponents = document.querySelectorAll('[wire\\:id]');
                console.log('All Livewire components:', Array.from(allComponents).map(el => el.getAttribute('wire:id')));

                // Debug: Check for any elements with wire:id that might be our components
                console.log('🔍 Debug: Checking for component elements...');
                allComponents.forEach((el, index) => {
                    const wireId = el.getAttribute('wire:id');
                    const className = el.className;
                    const tagName = el.tagName;
                    console.log(`Component ${index}:`, {
                        wireId,
                        className,
                        tagName,
                        element: el
                    });
                });

                // Also check for any divs that might contain our components
                const allDivs = document.querySelectorAll('div');
                console.log('🔍 Debug: Total divs found:', allDivs.length);

                // Look for any divs that might be our components
                allDivs.forEach((div, index) => {
                    if (div.innerHTML.includes('payment') || div.innerHTML.includes('checkout')) {
                        console.log(`Potential component div ${index}:`, {
                            className: div.className,
                            id: div.id,
                            innerHTML: div.innerHTML.substring(0, 200) + '...'
                        });
                    }
                });
            }, 1000);
        });

        // Listen for form submission
        document.addEventListener('livewire:submit', () => {
            console.log('Livewire form submission started');
        });

        // Listen for form submission errors
        document.addEventListener('livewire:error', (error) => {
            console.error('Livewire error:', error);
        });

        // Listen for redirect event from Livewire
        document.addEventListener('redirect-to-checkout', (event) => {
            console.log('Redirecting to checkout process:', event.detail);
            window.location.href = event.detail;
        });

        function debugFormData() {
            console.log('🔍 Debug: Form submission started');

            const form = document.getElementById('checkout-form');
            if (form) {
                console.log('✅ Form found:', form);

                const formData = new FormData(form);
                console.log('📋 Form data entries:');

                for (const [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }

                // Show debug info on page
                const debugInfo = document.getElementById('debug-info');
                if (debugInfo) {
                    debugInfo.innerHTML = '<h3 class="font-semibold mb-2">Form Data Being Submitted:</h3>';
                    for (const [key, value] of formData.entries()) {
                        debugInfo.innerHTML += `<p><strong>${key}:</strong> ${value}</p>`;
                    }
                }

                // Submit the form
                console.log('🚀 Submitting form to:', form.action);
                form.submit();

            } else {
                console.error('❌ Form not found!');
            }
        }

        // Also listen for actual form submission
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('checkout-form');
            if (form) {
                form.addEventListener('submit', (e) => {
                    console.log('🎯 Form submit event triggered');
                    console.log('Form action:', form.action);
                    console.log('Form method:', form.method);
                });
            }
        });
    </script>
</div>

