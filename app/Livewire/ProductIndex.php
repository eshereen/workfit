<?php

namespace App\Livewire;

use Exception;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'newest';
    public $category = '';
    public $wishlistProductIds = [];
    public $currencyCode = 'USD';
    public $currencySymbol = '$';
    public $isAutoDetected = false;

    // Cart modal properties
    public $showVariantModal = false;
    public $selectedProduct = null;
    public $selectedVariantId = null;
    public $selectedVariant = null;
    public $quantity = 1;

    #[On('wishlistUpdated')]
    public function loadWishlist()
    {
        if (Auth::check()) {
            $this->wishlistProductIds = Auth::user()->wishlist()->pluck('product_id')->toArray();
        } else {
            $this->wishlistProductIds = [];
        }
    }

        #[On('currencyChanged')]
    public function refreshCurrency()
    {
        Log::info('Currency change event received in ProductIndex');

        $this->loadCurrencyInfo();
        // Force a re-render so paginated items get fresh conversion
        $this->dispatch('$refresh');

        // Log the currency change
        Log::info('Currency changed in ProductIndex', [
            'new_currency' => $this->currencyCode,
            'new_symbol' => $this->currencySymbol
        ]);
    }

    // Alternative method to handle currency changes
    public function handleCurrencyChange($currencyCode = null)
    {
        Log::info('Manual currency change triggered', ['currency' => $currencyCode]);
        $this->refreshCurrency();
    }

    public function mount()
    {
        try {
            $this->loadWishlist();
            $this->loadCurrencyInfo();
        } catch (Exception $e) {
            // Handle wishlist loading error silently
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

            // Convert product prices to current currency
            $this->convertProductPrices();
        } catch (Exception $e) {
            // Use defaults if currency service fails
        }
    }

    protected function convertProductPrices($products = null)
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert all product prices in the collection
            $productsToConvert = $products;
            if ($productsToConvert) {
                foreach ($productsToConvert as $product) {
                    if ($product->price) {
                        $product->converted_price = $currencyService->convertFromUSD($product->price, $this->currencyCode);
                    }
                    if ($product->compare_price && $product->compare_price > 0) {
                        $product->converted_compare_price = $currencyService->convertFromUSD($product->compare_price, $this->currencyCode);
                    }
                }
            }
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }

    protected function convertVariantPrices()
    {
        if ($this->currencyCode === 'USD' || !$this->selectedProduct) {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert product price
            if ($this->selectedProduct->price) {
                $this->selectedProduct->converted_price = $currencyService->convertFromUSD($this->selectedProduct->price, $this->currencyCode);
            }

            // Convert variant prices
            if ($this->selectedProduct->variants) {
                foreach ($this->selectedProduct->variants as $variant) {
                    if ($variant->price) {
                        $variant->converted_price = $currencyService->convertFromUSD($variant->price, $this->currencyCode);
                    }
                }
            }
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleWishlist($productId)
    {
        if (!Auth::check()) {
            $this->dispatch('showNotification', [
                'message' => 'Please login to add items to your wishlist.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $user = Auth::user();
            $existingWishlist = $user->wishlist()->where('product_id', $productId)->first();
            $message = '';

            if ($existingWishlist) {
                // Remove from wishlist
                $existingWishlist->delete();
                $message = 'Product removed from wishlist!';
                // Remove from local array
                $this->wishlistProductIds = array_filter($this->wishlistProductIds, function($id) use ($productId) {
                    return $id != $productId;
                });
            } else {
                // Add to wishlist
                $user->wishlist()->create([
                    'product_id' => $productId
                ]);
                $message = 'Product added to wishlist!';
                // Add to local array
                $this->wishlistProductIds[] = $productId;
            }

            // Emit events
            $this->dispatch('wishlistUpdated');
            $this->dispatch('showNotification', [
                'message' => $message,
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            // Log::error('Error in toggleWishlist: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'message' => 'An error occurred while updating your wishlist: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function openVariantModal($productId)
    {
        $this->selectedProduct = Product::with('variants')->find($productId);
        $this->selectedVariantId = null;
        $this->selectedVariant = null;
        $this->quantity = 1;
        $this->showVariantModal = true;

        // Convert variant prices to current currency
        $this->convertVariantPrices();
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariantId = $variantId;
        $this->selectedVariant = $this->selectedProduct->variants->find($variantId);

        // Reset quantity if it exceeds stock
        if ($this->selectedVariant && $this->quantity > $this->selectedVariant->stock) {
            $this->quantity = $this->selectedVariant->stock;
        }
    }

    public function incrementQty()
    {
        $maxQty = $this->selectedVariant ? $this->selectedVariant->stock : 10;
        if ($this->quantity < $maxQty && $this->quantity < 10) {
            $this->quantity++;
        }
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        if (!$this->selectedVariant) {
            $this->dispatch('showNotification', [
                'message' => 'Please select a variant before adding to cart.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $cartService = app(CartService::class);
            $cartService->addItemWithVariant($this->selectedProduct, $this->selectedVariant, $this->quantity);

            // Close modal and reset
            $this->showVariantModal = false;
            $this->selectedProduct = null;
            $this->selectedVariantId = null;
            $this->selectedVariant = null;
            $this->quantity = 1;

            // Emit events
            $this->dispatch('cartUpdated');
            $this->dispatch('showNotification', [
                'message' => 'Product added to cart successfully!',
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Error adding product to cart: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function addSimpleProductToCart($productId, $quantity = 1)
    {
        try {
            $cartService = app(CartService::class);
            $product = Product::find($productId);

            if (!$product) {
                $this->dispatch('showNotification', [
                    'message' => 'Product not found.',
                    'type' => 'error'
                ]);
                return;
            }

            $cartService->addItem($product, $quantity);

            $this->dispatch('cartUpdated');
            $this->dispatch('showNotification', [
                'message' => 'Product added to cart successfully!',
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Error adding product to cart: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        // Check if currency has changed since last render
        $this->checkCurrencyChange();

        $query = Product::with(['category', 'subcategory', 'media', 'variants'])
            ->where('active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Convert product prices to current currency
        $this->convertProductPrices($products);

        return view('livewire.product-index', [
            'products' => $products
        ]);
    }

    protected function checkCurrencyChange()
    {
        try {
            $currencyService = app(CountryCurrencyService::class);
            $currentInfo = $currencyService->getCurrentCurrencyInfo();

            if ($this->currencyCode !== $currentInfo['currency_code']) {
                Log::info('Currency change detected in render', [
                    'old_currency' => $this->currencyCode,
                    'new_currency' => $currentInfo['currency_code']
                ]);

                $this->currencyCode = $currentInfo['currency_code'];
                $this->currencySymbol = $currentInfo['currency_symbol'];
                $this->isAutoDetected = $currentInfo['is_auto_detected'];
            }
        } catch (Exception $e) {
            // Handle error silently
        }
    }

    /**
     * Get the hex color code for a color name from config
     */
    public function getColorCode($colorName)
    {
        $colors = config('colors');
        return $colors[$colorName] ?? '#808080'; // Default to gray if color not found
    }

    /**
     * Get contrasting text color (black or white) for a given background color
     */
    public function getContrastColor($hexColor)
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Return black for light backgrounds, white for dark backgrounds
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}
