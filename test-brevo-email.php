<?php

/**
 * Brevo Email Test Script
 * Upload this file to your server root and run: php test-brevo-email.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=================================\n";
echo "Brevo Email Configuration Test\n";
echo "=================================\n\n";

// Show current mail configuration
echo "Current Configuration:\n";
echo "---------------------\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_PASSWORD: " . (config('mail.mailers.smtp.password') ? '****** (set)' : '(NOT SET)') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Ask for recipient email
echo "Enter recipient email address: ";
$toEmail = trim(fgets(STDIN));

if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    echo "ERROR: Invalid email address\n";
    exit(1);
}

echo "\nSending test email to: {$toEmail}\n";
echo "Please wait...\n\n";

try {
    // Send email with detailed error catching
    \Illuminate\Support\Facades\Mail::raw(
        'This is a test email from WorkFit via Brevo SMTP.\n\nIf you receive this email, your Brevo configuration is working correctly!',
        function ($message) use ($toEmail) {
            $message->to($toEmail)
                ->subject('WorkFit - Brevo Test Email')
                ->from(config('mail.from.address'), config('mail.from.name'));
        }
    );

    echo "✓ SUCCESS! Email sent successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Check inbox at: {$toEmail}\n";
    echo "2. Check spam/junk folder\n";
    echo "3. Check Brevo dashboard: https://app.brevo.com/\n";
    echo "   → Go to 'Transactional' → 'Logs' to see delivery status\n\n";

} catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
    echo "✗ SMTP Transport Error:\n";
    echo $e->getMessage() . "\n\n";
    echo "Common fixes:\n";
    echo "1. Verify MAIL_USERNAME and MAIL_PASSWORD in .env\n";
    echo "2. Check if sender email is verified in Brevo\n";
    echo "3. Verify Brevo SMTP credentials at: https://app.brevo.com/settings/keys/smtp\n\n";

    // Check for specific errors
    if (strpos($e->getMessage(), '535') !== false || strpos($e->getMessage(), 'authentication') !== false) {
        echo "\n⚠️  AUTHENTICATION ERROR\n";
        echo "Your Brevo username or password is incorrect.\n";
        echo "Current username: " . config('mail.mailers.smtp.username') . "\n";
        echo "Get correct credentials: https://app.brevo.com/settings/keys/smtp\n\n";
    } elseif (strpos($e->getMessage(), '550') !== false || strpos($e->getMessage(), 'sender') !== false) {
        echo "\n⚠️  SENDER VERIFICATION ERROR\n";
        echo "Your sender email (" . config('mail.from.address') . ") is not verified in Brevo.\n";
        echo "Verify it here: https://app.brevo.com/senders\n\n";
    }
    exit(1);

} catch (\Exception $e) {
    echo "✗ Error sending email:\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";

    // Check for specific Brevo errors in message
    $message = $e->getMessage();
    if (stripos($message, '535') !== false || stripos($message, 'authentication') !== false) {
        echo "\n⚠️  AUTHENTICATION ERROR\n";
        echo "Your Brevo credentials are incorrect.\n";
        echo "Fix: Get your SMTP credentials from https://app.brevo.com/settings/keys/smtp\n\n";
    } elseif (stripos($message, '550') !== false || stripos($message, 'sender') !== false) {
        echo "\n⚠️  SENDER ERROR\n";
        echo "Your sender email needs to be verified in Brevo.\n";
        echo "Fix: Verify your domain or sender email at https://app.brevo.com/senders\n\n";
    } elseif (stripos($message, 'Connection') !== false || stripos($message, 'timed out') !== false) {
        echo "\n⚠️  CONNECTION ERROR\n";
        echo "Cannot connect to Brevo SMTP server.\n";
        echo "Check: MAIL_HOST and MAIL_PORT in .env\n\n";
    }
    exit(1);
}

