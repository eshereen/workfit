<?php

namespace App\Livewire;

use Livewire\Component;

class PromoModal extends Component
{
    public bool $showModal = false;
    public bool $shouldShowModal = true;

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
