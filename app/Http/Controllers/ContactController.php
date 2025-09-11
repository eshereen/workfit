<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $title = 'WorkFit|Contact Us';
        return view('contact',compact('title'));
    }
    public function store(Request $request){
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        // Create contact record
        $contact = Contact::create($validated);

        // Send email
        try {
            Mail::to('workfitheadoffice@gmail.com')->later(now()->addSeconds(5), new ContactMail($contact));
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Contact message sent successfully');
    }
}
