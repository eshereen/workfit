<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    public function index()
    {
        $title = 'WorkFit|Contact Us';
        return view('contact',compact('title'));
    }
    public function store(Request $request){
        // Honeypot check: if the honeypot field is filled, it's likely a bot
        if ($request->filled('website')) {
            // Silently reject - don't show error to avoid teaching bots
            Log::info('Contact form submission rejected - honeypot field filled', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            // Return success message to user (don't reveal it's spam)
            return redirect()->back()->with('success', 'Contact message sent successfully');
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        // Remove empty subject if column doesn't exist (backwards compatibility)
        if (empty($validated['subject']) || !Schema::hasColumn('contacts', 'subject')) {
            unset($validated['subject']);
        }

        // Create contact record
        $contact = Contact::create($validated);

        // Return success immediately and send email in the background using ignore_user_abort
        ignore_user_abort(true);

        // Show success message to user immediately
        $response = redirect()->back()->with('success', 'Contact message sent successfully');

        // Send response to user
        if (function_exists('fastcgi_finish_request')) {
            session()->save();
            fastcgi_finish_request();
        }

        // Now send email after user has received response
        try {
            Log::info('Attempting to send contact form email', [
                'contact_id' => $contact->id,
                'customer_email' => $contact->email,
                'customer_name' => $contact->name,
                'to_email' => 'info@workfiteg.com',
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from' => config('mail.from.address'),
                ]
            ]);

            // Force immediate sending (not queued) and explicitly set sender
            Mail::to('info@workfiteg.com')->send(
                (new ContactMail($contact))
                    ->from(config('mail.from.address'), config('mail.from.name'))
            );

            Log::info('Contact form email sent successfully', [
                'contact_id' => $contact->id,
                'to_email' => 'info@workfiteg.com'
            ]);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            Log::error('SMTP Transport Error in contact form', [
                'contact_id' => $contact->id,
                'error_type' => 'TransportExceptionInterface',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage(), [
                'contact_id' => $contact->id,
                'error_type' => get_class($e),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $response;
    }
}
