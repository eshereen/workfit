<!DOCTYPE html>
<html>
<head>
    <title>Test Checkout - Debug COD Issue</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 300px; padding: 5px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Test Checkout - Debug COD Redirection Issue</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <h2>Test 1: Simple COD Redirect Test</h2>
    <a href="{{ route('checkout.test-simple-cod') }}">
        <button type="button">Test Simple COD Redirect (GET)</button>
    </a>

    <h2>Test 2: Direct COD Payment Test</h2>
    <form method="POST" action="{{ route('checkout.test-cod') }}">
        @csrf
        <button type="submit">Test COD Gateway Directly</button>
    </form>

    <h2>Test 3: Form Submission with COD</h2>
    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf

        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" value="Test" required>
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" value="User" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="test@example.com" required>
        </div>

        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="phone_number" value="1234567890" required>
        </div>

        <div class="form-group">
            <label>Billing Country:</label>
            <select name="billing_country_id" required>
                @foreach(\App\Models\Country::all() as $country)
                    <option value="{{ $country->id }}" {{ $country->code === 'EG' ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Billing State:</label>
            <input type="text" name="billing_state" value="Cairo" required>
        </div>

        <div class="form-group">
            <label>Billing City:</label>
            <input type="text" name="billing_city" value="Cairo" required>
        </div>

        <div class="form-group">
            <label>Billing Address:</label>
            <input type="text" name="billing_address" value="123 Test Street" required>
        </div>

        <input type="hidden" name="billing_building_number" value="1">
        <input type="hidden" name="shipping_country_id" value="1">
        <input type="hidden" name="shipping_state" value="Cairo">
        <input type="hidden" name="shipping_city" value="Cairo">
        <input type="hidden" name="shipping_address" value="123 Test Street">
        <input type="hidden" name="shipping_building_number" value="1">
        <input type="hidden" name="use_billing_for_shipping" value="1">
        <input type="hidden" name="currency" value="EGP">

        <div class="form-group">
            <label>Payment Method:</label>
            <select name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="cash_on_delivery" selected>Cash on Delivery (COD)</option>
                <option value="paymob">Paymob</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <button type="submit">Submit Test Order (COD)</button>
    </form>

    <h2>Test 4: Debug Current Request</h2>
    <div class="debug">
        <strong>Current User:</strong> {{ Auth::check() ? 'Authenticated (' . Auth::user()->email . ')' : 'Guest' }}<br>
        <strong>Cart Items:</strong> {{ app(\App\Services\CartService::class)->getCart()->count() }}<br>
        <strong>Session ID:</strong> {{ session()->getId() }}<br>
        <strong>CSRF Token:</strong> {{ csrf_token() }}
    </div>

    <h2>Test 5: Check Payment Methods for Egypt</h2>
    <div class="debug">
        @php
            $resolver = app(\App\Services\PaymentMethodResolver::class);
            $methods = $resolver->availableForCountry('EG');
        @endphp
        <strong>Available Payment Methods for Egypt:</strong>
        <ul>
            @foreach($methods as $method)
                <li>{{ $method->value }} ({{ $method->name }})</li>
            @endforeach
        </ul>
    </div>

    <script>
        // Add some client-side debugging
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Test checkout page loaded');

            // Log form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitting:', form.action);
                    console.log('Form data:');
                    const formData = new FormData(form);
                    for (const [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }
                });
            });
        });
    </script>
</body>
</html>
