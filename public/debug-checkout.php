<?php
// Debug script for checkout issues
// Access this via: yourdomain.com/debug-checkout.php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

echo "<h1>Checkout Debug Information</h1>";

// Check Laravel environment
echo "<h2>1. Laravel Environment</h2>";
echo "App Environment: " . (app()->environment()) . "<br>";
echo "App Debug: " . (config('app.debug') ? 'true' : 'false') . "<br>";
echo "App URL: " . config('app.url') . "<br>";

// Check database connection
echo "<h2>2. Database Connection</h2>";
try {
    $pdo = DB::connection()->getPdo();
    echo "Database: Connected successfully<br>";
    echo "Database Name: " . DB::connection()->getDatabaseName() . "<br>";
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
}

// Check Paymob configuration
echo "<h2>3. Paymob Configuration</h2>";
echo "PAYMOB_API_KEY: " . (env('PAYMOB_API_KEY') ? 'Set' : 'Not Set') . "<br>";
echo "PAYMOB_INTEGRATION_ID_CARD: " . (env('PAYMOB_INTEGRATION_ID_CARD') ? 'Set' : 'Not Set') . "<br>";
echo "PAYMOB_IFRAME_ID: " . (env('PAYMOB_IFRAME_ID') ? 'Set' : 'Not Set') . "<br>";

// Check PayPal configuration
echo "<h2>4. PayPal Configuration</h2>";
echo "PAYPAL_CLIENT_ID: " . (env('PAYPAL_CLIENT_ID') ? 'Set' : 'Not Set') . "<br>";
echo "PAYPAL_CLIENT_SECRET: " . (env('PAYPAL_CLIENT_SECRET') ? 'Set' : 'Not Set') . "<br>";
echo "PAYPAL_MODE: " . (env('PAYPAL_MODE', 'Not Set')) . "<br>";

// Check session configuration
echo "<h2>5. Session Configuration</h2>";
echo "Session Driver: " . config('session.driver') . "<br>";
echo "Session Lifetime: " . config('session.lifetime') . "<br>";

// Check file permissions
echo "<h2>6. File Permissions</h2>";
$storagePath = storage_path();
echo "Storage Path: " . $storagePath . "<br>";
echo "Storage Writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "<br>";

$logsPath = storage_path('logs');
echo "Logs Path: " . $logsPath . "<br>";
echo "Logs Writable: " . (is_writable($logsPath) ? 'Yes' : 'No') . "<br>";

// Check if checkout route exists
echo "<h2>7. Route Check</h2>";
try {
    $routes = Route::getRoutes();
    $checkoutRoute = null;
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'checkout') !== false) {
            $checkoutRoute = $route;
            break;
        }
    }
    echo "Checkout Route: " . ($checkoutRoute ? 'Found' : 'Not Found') . "<br>";
} catch (Exception $e) {
    echo "Route Error: " . $e->getMessage() . "<br>";
}

// Check recent errors
echo "<h2>8. Recent Errors</h2>";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $recentLogs = file_get_contents($logFile);
    $lines = explode("\n", $recentLogs);
    $recentErrors = array_slice($lines, -20);
    echo "Last 20 log lines:<br>";
    echo "<pre>";
    foreach ($recentErrors as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "Log file not found<br>";
}

echo "<h2>9. PHP Information</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";

echo "<h2>10. Test Session Storage</h2>";
try {
    session(['test_key' => 'test_value']);
    $testValue = session('test_key');
    echo "Session Test: " . ($testValue === 'test_value' ? 'Working' : 'Failed') . "<br>";
    session()->forget('test_key');
} catch (Exception $e) {
    echo "Session Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after debugging for security.</p>";
?>
