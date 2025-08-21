<div>
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

    <!-- Component loaded indicator -->
    <div class="mb-2 text-xs text-green-600">
        ✓ Livewire component loaded successfully
    </div>

    <!-- Debug information (remove in production) -->
    @if(config('app.debug'))
        <div class="mb-4 p-3 bg-gray-100 rounded text-xs">
            <p><strong>Debug Info:</strong></p>
            <p>Methods: {{ implode(', ', $methods) }}</p>
            <p>Selected: {{ $selectedMethod }}</p>
            <p>Credit Card Available: {{ $this->isCreditCardAvailable() ? 'Yes' : 'No' }}</p>
            <p>PayPal Type: {{ $paypalPaymentType }}</p>
            <p>PayPal Type Debug: <code>{{ var_export($paypalPaymentType, true) }}</code></p>
            <p>Form PayPal Type: <span id="form_paypal_type_display">Not set</span></p>
            <p>Form Payment Method: <span id="form_payment_method_display">Not set</span></p>
            <p>Livewire ID: <code>{{ $this->getId() }}</code></p>
            <div class="mt-2 space-x-2">
                <button type="button" wire:click="testUS" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Test US</button>
                <button type="button" wire:click="testEG" class="px-2 py-1 bg-green-500 text-white rounded text-xs">Test Egypt</button>
                <button type="button" wire:click="testUpdateMethods('AU')" class="px-2 py-1 bg-orange-500 text-white rounded text-xs">Test Australia</button>
                <button type="button" wire:click="testUpdateMethods('AE')" class="px-2 py-1 bg-purple-500 text-white rounded text-xs">Test UAE</button>
                <button type="button" wire:click="simpleTest" class="px-2 py-1 bg-yellow-500 text-white rounded text-xs">Simple Test</button>
                <button type="button" wire:click="ping" class="px-2 py-1 bg-red-500 text-white rounded text-xs">Ping</button>
                <button type="button" onclick="testFormUpdate()" class="px-2 py-1 bg-gray-500 text-white rounded text-xs">Test Form Update</button>
                <button type="button" onclick="debugCountrySelects()" class="px-2 py-1 bg-pink-500 text-white rounded text-xs">Debug Selects</button>
                <button type="button" onclick="testSimpleAjax()" class="px-2 py-1 bg-emerald-500 text-white rounded text-xs">Simple AJAX</button>
                <button type="button" onclick="testCountryChange(8, 'Australia')" class="px-2 py-1 bg-teal-500 text-white rounded text-xs">JS Test AU</button>
                <button type="button" onclick="testAjaxCall(8, 'Australia')" class="px-2 py-1 bg-indigo-500 text-white rounded text-xs">AJAX Test AU</button>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @foreach($methods as $method)
            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
                <input type="radio"
                       name="payment_method"
                       value="{{ $method }}"
                       wire:model="selectedMethod"
                       required
                       class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
                <div class="flex-1">
                    <div class="flex items-center">
                        @if($method === 'paypal')
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.067 8.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478zM20.067 12.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478z"/>
                            </svg>
                        @elseif($method === 'paymob')
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>

                        @elseif($method === 'cash_on_delivery')
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        @endif
                        <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_',' ', $method)) }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($method === 'paypal')
                            Pay with your PayPal account
                        @elseif($method === 'paymob')
                            Local payment solution for Egypt and MENA region

                        @elseif($method === 'cash_on_delivery')
                            Pay with cash when your order is delivered
                        @endif
                    </p>

                    @if($method === 'paypal')
                        <div class="mt-3 space-y-2">
                            <label class="flex items-center">
                                <input type="radio"
                                       name="paypal_payment_type_group"
                                       wire:model="paypalPaymentType"
                                       value="paypal_account"
                                       class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="text-sm text-gray-700">PayPal Account</span>
                            </label>
                            @if($this->isCreditCardAvailable())
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="paypal_payment_type_group"
                                           wire:model="paypalPaymentType"
                                           value="credit_card"
                                           class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700">Credit/Debit Card (Processed by PayPal)</span>
                                </label>
                            @endif

                            <div class="mt-2 text-xs text-gray-500">
                                @if($paypalPaymentType === 'paypal_account')
                                    <p>• Pay with your existing PayPal account</p>
                                    <p>• Quick and secure checkout</p>
                                    <p>• No need to enter card details</p>
                                @else
                                    <p>• Pay with any major credit or debit card</p>
                                    <p>• No PayPal account required</p>
                                    <p>• Secure processing by PayPal</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </label>
        @endforeach
    </div>

    @error('payment_method')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror

    <!-- Hidden input for PayPal payment type - REMOVED to prevent form submission issues -->
    <!-- <input type="hidden" name="paypal_payment_type" value="{{ $paypalPaymentType }}"> -->

    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-gray-600">
                Your payment information is secure and encrypted. We never store your payment details.
            </p>
        </div>
    </div>

    <script>
        // Prevent form submission when test buttons are clicked
        document.addEventListener('click', function(e) {
            if (e.target.matches('button[wire\\:click]')) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Button click prevented from submitting form');
            }
        });

        // Sync PayPal payment type with main form
        document.addEventListener('DOMContentLoaded', function() {
            // Find the hidden inputs in the main form
            const paypalTypeInput = document.querySelector('#paypal_payment_type_input');
            const paymentMethodInput = document.querySelector('#payment_method_input');

            if (paypalTypeInput) {
                console.log('Found PayPal payment type input:', paypalTypeInput.value);

                // Update it when Livewire component changes
                const radioButtons = document.querySelectorAll('input[name="paypal_payment_type_group"]');
                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function() {
                        console.log('PayPal payment type changed to:', this.value);
                        paypalTypeInput.value = this.value;
                        console.log('Updated main form input to:', paypalTypeInput.value);

                        // Update debug display
                        const debugDisplay = document.querySelector('#form_paypal_type_display');
                        if (debugDisplay) {
                            debugDisplay.textContent = this.value;
                        }

                        // Small delay to ensure form is updated
                        setTimeout(() => {
                            console.log('Form input after delay:', paypalTypeInput.value);
                        }, 100);
                    });
                });
            }

            if (paymentMethodInput) {
                console.log('Found payment method input:', paymentMethodInput.value);

                // Update it when payment method changes in Livewire
                const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
                paymentMethodRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        console.log('Payment method changed to:', this.value);
                        paymentMethodInput.value = this.value;
                        console.log('Updated main form input to:', paymentMethodInput.value);

                        // Small delay to ensure form is updated
                        setTimeout(() => {
                            console.log('Payment method input after delay:', paymentMethodInput.value);

                            // Update debug display
                            const debugDisplay = document.querySelector('#form_payment_method_display');
                            if (debugDisplay) {
                                debugDisplay.textContent = paymentMethodInput.value;
                            }
                        }, 100);
                    });
                });
            }
        });

        // Listen for country changes and update payment methods
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelects = document.querySelectorAll('select[name*="country"], input[name*="country"]');

            countrySelects.forEach(select => {
                select.addEventListener('change', function() {
                    const countryCode = this.value;
                    if (countryCode) {
                        // Call the Livewire method to update payment methods
                        // Note: This will be handled by the main checkout.js file
                        console.log('Country changed to:', countryCode);
                    }
                });
            });
        });

        // Test function to manually update form inputs
        function testFormUpdate() {
            console.log('Testing form update...');

            const paymentMethodInput = document.querySelector('#payment_method_input');
            const paypalTypeInput = document.querySelector('#paypal_payment_type_input');

            if (paymentMethodInput) {
                paymentMethodInput.value = 'paypal';
                console.log('Updated payment method to:', paymentMethodInput.value);
            }

            if (paypalTypeInput) {
                paypalTypeInput.value = 'credit_card';
                console.log('Updated PayPal type to:', paypalTypeInput.value);
            }

            // Update debug displays
            const debugDisplay = document.querySelector('#form_payment_method_display');
            if (debugDisplay) {
                debugDisplay.textContent = paymentMethodInput?.value || 'Not set';
            }

            const paypalDebugDisplay = document.querySelector('#form_paypal_type_display');
            if (paypalDebugDisplay) {
                paypalDebugDisplay.textContent = paypalTypeInput?.value || 'Not set';
            }

            console.log('Form update test completed');
        }
    </script>
</div>

