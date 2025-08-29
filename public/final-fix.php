<?php
// Create: public/final-fix.php

// Clear OPcache
if(function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared<br>";
}

// Clear stat cache
clearstatcache(true);

// Force load clean environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear all Laravel caches
\Artisan::call('config:clear');
\Artisan::call('cache:clear');
\Artisan::call('route:clear');
\Artisan::call('view:clear');

echo "All caches cleared<br>";
echo "APP_URL from env: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "<br>";
echo "APP_URL from config: " . config('app.url') . "<br>";

// Cache new config
\Artisan::call('config:cache');
echo "New config cached<br>";