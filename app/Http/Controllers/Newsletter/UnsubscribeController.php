<?php

// app/Http/Controllers/Newsletter/UnsubscribeController.php
namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UnsubscribeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'token' => ['required','string'],
        ]);

        $record = Newsletter::where('email', $request->email)->firstOrFail();

        if (!Hash::check($request->token, $record->token)) {
            abort(403, 'Invalid token.');
        }

        $record->delete(); // soft-delete preserves audit trail
        return view('newsletter.unsubscribed');
    }
}

