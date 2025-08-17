<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\Customer;
use App\Events\OrderPlaced;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\CountryCurrencyService;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $currencyService;

    public function __construct(CartService $cartService, CountryCurrencyService $currencyService)
    {
        $this->cartService = $cartService;
        $this->currencyService = $currencyService;
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
     * Process checkout for authenticated users
     */
    public function processAuthenticatedCheckout(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'payment_method' => 'required|string|in:paypal,paymob,cash_on_delivery',
            'notes' => 'nullable|string|max:500',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'coupon_discount' => 'nullable|numeric|min:0',
            'use_billing_for_shipping' => 'boolean',
        ]);

        // Log the raw request data to see what's actually being sent
        Log::info('Raw request data for authenticated checkout', [
            'all_data' => $request->all(),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
            'payment_method' => $request->input('payment_method')
        ]);

        $user = Auth::user();
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
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

            // Clear cart
            $this->cartService->clearCart();

            DB::commit();

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
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.phone' => 'required|string|max:255',
            'shipping_address.address' => 'required|string|max:500',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'nullable|string|max:255',
            'shipping_address.postal_code' => 'nullable|string|max:20',
            'shipping_address.country' => 'required|string|max:255',
            'billing_address' => 'required|array',
            'billing_address.name' => 'required|string|max:255',
            'billing_address.email' => 'required|email|max:255',
            'billing_address.phone' => 'required|string|max:255',
            'billing_address.address' => 'required|string|max:500',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.state' => 'nullable|string|max:255',
            'billing_address.postal_code' => 'nullable|string|max:20',
            'billing_address.country' => 'required|string|max:255',
            'payment_method' => 'required|string|in:paypal,paymob,cash_on_delivery',
            'notes' => 'nullable|string|max:500',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'coupon_discount' => 'nullable|numeric|min:0',
            'use_billing_for_shipping' => 'boolean',
        ]);

        // Log the raw request data to see what's actually being sent
        Log::info('Raw request data for guest checkout', [
            'all_data' => $request->all(),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
            'payment_method' => $request->input('payment_method')
        ]);

        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
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
            $this->cartService->clearCart();

            DB::commit();

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
        $customer = Customer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'email' => $user->email,
                'first_name' => $user->first_name ?? $data['first_name'] ?? null,
                'last_name' => $user->last_name ?? $data['last_name'] ?? null,
                'phone' => $data['phone_number'] ?? $user->phone ?? null,
                'country_id' => $data['country_id'],
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'address' => $data['shipping_address']['address'] ?? null,
                //'zip' => $data['shipping_address']['postal_code'] ?? null,
            ]
        );

        return $customer;
    }

    /**
     * Create customer for guest user
     */
    protected function createGuestCustomer(array $data)
    {
        Log::info('Creating guest customer', [
            'country_name' => $data['shipping_address']['country'] ?? 'not set',
            'shipping_address' => $data['shipping_address']
        ]);

        // Find country by name (case insensitive)
        $country = Country::where('name', 'LIKE', $data['shipping_address']['country'])->first();

        if (!$country) {
            // Try to find by partial match
            $country = Country::where('name', 'LIKE', '%' . $data['shipping_address']['country'] . '%')->first();
        }

        if (!$country) {
            Log::error('Country not found', [
                'requested_country' => $data['shipping_address']['country'],
                'available_countries' => Country::pluck('name')->toArray()
            ]);
            throw new \Exception('Selected country "' . $data['shipping_address']['country'] . '" not found. Available countries: ' . implode(', ', Country::pluck('name')->toArray()));
        }

        Log::info('Country found', [
            'country_id' => $country->id,
            'country_name' => $country->name
        ]);

        return Customer::create([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone_number'],
            'country_id' => $country->id,
            'state' => $data['shipping_address']['state'] ?? null,
            'city' => $data['shipping_address']['city'] ?? null,
            'address' => $data['shipping_address']['address'] ?? null,
            //'zip' => $data['shipping_address']['postal_code'] ?? null,
        ]);
    }

    /**
     * Create order
     */
    protected function createOrder(array $data, Customer $customer, ?User $user, bool $isGuest)
    {
        $shippingAddress = $data['shipping_address'];
        $billingAddress = $data['billing_address'];

        // If use billing for shipping is checked, copy billing address
        if ($data['use_billing_for_shipping'] ?? false) {
            $shippingAddress = $billingAddress;
        }

        // Get country and currency information
        $country = Country::find($customer->country_id);
        $currencyCode = $country ? $country->currency_code : 'USD';

        // Convert prices to local currency
        $subtotal = $this->currencyService->convertFromUSD($this->cartService->getSubtotal(), $currencyCode);
        $taxAmount = $this->currencyService->convertFromUSD($this->cartService->getTaxAmount(), $currencyCode);
        $shippingAmount = $this->currencyService->convertFromUSD($this->cartService->getShippingCost(), $currencyCode);
        $totalAmount = $this->currencyService->convertFromUSD($this->cartService->getTotal(), $currencyCode);

        return Order::create([
            'order_number' => $this->generateOrderNumber(),
            'guest_token' => $isGuest ? Str::random(32) : null,
            'user_id' => $user?->id,
            'customer_id' => $customer->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? $user?->email,
            'phone_number' => $data['phone_number'] ?? $user?->phone,
            'country_id' => $customer->country_id,
            'state' => $customer->state,
            'city' => $customer->city,
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
            'loyalty_points_used' => 0,
            'billing_address' => json_encode($billingAddress),
            'shipping_address' => json_encode($shippingAddress),
        ]);
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
            $orderNumber = 'WF-' . date('Y') . '-' . strtoupper(Str::random(8));
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
        return response()->json([
            'method' => $request->method(),
            'all_data' => $request->all(),
            'shipping_address' => $request->input('shipping_address'),
            'billing_address' => $request->input('billing_address'),
            'payment_method' => $request->input('payment_method'),
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_number' => $request->input('phone_number'),
            'headers' => $request->headers->all()
        ]);
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
}
