<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;

class OptimizePerformance extends Command
{
    protected $signature = 'optimize:performance';
    protected $description = 'Optimize application performance';

    public function handle()
    {
        $this->info('Starting performance optimization...');

        // Clear all caches
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        // Warm up critical caches
        $this->warmUpCaches();

        // Optimize database queries
        $this->optimizeDatabase();

        $this->info('Performance optimization completed!');
    }

    private function warmUpCaches()
    {
        $this->info('Warming up caches...');

        // Cache header categories
        $categories = Category::where('active', true)
            ->withCount(['products' => function ($query) {
                $query->where('active', true);
            }])
            ->orderBy('name')
            ->take(4)
            ->get()
            ->map(function ($category) {
                $category->media_url = $category->getFirstMediaUrl('main_image', 'small_webp');
                return $category;
            });

        Cache::put('header_categories', $categories, 1800);

        // Cache product image data
        $products = Product::where('active', true)->take(20)->get();
        foreach ($products as $product) {
            $imageData = app('App\Services\ImageOptimizationService')->getProductImageData($product);
            Cache::put("product_image_data_{$product->id}", $imageData, 1800);
        }

        $this->info('Caches warmed up successfully!');
    }

    private function optimizeDatabase()
    {
        $this->info('Optimizing database...');

        // Analyze tables for better query planning
        DB::statement('ANALYZE TABLE products');
        DB::statement('ANALYZE TABLE categories');
        DB::statement('ANALYZE TABLE product_variants');

        $this->info('Database optimization completed!');
    }
}
