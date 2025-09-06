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

Route::get('/', [FrontendController::class, 'home_index'])->name('home');
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


// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// EMERGENCY DEBUG ROUTES - NO CSRF PROTECTION
Route::get('/debug/session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'session_token' => session()->token(),
        'session_data' => session()->all(),
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_same_site' => config('session.same_site'),
        'app_env' => app()->environment(),
        'server_vars' => [
            'HTTP_HOST' => request()->getHost(),
            'HTTPS' => request()->isSecure(),
            'REQUEST_URI' => request()->getRequestUri(),
            'USER_AGENT' => request()->userAgent(),
        ]
    ]);
})->name('debug.session');

Route::get('/debug/middleware', function () {
    return response()->json([
        'livewire_csrf_middleware_exists' => class_exists(\App\Http\Middleware\LivewireCSRFMiddleware::class),
        'csrf_middleware_class' => \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        'livewire_csrf_file_exists' => file_exists(app_path('Http/Middleware/LivewireCSRFMiddleware.php')),
        'verify_csrf_token_exists' => file_exists(app_path('Http/Middleware/VerifyCsrfToken.php')),
        'verify_csrf_token_content' => file_exists(app_path('Http/Middleware/VerifyCsrfToken.php')) ? file_get_contents(app_path('Http/Middleware/VerifyCsrfToken.php')) : 'FILE NOT FOUND',
        'bootstrap_app_content' => file_get_contents(base_path('bootstrap/app.php')),
        'middleware_replaced' => str_contains(file_get_contents(base_path('bootstrap/app.php')), 'LivewireCSRFMiddleware'),
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
    ]);
})->name('debug.middleware');

// EMERGENCY TEST - NO CSRF AT ALL
Route::any('/debug/no-csrf-test', function () {
    \Log::info('NO CSRF Test Hit - This should always work');
    return response()->json([
        'success' => true,
        'message' => 'This endpoint has NO CSRF protection',
        'method' => request()->method(),
        'timestamp' => now()->toISOString(),
    ]);
})->name('debug.no-csrf');

Route::post('/debug/csrf-test', function () {
    try {
        \Log::info('CSRF Test Endpoint Hit', [
            'csrf_token_from_session' => csrf_token(),
            'csrf_token_from_header' => request()->header('X-CSRF-TOKEN'),
            'session_id' => session()->getId(),
            'all_headers' => request()->headers->all(),
            'method' => request()->method()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CSRF token is working correctly',
            'csrf_token' => csrf_token(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
            'request_token' => request()->header('X-CSRF-TOKEN'),
            'tokens_match' => hash_equals(csrf_token(), request()->header('X-CSRF-TOKEN')),
        ]);
    } catch (\Exception $e) {
        \Log::error('CSRF Test Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'csrf_token' => csrf_token(),
        ], 500);
    }
})->name('debug.csrf');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
Route::post('/checkout/guest', [CheckoutController::class, 'processGuestCheckout'])->name('checkout.guest');

// ****Other checkout routes, organized by payment method****
// PayPal routes
Route::prefix('checkout/paypal')->group(function () {
    Route::post('/create', [\App\Http\Controllers\PayPalController::class, 'createOrder'])->name('checkout.paypal.create');
    Route::post('/capture/{payment}', [\App\Http\Controllers\PayPalController::class, 'captureOrder'])->name('checkout.paypal.capture');
    Route::get('/success/{payment}', [\App\Http\Controllers\PayPalController::class, 'success'])->name('checkout.paypal.success');
    Route::get('/cancel/{payment}', [\App\Http\Controllers\PayPalController::class, 'cancel'])->name('checkout.paypal.cancel');
});

// PayPal Credit Card routes
Route::prefix('checkout/paypal-credit-card')->group(function () {
    Route::get('/form/{payment}', [\App\Http\Controllers\PayPalCreditCardController::class, 'showForm'])->name('checkout.paypal.credit-card.form');
    Route::post('/capture/{payment}', [\App\Http\Controllers\PayPalCreditCardController::class, 'captureOrder'])->name('checkout.paypal.credit-card.capture');
    Route::get('/success/{payment}', [\App\Http\Controllers\PayPalCreditCardController::class, 'success'])->name('checkout.paypal.credit-card.success');
    Route::get('/cancel/{payment}', [\App\Http\Controllers\PayPalCreditCardController::class, 'cancel'])->name('checkout.paypal.credit-card.cancel');
});

// Thank you page route (used by PaymentReturnController)
Route::get('/thankyou/{order}', [\App\Http\Controllers\CheckoutController::class, 'thankYou'])->name('thankyou');

// Checkout thank you page route
Route::get('/checkout/thank-you/{order}', [\App\Http\Controllers\CheckoutController::class, 'thankYou'])->name('checkout.thank-you');

// Checkout success and failure routes
Route::get('/checkout/success/{order}', function ($orderNumber) {
    $order = \App\Models\Order::where('order_number', $orderNumber)->first();
    if (!$order) {
        abort(404, 'Order not found');
    }
    return view('checkout.success', compact('order'));
})->name('checkout.success');

Route::get('/checkout/failure/{order}', function ($orderNumber) {
    $order = \App\Models\Order::where('order_number', $orderNumber)->first();
    if (!$order) {
        abort(404, 'Order not found');
    }
    return view('checkout.failure', compact('order'));
})->name('checkout.failure');

// Paymob routes (removed - PaymobController doesn't exist, using PaymentReturnController instead)

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
Route::get('/products',[ProductController::class,'index'])->name('products.index');
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
    Route::get('/profile', Profile::class)->name('settings.profile');
    Route::get('/profile/password', Password::class)->name('settings.password');
    Route::get('/profile/appearance', Appearance::class)->name('settings.appearance');
    Route::get('/wishlist',\App\Livewire\WishlistIndex::class)->name('wishlist.index');
    // Route::get('/orders',\App\Livewire\OrdersIndex::class)->name('orders');
    // Route::get('/order/{order}',\App\Livewire\OrderView::class)->name('order.view');
});

