<?php
// Simple debug script for checkout issues
// Access this via: yourdomain.com/simple-debug.php

echo "<h1>Simple Checkout Debug Information</h1>";

// Check if .env file exists
echo "<h2>1. Environment File</h2>";
$envFile = __DIR__ . '/../.env';
echo "ENV File Exists: " . (file_exists($envFile) ? 'Yes' : 'No') . "<br>";

// Check if .env file is readable
if (file_exists($envFile)) {
    echo "ENV File Readable: " . (is_readable($envFile) ? 'Yes' : 'No') . "<br>";

    // Read .env file and check for key variables
    $envContent = file_get_contents($envFile);
    echo "APP_ENV: " . (strpos($envContent, 'APP_ENV=') !== false ? 'Set' : 'Not Set') . "<br>";
    echo "APP_DEBUG: " . (strpos($envContent, 'APP_DEBUG=') !== false ? 'Set' : 'Not Set') . "<br>";
    echo "DB_CONNECTION: " . (strpos($envContent, 'DB_CONNECTION=') !== false ? 'Set' : 'Not Set') . "<br>";
    echo "PAYMOB_API_KEY: " . (strpos($envContent, 'PAYMOB_API_KEY=') !== false ? 'Set' : 'Not Set') . "<br>";
    echo "PAYMOB_IFRAME_ID: " . (strpos($envContent, 'PAYMOB_IFRAME_ID=') !== false ? 'Set' : 'Not Set') . "<br>";
}

// Check file permissions
echo "<h2>2. File Permissions</h2>";
$storagePath = __DIR__ . '/../storage';
echo "Storage Path: " . $storagePath . "<br>";
echo "Storage Exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "<br>";
echo "Storage Writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "<br>";

$logsPath = __DIR__ . '/../storage/logs';
echo "Logs Path: " . $logsPath . "<br>";
echo "Logs Exists: " . (is_dir($logsPath) ? 'Yes' : 'No') . "<br>";
echo "Logs Writable: " . (is_writable($logsPath) ? 'Yes' : 'No') . "<br>";

// Check if Laravel log file exists
$logFile = __DIR__ . '/../storage/logs/laravel.log';
echo "Log File Exists: " . (file_exists($logFile) ? 'Yes' : 'No') . "<br>";
if (file_exists($logFile)) {
    echo "Log File Readable: " . (is_readable($logFile) ? 'Yes' : 'No') . "<br>";
    echo "Log File Size: " . filesize($logFile) . " bytes<br>";

    // Show last few lines
    $lines = file($logFile);
    $recentLines = array_slice($lines, -10);
    echo "Last 10 log lines:<br>";
    echo "<pre>";
    foreach ($recentLines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
}

// Check PHP configuration
echo "<h2>3. PHP Configuration</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";

// Check if vendor directory exists
echo "<h2>4. Dependencies</h2>";
$vendorPath = __DIR__ . '/../vendor';
echo "Vendor Directory: " . (is_dir($vendorPath) ? 'Exists' : 'Missing') . "<br>";

$composerLock = __DIR__ . '/../composer.lock';
echo "Composer Lock: " . (file_exists($composerLock) ? 'Exists' : 'Missing') . "<br>";

// Check if bootstrap directory exists
echo "<h2>5. Laravel Bootstrap</h2>";
$bootstrapPath = __DIR__ . '/../bootstrap';
echo "Bootstrap Directory: " . (is_dir($bootstrapPath) ? 'Exists' : 'Missing') . "<br>";

$appPath = __DIR__ . '/../bootstrap/app.php';
echo "Bootstrap App File: " . (file_exists($appPath) ? 'Exists' : 'Missing') . "<br>";

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after debugging for security.</p>";
?>
