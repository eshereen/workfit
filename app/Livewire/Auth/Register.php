<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Coupon;
use Livewire\Component;
use App\Mail\RegisterMail;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

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
        $coupon = Coupon::where('code', 'NEW_USER10')->first();
        // Send welcome email
      
        Mail::to($user->email)->queue(
            (new RegisterMail($user, $coupon))->delay(now()->addSeconds(5))
        );

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
