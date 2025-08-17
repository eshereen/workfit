<?php

// app/Http/Controllers/Newsletter/VerifyController.php
namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerifyController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'token' => ['required','string'],
        ]);

        // Must be a valid signed link (expires in 3 days)
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $record = Newsletter::withTrashed()->where('email', $request->email)->firstOrFail();

        if (!Hash::check($request->token, $record->token)) {
            abort(403, 'Invalid token.');
        }

        // Mark verified and rotate token (used later for unsubscribe)
        $record->restore();
        $record->verified = true;
        $record->token = Hash::make(str()->random(48)); // rotate
        $record->save();

        return view('newsletter.verified'); // simple "You’re confirmed" page
    }
}
