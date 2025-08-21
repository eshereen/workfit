<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\Customer;
use App\Events\OrderPlaced;
use Illuminate\Support\Str;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentMethodResolver;
use App\Services\CountryCurrencyService;
use App\Models\Payment;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $currencyService;
    protected $paymentService;
    protected $methodResolver;

    public function __construct(
        CartService $cartService,
        CountryCurrencyService $currencyService,
        PaymentService $paymentService,
        PaymentMethodResolver $methodResolver
    ) {
        $this->cartService = $cartService;
        $this->currencyService = $currencyService;
        $this->paymentService = $paymentService;
        $this->methodResolver = $methodResolver;
    }

    /**
     * Show checkout page
     */
    public function checkout(Request $request)
    {
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $countries = Country::all();
        $user = Auth::user();

        // Get current currency preference (IP detection + user override)
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Set country in session for payment method selection
        if (request()->has('country_id')) {
            $country = Country::find(request('country_id'));
            if ($country) {
                session(['checkout_country' => $country->code]);
            }
        }

        // Get base prices in USD
        $baseSubtotal = $this->cartService->getSubtotal();
        $baseTaxAmount = $this->cartService->getTaxAmount();
        $baseShippingAmount = $this->cartService->getShippingCost();
        $baseTotal = $this->cartService->getTotal();

        // Convert prices to preferred currency
        $subtotal = $this->currencyService->convertFromUSD($baseSubtotal, $currencyInfo['currency_code']);
        $tax_amount = $this->currencyService->convertFromUSD($baseTaxAmount, $currencyInfo['currency_code']);
        $shipping_amount = $this->currencyService->convertFromUSD($baseShippingAmount, $currencyInfo['currency_code']);
        $total = $this->currencyService->convertFromUSD($baseTotal, $currencyInfo['currency_code']);

        return view('checkout', [
            'cartItems' => $cart,
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'shipping_amount' => $shipping_amount,
            'total' => $total,
            'countries' => $countries,
            'user' => $user,
            'currencyCode' => $currencyInfo['currency_code'],
            'currencySymbol' => $currencyInfo['currency_symbol'],
            'isAutoDetected' => $currencyInfo['is_auto_detected'],
        ]);
    }

    /**
     * Unified checkout method that handles both authenticated and guest users
     */
    public function processCheckout(Request $request)
    {
        // Check if we have session data from Livewire component
        $sessionData = session('checkout_data');
        if ($sessionData && empty($request->input('payment_method'))) {
            Log::info('Using session data from Livewire component', ['session_data' => $sessionData]);

            // Merge session data into request
            $request->merge($sessionData);

            // Clear the session data after using it
            session()->forget('checkout_data');
        }

        // Debug: Log authentication status
        Log::info('Unified checkout: Processing request', [
            'is_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'request_all' => $request->all(),
            'has_session_data' => !empty($sessionData),
            'session_data' => $sessionData
        ]);

        // Check if user is authenticated
        if (Auth::check()) {
            Log::info('Unified checkout: User is authenticated, calling processAuthenticatedCheckout');
            return $this->processAuthenticatedCheckout($request);
        } else {
            Log::info('Unified checkout: User is guest, calling processGuestCheckout');
            return $this->processGuestCheckout($request);
        }
    }

    /**
     * Process checkout for authenticated users
     */
            public function processAuthenticatedCheckout(Request $request)
    {
        // Get data directly from request (traditional form submission) - AUTHENTICATED METHOD
        $validated = $request->validate([
            // AUTHENTICATED CHECKOUT VALIDATION RULES START
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'billing_country_id' => 'required|exists:countries,id',
            'billing_state' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_address' => 'required|string|max:500',
            'billing_building_number' => 'nullable|string|max:50',
            'shipping_country_id' => 'required|exists:countries,id',
            'shipping_state' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_building_number' => 'nullable|string|max:50',
            'use_billing_for_shipping' => 'boolean',
            'payment_method' => 'required|string|in:paypal,paymob,cash_on_delivery',
            'paypal_payment_type' => 'nullable|string|in:paypal_account,credit_card',
            'currency' => 'required|string|max:3',
        ]);

        // Debug: Log what we're getting from request
        Log::info('CheckoutController: Request data validated for authenticated checkout', [
            'validated_data' => $validated,
            'request_all' => $request->all(),
            'payment_method' => $validated['payment_method'] ?? 'NOT_SET',
            'payment_method_type' => gettype($validated['payment_method'] ?? null),
            'is_cod' => ($validated['payment_method'] ?? '') === 'cash_on_delivery',
            'is_paymob' => ($validated['payment_method'] ?? '') === 'paymob',
            'is_paypal' => ($validated['payment_method'] ?? '') === 'paypal'
        ]);

        $user = Auth::user();
        $cart = $this->cartService->getCart();

        // Debug: Log cart information
        Log::info('Checkout: Cart information', [
            'cart_count' => $cart->count(),
            'cart_items' => $cart->toArray(),
            'cart_is_empty' => $cart->isEmpty()
        ]);

        if ($cart->isEmpty()) {
            Log::warning('Checkout: Cart is empty, redirecting to cart page');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        try {
            DB::beginTransaction();

            // Log the checkout attempt
            Log::info('Authenticated checkout attempt', [
                'user_id' => $user->id,
                'request_all' => $request->all(),
                'validated_data' => $validated,
                'cart_count' => $cart->count(),
                'cart_items' => $cart->toArray()
            ]);

            // Create or update customer
            $customer = $this->createOrUpdateCustomer($user, $validated);
            Log::info('Customer created/updated successfully', ['customer_id' => $customer->id]);

            // Create order
            $order = $this->createOrder($validated, $customer, $user, false);
            Log::info('Order created successfully', ['order_id' => $order->id]);
            event(new OrderPlaced($order));
            // Create order items
            $this->createOrderItems($order, $cart);
            Log::info('Order items created successfully');


            // Check availability for the selected country
            $country = Country::find($order->country_id);
            $available = $this->methodResolver->availableForCountry($country->code ?? 'EG'); // ensure you store ISO code

            // Special handling for PayPal credit card - if PayPal is available, credit card should also be available
            $paymentMethod = PaymentMethod::from($validated['payment_method']);
            $isPayPalCreditCard = ($validated['payment_method'] === 'paypal' &&
                                 isset($validated['paypal_payment_type']) &&
                                 $validated['paypal_payment_type'] === 'credit_card');

            if (!in_array($paymentMethod, $available, true)) {
                // If it's PayPal credit card and PayPal is available, allow it
                if ($isPayPalCreditCard && in_array(PaymentMethod::PAYPAL, $available, true)) {
                    Log::info('PayPal credit card payment allowed - PayPal is available for country', [
                        'country_code' => $country->code ?? 'EG',
                        'payment_method' => $validated['payment_method'],
                        'paypal_payment_type' => $validated['paypal_payment_type'] ?? 'not set'
                    ]);
                } else {
                    throw new \Exception('Selected payment method is not available in your country.');
                }
            }

// Initiate payment
$returnUrl = route('payments.return', ['order' => $order->id]);
$cancelUrl = route('payments.cancel', ['order' => $order->id]);

            // Determine payment type for PayPal
            $paymentType = 'paypal_account'; // default
            if ($validated['payment_method'] === 'paypal' && isset($validated['paypal_payment_type'])) {
                $paymentType = $validated['paypal_payment_type'];
            }

            Log::info('PayPal payment type determined', [
                'payment_method' => $validated['payment_method'],
                'paypal_payment_type' => $validated['paypal_payment_type'] ?? 'not set',
                'final_payment_type' => $paymentType,
                'use_credit_card' => ($paymentType === 'credit_card')
            ]);

            // Log payment method before processing
            Log::info('About to process payment', [
                'payment_method' => $validated['payment_method'],
                'order_id' => $order->id,
                'is_cod' => $validated['payment_method'] === 'cash_on_delivery'
            ]);

            $result = $this->paymentService->createPayment(
                $order,
                PaymentMethod::from($validated['payment_method']),
                $returnUrl,
                $cancelUrl,
                $paymentType
            );

            Log::info('Payment processing result', [
                'payment_method' => $validated['payment_method'],
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url']),
                'redirect_url' => $result['redirect_url'] ?? null
            ]);

// Order items already created above - no need to create them again

// Clear cart
$this->cartService->clearCart();
DB::commit();

            // Handle different payment methods appropriately
            Log::info('Authenticated checkout: Processing payment method', [
                'payment_method' => $validated['payment_method'],
                'is_cod' => $validated['payment_method'] === 'cash_on_delivery',
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url']),
                'redirect_url' => $result['redirect_url'] ?? null
            ]);

            if ($validated['payment_method'] === 'cash_on_delivery') {
                // COD should never redirect - go directly to thank you page
                Log::info('COD payment completed, redirecting to thank you page', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('success', 'Order placed successfully! Payment will be collected on delivery.');
            } elseif (!empty($result['redirect_url'])) {
                // Other payment methods (Paymob, PayPal) that need external redirect
                Log::info('Authenticated checkout: Redirecting to external gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            }

            // For PayPal credit card payments, redirect to our custom page
            if (isset($result['requires_frontend_processing']) && $result['requires_frontend_processing']) {
                Log::info('Redirecting to PayPal credit card page', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->to($result['redirect_url']);
            }

            // If we reach here, something went wrong - redirect to thank you page as fallback
            Log::warning('No redirect URL found for payment method, redirecting to thank you page as fallback', [
                'payment_method' => $validated['payment_method'],
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url'])
            ]);
            return redirect()->route('thankyou', ['order' => $order->id])
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing authenticated checkout: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while processing your order. Please try again.')
                        ->withInput();
        }
    }

    /**
     * Process guest checkout (no login required)
     */
    public function processGuestCheckout(Request $request)
    {
        // Get data directly from request (traditional form submission) - GUEST CHECKOUT METHOD
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'billing_country_id' => 'required|exists:countries,id',
            'billing_state' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_address' => 'required|string|max:500',
            'billing_building_number' => 'nullable|string|max:50',
            'shipping_country_id' => 'required|exists:countries,id',
            'shipping_state' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_building_number' => 'nullable|string|max:50',
            'use_billing_for_shipping' => 'boolean',
            'payment_method' => 'required|string|in:paypal,paymob,cash_on_delivery',
            'paypal_payment_type' => 'nullable|string|in:paypal_account,credit_card',
            'currency' => 'required|string|max:3',
        ]);

        // Debug: Log what we're getting from request - GUEST CHECKOUT
        Log::info('CheckoutController: Request data validated for guest checkout', [
            'validated_data' => $validated,
            'request_all' => $request->all(),
            'payment_method' => $validated['payment_method'] ?? 'NOT_SET',
            'payment_method_type' => gettype($validated['payment_method'] ?? null),
            'is_cod' => ($validated['payment_method'] ?? '') === 'cash_on_delivery',
            'is_paymob' => ($validated['payment_method'] ?? '') === 'paymob',
            'is_paypal' => ($validated['payment_method'] ?? '') === 'paypal'
        ]);

        $cart = $this->cartService->getCart();

        // Debug: Log cart information
        Log::info('Checkout: Cart information', [
            'cart_count' => $cart->count(),
            'cart_items' => $cart->toArray(),
            'cart_is_empty' => $cart->isEmpty()
        ]);

        if ($cart->isEmpty()) {
            Log::warning('Checkout: Cart is empty, redirecting to cart page');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        try {
            DB::beginTransaction();

            // Log the checkout attempt
            Log::info('Guest checkout attempt', [
                'request_all' => $request->all(),
                'validated_data' => $validated,
                'cart_count' => $cart->count(),
                'cart_items' => $cart->toArray()
            ]);

            // Create customer
            $customer = $this->createGuestCustomer($validated);
            Log::info('Customer created successfully', ['customer_id' => $customer->id]);

            // Create order
            $order = $this->createOrder($validated, $customer, null, true);
            Log::info('Order created successfully', ['order_id' => $order->id]);
            event(new OrderPlaced($order));
            // Create order items
            $this->createOrderItems($order, $cart);
            Log::info('Order items created successfully');

            // Clear cart
            // Check availability for the selected country
            $country = Country::find($order->country_id);
            $available = $this->methodResolver->availableForCountry($country->code ?? 'EG'); // ensure you store ISO code

            // Special handling for PayPal credit card - if PayPal is available, credit card should also be available
            $paymentMethod = PaymentMethod::from($validated['payment_method']);
            $isPayPalCreditCard = ($validated['payment_method'] === 'paypal' &&
                                 isset($validated['paypal_payment_type']) &&
                                 $validated['paypal_payment_type'] === 'credit_card');

            if (!in_array($paymentMethod, $available, true)) {
                // If it's PayPal credit card and PayPal is available, allow it
                if ($isPayPalCreditCard && in_array(PaymentMethod::PAYPAL, $available, true)) {
                    Log::info('PayPal credit card payment allowed (guest) - PayPal is available for country', [
                        'country_code' => $country->code ?? 'EG',
                        'payment_method' => $validated['payment_method'],
                        'paypal_payment_type' => $validated['paypal_payment_type'] ?? 'not set'
                    ]);
                } else {
                    throw new \Exception('Selected payment method is not available in your country.');
                }
            }

// Initiate payment
$returnUrl = route('payments.return', ['order' => $order->id]);
$cancelUrl = route('payments.cancel', ['order' => $order->id]);

// Determine payment type for PayPal
$paymentType = 'paypal_account'; // default
if ($validated['payment_method'] === 'paypal' && isset($validated['paypal_payment_type'])) {
    $paymentType = $validated['paypal_payment_type'];
}

Log::info('PayPal payment type determined (guest)', [
    'payment_method' => $validated['payment_method'],
    'paypal_payment_type' => $validated['paypal_payment_type'] ?? 'not set',
    'final_payment_type' => $paymentType,
    'use_credit_card' => ($paymentType === 'credit_card')
]);

// For credit card payments, don't set a return URL since they don't redirect back from PayPal
if ($paymentType === 'credit_card') {
    $returnUrl = null;
}

            // Log payment method before processing
            Log::info('About to process payment (guest)', [
                'payment_method' => $validated['payment_method'],
                'order_id' => $order->id,
                'is_cod' => $validated['payment_method'] === 'cash_on_delivery'
            ]);

            $result = $this->paymentService->createPayment(
                $order,
                PaymentMethod::from($validated['payment_method']),
                $returnUrl,
                $cancelUrl,
                $paymentType
            );

            Log::info('Payment processing result (guest)', [
                'payment_method' => $validated['payment_method'],
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url']),
                'redirect_url' => $result['redirect_url'] ?? null
            ]);

// Order items already created above - no need to create them again

// Clear cart
$this->cartService->clearCart();
DB::commit();

            // Handle different payment methods appropriately
            Log::info('Guest checkout: Processing payment method', [
                'payment_method' => $validated['payment_method'],
                'is_cod' => $validated['payment_method'] === 'cash_on_delivery',
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url']),
                'redirect_url' => $result['redirect_url'] ?? null
            ]);

            if ($validated['payment_method'] === 'cash_on_delivery') {
                // COD should never redirect - go directly to thank you page
                Log::info('COD payment completed (guest), redirecting to thank you page', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('success', 'Order placed successfully! Payment will be collected on delivery.');
            } elseif (!empty($result['redirect_url'])) {
                // Other payment methods (Paymob, PayPal) that need external redirect
                Log::info('Guest checkout: Redirecting to external gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            }

            // For PayPal credit card payments, redirect to our custom page
            if (isset($result['requires_frontend_processing']) && $result['requires_frontend_processing']) {
                return redirect()->to($result['redirect_url']);
            }

            // If we reach here, something went wrong - redirect to thank you page as fallback
            Log::warning('No redirect URL found for payment method (guest), redirecting to thank you page as fallback', [
                'payment_method' => $validated['payment_method'],
                'result_keys' => array_keys($result),
                'has_redirect_url' => !empty($result['redirect_url'])
            ]);
            return redirect()->route('thankyou', ['order' => $order->id])
                ->with('success', 'Order placed successfully!');


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing guest checkout: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while processing your order. Please try again.')
                        ->withInput();
        }
    }

    /**
     * Create or update customer for authenticated user
     */
    protected function createOrUpdateCustomer(User $user, array $data)
    {
        // Prepare customer data with new database structure
        $customerData = [
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],

            // Billing address
            'billing_country_id' => $data['billing_country_id'],
            'billing_state' => $data['billing_state'],
            'billing_city' => $data['billing_city'],
            'billing_address' => $data['billing_address'],
            'billing_building_number' => $data['billing_building_number'],

            // Shipping address (copy from billing if using billing for shipping)
            'shipping_country_id' => $data['use_billing_for_shipping'] ? $data['billing_country_id'] : $data['shipping_country_id'],
            'shipping_state' => $data['use_billing_for_shipping'] ? $data['billing_state'] : $data['shipping_state'],
            'shipping_city' => $data['use_billing_for_shipping'] ? $data['billing_city'] : $data['shipping_city'],
            'shipping_address' => $data['use_billing_for_shipping'] ? $data['billing_address'] : $data['shipping_address'],
            'shipping_building_number' => $data['use_billing_for_shipping'] ? $data['billing_building_number'] : $data['shipping_building_number'],

            'use_billing_for_shipping' => $data['use_billing_for_shipping'],
        ];

        $customer = Customer::updateOrCreate(
            ['user_id' => $user->id],
            $customerData
        );

        return $customer;
    }

    /**
     * Create customer for guest user
     */
    protected function createGuestCustomer(array $data)
    {
        // Prepare customer data with new database structure
        $customerData = [
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],

            // Billing address
            'billing_country_id' => $data['billing_country_id'],
            'billing_state' => $data['billing_state'],
            'billing_city' => $data['billing_city'],
            'billing_address' => $data['billing_address'],
            'billing_building_number' => $data['billing_building_number'],

            // Shipping address (copy from billing if using billing for shipping)
            'shipping_country_id' => $data['use_billing_for_shipping'] ? $data['billing_country_id'] : $data['shipping_country_id'],
            'shipping_state' => $data['use_billing_for_shipping'] ? $data['billing_state'] : $data['shipping_state'],
            'shipping_city' => $data['use_billing_for_shipping'] ? $data['billing_city'] : $data['shipping_city'],
            'shipping_address' => $data['use_billing_for_shipping'] ? $data['billing_address'] : $data['shipping_address'],
            'shipping_building_number' => $data['use_billing_for_shipping'] ? $data['billing_building_number'] : $data['shipping_building_number'],

            'use_billing_for_shipping' => $data['use_billing_for_shipping'],
        ];

        return Customer::create($customerData);
    }

    /**
     * Create order
     */
    protected function createOrder(array $data, Customer $customer, ?User $user, bool $isGuest)
    {
        // Debug: Log the data being received
        Log::info('createOrder: Data received', [
            'data' => $data,
            'customer_id' => $customer->id,
            'is_guest' => $isGuest
        ]);

        // Get country and currency information from billing country
        $country = Country::find($data['billing_country_id']);
        $currencyCode = $country ? $country->currency_code : 'USD';

        // Convert prices to local currency
        $subtotal = $this->currencyService->convertFromUSD($this->cartService->getSubtotal(), $currencyCode);
        $taxAmount = $this->currencyService->convertFromUSD($this->cartService->getTaxAmount(), $currencyCode);
        $shippingAmount = $this->currencyService->convertFromUSD($this->cartService->getShippingCost(), $currencyCode);
        $totalAmount = $this->currencyService->convertFromUSD($this->cartService->getTotal(), $currencyCode);

        $orderData = [
            'order_number' => $this->generateOrderNumber(),
            'guest_token' => $isGuest ? Str::random(32) : null,
            'user_id' => $user?->id,
            'customer_id' => $customer->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? $user?->email,
            'phone_number' => $data['phone_number'] ?? $user?->phone,
            'country_id' => $data['billing_country_id'],
            'state' => $data['billing_state'],
            'city' => $data['billing_city'],
            'notes' => $data['notes'] ?? null,
            'coupon_id' => $data['coupon_id'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $data['coupon_discount'] ?? 0,
            'total_amount' => $totalAmount,
            'currency' => $currencyCode,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'is_guest' => $isGuest,
            'use_billing_for_shipping' => $data['use_billing_for_shipping'],
            'billing_address' => $data['billing_address'],
            'billing_building_number' => $data['billing_building_number'],
            'shipping_address' => $data['use_billing_for_shipping'] ? $data['billing_address'] : $data['shipping_address'],
            'shipping_building_number' => $data['use_billing_for_shipping'] ? $data['billing_building_number'] : $data['shipping_building_number'],
        ];

        // Debug: Log the order data being inserted
        Log::info('createOrder: Order data to be inserted', [
            'order_data' => $orderData
        ]);

        return Order::create($orderData);
    }

    /**
     * Create order items from cart
     */
    protected function createOrderItems(Order $order, $cart)
    {
        Log::info('Creating order items', [
            'order_id' => $order->id,
            'cart_count' => $cart->count()
        ]);

        foreach ($cart as $item) {
            Log::info('Processing cart item', [
                'item' => $item,
                'has_id' => isset($item['id']),
                'has_quantity' => isset($item['quantity']),
                'has_price' => isset($item['price']),
                'has_attributes' => isset($item['attributes']),
                'variant_id' => $item['attributes']['variant_id'] ?? null
            ]);

            // Extract product ID from the cart item ID (format: "product_id-variant_id")
            $productId = null;
            if (strpos($item['id'], '-') !== false) {
                $parts = explode('-', $item['id']);
                $productId = (int) $parts[0];
            } else {
                $productId = (int) $item['id'];
            }

            Log::info('Extracted product ID', [
                'original_id' => $item['id'],
                'extracted_product_id' => $productId,
                'variant_id' => $item['attributes']['variant_id'] ?? null
            ]);

            // Validate that we have a valid product ID
            if (!$productId || $productId <= 0) {
                throw new \Exception("Invalid product ID extracted from cart item: {$item['id']}");
            }

            try {
                $order->items()->create([
                    'product_id' => $productId,
                    'product_variant_id' => $item['attributes']['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                Log::info('Order item created successfully', [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $item['attributes']['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create order item', [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $item['attributes']['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        Log::info('Order items created successfully', [
            'order_id' => $order->id,
            'items_count' => $order->items()->count()
        ]);
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber()
    {
        do {
            // Add timestamp to ensure uniqueness
            $timestamp = microtime(true);
            $microseconds = sprintf("%06d", ($timestamp - floor($timestamp)) * 1000000);

            // Add more randomness: current timestamp + microseconds + random string + user IP hash
            $ipHash = substr(md5(request()->ip() . request()->userAgent()), 0, 8);
            $randomString = strtoupper(Str::random(6));

            $orderNumber = 'WF-' . date('Y-m-d-H-i-s') . '-' . $microseconds . '-' . $randomString . '-' . $ipHash;

            // Log the generated order number for debugging
            Log::info('Generated order number', [
                'order_number' => $orderNumber,
                'timestamp' => $timestamp,
                'microseconds' => $microseconds,
                'random_string' => $randomString,
                'ip_hash' => $ipHash
            ]);

        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Show order confirmation page
     */
    public function orderConfirmation($orderId)
    {
        $order = Order::with(['items.product', 'items.variant', 'customer'])
                     ->where('id', $orderId)
                     ->firstOrFail();

        // Check if user can view this order
        if (Auth::check()) {
            if ($order->user_id !== Auth::id() && $order->is_guest) {
                abort(403, 'You cannot view this order.');
            }
        } else {
            if ($order->is_guest && !request()->has('token')) {
                abort(403, 'Guest access denied.');
            }
        }

        // Get currency info for display
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        return view('checkout.confirmation', compact('order', 'currencyInfo'));
    }

    /**
     * Debug route to see what form data is being submitted
     */
    public function debugForm(Request $request)
    {
        Log::info('Debug form called', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'payment_method' => $request->input('payment_method'),
            'payment_method_type' => gettype($request->input('payment_method')),
            'is_cod' => $request->input('payment_method') === 'cash_on_delivery',
            'is_paymob' => $request->input('payment_method') === 'paymob',
            'is_paypal' => $request->input('payment_method') === 'paypal',
            'headers' => $request->headers->all()
        ]);

        return response()->json([
            'method' => $request->method(),
            'all_data' => $request->all(),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
            'payment_method' => $request->input('payment_method'),
            'payment_method_type' => gettype($request->input('payment_method')),
            'is_cod' => $request->input('payment_method') === 'cash_on_delivery',
            'is_paymob' => $request->input('payment_method') === 'paymob',
            'is_paypal' => $request->input('payment_method') === 'paypal',
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'headers' => $request->headers->all()
        ]);
    }

    /**
     * Simple test to check COD behavior directly
     */
    public function testSimpleCod(Request $request)
    {
        Log::info('Simple COD test called');

        $paymentMethod = 'cash_on_delivery';

        // Test the exact condition we use in processCheckout
        $isCod = ($paymentMethod === 'cash_on_delivery');

        Log::info('Simple COD test', [
            'payment_method' => $paymentMethod,
            'is_cod' => $isCod,
            'should_redirect_to_thank_you' => $isCod
        ]);

        if ($paymentMethod === 'cash_on_delivery') {
            Log::info('Simple COD test: Redirecting to thank you page');
            return redirect()->route('thankyou', ['order' => 999])
                ->with('success', 'COD test successful! This proves COD redirection works.');
        }

        return response()->json(['error' => 'Should not reach here']);
    }

    /**
     * Test COD payment specifically to debug redirection issue
     */
    public function testCodPayment(Request $request)
    {
        Log::info('Testing COD payment directly');

        try {
            // Create a minimal test data set for COD
            $testData = [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'phone_number' => '1234567890',
                'billing_country_id' => 1, // Assuming Egypt is ID 1
                'billing_state' => 'Cairo',
                'billing_city' => 'Cairo',
                'billing_address' => '123 Test St',
                'billing_building_number' => '1',
                'shipping_country_id' => 1,
                'shipping_state' => 'Cairo',
                'shipping_city' => 'Cairo',
                'shipping_address' => '123 Test St',
                'shipping_building_number' => '1',
                'use_billing_for_shipping' => true,
                'payment_method' => 'cash_on_delivery',
                'currency' => 'EGP'
            ];

            Log::info('Test COD: Creating fake cart');
            // Add a test item to cart if empty
            $cart = $this->cartService->getCart();
            if ($cart->isEmpty()) {
                Log::info('Test COD: Cart is empty, this would normally fail');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty. Add items first.',
                    'cart_count' => $cart->count()
                ]);
            }

            Log::info('Test COD: Processing payment with data', $testData);

            // Test the COD gateway directly
            $gateway = app(\App\Payments\Gateways\CodGateway::class);
            Log::info('Test COD: COD gateway created');

            // Create a test payment record
            $testPayment = new \App\Models\Payment([
                'provider' => 'cash_on_delivery',
                'status' => 'initiated',
                'currency' => 'EGP',
                'amount_minor' => 10000, // 100 EGP
            ]);

            // Create a test order
            $testOrder = new \App\Models\Order([
                'order_number' => 'TEST-' . time(),
                'currency' => 'EGP',
                'total_amount' => 100,
                'payment_method' => 'cash_on_delivery'
            ]);

            Log::info('Test COD: Calling gateway initiate method');
            $result = $gateway->initiate($testOrder, $testPayment);

            Log::info('Test COD: Gateway result', [
                'result' => $result,
                'has_redirect_url' => !empty($result['redirect_url']),
                'redirect_url' => $result['redirect_url'] ?? null
            ]);

            // Test the payment method check
            $paymentMethod = $testData['payment_method'];
            $isCod = ($paymentMethod === 'cash_on_delivery');

            Log::info('Test COD: Payment method check', [
                'payment_method' => $paymentMethod,
                'is_cod' => $isCod,
                'should_redirect' => !$isCod && !empty($result['redirect_url'])
            ]);

            return response()->json([
                'status' => 'success',
                'test_data' => $testData,
                'gateway_result' => $result,
                'payment_method_check' => [
                    'payment_method' => $paymentMethod,
                    'is_cod' => $isCod,
                    'should_redirect' => !$isCod && !empty($result['redirect_url']),
                    'has_redirect_url' => !empty($result['redirect_url']),
                    'redirect_url' => $result['redirect_url'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Test COD failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test method to debug checkout issues
     */
    public function testCheckout(Request $request)
    {
        try {
            $cart = $this->cartService->getCart();
            $cartData = $cart->toArray();

            Log::info('Test checkout - Cart data', [
                'cart_count' => $cart->count(),
                'cart_items' => $cartData
            ]);

            // Test customer creation
            $testData = [
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'phone_number' => '1234567890',
                'shipping_address' => [
                    'country' => 'United States',
                    'state' => 'CA',
                    'city' => 'Test City',
                    'address' => '123 Test St',
                    'postal_code' => '12345'
                ],
                'billing_address' => [
                    'country' => 'United States',
                    'state' => 'CA',
                    'city' => 'Test City',
                    'address' => '123 Test St',
                    'postal_code' => '12345'
                ],
                'payment_method' => 'cash_on_delivery'
            ];

            $customer = $this->createGuestCustomer($testData);
            Log::info('Test customer created', ['customer_id' => $customer->id]);

            return response()->json([
                'success' => true,
                'cart_data' => $cartData,
                'customer_created' => true,
                'customer_id' => $customer->id
            ]);

        } catch (\Exception $e) {
            Log::error('Test checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get currency conversion for AJAX requests
     */
    public function getCurrencyConversion(Request $request)
    {
        $countryName = $request->get('country');

        if (!$countryName) {
            return response()->json(['success' => false, 'message' => 'Country is required']);
        }

        $country = Country::where('name', $countryName)->first();

        if (!$country) {
            return response()->json(['success' => false, 'message' => 'Country not found']);
        }

        $currencyCode = $country->currency_code;
        $currencySymbol = $this->currencyService->getCurrencySymbol($currencyCode);

        // Convert cart prices to selected currency
        $cartData = [
            'subtotal' => $this->cartService->getSubtotal(),
            'tax_amount' => $this->cartService->getTaxAmount(),
            'shipping_amount' => $this->cartService->getShippingCost(),
            'total' => $this->cartService->getTotal(),
        ];

        $convertedPrices = $this->currencyService->convertCartToCurrency($cartData, $country->id);
        $convertedPrices['currencyCode'] = $currencyCode;

        return response()->json([
            'success' => true,
            'currencySymbol' => $currencySymbol,
            'prices' => $convertedPrices
        ]);
    }

    /**
     * Change user's preferred currency
     */
    public function changeCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|max:3',
            'country_id' => 'required|integer|exists:countries,id',
        ]);

        try {
            $this->currencyService->setPreferredCountry($validated['country_id']);

            return response()->json([
                'success' => true,
                'message' => 'Currency updated successfully',
                'currency_code' => $validated['currency_code'],
                'country_id' => $validated['country_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update currency',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show thank you page
     */
    public function thankYou($orderId)
    {
        $order = Order::with(['items.product', 'items.variant', 'customer'])
                     ->where('id', $orderId)
                     ->firstOrFail();

        // Get currency info for display
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        return view('checkout.thank-you', compact('order', 'currencyInfo'));
    }



    /**
     * Show PayPal credit card payment page
     */
    public function showPayPalCreditCard(Payment $payment)
    {
        // Verify this is a PayPal credit card payment
        if ($payment->provider !== 'paypal' ||
            ($payment->meta['payment_type'] ?? '') !== 'credit_card') {
            abort(404);
        }

        $order = $payment->order;

        return view('checkout.paypal-credit-card', compact('payment', 'order'));
    }

    /**
     * Capture PayPal credit card payment
     */
    public function capturePayPalCreditCard(Request $request, Payment $payment)
    {
        Log::info('PayPal credit card capture initiated', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'provider' => $payment->provider,
            'payment_type' => $payment->meta['payment_type'] ?? 'unknown',
            'request_data' => $request->all()
        ]);

        // Verify this is a PayPal credit card payment
        if ($payment->provider !== 'paypal' ||
            ($payment->meta['payment_type'] ?? '') !== 'credit_card') {
            Log::error('Invalid payment type for capture', [
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'payment_type' => $payment->meta['payment_type'] ?? 'unknown'
            ]);
            abort(404, 'Invalid payment type');
        }

        $validated = $request->validate([
            'paypal_order_id' => 'required|string',
        ]);

        Log::info('PayPal order ID validated', [
            'payment_id' => $payment->id,
            'paypal_order_id' => $validated['paypal_order_id']
        ]);

        try {
            // Update payment with PayPal order ID
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paypal_order_id' => $validated['paypal_order_id'],
                    'capture_attempted_at' => now()->toISOString()
                ])
            ]);

            Log::info('Payment meta updated with PayPal order ID', [
                'payment_id' => $payment->id,
                'paypal_order_id' => $validated['paypal_order_id']
            ]);

            // Capture the payment using PayPal gateway
            $gateway = app(\App\Payments\Gateways\PaypalGateway::class);
            Log::info('PayPal gateway instance created', [
                'payment_id' => $payment->id,
                'gateway_class' => get_class($gateway)
            ]);

            $result = $gateway->captureOrder($payment, $validated['paypal_order_id']);

            Log::info('PayPal gateway capture result', [
                'payment_id' => $payment->id,
                'result' => $result
            ]);

            if ($result['success']) {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'meta' => array_merge($payment->meta ?? [], [
                        'capture_completed_at' => now()->toISOString(),
                        'capture_result' => $result
                    ])
                ]);

                Log::info('Payment completed successfully', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'status' => 'completed'
                ]);

                // Redirect to thank you page
                return redirect()->route('thankyou', ['order' => $payment->order_id])
                    ->with('success', 'Payment completed successfully!');
            } else {
                Log::error('PayPal capture failed', [
                    'payment_id' => $payment->id,
                    'error_message' => $result['message'] ?? 'Unknown error',
                    'result' => $result
                ]);

                return back()->with('error', 'Payment failed: ' . ($result['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Error capturing PayPal credit card payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update payment with error information
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'capture_error' => $e->getMessage(),
                    'capture_error_at' => now()->toISOString()
                ])
            ]);

            return back()->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
    }

}
