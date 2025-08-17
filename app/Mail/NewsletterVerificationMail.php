<?php
// app/Mail/NewsletterVerificationMail.php
namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NewsletterVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public string $plainToken
    ) {}

    public function build()
    {
        // Signed URL to reduce tampering + our token (double security)
        $url = URL::temporarySignedRoute(
            'newsletter.verify',
            now()->addDays(3),
            ['email' => $this->newsletter->email, 'token' => $this->plainToken]
        );

        return $this->subject('Confirm your WorkFit newsletter subscription')
            ->markdown('emails.newsletter.verify', ['url' => $url]);
    }
}
