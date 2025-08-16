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
Route::post('/checkout/guest', [CheckoutController::class, 'processGuestCheckout'])->name('checkout.guest');
Route::post('/checkout/authenticated', [CheckoutController::class, 'processAuthenticatedCheckout'])->name('checkout.authenticated');
Route::get('/checkout/currency', [CurrencyController::class, 'getCheckoutCurrency'])->name('checkout.currency');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'orderConfirmation'])->name('checkout.confirmation');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('thankyou');
Route::get('/checkout/test', [CheckoutController::class, 'testCheckout'])->name('checkout.test');
Route::post('/checkout/debug', [CheckoutController::class, 'debugForm'])->name('checkout.debug');

// Currency test routes
Route::get('/test/currency/{code}', function($code) {
    $service = app(\App\Services\CountryCurrencyService::class);
    $service->setPreferredCurrency($code);

    return response()->json([
        'success' => true,
        'currency_set' => $code,
        'session_data' => [
            'preferred_currency' => session('preferred_currency'),
            'currency_initialized' => session('currency_initialized'),
            'detected_country' => session('detected_country'),
            'detected_currency' => session('detected_currency'),
        ]
    ]);
})->name('test.currency');

//cart
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/{rowId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{rowId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/count', [CartController::class, 'count'])->name('cart.count');
});

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
