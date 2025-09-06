<div>
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

    <div class="space-y-4">
        @foreach($methods as $method)
            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
                <input type="radio"
                       name="payment_method_selector"
                       value="{{ $method }}"
                       wire:model.live="selectedMethod"
                       class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
                <div class="flex-1">
                    <div class="flex items-center">
                        @if($method === 'paypal')
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.067 8.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478zM20.067 12.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478z"/>
                            </svg>
                        @elseif($method === 'paymob')
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>

                        @elseif($method === 'cash_on_delivery')
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        @endif
                        <span class="font-medium text-gray-900">
                            @if($method === 'paymob')
                                Credit Card
                            @else
                                {{ ucfirst(str_replace('_',' ', $method)) }}
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($method === 'paypal')
                            Pay with your PayPal account
                        @elseif($method === 'paymob')
                            Pay securely with your credit or debit card

                        @elseif($method === 'cash_on_delivery')
                            Pay with cash when your order is delivered
                        @endif
                    </p>

                    @if($method === 'paypal')
                        <div class="mt-3">
                            <div class="text-xs text-gray-500">
                                <p>• Pay with PayPal account or credit/debit card</p>
                                <p>• Secure payment processing by PayPal</p>
                                <p>• Choose payment method on PayPal's secure page</p>
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

</div>

