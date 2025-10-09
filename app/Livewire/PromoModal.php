<?php

namespace App\Livewire;

use App\Models\Coupon;
use App\Mail\PromoCouponMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PromoModal extends Component
{
    public bool $showModal = false;
    public bool $shouldShowModal = true;
    public string $email = '';
    public bool $isSubmitting = false;

    protected $rules = [
        'email' => 'required|email|max:255',
    ];

    protected $messages = [
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
    ];

    public function mount()
    {
        // Check if user has already seen the modal in this session
        if (session('promo_modal_seen')) {
            $this->shouldShowModal = false;
            $this->showModal = false;
        } else {
            // Show modal after 1 second delay
            $this->dispatch('show-modal-after-delay');
        }
    }

    public function showModalAfterDelay()
    {
        if ($this->shouldShowModal) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        // Remember that user has seen the modal in this session
        session(['promo_modal_seen' => true]);
    }

    public function submitEmail()
    {
        $this->validate();

        $this->isSubmitting = true;

        try {
            // Check if email already has a promo coupon
            $existingCoupon = Coupon::where('code', 'LIKE', 'WELCOME%')
                ->where('meta_data->email', strtolower($this->email))
                ->first();

            if ($existingCoupon) {
                $this->dispatch('showNotification', [
                    'message' => 'This email has already received a welcome coupon.',
                    'type' => 'error'
                ]);
                $this->isSubmitting = false;
                return;
            }

            // Generate unique coupon code
            $couponCode = 'WELCOME' . strtoupper(Str::random(8));

            // Create one-time use coupon (10% off)
            $coupon = Coupon::create([
                'code' => $couponCode,
                'type' => 'percentage',
                'value' => 10,
                'usage_limit' => 1,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(30),
                'active' => true,
                'meta_data' => ['email' => strtolower($this->email)] // Store email in meta_data
            ]);

            // Send email with coupon
            Mail::to($this->email)->send(new PromoCouponMail($coupon));

            // Show success notification
            $this->dispatch('showNotification', [
                'message' => 'Check your email! Your exclusive 10% OFF coupon has been sent.',
                'type' => 'success'
            ]);

            // Close modal and mark as seen
            $this->closeModal();
            $this->reset('email');

        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Something went wrong. Please try again.',
                'type' => 'error'
            ]);
        }

        $this->isSubmitting = false;
    }

    public function render()
    {
        return view('livewire.promo-modal');
    }
}
