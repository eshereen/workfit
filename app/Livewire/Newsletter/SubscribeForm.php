<?php

namespace App\Livewire\Newsletter;

use App\Mail\NewsletterVerificationMail;
use App\Models\Country;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\Component;

class SubscribeForm extends Component
{
    public string $email = '';

    public bool $submitted = false;
    public string $bot_field = ''; // simple honeypot

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns'],

        ];
    }

    public function submit(): void
    {
        // Basic anti-bot
        if (!empty($this->bot_field)) {
            return; // silently drop
        }

        // Rate limit by IP
        $key = 'newsletter-subscribe:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many attempts. Please try again in a minute.',
            ]);
        }
        RateLimiter::hit($key, 60);

        $this->validate();

        $existing = Newsletter::withTrashed()
            ->where('email', $this->email)
            ->first();

        // Issue fresh token (plain + hashed)
        $plain = Str::random(48);
        $hash  = Hash::make($plain);

        if (!$existing) {
            $newsletter = Newsletter::create([
                'email'      => $this->email,

                'token'      => $hash,
                'verified'   => false,
            ]);
        } else {
            // If soft-deleted, restore. Either way re-issue token & set verified=false
            $existing->restore();
            $existing->fill([

                'token'      => $hash,
                'verified'   => false,
            ])->save();
            $newsletter = $existing;
        }

        // If already verified and active, block duplicates
        if ($newsletter->verified) {
            $this->addError('email', 'This email is already subscribed.');
            return;
        }

        // Send verification email (queue in prod)
        Mail::to($this->email)->queue(new NewsletterVerificationMail($newsletter, $plain));

        $this->reset(['email']);
        $this->submitted = true;

        // Emit event for JavaScript to handle auto-hide
        $this->dispatch('newsletter-subscribed');
    }

    public function render()
    {
        return view('livewire.newsletter.subscribe-form', [
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}
