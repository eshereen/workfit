<?php

use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\Newsletter\VerifyController;
use App\Http\Controllers\Newsletter\UnsubscribeController;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Currency routes
Route::prefix('currency')->group(function () {
    Route::post('/change', [CurrencyController::class, 'changeCurrency'])->name('currency.change');
    Route::get('/current', [CurrencyController::class, 'getCurrentCurrency'])->name('currency.current');
    Route::post('/reset', [CurrencyController::class, 'resetToDetected'])->name('currency.reset');
    Route::get('/test', function() {
        $currencyService = app(\App\Services\CountryCurrencyService::class);
        $usdAmount = 10.00;
        $egpAmount = $currencyService->convertFromUSD($usdAmount, 'EGP');
        $sarAmount = $currencyService->convertFromUSD($usdAmount, 'SAR');

        return response()->json([
            'test_amount_usd' => $usdAmount,
            'converted_to_egp' => $egpAmount,
            'converted_to_sar' => $sarAmount,
            'current_currency' => $currencyService->getCurrentCurrencyInfo()
        ]);
    })->name('currency.test');
});

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/guest', [CheckoutController::class, 'processGuestCheckout'])->name('checkout.guest');
// Route::post('/checkout/currency', [CurrencyController::class, 'updateCheckoutCountry'])->name('checkout.currency.update'); // Removed for Livewire-only approach
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'orderConfirmation'])->name('checkout.confirmation');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('thankyou');
Route::get('/checkout/test', [CheckoutController::class, 'testCheckout'])->name('checkout.test');
Route::post('/checkout/debug', [CheckoutController::class, 'debugForm'])->name('checkout.debug');
Route::post('/checkout/test-cod', [CheckoutController::class, 'testCodPayment'])->name('checkout.test-cod');
Route::get('/checkout/test-simple-cod', [CheckoutController::class, 'testSimpleCod'])->name('checkout.test-simple-cod');
Route::get('/checkout/test-direct', function() {
    return view('test-checkout');
})->name('checkout.test-direct');

// PayPal credit card payment route
Route::get('/checkout/paypal/credit-card/{payment}', [CheckoutController::class, 'showPayPalCreditCard'])->name('checkout.paypal.credit-card');
Route::post('/checkout/paypal/credit-card/{payment}/capture', [CheckoutController::class, 'capturePayPalCreditCard'])->name('checkout.paypal.credit-card.capture');

// PayPal webhook route
Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handleWebhook'])->name('paypal.webhook');

