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
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\Newsletter\VerifyController;
use App\Http\Controllers\Newsletter\UnsubscribeController;

Route::get('/', [FrontendController::class, 'index'])->name('home');
/*** Products Pages */
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collection/{collection:slug}', [CollectionController::class, 'show'])->name('collection.show');
Route::get('/categories', [CategoriesController::class, 'all'])->name('categories.all');
Route::get('/categories/{categorySlug?}', [CategoriesController::class, 'index'])->name('categories.index');

// Currency routes
Route::prefix('currency')->group(function () {
    Route::post('/change', [CurrencyController::class, 'changeCurrency'])->name('currency.change');
    Route::get('/current', [CurrencyController::class, 'getCurrentCurrency'])->name('currency.current');
    Route::post('/reset', [CurrencyController::class, 'resetToDetected'])->name('currency.reset');


});

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/guest', [CheckoutController::class, 'processGuestCheckout'])->name('checkout.guest');
 Route::post('/checkout/currency', [CurrencyController::class, 'updateCheckoutCountry'])->name('checkout.currency.update'); // Removed for Livewire-only approach
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'orderConfirmation'])->name('checkout.confirmation');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('thankyou');
Route::get('/checkout/test', [CheckoutController::class, 'testCheckout'])->name('checkout.test');
Route::post('/checkout/debug', [CheckoutController::class, 'debugForm'])->name('checkout.debug');
Route::post('/checkout/test-cod', [CheckoutController::class, 'testCodPayment'])->name('checkout.test-cod');
Route::get('/checkout/test-simple-cod', [CheckoutController::class, 'testSimpleCod'])->name('checkout.test-simple-cod');


// PayPal credit card payment route
Route::get('/checkout/paypal/credit-card/{payment}', [CheckoutController::class, 'showPayPalCreditCard'])->name('checkout.paypal.credit-card');
Route::post('/checkout/paypal/credit-card/{payment}/capture', [CheckoutController::class, 'capturePayPalCreditCard'])->name('checkout.paypal.credit-card.capture');

// PayPal webhook route
Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handleWebhook'])->name('paypal.webhook');

// Paymob callback URL - handles both GET and POST requests
Route::match(['get', 'post'], '/api/paymob/callback', [\App\Http\Controllers\PaymentController::class, 'handlePaymobCallback'])->name('paymob.callback');

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
//Terms
Route::get('/terms', [FrontendController::class, 'terms'])->name('terms');
//Privacy
Route::get('/privacy', [FrontendController::class, 'privacy'])->name('privacy');
//About
Route::get('/about', [FrontendController::class, 'about'])->name('about');
//Refund
Route::get('/return-policy', [FrontendController::class, 'return'])->name('return');
//Location
Route::get('/location', [FrontendController::class, 'location'])->name('location');
/*** Contact Page */
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*** Dashboard  */

Route::get('dashboard', \App\Livewire\Dashboard::class)
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


