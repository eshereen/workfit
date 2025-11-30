<?php
/**
 * Email Test Script for WorkFit
 * Upload this to your server root and run: php test-mail.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== WorkFit Email Configuration Test ===\n\n";

// Display current mail configuration
echo "1. Current Mail Configuration:\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "   MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "   MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "   MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "   MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Test 1: Simple raw email
echo "2. Testing Simple Email Send:\n";
try {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from WorkFit.', function($message) {
        $message->to('info@workfiteg.com')
                ->subject('Test Email - WorkFit');
    });
    echo "   ✅ Simple email sent successfully!\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    echo "   Full error:\n";
    echo "   " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    echo "   " . $e->getTraceAsString() . "\n\n";
}

// Test 2: Check SMTP connection
echo "3. Testing SMTP Connection:\n";
try {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $encryption = config('mail.mailers.smtp.encryption');

    echo "   Attempting to connect to {$host}:{$port} with {$encryption}...\n";

    $socket = @fsockopen(
        ($encryption === 'ssl' ? 'ssl://' : '') . $host,
        $port,
        $errno,
        $errstr,
        10
    );

    if ($socket) {
        echo "   ✅ SMTP connection successful!\n";
        fclose($socket);
    } else {
        echo "   ❌ SMTP connection failed: {$errstr} ({$errno})\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check email credentials
echo "4. Email Account Info:\n";
echo "   - Make sure 'info@workfiteg.com' exists in your cPanel\n";
echo "   - Verify the password is correct\n";
echo "   - Check if the email account is not suspended\n\n";

// Test 4: Check for common issues
echo "5. Common Issues Checklist:\n";
echo "   [ ] Email account exists in cPanel\n";
echo "   [ ] Password is correct (no typos)\n";
echo "   [ ] Port 465 is not blocked by server\n";
echo "   [ ] SSL encryption is enabled\n";
echo "   [ ] .env file is properly configured\n";
echo "   [ ] Config cache cleared: php artisan config:clear\n\n";

echo "=== Test Complete ===\n";
echo "Check the logs at: storage/logs/laravel.log\n";

