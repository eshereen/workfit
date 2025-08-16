<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class CartWishlistCounts extends Component
{
    public $cartCount = 0;
    public $wishlistCount = 0;

    #[On('cartUpdated')]
    public function cartUpdated()
    {
        $this->updateCounts();
    }

    #[On('wishlistUpdated')]
    public function wishlistUpdated()
    {
        $this->updateCounts();
    }

    public function mount()
    {
        $this->updateCounts();
    }

    public function updateCounts()
    {
        // Update cart count
        $cartService = app(CartService::class);
        $this->cartCount = $cartService->getCount();

        // Update wishlist count
        if (Auth::check()) {
            $this->wishlistCount = Auth::user()->wishlist()->count();
        } else {
            $this->wishlistCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.cart-wishlist-counts');
    }
}
