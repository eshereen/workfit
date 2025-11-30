<!-- Payment methods are now handled by the main checkout-form component -->

<!-- Fallback Payment Methods (if Livewire fails) -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6" id="fallback-payment-methods" style="display: none;">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

    <div class="space-y-4">
        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
            <input type="radio"
                   name="payment_method"
                   value="paypal"
                   {{ old('payment_method') == 'paypal' ? 'checked' : '' }}
                   required
                   class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
            <div class="flex-1">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.067 8.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478zM20.067 12.478c.492.315.844.825.844 1.478 0 .653-.352 1.163-.844 1.478-.492.315-1.163.478-1.844.478H17.5v-2.956h.723c.681 0 1.352.163 1.844.478z"/>
                    </svg>
                    <span class="font-medium text-gray-900">PayPal</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Pay with your PayPal account</p>
            </div>
        </label>

        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
            <input type="radio"
                   name="payment_method"
                   value="paymob"
                   {{ old('payment_method') == 'paymob' ? 'checked' : '' }}
                   required
                   class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
            <div class="flex-1">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="font-medium text-gray-900">Paymob</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Local payment solution for Egypt and MENA region</p>
            </div>
        </label>

        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:border-red-300 transition">
            <input type="radio"
                   name="payment_method"
                   value="cash_on_delivery"
                   {{ old('payment_method', 'cash_on_delivery') == 'cash_on_delivery' ? 'checked' : '' }}
                   required
                   class="mt-1 mr-3 text-red-600 focus:ring-red-500 border-gray-300">
            <div class="flex-1">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="font-medium text-gray-900">Cash on Delivery</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Pay with cash when your order is delivered</p>
            </div>
        </label>
    </div>

    @error('payment_method')
        <p class="text-yellow-900 text-sm mt-2">{{ $message }}</p>
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

<script>
    // Check if Livewire component loaded successfully, if not show fallback
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const livewireComponent = document.querySelector('[wire\\:id]');
            const fallbackMethods = document.getElementById('fallback-payment-methods');

            if (!livewireComponent && fallbackMethods) {
                fallbackMethods.style.display = 'block';
                console.log('Livewire component not found, showing fallback payment methods');
            }
        }, 2000); // Wait 2 seconds for Livewire to load
    });
</script>