// Clear all caches route
Route::get('/debug/clear-all-cache', function () {
    try {
        // Clear Laravel caches
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        // Clear specific homepage cache keys
        $homepage_keys = [
            'home_categories',
            'home_men_category',
            'home_women_category',
            'home_recent_products',
            'home_collections',
            'home_featured_products'
        ];

        $keys_cleared = [];
        foreach ($homepage_keys as $key) {
            if (cache()->forget($key)) {
                $keys_cleared[] = $key;
            }
        }

        // Clear OPcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $opcache_cleared = 'YES';
        } else {
            $opcache_cleared = 'NOT AVAILABLE';
        }

        // Clear APCu cache if available
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
            $apcu_cleared = 'YES';
        } else {
            $apcu_cleared = 'NOT AVAILABLE';
        }

        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully',
            'cleared' => [
                'laravel_cache' => 'YES',
                'route_cache' => 'YES',
                'config_cache' => 'YES',
                'view_cache' => 'YES',
                'opcache' => $opcache_cleared,
                'apcu_cache' => $apcu_cleared,
            ],
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
})->name('debug.clear-cache');

// Debug route to check homepage data on live server
Route::get('/debug/homepage-data', function () {
    // Test men's category (your live server has different slug)
    $men = \App\Models\Category::where('categories.active', true)
        ->where(function($query) {
            $query->where('categories.slug', 'men ')
                  ->orWhere('categories.slug', 'men')
                  ->orWhere('categories.name', 'LIKE', '%men%')
                  ->orWhere('categories.name', 'LIKE', '%MEN%');
        })
        ->with(['directProducts' => function ($query) {
            $query->where('products.active', true)->take(8);
        }])
        ->first();

    // Test women's category
    $women = \App\Models\Category::where('categories.active', true)
        ->where('categories.slug', 'women')
        ->with(['directProducts' => function ($query) {
            $query->where('products.active', true)->take(8);
        }])
        ->first();

    return response()->json([
        'men' => [
            'category' => $men ? $men->name . ' (ID: ' . $men->id . ')' : 'Not found',
            'products_count' => $men ? $men->directProducts->count() : 0,
            'products' => $men ? $men->directProducts->map(function($p) {
                return $p->name . ' (Category ID: ' . $p->category_id . ')';
            }) : []
        ],
        'women' => [
            'category' => $women ? $women->name . ' (ID: ' . $women->id . ')' : 'Not found',
            'products_count' => $women ? $women->directProducts->count() : 0,
            'products' => $women ? $women->directProducts->map(function($p) {
                return $p->name . ' (Category ID: ' . $p->category_id . ')';
            }) : []
        ],
        'files_updated' => [
            'category_model' => method_exists(\App\Models\Category::class, 'directProducts') ? 'YES' : 'NO',
            'controller_exists' => file_exists(app_path('Http/Controllers/FrontendController.php')) ? 'YES' : 'NO',
            'view_exists' => file_exists(resource_path('views/home.blade.php')) ? 'YES' : 'NO',
        ],
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'environment' => app()->environment(),
        'cache_cleared' => true
    ]);
})->name('debug.homepage-data');

// Test actual controller data without cache
Route::get('/debug/controller-test', function () {
    // Simulate exact controller logic without cache
    $men = \App\Models\Category::where('categories.active', true)
        ->where(function($query) {
            $query->where('categories.slug', 'men ')
                  ->orWhere('categories.slug', 'men')
                  ->orWhere('categories.name', 'LIKE', '%men%')
                  ->orWhere('categories.name', 'LIKE', '%MEN%');
        })
        ->with(['directProducts' => function ($query) {
            $query->with(['media' => function ($q) {
                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                      ->where('collection_name', 'main_image')
                      ->whereNotNull('disk')
                      ->limit(1);
                }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                ->where('products.active', true)
                ->take(8);
        }])
        ->first();

    $women = \App\Models\Category::where('categories.active', true)
        ->where(function($query) {
            $query->where('categories.slug', 'women')
                  ->orWhere('categories.slug', 'women-1')
                  ->orWhere('categories.name', 'LIKE', '%women%')
                  ->orWhere('categories.name', 'LIKE', '%Women%');
        })
        ->with(['directProducts' => function ($query) {
            $query->with(['media' => function ($q) {
                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                      ->where('collection_name', 'main_image')
                      ->whereNotNull('disk')
                      ->limit(1);
                }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                ->where('products.active', true)
                ->take(8);
        }])
        ->first();

    return response()->json([
        'men' => [
            'found' => $men ? 'YES' : 'NO',
            'category' => $men ? $men->name . ' (ID: ' . $men->id . ')' : 'Not found',
            'products_count' => $men ? $men->directProducts->count() : 0,
            'products' => $men ? $men->directProducts->map(function($p) {
                return $p->name . ' (Category ID: ' . $p->category_id . ')';
            }) : []
        ],
        'women' => [
            'found' => $women ? 'YES' : 'NO',
            'category' => $women ? $women->name . ' (ID: ' . $women->id . ')' : 'Not found',
            'products_count' => $women ? $women->directProducts->count() : 0,
            'products' => $women ? $women->directProducts->map(function($p) {
                return $p->name . ' (Category ID: ' . $p->category_id . ')';
            }) : []
        ],
        'note' => 'This tests the exact controller logic without cache',
        'timestamp' => now()->format('Y-m-d H:i:s'),
    ]);
})->name('debug.controller-test');

require __DIR__.'/auth.php';
