<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use App\Events\OrderPlaced;
use App\Events\PaymentStatusChanged;
use App\Listeners\AwardLoyaltyPoints;
use App\Listeners\HandlePaymentStatusChange;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            AwardLoyaltyPoints::class,
        ],
        PaymentStatusChanged::class => [
            HandlePaymentStatusChange::class,
        ],
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Schema::defaultStringLength(191);
        // Temporarily disabled to debug 502 on checkout (emails in observer)
         Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        // Share categories with all views - Optimized with caching and eager loading
        View::composer('*', function ($view) {
            $categories = cache()->remember('header_categories', 1800, function () {
                return Category::where('active', true)
                    ->withCount(['products' => function ($query) {
                        $query->where('active', true);
                    }])
                    ->orderBy('name')
                    ->take(4)
                    ->get()
                    ->map(function ($category) {
                        // Add media URL to avoid N+1 queries
                        $category->media_url = $category->getFirstMediaUrl('main_image', 'small_webp');
                        return $category;
                    });
            });

            $view->with('categories', $categories);
        });

        // Share all categories for category pages - Cached separately
        View::composer(['livewire.categories-grid', 'livewire.category-products'], function ($view) {
            $allCategories = cache()->remember('all_categories', 900, function () {
                return Category::where('active', true)
                    ->withCount(['products' => function ($query) {
                        $query->where('active', true);
                    }])
                    ->with(['subcategories' => function ($query) {
                        $query->where('active', true);
                    }])
                    ->orderBy('name')
                    ->get()
                    ->map(function ($category) {
                        // Add media URL to avoid N+1 queries
                        $category->media_url = $category->getFirstMediaUrl('main_image', 'small_webp');
                        return $category;
                    });
            });

            $view->with('allCategories', $allCategories);
        });

        // Cache clearing events for categories
        Event::listen(['eloquent.created: App\Models\Category', 'eloquent.updated: App\Models\Category', 'eloquent.deleted: App\Models\Category'], function () {
            Cache::forget('header_categories');
            Cache::forget('all_categories');
        });

        // Cache clearing events for products (affects category counts)
        Event::listen(['eloquent.created: App\Models\Product', 'eloquent.updated: App\Models\Product', 'eloquent.deleted: App\Models\Product'], function () {
            Cache::forget('header_categories');
            Cache::forget('all_categories');
        });

        // Optimize database queries with query logging in development
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log slow queries (>100ms)
                    Log::info('Slow Query: ' . $query->sql, [
                        'time' => $query->time,
                        'bindings' => $query->bindings
                    ]);
                }
            });
        }
    }
}