// Debug route for PayPal gateway testing
Route::get('/debug/paypal/{payment}', function(\App\Models\Payment $payment) {
    try {
        $gateway = app(\App\Payments\Gateways\PaypalGateway::class);

        // Test access token
        $tokenTest = $gateway->testAccessToken();

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'payment_provider' => $payment->provider,
            'payment_meta' => $payment->meta,
            'token_test' => $tokenTest,
            'gateway_class' => get_class($gateway)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.paypal');

// Test PayPal gateway directly
Route::get('/debug/paypal-test', function() {
    try {
        $gateway = app(\App\Payments\Gateways\PaypalGateway::class);
        
        // Test PayPal connectivity using the new method
        $result = $gateway->testConnectivity();
        
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.paypal-test');

// Debug payment data
Route::get('/debug/payment/{id}', function($id) {
    try {
        $payment = \App\Models\Payment::find($id);
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'error' => 'Payment not found'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'meta' => $payment->meta,
                'order_id' => $payment->order_id,
                'order_exists' => $payment->order ? true : false,
                'order_data' => $payment->order ? [
                    'id' => $payment->order->id,
                    'order_number' => $payment->order->order_number,
                    'total_amount' => $payment->order->total_amount,
                    'currency' => $payment->order->currency
                ] : null
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.payment');

// Debug route for payment methods
Route::get('/debug/payment-methods/{countryCode}', function($countryCode) {
    $resolver = app(\App\Services\PaymentMethodResolver::class);
    $methods = $resolver->availableForCountry($countryCode);
    $creditCardAvailable = $resolver->isCreditCardAvailableForCountry($countryCode);

    return response()->json([
        'country_code' => $countryCode,
        'methods' => array_map(fn($m) => $m->value, $methods),
        'credit_card_available' => $creditCardAvailable,
        'session_country' => session('checkout_country')
    ]);
})->name('debug.payment-methods');

// Debug route for Paymob callback testing
Route::get('/debug/paymob-orders', function() {
    $orders = \App\Models\Order::latest()->take(10)->get(['id', 'order_number', 'created_at']);
    $payments = \App\Models\Payment::latest()->take(10)->get(['id', 'order_id', 'provider', 'status', 'created_at']);
    
    return response()->json([
        'recent_orders' => $orders,
        'recent_payments' => $payments
    ]);
})->name('debug.paymob-orders');

// Debug route for testing Paymob order lookup
Route::get('/debug/paymob-lookup/{merchantOrderId}', function($merchantOrderId) {
    // Simulate the order lookup logic from the controller
    $order = null;
    $debug = [];
    
    // Try exact match first
    $order = \App\Models\Order::where('order_number', $merchantOrderId)->first();
    $debug['exact_match'] = [
        'merchant_order_id' => $merchantOrderId,
        'found' => $order ? 'yes' : 'no',
        'order_id' => $order ? $order->id : null
    ];
    
    // If not found, try partial match (remove Paymob's timestamp suffix)
    if (!$order) {
        $baseOrderNumber = preg_replace('/-\d+$/', '', $merchantOrderId);
        $order = \App\Models\Order::where('order_number', $baseOrderNumber)->first();
        $debug['base_match'] = [
            'base_order_number' => $baseOrderNumber,
            'found' => $order ? 'yes' : 'no',
            'order_id' => $order ? $order->id : null
        ];
    }
    
    // If still not found, try LIKE search
    if (!$order) {
        $baseOrderNumber = preg_replace('/-\d+$/', '', $merchantOrderId);
        $order = \App\Models\Order::where('order_number', 'LIKE', $baseOrderNumber . '%')->first();
        $debug['like_match'] = [
            'pattern' => $baseOrderNumber . '%',
            'found' => $order ? 'yes' : 'no',
            'order_id' => $order ? $order->id : null
        ];
    }
    
    return response()->json([
        'merchant_order_id' => $merchantOrderId,
        'debug' => $debug,
        'final_result' => $order ? [
            'found' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ] : [
            'found' => false
        ]
    ]);
})->name('debug.paymob-lookup');

// Fixed callback URL for Paymob (since {order_id} placeholder isn't working)
Route::get('/api/paymob/callback', [\App\Http\Controllers\PaymentController::class, 'handlePaymobCallback'])->name('paymob.callback');

// Simple test route to verify the controller method works
Route::get('/paymob/test', function() {
    return response()->json([
        'message' => 'Paymob test route working',
        'controller_exists' => class_exists(\App\Http\Controllers\PaymentController::class),
        'method_exists' => method_exists(\App\Http\Controllers\PaymentController::class, 'handlePaymobCallback'),
        'timestamp' => now()
    ]);
})->name('paymob.test');

// Test route for Paymob callback
Route::get('/test-paymob-callback', function() {
    return response()->json([
        'message' => 'Paymob callback route is working!',
        'timestamp' => now(),
        'route_name' => 'paymob.callback'
    ]);
})->name('test.paymob.callback');

// Payment webhooks
Route::post('/payments/webhook/{gateway}', [\App\Http\Controllers\PaymentController::class, 'handleWebhook'])->name('webhook.gateway');

// Payment return and cancel (must come AFTER specific routes)
Route::get('/payments/return/{order}', [\App\Http\Controllers\PaymentController::class, 'handleReturn'])->name('payments.return');
Route::get('/payments/cancel/{order}', [\App\Http\Controllers\PaymentController::class, 'handleCancel'])->name('payments.cancel');

// Paymob callbacks now handled by standard routes:
// - Webhook: /payments/webhook/paymob (handles payment status updates)
// - Response: /payments/return/{order_id} (handles user redirects)





//cart
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{rowId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/count', [CartController::class, 'count'])->name('cart.count');
});
Route::get('/newsletter/verify', VerifyController::class)->name('newsletter.verify');
Route::get('/newsletter/unsubscribe', UnsubscribeController::class)->name('newsletter.unsubscribe');
Route::get('/terms', [FrontendController::class, 'terms'])->name('terms');
Route::get('/privacy', [FrontendController::class, 'privacy'])->name('privacy');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Wishlist route (Livewire handles the functionality)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
});

require __DIR__.'/auth.php';

// Debug route for checking order items
Route::get('/debug/order-items/{orderId}', function($orderId) {
    try {
        $order = \App\Models\Order::with(['items.product', 'items.variant'])->find($orderId);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found']);
        }
        
        $items = $order->items;
        $itemsData = [];
        
        foreach ($items as $item) {
            $itemsData[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name ?? 'N/A',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'variant_id' => $item->variant_id,
                'variant_info' => $item->variant ? $item->variant->color . ', ' . $item->variant->size : 'N/A',
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        }
        
        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_items' => $items->count(),
            'items' => $itemsData,
            'duplicate_check' => [
                'product_ids' => $items->pluck('product_id')->toArray(),
                'unique_product_ids' => $items->pluck('product_id')->unique()->toArray(),
                'has_duplicates' => $items->pluck('product_id')->count() !== $items->pluck('product_id')->unique()->count()
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.order-items');
