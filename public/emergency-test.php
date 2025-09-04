<?php
// EMERGENCY BYPASS - Test if basic PHP works without Laravel
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Direct PHP test - bypasses ALL Laravel middleware',
    'method' => $_SERVER['REQUEST_METHOD'],
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
    ],
    'files_uploaded' => [
        'bootstrap_app' => file_exists(__DIR__ . '/../bootstrap/app.php'),
        'csrf_middleware' => file_exists(__DIR__ . '/../app/Http/Middleware/VerifyCsrfToken.php'),
        'routes_web' => file_exists(__DIR__ . '/../routes/web.php'),
    ],
    'csrf_middleware_content' => file_exists(__DIR__ . '/../app/Http/Middleware/VerifyCsrfToken.php') ?
        file_get_contents(__DIR__ . '/../app/Http/Middleware/VerifyCsrfToken.php') : 'FILE NOT FOUND',
    'bootstrap_content' => file_exists(__DIR__ . '/../bootstrap/app.php') ?
        file_get_contents(__DIR__ . '/../bootstrap/app.php') : 'FILE NOT FOUND'
]);
?>
