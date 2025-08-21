<?php

use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\Newsletter\VerifyController;
use App\Http\Controllers\Newsletter\UnsubscribeController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\PayPalWebhookController;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

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
Route::post('/checkout', [CheckoutController::class, 'processAuthenticatedCheckout'])->name('checkout.process');
Route::post('/checkout/guest', [CheckoutController::class, 'processGuestCheckout'])->name('checkout.guest');
// Route::post('/checkout/currency', [CurrencyController::class, 'updateCheckoutCountry'])->name('checkout.currency.update'); // Removed for Livewire-only approach
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'orderConfirmation'])->name('checkout.confirmation');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('thankyou');
Route::get('/checkout/test', [CheckoutController::class, 'testCheckout'])->name('checkout.test');
Route::post('/checkout/debug', [CheckoutController::class, 'debugForm'])->name('checkout.debug');

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

// Payment webhooks
Route::post('/payments/webhook/{gateway}', [\App\Http\Controllers\PaymentController::class, 'handleWebhook'])->name('webhook.gateway');

// Payment return and cancel
Route::get('/payments/return/{order}', [\App\Http\Controllers\PaymentController::class, 'handleReturn'])->name('payments.return');
Route::get('/payments/cancel/{order}', [\App\Http\Controllers\PaymentController::class, 'handleCancel'])->name('payments.cancel');





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
