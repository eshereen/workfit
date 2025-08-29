<?php
// Create: public/find-env.php
echo "<h3>Finding all .env files:</h3>";

// Check common locations
$locations = [
    __DIR__ . '/../.env',
    __DIR__ . '/.env', 
    '/home/elragtow/.env',
    '/home/elragtow/workfit.medsite.dev/.env',
    '/home/elragtow/workfit.medsite.dev/public/.env'
];

foreach($locations as $path) {
    if(file_exists($path)) {
        echo "Found .env at: " . $path . "<br>";
        $content = file_get_contents($path);
        if(strpos($content, 'workfit.test') !== false) {
            echo "⚠️ This file contains 'workfit.test'<br>";
        }
        if(strpos($content, 'workfit.medsite.dev') !== false) {
            echo "✅ This file contains 'workfit.medsite.dev'<br>";
        }
        echo "First line: " . strtok($content, "\n") . "<br><br>";
    }
}

// Check for opcache
if(function_exists('opcache_get_status')) {
    echo "<h3>OPcache Status:</h3>";
    $status = opcache_get_status();
    echo "Enabled: " . ($status['opcache_enabled'] ? 'YES' : 'NO') . "<br>";
}