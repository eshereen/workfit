document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Simple Checkout JS Loaded - Livewire Only');

    // Simple form submission helper
    window.updateFormBeforeSubmit = function() {
        // Sync Livewire payment method selection with main form
        const livewirePaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const formPaymentMethod = document.querySelector('#payment_method_input');

        if (livewirePaymentMethod && formPaymentMethod) {
            formPaymentMethod.value = livewirePaymentMethod.value;
            console.log('✅ Payment method synced:', livewirePaymentMethod.value);
        }

        const livewirePaypalType = document.querySelector('input[name="paypal_payment_type_group"]:checked');
        const formPaypalType = document.querySelector('#paypal_payment_type_input');

        if (livewirePaypalType && formPaypalType) {
            formPaypalType.value = livewirePaypalType.value;
            console.log('✅ PayPal type synced:', livewirePaypalType.value);
        }
    };
});
