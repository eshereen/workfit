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
use App\Observers\ProductObserver;
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
        Product::observe(ProductObserver::class);

        // Share categories with all views - Optimized with caching and eager loading
        View::composer('*', function ($view) {
            // Quick check: skip if no data exists
            $hasCategories = cache()->remember('has_categories', 3600, function () {
                try {
                    return Category::exists();
                } catch (\Exception $e) {
                    return false;
                }
            });

            if (!$hasCategories) {
                $view->with('categories', collect([]));
                $view->with('collections', collect([]));
                return;
            }

            try {
                $categories = cache()->remember('header_categories', 1800, function () {
                    try {
                        return Category::where('categories.active', true)
                            ->with(['media', 'subcategories' => function ($query) {
                                $query->where('subcategories.active', true)->orderBy('name');
                            }])
                            ->withCount(['products' => function ($query) {
                                $query->where('products.active', true);
                            }])
                            ->orderBy('name')
                            ->take(4)
                            ->get()
                            ->map(function ($category) {
                                $category->media_url = $category->getFirstMediaUrl('main_image', 'small_webp');
                                return $category;
                            });
                    } catch (\Exception $e) {
                        Log::warning('Failed to load header categories', [
                            'error' => $e->getMessage()
                        ]);
                        return collect([]);
                    }
                });
            } catch (\Exception $e) {
                $categories = collect([]);
            }

            // Get collections for dropdown
            try {
                $collections = cache()->remember('header_collections', 1800, function () {
                    try {
                        return \App\Models\Collection::where('collections.active', true)
                            ->withCount(['products' => function ($query) {
                                $query->where('products.active', true);
                            }])
                            ->orderBy('name')
                            ->get();
                    } catch (\Exception $e) {
                        Log::warning('Failed to load header collections', [
                            'error' => $e->getMessage()
                        ]);
                        return collect([]);
                    }
                });
            } catch (\Exception $e) {
                $collections = collect([]);
            }

            $view->with('categories', $categories);
            $view->with('collections', $collections);
        });

        // Share all categories for category pages - Cached separately
        View::composer(['livewire.categories-grid', 'livewire.category-products'], function ($view) {
            try {
                $allCategories = cache()->remember('all_categories', 900, function () {
                    try {
                        return Category::where('categories.active', true)
                            ->with(['media'])
                            ->withCount(['products' => function ($query) {
                                $query->where('products.active', true);
                            }])
                            ->with(['subcategories' => function ($query) {
                                $query->where('subcategories.active', true);
                            }])
                            ->orderBy('name')
                            ->get()
                            ->map(function ($category) {
                                $category->media_url = $category->getFirstMediaUrl('main_image', 'small_webp');
                                return $category;
                            });
                    } catch (\Exception $e) {
                        Log::warning('Failed to load all categories', [
                            'error' => $e->getMessage()
                        ]);
                        return collect([]);
                    }
                });
            } catch (\Exception $e) {
                $allCategories = collect([]);
            }

            $view->with('allCategories', $allCategories);
        });

        // Cache clearing events for categories
        Event::listen(['eloquent.created: App\Models\Category', 'eloquent.updated: App\Models\Category', 'eloquent.deleted: App\Models\Category'], function () {
            Cache::forget('header_categories');
            Cache::forget('all_categories');
        });

        // Cache clearing events for collections
        Event::listen(['eloquent.created: App\Models\Collection', 'eloquent.updated: App\Models\Collection', 'eloquent.deleted: App\Models\Collection'], function () {
            Cache::forget('header_collections');
        });

        // Cache clearing events for subcategories
        Event::listen(['eloquent.created: App\Models\Subcategory', 'eloquent.updated: App\Models\Subcategory', 'eloquent.deleted: App\Models\Subcategory'], function () {
            Cache::forget('header_categories');
            Cache::forget('all_categories');
        });

        // Cache clearing events for products (affects category counts)
        Event::listen(['eloquent.created: App\Models\Product', 'eloquent.updated: App\Models\Product', 'eloquent.deleted: App\Models\Product'], function () {
            Cache::forget('header_categories');
            Cache::forget('all_categories');
            Cache::forget('header_collections');
        });

        // Cache clearing events for orders and order items (affects best seller calculations)
        Event::listen(['eloquent.created: App\Models\Order', 'eloquent.updated: App\Models\Order', 'eloquent.deleted: App\Models\Order'], function () {
            // Clear best seller cache when orders change
            $bestSellerService = app(\App\Services\BestSellerService::class);
            $bestSellerService->clearCache();
        });

        Event::listen(['eloquent.created: App\Models\OrderItem', 'eloquent.updated: App\Models\OrderItem', 'eloquent.deleted: App\Models\OrderItem'], function () {
            // Clear best seller cache when order items change
            $bestSellerService = app(\App\Services\BestSellerService::class);
            $bestSellerService->clearCache();
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
