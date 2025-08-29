<?php
// Create: public/view-full-env.php
$envPath = __DIR__ . '/../.env';
$content = file_get_contents($envPath);

echo "<h3>Full .env file content:</h3>";
echo "<pre>" . htmlspecialchars($content) . "</pre>";

echo "<h3>Lines containing 'workfit':</h3>";
$lines = explode("\n", $content);
foreach($lines as $i => $line) {
    if(stripos($line, 'workfit') !== false) {
        echo "Line " . ($i+1) . ": " . htmlspecialchars($line) . "<br>";
    }
}