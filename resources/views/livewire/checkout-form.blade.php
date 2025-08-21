<div>
    <!-- Billing Address -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Address</h2>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox"
                       wire:model.live="useBillingForShipping"
                       class="mr-2 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <span class="text-sm text-gray-700">Use billing address for shipping</span>
            </label>
        </div>

        <div class="mb-4">
            <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
            <select wire:model.live="billingCountry"
                    id="billing_country"
                    name="billing_address[country]"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <option value="">Select a country</option>
                @foreach($this->countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
            @error('billingCountry')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Add other billing address fields here as needed -->
    </div>

    <!-- Shipping Address -->
    @if(!$useBillingForShipping)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Address</h2>

        <div class="mb-4">
            <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
            <select wire:model.live="shippingCountry"
                    id="shipping_country"
                    name="shipping_address[country]"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <option value="">Select a country</option>
                @foreach($this->countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
            @error('shippingCountry')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Add other shipping address fields here as needed -->
    </div>
    @endif

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

        <!-- Debug info in development -->
        @if(config('app.debug'))
            <div class="mb-4 p-3 bg-gray-100 rounded text-xs">
                <p><strong>Debug Info:</strong></p>
                <p>Available Methods: {{ implode(', ', $paymentMethods) }}</p>
                <p>Selected: {{ $selectedPaymentMethod }}</p>
                <p>Credit Card Available: {{ $creditCardAvailable ? 'Yes' : 'No' }}</p>
                <p>Current Currency: {{ $currentCurrency }} ({{ $currentSymbol }})</p>
                <p>Billing Country: {{ $billingCountry }}</p>
                <p>Shipping Country: {{ $shippingCountry }}</p>
                                       <div class="mt-2">
                           <button type="button" onclick="testCurrencyUpdate()" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Test Currency Update</button>
                           <button type="button" onclick="debugCurrencySelector()" class="px-2 py-1 bg-purple-500 text-white rounded text-xs ml-2">Debug Navbar</button>
                           <button type="button" onclick="testBrowserEvents()" class="px-2 py-1 bg-green-500 text-white rounded text-xs ml-2">Test Events</button>
                       </div>
            </div>

            <script>
                function testCurrencyUpdate() {
                    console.log('🧪 Testing currency update...');

                    // Try to find currency selector and call its method
                    const currencyComponents = document.querySelectorAll('.currency-selector[wire\\:id]');
                    console.log('Found currency components:', currencyComponents.length);

                    currencyComponents.forEach((component, index) => {
                        const wireId = component.getAttribute('wire:id');
                        console.log(`Currency component ${index}:`, wireId);

                        if (window.Livewire && wireId) {
                            try {
                                const livewireComponent = window.Livewire.find(wireId);
                                if (livewireComponent) {
                                    console.log('📞 Calling updateToCurrency with AUD');
                                    livewireComponent.call('updateToCurrency', 'AUD');
                                }
                            } catch (e) {
                                console.error('Error:', e);
                            }
                        }
                    });
                }

                function debugCurrencySelector() {
                    console.log('🔍 DEBUGGING CURRENCY SELECTOR IN NAVBAR...');

                    // Check all possible selectors
                    const selectors = [
                        '.currency-selector[wire\\:id]',
                        '[wire\\:id*="currency-selector"]',
                        '[x-data*="open"][wire\\:id]',
                        '[wire\\:id]'
                    ];

                    selectors.forEach((selector, i) => {
                        const elements = document.querySelectorAll(selector);
                        console.log(`Selector ${i+1} (${selector}): Found ${elements.length} elements`);
                        elements.forEach((el, j) => {
                            const wireId = el.getAttribute('wire:id');
                            const hasClass = el.classList.contains('currency-selector');
                            const textContent = el.textContent.includes('Currency') || el.textContent.includes('USD') || el.textContent.includes('$');
                            console.log(`  Element ${j+1}: wire:id=${wireId}, hasCurrencyClass=${hasClass}, hasPrice=${textContent}`);
                        });
                    });

                    // Try to find and test currency selector specifically
                    const currencyEl = document.querySelector('.currency-selector[wire\\:id]');
                    if (currencyEl) {
                        const wireId = currencyEl.getAttribute('wire:id');
                        console.log('🎯 Found currency selector with ID:', wireId);

                        if (window.Livewire) {
                            try {
                                const component = window.Livewire.find(wireId);
                                console.log('📡 Livewire component:', component ? 'FOUND' : 'NOT FOUND');
                                if (component) {
                                    console.log('🧪 Testing updateToCurrency...');
                                    component.call('updateToCurrency', 'GBP');
                                }
                            } catch (e) {
                                console.error('❌ Error accessing component:', e);
                            }
                        }
                    } else {
                        console.log('❌ No currency selector found');
                    }
                }

                function testBrowserEvents() {
                    console.log('🧪 Testing browser events...');

                    // Test currency changed event
                    window.dispatchEvent(new CustomEvent('livewire-currency-changed', {
                        detail: { currency: 'EUR', symbol: '€' }
                    }));

                    console.log('📡 Browser event dispatched: livewire-currency-changed with EUR');

                    setTimeout(() => {
                        // Test country changed event
                        window.dispatchEvent(new CustomEvent('livewire-country-changed', {
                            detail: { countryCode: 'FR', currency: 'EUR' }
                        }));
                        console.log('📡 Browser event dispatched: livewire-country-changed with FR');
                    }, 1000);
                }
            </script>
        @endif

        @if(empty($paymentMethods))
            <div class="text-center py-8">
                <p class="text-gray-500">Please select a country to see available payment methods.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($paymentMethods as $method)
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition {{ $selectedPaymentMethod === $method ? 'border-red-500 bg-red-50' : '' }}">
                        <input type="radio"
                               wire:model.live="selectedPaymentMethod"
                               name="payment_method"
                               value="{{ $method }}"
                               required
                               class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
                        <div class="flex-1">
                            <div class="flex items-center">
                                @if($method === 'paypal')
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.067 8.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478z"/>
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
                                <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($method === 'paypal')
                                    Pay with your PayPal account or credit card
                                @elseif($method === 'paymob')
                                    Local payment solution for Egypt and MENA region
                                @elseif($method === 'cash_on_delivery')
                                    Pay with cash when your order is delivered
                                @endif
                            </p>

                            @if($method === 'paypal' && $selectedPaymentMethod === 'paypal')
                                <div class="mt-3 space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio"
                                               wire:model.live="paypalPaymentType"
                                               name="paypal_payment_type_group"
                                               value="paypal_account"
                                               class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="text-sm text-gray-700">PayPal Account</span>
                                    </label>
                                    @if($creditCardAvailable)
                                        <label class="flex items-center">
                                            <input type="radio"
                                                   wire:model.live="paypalPaymentType"
                                                   name="paypal_payment_type_group"
                                                   value="credit_card"
                                                   class="mr-2 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="text-sm text-gray-700">Credit/Debit Card (Processed by PayPal)</span>
                                        </label>
                                    @endif

                                    <div class="mt-2 text-xs text-gray-500">
                                        @if($paypalPaymentType === 'paypal_account')
                                            <p>• Pay with your existing PayPal account</p>
                                            <p>• Quick and secure checkout</p>
                                        @else
                                            <p>• Pay with any major credit or debit card</p>
                                            <p>• No PayPal account required</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        @endif

        @error('selectedPaymentMethod')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror

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
    </div>

    <!-- Hidden inputs for form submission -->
    <input type="hidden" name="payment_method" value="{{ $selectedPaymentMethod }}" id="payment_method_input">
    <input type="hidden" name="paypal_payment_type" value="{{ $paypalPaymentType }}" id="paypal_payment_type_input">
</div>
