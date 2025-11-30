<?php
/**
 * Performance Optimization Script for WorkFit
 * Run this on your server: php optimize-performance.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== WorkFit Performance Optimization ===\n\n";

// 1. Clear all caches
echo "1. Clearing all caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Config cache cleared\n";

    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✅ Route cache cleared\n";

    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✅ View cache cleared\n";

    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✅ Application cache cleared\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 2. Optimize for production
echo "2. Optimizing for production...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    echo "   ✅ Config cached\n";

    \Illuminate\Support\Facades\Artisan::call('route:cache');
    echo "   ✅ Routes cached\n";

    \Illuminate\Support\Facades\Artisan::call('view:cache');
    echo "   ✅ Views cached\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Check database connection
echo "3. Checking database connection...\n";
try {
    $start = microtime(true);
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✅ Database connected ({$time}ms)\n";

    if ($time > 100) {
        echo "   ⚠️  Warning: Database connection is slow (>{$time}ms)\n";
        echo "   Tip: Check if database is on same server\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Check table counts
echo "4. Checking database tables...\n";
try {
    $categories = \App\Models\Category::count();
    $products = \App\Models\Product::count();
    $collections = \App\Models\Collection::count();

    echo "   Categories: {$categories}\n";
    echo "   Products: {$products}\n";
    echo "   Collections: {$collections}\n";

    if ($categories === 0 && $products === 0) {
        echo "   ℹ️  Database is empty - homepage will load faster now\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Check APP_DEBUG setting
echo "5. Checking environment settings...\n";
$debug = config('app.debug');
$env = config('app.env');
echo "   APP_ENV: {$env}\n";
echo "   APP_DEBUG: " . ($debug ? 'true' : 'false') . "\n";

if ($debug && $env === 'production') {
    echo "   ⚠️  WARNING: APP_DEBUG is enabled in production!\n";
    echo "   Set APP_DEBUG=false in .env for better performance\n";
}
echo "\n";

// 6. Check session driver
echo "6. Checking session configuration...\n";
$sessionDriver = config('session.driver');
echo "   SESSION_DRIVER: {$sessionDriver}\n";

if ($sessionDriver === 'file') {
    echo "   ℹ️  Using file sessions (OK for small sites)\n";
    echo "   Tip: Use 'database' or 'redis' for better performance\n";
}
echo "\n";

// 7. Check cache driver
echo "7. Checking cache configuration...\n";
$cacheDriver = config('cache.default');
echo "   CACHE_DRIVER: {$cacheDriver}\n";

if ($cacheDriver === 'file') {
    echo "   ℹ️  Using file cache (OK for small sites)\n";
    echo "   Tip: Use 'redis' or 'memcached' for better performance\n";
}
echo "\n";

// 8. Performance recommendations
echo "8. Performance Recommendations:\n";
echo "   [ ] Set APP_DEBUG=false in production\n";
echo "   [ ] Set APP_ENV=production\n";
echo "   [ ] Enable OPcache in PHP\n";
echo "   [ ] Use Redis/Memcached for cache\n";
echo "   [ ] Use database/redis for sessions\n";
echo "   [ ] Enable Gzip compression\n";
echo "   [ ] Optimize images (WebP format)\n";
echo "   [ ] Use CDN for static assets\n";
echo "\n";

echo "=== Optimization Complete ===\n";
echo "Test your site now - it should be faster!\n";

