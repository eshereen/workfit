<?php

namespace App\Livewire\Newsletter;

use Throwable;
use App\Mail\NewsletterVerificationMail;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

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

        // Send verification email (queue in prod when worker is running; otherwise sync send)
        try {
            $usingSyncQueue = config('queue.default') === 'sync';
            if ($usingSyncQueue) {
                Mail::to($this->email)->send(new NewsletterVerificationMail($newsletter, $plain));
                Log::info('Newsletter verification email sent synchronously', ['email' => $this->email]);
            } else {
                Mail::to($this->email)->queue(new NewsletterVerificationMail($newsletter, $plain));
                Log::info('Newsletter verification email queued', ['email' => $this->email]);
            }
        } catch (Throwable $e) {
            Log::error('Newsletter email dispatch failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
            // Optional: surface a friendly error (kept silent to avoid leaking details)
            $this->addError('email', 'We could not send the verification email right now. Please try again later.');
            return;
        }

        $this->reset(['email']);
        $this->submitted = true;

        // Emit event for JavaScript to handle auto-hide
        $this->dispatch('newsletter-subscribed');
    }

    public function render()
    {
        return view('livewire.newsletter.subscribe-form');
    }
}
