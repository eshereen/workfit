<?php
// Create: public/check-env.php
echo "<h3>Environment Check</h3>";
echo "Current directory: " . getcwd() . "<br>";
echo "Project root: " . dirname(__DIR__) . "<br>";

$envPath = dirname(__DIR__) . '/.env';
echo "Looking for .env at: " . $envPath . "<br>";
echo ".env exists: " . (file_exists($envPath) ? 'YES' : 'NO') . "<br>";

if (file_exists($envPath)) {
    echo "<h4>.env content (first 10 lines):</h4>";
    $lines = array_slice(file($envPath), 0, 10);
    foreach($lines as $line) {
        echo htmlspecialchars($line) . "<br>";
    }
}

echo "<h4>Current $_ENV values:</h4>";
echo "APP_URL from \$_ENV: " . ($_ENV['APP_URL'] ?? 'NOT SET') . "<br>";
echo "APP_URL from getenv: " . (getenv('APP_URL') ?: 'NOT SET') . "<br>";