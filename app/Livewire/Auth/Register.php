<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Coupon;
use App\Enums\CouponType;
use Livewire\Component;
use App\Mail\RegisterMail;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));
        // Create a unique one-time coupon for the new user
        $coupon = null;
        try {
            $code = 'WELCOME-' . strtoupper(Str::random(8));
            // Ensure uniqueness
            while (Coupon::where('code', $code)->exists()) {
                $code = 'WELCOME-' . strtoupper(Str::random(8));
            }

            $coupon = Coupon::create([
                'code' => $code,
                'type' => CouponType::Percentage,
                'value' => 10, // 10% off
                'min_order_amount' => null,
                'usage_limit' => 1, // one-time use
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(14), // valid for 14 days
                'active' => true,
            ]);
        } catch (\Throwable $e) {
            // If coupon creation fails, continue registration without blocking
            $coupon = null;
        }
        // Send welcome email

        Mail::to($user->email)->queue(
            (new RegisterMail($user, $coupon))->delay(now()->addSeconds(5))
        );

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
