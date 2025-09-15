<?php

namespace App\Livewire;

use Livewire\Component;

class PromoModal extends Component
{
    public bool $showModal = true;

    public function mount()
    {
        // Check if user has already seen the modal in this session
        if (session('promo_modal_seen')) {
            $this->showModal = false;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        // Remember that user has seen the modal in this session
        session(['promo_modal_seen' => true]);
    }

    public function redirectToRegister()
    {
        $this->closeModal();
        return redirect()->route('register');
    }

    public function render()
    {
        return view('livewire.promo-modal');
    }
}
