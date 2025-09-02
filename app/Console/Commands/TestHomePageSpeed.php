<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\FrontendController;

class TestHomePageSpeed extends Command
{
    protected $signature = 'test:homepage-speed';
    protected $description = 'Test home page loading speed';

    public function handle()
    {
        $this->info('Testing home page loading speed...');

        // Clear query log
        DB::enableQueryLog();

        // Measure time
        $startTime = microtime(true);

        // Simulate home page load
        $this->simulateHomePageLoad();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Get query log
        $queries = DB::getQueryLog();
        
        $this->info("Execution time: " . round($executionTime, 2) . "ms");
        $this->info("Total queries executed: " . count($queries));
        
        // Performance rating
        if ($executionTime < 100) {
            $this->info("Performance: 🟢 EXCELLENT (< 100ms)");
        } elseif ($executionTime < 500) {
            $this->info("Performance: 🟡 GOOD (100-500ms)");
        } elseif ($executionTime < 1000) {
            $this->info("Performance: 🟠 FAIR (500ms-1s)");
        } else {
            $this->info("Performance: 🔴 POOR (> 1s)");
        }

        // Group queries by table
        $queryGroups = [];
        foreach ($queries as $query) {
            $sql = $query['sql'];
            if (preg_match('/FROM\s+`?(\w+)`?/i', $sql, $matches)) {
                $table = $matches[1];
                $queryGroups[$table] = ($queryGroups[$table] ?? 0) + 1;
            }
        }

        $this->info("\nQueries by table:");
        foreach ($queryGroups as $table => $count) {
            $this->line("  {$table}: {$count} queries");
        }

        // Check cache status
        $this->info("\nCache status:");
        $cacheKeys = [
            'home_categories',
            'home_featured_products',
            'home_men_category',
            'home_women_category',
            'home_kids_category'
        ];

        foreach ($cacheKeys as $key) {
            $exists = Cache::has($key);
            $this->line("  {$key}: " . ($exists ? 'CACHED' : 'NOT CACHED'));
        }

        $this->info("\nSpeed test completed!");
    }

    private function simulateHomePageLoad()
    {
        // Simulate the optimized queries from FrontendController
        $categories = cache()->remember('home_categories', 1800, function () {
            return Category::withCount(['products' => function ($query) {
                $query->where('active', true);
            }])->where('active', true)->take(4)->get();
        });

        $featured = cache()->remember('home_featured_products', 900, function () {
            return Product::with(['category:id,name,slug', 'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                      ->whereIn('collection_name', ['main_image'])
                      ->whereNotNull('disk')
                      ->limit(1);
            }])
            ->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured', 'created_at')
            ->where('active', true)
            ->where('featured', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        });

        $men = cache()->remember('home_men_category', 1800, function () {
            return Category::with(['products' => function ($query) {
                $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                      ->where('active', true)
                      ->with(['media' => function ($q) {
                          $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                            ->whereIn('collection_name', ['main_image'])
                            ->whereNotNull('disk')
                            ->limit(1);
                      }])
                      ->take(8);
            }])->where('name', 'Men')->first();
        });

        $women = cache()->remember('home_women_category', 1800, function () {
            return Category::with(['products' => function ($query) {
                $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                      ->where('active', true)
                      ->with(['media' => function ($q) {
                          $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                            ->whereIn('collection_name', ['main_image'])
                            ->whereNotNull('disk')
                            ->limit(1);
                      }])
                      ->take(8);
            }])->where('name', 'Women')->first();
        });

        $kids = cache()->remember('home_kids_category', 1800, function () {
            return Category::with(['products' => function ($query) {
                $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                      ->where('active', true)
                      ->with(['media' => function ($q) {
                          $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                            ->whereIn('collection_name', ['main_image'])
                            ->whereNotNull('disk')
                            ->limit(1);
                      }])
                      ->take(8);
            }])->where('name', 'Kids')->first();
        });
    }
}
