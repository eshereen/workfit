<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email? : The email address to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing email configuration...');
        $this->newLine();

        // Display current configuration
        $this->info('Current Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', Config::get('mail.default')],
                ['MAIL_HOST', Config::get('mail.mailers.smtp.host')],
                ['MAIL_PORT', Config::get('mail.mailers.smtp.port')],
                ['MAIL_ENCRYPTION', Config::get('mail.mailers.smtp.encryption')],
                ['MAIL_USERNAME', Config::get('mail.mailers.smtp.username')],
                ['MAIL_PASSWORD', Config::get('mail.mailers.smtp.password') ? '****** (set)' : '(not set)'],
                ['MAIL_FROM_ADDRESS', Config::get('mail.from.address')],
                ['MAIL_FROM_NAME', Config::get('mail.from.name')],
                ['MAIL_VERIFY_PEER', Config::get('mail.mailers.smtp.verify_peer', 'not set')],
            ]
        );
        $this->newLine();

        $toEmail = $this->argument('email') ?? Config::get('mail.from.address');

        if (!$toEmail) {
            $this->error('No email address provided and MAIL_FROM_ADDRESS is not set.');
            return Command::FAILURE;
        }

        $this->info("Attempting to send test email to: {$toEmail}");

        try {
            Mail::raw('This is a test email from WorkFit. If you receive this, your email configuration is working correctly!', function ($message) use ($toEmail) {
                $message->to($toEmail)
                    ->subject('WorkFit - Test Email');
            });

            $this->newLine();
            $this->info('✓ Email sent successfully!');
            $this->info('Check your inbox (and spam folder) for the test email.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Failed to send email!');
            $this->newLine();
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Common fixes:');
            $this->line('1. Verify your .env file has correct Gmail settings');
            $this->line('2. Use Gmail App Password (not regular password)');
            $this->line('3. Run: php artisan config:clear');
            $this->line('4. Check if port 587 or 465 is blocked');

            return Command::FAILURE;
        }
    }
}

