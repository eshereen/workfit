@extends('layouts.app')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('title', 'PayPal Credit Card Payment')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Complete Your Payment</h1>
                <p class="text-gray-600 mt-1">Order #{{ $order->id }} - {{ $order->currency }} {{ number_format($order->total_amount, 2) }}</p>
            </div>

            <!-- Payment Details -->
            <div class="px-6 py-4">
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Order Summary</h3>
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if(isset($item->product->media[0]))
                                    <img src="{{ $item->product->media[0]->getUrl() }}"
                                         alt="{{ $item->name }}"
                                         class="w-12 h-12 object-cover rounded mr-3">
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item->name }}</h3>
                                    @if(isset($item->variant))
                                        <p class="text-sm text-gray-600">
                                            @if($item->variant->size){{ $item->variant->size }}@endif
                                            @if($item->variant->size && $item->variant->color), @endif
                                            @if($item->variant->color){{ $item->variant->color }}@endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-900">Qty: {{ $item->quantity }}</p>
                                <p class="font-medium text-gray-900">{{ $order->currency }} {{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between font-bold">
                                <span>Total</span>
                                <span>{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PayPal Smart Payment Buttons -->
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pay with Credit Card via PayPal</h3>
                    <p class="text-gray-600 mb-6">Your payment information is secure and encrypted by PayPal.</p>

                    <div id="paypal-button-container" class="mb-6">
                        <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-blue-600 font-medium">Loading PayPal...</p>
                        </div>
                    </div>

                    <!-- Manual recovery section -->
                    <div id="manual-recovery-section" class="mb-4" style="display: none;">
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-700 text-sm font-medium">Pending Payment Detected</p>
                            <p class="text-yellow-600 text-xs mt-1">A previous payment attempt was interrupted.</p>
                            <button onclick="attemptManualRecovery()" class="mt-2 px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                Complete Payment
                            </button>
                            <button onclick="resetPaymentState()" class="mt-2 ml-2 px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                Start Over
                            </button>
                        </div>
                    </div>

                                    <!-- Debug info -->
                @if(config('app.debug'))
                <div class="mt-4 p-3 bg-gray-100 rounded text-left text-xs">
                    <p><strong>Debug Info:</strong></p>
                    <p>Payment ID: {{ $payment->id }}</p>
                    <p>Order ID: {{ $order->id }}</p>
                    <p>Amount: {{ $order->currency }} {{ number_format($order->total_amount, 2) }}</p>
                    <p>Status: {{ $payment->status }}</p>
                    <p>Provider: {{ $payment->provider }}</p>
                    <p>Payment Type: {{ $payment->meta['payment_type'] ?? 'N/A' }}</p>
                    <p>Currency: {{ $order->currency }}</p>
                    <p>PayPal Client ID: {{ config('paypal.sandbox.client_id') ? 'Set' : 'Not Set' }}</p>
                </div>
                @endif

                    <div class="text-sm text-gray-500 mt-4">
                        <p>🔒 Your payment information is secure and encrypted</p>
                        <p>💳 Accepts all major credit cards</p>
                        <p>🔄 Powered by PayPal's secure payment system</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PayPal SDK -->
<script>
console.log('PayPal configuration:', {
    client_id: '{{ config('paypal.sandbox.client_id') }}',
    currency: '{{ strtoupper($order->currency) }}',
    mode: '{{ config('paypal.mode') }}'
});
</script>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency={{ strtoupper($order->currency) }}&intent=capture&components=buttons"></script>

<script>
// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error caught:', e.error);
    console.error('Error details:', {
        message: e.error?.message,
        stack: e.error?.stack,
        filename: e.filename,
        lineno: e.lineno
    });
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('PayPal SDK loaded, creating buttons...');

    // Set a timeout for PayPal SDK loading
    setTimeout(function() {
        if (typeof paypal === 'undefined') {
            console.error('PayPal SDK failed to load within timeout');
            document.getElementById('paypal-button-container').innerHTML = `
                <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-600 font-medium">PayPal SDK Failed to Load</p>
                    <p class="text-yellow-900 text-sm mt-1">Please check your internet connection and refresh the page.</p>
                    <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Refresh Page
                    </button>
                </div>
            `;
            return;
        }

        console.log('PayPal SDK loaded successfully, proceeding with button creation...');
        createPayPalButtons();
    }, 5000); // 5 second timeout

    // Check if PayPal SDK loaded correctly
    if (typeof paypal === 'undefined') {
        console.error('PayPal SDK not loaded');
        document.getElementById('paypal-button-container').innerHTML = '<div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-red-600">PayPal SDK failed to load. Please refresh the page.</p></div>';
        return;
    }

    createPayPalButtons();
});

function createPayPalButtons() {

    console.log('PayPal SDK loaded successfully');

    // Check for pending payments but don't auto-recover
    checkForPendingPaymentDisplay();

    paypal.Buttons({
        createOrder: function(data, actions) {
            console.log('Creating PayPal order...');

            const orderData = {
                purchase_units: [{
                    amount: {
                        currency_code: '{{ strtoupper($order->currency) }}',
                        value: '{{ number_format($order->total_amount, 2, '.', '') }}',
                    },
                    custom_id: '{{ $payment->id }}',
                    description: 'Order #{{ $order->id }}',
                }],
                application_context: {
                    shipping_preference: 'NO_SHIPPING',
                    user_action: 'PAY_NOW',
                    brand_name: '{{ config('app.name', 'Your Store') }}',
                    landing_page: 'BILLING',
                    locale: 'en-US',
                }
            };

            console.log('Order data:', orderData);

            return actions.order.create(orderData).then(function(orderID) {
                console.log('PayPal order created successfully:', orderID);
                return orderID;
            }).catch(function(error) {
                console.error('PayPal order creation failed:', error);
                throw error;
            });
        },
        onApprove: function(data, actions) {
            console.log('Payment approved, capturing order:', data.orderID);

            // Show loading state
            document.getElementById('paypal-button-container').innerHTML = '<div class="text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><p class="mt-2 text-gray-600">Processing payment...</p></div>';

            // Store the order ID in localStorage as backup
            localStorage.setItem('paypal_order_id', data.orderID);
            localStorage.setItem('payment_id', '{{ $payment->id }}');

            // Try to capture the payment
            return actions.order.capture().then(function(details) {
                console.log('Payment captured successfully:', details);

                // Clear localStorage
                localStorage.removeItem('paypal_order_id');
                localStorage.removeItem('payment_id');

                // Submit to backend
                submitPaymentToBackend(data.orderID);

            }).catch(function(error) {
                console.error('Payment capture failed:', error);

                // If capture fails due to popup closing, try to submit anyway
                if (error.message && error.message.includes('Target window is closed')) {
                    console.log('Popup closed, attempting to submit payment anyway...');
                    submitPaymentToBackend(data.orderID);
                } else {
                    // Show error for other types of failures
                    const container = document.getElementById('paypal-button-container');
                    container.innerHTML = `
                        <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-600 font-medium">Payment Failed</p>
                            <p class="text-yellow-900 text-sm mt-1">Payment capture failed: ${error.message || 'Unknown error'}</p>
                            <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Try Again
                            </button>
                        </div>
                    `;
                }
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);

            // Show error message instead of reloading
            const container = document.getElementById('paypal-button-container');
            container.innerHTML = `
                <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-600 font-medium">Payment Error</p>
                    <p class="text-yellow-900 text-sm mt-1">An error occurred with PayPal. Please try again.</p>
                    <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Try Again
                    </button>
                </div>
            `;
        },
        style: {
            layout: 'vertical',
            color: 'blue',
            shape: 'rect',
            label: 'pay'
        }
    }).render('#paypal-button-container');
}

// Helper function to submit payment to backend
function submitPaymentToBackend(orderID) {
    try {
        console.log('Submitting payment to backend:', orderID);

        // Clear recovery flag
        sessionStorage.removeItem('recovery_attempted');

        // Show processing message
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-blue-600 font-medium">Processing Payment...</p>
                <p class="text-blue-500 text-sm mt-1">Please wait while we complete your payment.</p>
            </div>
        `;

        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('checkout.paypal.credit-card.capture', $payment) }}';

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'paypal_order_id';
        input.value = orderID;

        form.appendChild(input);
        document.body.appendChild(form);

        console.log('Submitting form to backend...');
        form.submit();

    } catch (error) {
        console.error('Error submitting form:', error);

        // Show error message
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600 font-medium">Submission Error</p>
                <p class="text-yellow-900 text-sm mt-1">Error: ${error.message}</p>
                <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Try Again
                </button>
            </div>
        `;
    }
}

// Check for pending payments and show manual recovery options
function checkForPendingPaymentDisplay() {
    const orderID = localStorage.getItem('paypal_order_id');
    const paymentID = localStorage.getItem('payment_id');

    console.log('Checking for pending payments:', { orderID, paymentID, currentPaymentID: '{{ $payment->id }}' });

    if (orderID && paymentID && paymentID === '{{ $payment->id }}') {
        console.log('Found pending payment, showing manual recovery options');
        document.getElementById('manual-recovery-section').style.display = 'block';
    }
}

// Manual recovery attempt
function attemptManualRecovery() {
    const orderID = localStorage.getItem('paypal_order_id');
    const paymentID = localStorage.getItem('payment_id');

    console.log('Manual recovery initiated:', { orderID, paymentID, currentPaymentID: '{{ $payment->id }}' });

    if (orderID && paymentID === '{{ $payment->id }}') {
        console.log('Starting manual recovery for order:', orderID);

        // Show processing message
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-blue-600 font-medium">Completing Payment...</p>
                <p class="text-blue-500 text-sm mt-1">Please wait while we process your payment.</p>
            </div>
        `;

        // Submit the payment
        submitPaymentToBackend(orderID);
    } else {
        console.error('Payment mismatch or missing data');
        alert('Payment data mismatch. Please start a new payment.');
        resetPaymentState();
    }
}

// Function to reset payment state
function resetPaymentState() {
    console.log('Resetting payment state...');
    localStorage.removeItem('paypal_order_id');
    localStorage.removeItem('payment_id');
    sessionStorage.removeItem('recovery_attempted');

    // Hide recovery section
    document.getElementById('manual-recovery-section').style.display = 'none';

    // Reload the page to reset everything
    location.reload();
}
</script>
@endsection
