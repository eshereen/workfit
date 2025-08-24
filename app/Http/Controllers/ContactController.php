<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $title = 'WorkFit|Contact Us';
        return view('contact',compact('title'));
    }
    public function store(Request $request){
        $contact = Contact::create($request->all());
        Mail::to('admin@admin.com')->send(new ContactMail($contact));
        return redirect()->back()->with('success', 'Contact message sent successfully');
    }
}
