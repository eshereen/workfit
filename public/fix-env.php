<?php
// Create: public/fix-env.php
require_once __DIR__.'/../vendor/autoload.php';

// Manually load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

echo "Environment variables loaded:<br>";
echo "APP_URL: " . $_ENV['APP_URL'] . "<br>";
echo "APP_ENV: " . $_ENV['APP_ENV'] . "<br>";

// Now bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear all caches
\Artisan::call('config:clear');
\Artisan::call('cache:clear');
\Artisan::call('route:clear');
\Artisan::call('view:clear');

echo "<br>Caches cleared<br>";

// Force set the config
config(['app.url' => $_ENV['APP_URL']]);
config(['app.env' => $_ENV['APP_ENV']]);
config(['app.debug' => $_ENV['APP_DEBUG'] === 'true']);

echo "Config updated:<br>";
echo "APP_URL: " . config('app.url') . "<br>";
echo "APP_ENV: " . config('app.env') . "<br>";

// Cache the new configuration
\Artisan::call('config:cache');
echo "<br>New config cached successfully!<br>";
echo "<br><a href='/'>Go to homepage</a>";