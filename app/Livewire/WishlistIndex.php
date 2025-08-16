<?php

namespace App\Livewire;

use App\Models\Wishlist;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class WishlistIndex extends Component
{
    public $wishlistItems = [];
    public $currencyCode = 'USD';
    public $currencySymbol = '$';
    public $isAutoDetected = false;

    #[On('wishlistUpdated')]
    public function refreshWishlist()
    {
        $this->loadWishlist();
    }

    #[On('currencyChanged')]
    public function refreshCurrency()
    {
        $this->loadCurrencyInfo();
        $this->convertProductPrices();
    }

    public function mount()
    {
        $this->loadWishlist();
        $this->loadCurrencyInfo();
    }

    public function loadWishlist()
    {
        if (Auth::check()) {
            $this->wishlistItems = Auth::user()->wishlist()
                ->with(['product.media', 'product.category'])
                ->get();

            // Convert product prices to current currency
            $this->convertProductPrices();
        }
    }

    public function loadCurrencyInfo()
    {
        try {
            $currencyService = app(CountryCurrencyService::class);
            $currencyInfo = $currencyService->getCurrentCurrencyInfo();

            $this->currencyCode = $currencyInfo['currency_code'];
            $this->currencySymbol = $currencyInfo['currency_symbol'];
            $this->isAutoDetected = $currencyInfo['is_auto_detected'];
        } catch (\Exception $e) {
            // Use defaults if currency service fails
        }
    }

    protected function convertProductPrices()
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert all product prices in the collection
            foreach ($this->wishlistItems as $wishlistItem) {
                if ($wishlistItem->product->price) {
                    $wishlistItem->product->converted_price = $currencyService->convertFromUSD($wishlistItem->product->price, $this->currencyCode);
                }
                if ($wishlistItem->product->compare_price && $wishlistItem->product->compare_price > 0) {
                    $wishlistItem->product->converted_compare_price = $currencyService->convertFromUSD($wishlistItem->product->compare_price, $this->currencyCode);
                }
            }
        } catch (\Exception $e) {
            // Handle conversion error silently
        }
    }

    public function removeFromWishlist($wishlistId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your wishlist.');
            return;
        }

        $wishlistItem = Auth::user()->wishlist()->findOrFail($wishlistId);
        $wishlistItem->delete();

        session()->flash('success', 'Item removed from wishlist');
        $this->loadWishlist();
        $this->dispatch('wishlistUpdated');
    }

    public function clearWishlist()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to manage your wishlist.');
            return;
        }

        Auth::user()->wishlist()->delete();
        session()->flash('success', 'Your wishlist has been cleared');
        $this->loadWishlist();
        $this->dispatch('wishlistUpdated');
    }

    public function addToCart($productId, $hasVariants = false)
    {
        if ($hasVariants) {
            // Redirect to product page for variant selection
            $product = \App\Models\Product::find($productId);
            return redirect()->route('product.show', $product->slug);
        }

        try {
            $cartService = app(CartService::class);
            $product = \App\Models\Product::find($productId);

            $cartService->addItem($product, 1);

            session()->flash('success', 'Product added to cart successfully!');
            $this->dispatch('cartUpdated');

        } catch (\Exception $e) {
            session()->flash('error', 'Error adding product to cart: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.wishlist-index');
    }
}
