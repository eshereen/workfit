<?php

namespace App\Http\Controllers;

use App\Services\LoyaltyService;
use Exception;
use App\Payments\Gateways\CodGateway;
use App\Payments\Gateways\PaypalGateway;
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
            'title' => 'WorkFit|Checkout',
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



        // Check if user is authenticated
        if (Auth::check()) {

            return $this->processAuthenticatedCheckout($request);
        } else {

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
            'loyalty_discount' => 'nullable|numeric|min:0',
            'loyalty_points_applied' => 'nullable|integer|min:0',
        ]);



        $user = Auth::user();
        $cart = $this->cartService->getCart();



        if ($cart->isEmpty()) {

            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        try {
            DB::beginTransaction();



            // Create or update customer
            $customer = $this->createOrUpdateCustomer($user, $validated);


            // Create order
            $order = $this->createOrder($validated, $customer, $user, false);


            // Handle loyalty points redemption if applied
            if (!empty($validated['loyalty_points_applied']) && $validated['loyalty_points_applied'] > 0) {
                try {
                    $loyaltyService = app(LoyaltyService::class);
                    $loyaltyService->redeemPointsForDiscount(
                        $user,
                        $validated['loyalty_points_applied'],
                        $order,
                        "Order #{$order->order_number} discount"
                    );

                } catch (Exception $e) {
                    Log::error('Failed to redeem loyalty points', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the entire checkout if loyalty points redemption fails
                }
            }

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
                    throw new Exception('Selected payment method is not available in your country.');
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


            $result = $this->paymentService->createPayment(
                $order,
                PaymentMethod::from($validated['payment_method']),
                $returnUrl,
                $cancelUrl,
                $paymentType
            );



// Order items already created above - no need to create them again

// Clear cart
$this->cartService->clearCart();
DB::commit();

            // Log final state before redirection
            Log::info('CheckoutController: Final redirection decision', [
                'validated_payment_method' => $validated['payment_method'],
                'result_redirect_url' => $result['redirect_url'] ?? 'null',
                'payment_requires_frontend_processing' => $result['requires_frontend_processing'] ?? false,
            ]);

            // Handle different payment methods appropriately
            if ($validated['payment_method'] === 'cash_on_delivery') {
                // COD should never redirect - go directly to thank you page
                Log::info('COD payment completed, redirecting to thank you page', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('success', 'Order placed successfully! Payment will be collected on delivery.');
            } elseif ($validated['payment_method'] === 'paymob' && !empty($result['redirect_url'])) {
                // Paymob needs external redirect
                Log::info('Authenticated checkout: Redirecting to Paymob gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            } elseif ($validated['payment_method'] === 'paypal' && !empty($result['redirect_url'])) {
                // PayPal (account or credit card requiring external redirect)
                Log::info('Authenticated checkout: Redirecting to PayPal gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            } elseif (isset($result['requires_frontend_processing']) && $result['requires_frontend_processing'] && !empty($result['redirect_url'])) {
                // For PayPal credit card payments, redirect to our custom page if needed
                Log::info('Authenticated checkout: Redirecting for frontend PayPal processing', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->to($result['redirect_url']);
            } else {
                // Fallback for unexpected scenarios where Paymob/PayPal was chosen but no redirect_url
                Log::warning('Authenticated checkout: Payment method chosen but no redirect_url, falling back to thank you page.', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id,
                    'result' => $result // Log full result for debugging
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('error', 'Payment initiated, but no redirect was provided. Please check your order status.');
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing authenticated checkout: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

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



        $cart = $this->cartService->getCart();



        if ($cart->isEmpty()) {

            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        try {
            DB::beginTransaction();

            // Log the checkout attempt


            // Create customer
            $customer = $this->createGuestCustomer($validated);


            // Create order
            $order = $this->createOrder($validated, $customer, null, true);

            event(new OrderPlaced($order));
            // Create order items
            $this->createOrderItems($order, $cart);


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
                    throw new Exception('Selected payment method is not available in your country.');
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



// For credit card payments, don't set a return URL since they don't redirect back from PayPal
if ($paymentType === 'credit_card') {
    $returnUrl = null;
}


            $result = $this->paymentService->createPayment(
                $order,
                PaymentMethod::from($validated['payment_method']),
                $returnUrl,
                $cancelUrl,
                $paymentType
            );


// Order items already created above - no need to create them again

// Clear cart
$this->cartService->clearCart();
DB::commit();

            // Log final state before redirection
            Log::info('CheckoutController: Guest checkout final redirection decision', [
                'validated_payment_method' => $validated['payment_method'],
                'result_redirect_url' => $result['redirect_url'] ?? 'null',
                'payment_requires_frontend_processing' => $result['requires_frontend_processing'] ?? false,
            ]);

            // Handle different payment methods appropriately
            if ($validated['payment_method'] === 'cash_on_delivery') {
                // COD should never redirect - go directly to thank you page
                Log::info('Guest checkout: COD payment completed, redirecting to thank you page', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('success', 'Order placed successfully! Payment will be collected on delivery.');
            } elseif ($validated['payment_method'] === 'paymob' && !empty($result['redirect_url'])) {
                // Paymob needs external redirect
                Log::info('Guest checkout: Redirecting to Paymob gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            } elseif ($validated['payment_method'] === 'paypal' && !empty($result['redirect_url'])) {
                // PayPal (account or credit card requiring external redirect)
                Log::info('Guest checkout: Redirecting to PayPal gateway', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->away($result['redirect_url']);
            } elseif (isset($result['requires_frontend_processing']) && $result['requires_frontend_processing'] && !empty($result['redirect_url'])) {
                // For PayPal credit card payments, redirect to our custom page if needed
                Log::info('Guest checkout: Redirecting for frontend PayPal processing', [
                    'payment_method' => $validated['payment_method'],
                    'redirect_url' => $result['redirect_url']
                ]);
                return redirect()->to($result['redirect_url']);
            } else {
                // Fallback for unexpected scenarios where Paymob/PayPal was chosen but no redirect_url
                Log::warning('Guest checkout: Payment method chosen but no redirect_url, falling back to thank you page.', [
                    'payment_method' => $validated['payment_method'],
                    'order_id' => $order->id,
                    'result' => $result // Log full result for debugging
                ]);
                return redirect()->route('thankyou', ['order' => $order->id])
                    ->with('error', 'Payment initiated, but no redirect was provided. Please check your order status.');
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing guest checkout: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

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


        // Get country and currency information from billing country
        $country = Country::find($data['billing_country_id']);
        $currencyCode = $country ? $country->currency_code : 'USD';

        // Convert prices to local currency
        $subtotal = $this->currencyService->convertFromUSD($this->cartService->getSubtotal(), $currencyCode);
        $taxAmount = $this->currencyService->convertFromUSD($this->cartService->getTaxAmount(), $currencyCode);
        $shippingAmount = $this->currencyService->convertFromUSD($this->cartService->getShippingCost(), $currencyCode);
        $totalAmount = $this->currencyService->convertFromUSD($this->cartService->getTotal(), $currencyCode);

        // Apply loyalty discount if provided - already in local currency
        $loyaltyDiscountLocal = $data['loyalty_discount'] ?? 0;
        $finalTotal = max(0, $totalAmount - $loyaltyDiscountLocal);

        // Log loyalty discount application
        if ($loyaltyDiscountLocal > 0) {
            Log::info('Loyalty discount applied', [
                'loyalty_discount_local' => $loyaltyDiscountLocal,
                'target_currency' => $currencyCode,
                'original_total' => $totalAmount,
                'final_total' => $finalTotal
            ]);
        }

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
            'discount_amount' => ($data['coupon_discount'] ?? 0) + $loyaltyDiscountLocal,
            'total_amount' => $finalTotal,
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



            // Validate that we have a valid product ID
            if (!$productId || $productId <= 0) {
                throw new Exception("Invalid product ID extracted from cart item: {$item['id']}");
            }

            try {
                $order->items()->create([
                    'product_id' => $productId,
                    'product_variant_id' => $item['attributes']['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);


            } catch (Exception $e) {
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
        } catch (Exception $e) {
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



        try {
            // Update payment with PayPal order ID
            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paypal_order_id' => $validated['paypal_order_id'],
                    'capture_attempted_at' => now()->toISOString()
                ])
            ]);


            // Capture the payment using PayPal gateway
            $gateway = app(PaypalGateway::class);
            Log::info('PayPal gateway instance created', [
                'payment_id' => $payment->id,
                'gateway_class' => get_class($gateway)
            ]);

            $result = $gateway->captureOrder($payment, $validated['paypal_order_id']);



            if ($result['success']) {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'meta' => array_merge($payment->meta ?? [], [
                        'capture_completed_at' => now()->toISOString(),
                        'capture_result' => $result
                    ])
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

        } catch (Exception $e) {
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
