<?php

namespace App\Livewire;

use Exception;
use App\Models\Product;
use Livewire\Component;
use App\Models\Wishlist;
use Livewire\Attributes\On;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\CountryCurrencyService;

class ProductShow extends Component
{
    public Product $product;
    public $quantity = 1;
    public $selectedVariantId = null;
    public $selectedVariant = null;
    public $isInWishlist = false;
    public $currencyCode = 'USD';
    public $currencySymbol = '$';
    public $isAutoDetected = false;

    protected $rules = [
        'quantity' => 'required|integer|min:1|max:10',
        'selectedVariantId' => 'nullable|exists:product_variants,id'
    ];

    public function mount(Product $product)
    {
        $this->product = $product->load(['variants', 'category', 'subcategory', 'media']);

        // Check if product is in user's wishlist
        $this->checkWishlistStatus();

        // Load currency information
        $this->loadCurrencyInfo();

        // Set default selected variant if product has variants
        if ($this->product->variants->isNotEmpty()) {
            $this->selectedVariant = $this->product->variants->first();
            $this->selectedVariantId = $this->selectedVariant->id;
            // Always start with quantity 1, regardless of stock
            $this->quantity = 1;
        } else {
            // For simple products without variants, start with quantity 1
            $this->quantity = 1;
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

    protected function convertProductPrices()
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert product price
            if ($this->product->price) {
                $this->product->converted_price = $currencyService->convertFromUSD($this->product->price, $this->currencyCode);
            }

            // Convert compare price
            if ($this->product->compare_price && $this->product->compare_price > 0) {
                $this->product->converted_compare_price = $currencyService->convertFromUSD($this->product->compare_price, $this->currencyCode);
            }

            // Convert variant prices
            if ($this->product->variants) {
                foreach ($this->product->variants as $variant) {
                    $this->convertVariantPrice($variant);
                }
            }
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }

    protected function convertVariantPrice($variant)
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            if ($variant && $variant->price) {
                $variant->converted_price = $currencyService->convertFromUSD($variant->price, $this->currencyCode);
            }
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }

    // Handle currency changes
    public function handleCurrencyChange($currencyCode = null)
    {
        Log::info('ProductShow: Currency change triggered', ['currency' => $currencyCode]);
        $this->loadCurrencyInfo();
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

    #[On('currencyChanged')]
    public function refreshCurrency()
    {
        $this->loadCurrencyInfo();
        $this->convertProductPrices();
    }

    #[On('currency-changed')]
    public function handleCurrencyChanged($currencyCode = null)
    {
        Log::info('ProductShow: Received currency-changed event', ['currency_code' => $currencyCode]);
        $this->loadCurrencyInfo();
        $this->convertProductPrices();
    }

    #[On('global-currency-changed')]
    public function handleGlobalCurrencyChanged($currencyCode = null)
    {
        Log::info('ProductShow: Received global-currency-changed event', ['currency_code' => $currencyCode]);
        $this->loadCurrencyInfo();
        $this->convertProductPrices();
    }

    public function incrementQty()
    {
        Log::info('incrementQty method called', [
            'current_quantity_before' => $this->quantity,
            'selected_variant' => $this->selectedVariant ? 'yes' : 'no',
            'variant_stock' => $this->selectedVariant ? $this->selectedVariant->stock : 'N/A',
            'product_quantity' => $this->product->quantity
        ]);

        $maxQty = $this->selectedVariant ? $this->selectedVariant->stock : $this->product->quantity;
        $maxQty = min($maxQty, 10); // Cap at 10 maximum

        Log::info('Calculated max quantity', [
            'max_qty' => $maxQty,
            'can_increment' => $this->quantity < $maxQty
        ]);

        if ($this->quantity < $maxQty) {
            $oldQuantity = $this->quantity;
            $this->quantity++;

            Log::info('Quantity incremented successfully', [
                'old_quantity' => $oldQuantity,
                'new_quantity' => $this->quantity,
                'max_qty' => $maxQty,
                'has_variant' => $this->selectedVariant ? 'yes' : 'no'
            ]);

        } else {
            Log::info('Cannot increment quantity - at maximum', [
                'current_quantity' => $this->quantity,
                'max_qty' => $maxQty
            ]);
        }
    }

    public function decrementQty()
    {
        Log::info('decrementQty method called', [
            'current_quantity_before' => $this->quantity
        ]);

        if ($this->quantity > 1) {
            $oldQuantity = $this->quantity;
            $this->quantity--;

            Log::info('Quantity decremented successfully', [
                'old_quantity' => $oldQuantity,
                'new_quantity' => $this->quantity
            ]);

        } else {
            Log::info('Cannot decrement quantity - at minimum', [
                'current_quantity' => $this->quantity
            ]);
        }
    }











        public function selectVariant($variantId)
    {
        $this->selectedVariantId = $variantId;
        $this->selectedVariant = $this->product->variants->find($variantId);

        // Reset quantity if it exceeds stock
        if ($this->selectedVariant) {
            $this->quantity = min($this->quantity, $this->selectedVariant->stock);
            if ($this->quantity < 1) {
                $this->quantity = 1;
            }

            // Convert variant price to current currency
            $this->convertVariantPrice($this->selectedVariant);
        }
    }


    public function addToCart()
    {
        Log::info('addToCart called', [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => $this->quantity,
            'selected_variant_id' => $this->selectedVariantId,
            'selected_variant' => $this->selectedVariant ? $this->selectedVariant->id : null,
            'has_variants' => $this->product->variants->isNotEmpty(),
            'variants_count' => $this->product->variants->count()
        ]);

        $this->validate();

        if ($this->product->variants->isNotEmpty() && !$this->selectedVariantId) {
            Log::warning('No variant selected for product with variants');
            $this->dispatch('showNotification', [
                'message' => 'Please select a variant before adding to cart.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $cartService = app(CartService::class);

            // Get the current variant if exists
            $variant = $this->selectedVariantId ? $this->product->variants->find($this->selectedVariantId) : null;

            if ($variant) {
                Log::info('Adding variant to cart', [
                    'variant_id' => $variant->id,
                    'variant_color' => $variant->color,
                    'variant_size' => $variant->size,
                    'variant_stock' => $variant->stock,
                    'quantity' => $this->quantity
                ]);

                // Check stock for variant
                if ($variant->stock < $this->quantity) {
                    Log::warning('Insufficient stock for variant', [
                        'variant_stock' => $variant->stock,
                        'requested_quantity' => $this->quantity
                    ]);
                    $this->dispatch('showNotification', [
                        'message' => 'Not enough stock available for the selected variant.',
                        'type' => 'error'
                    ]);
                    return;
                }
                // Add variant to cart
                $cartService->addItemWithVariant($this->product, $variant, $this->quantity);
            } else {
                Log::info('Adding simple product to cart', [
                    'product_stock' => $this->product->quantity,
                    'quantity' => $this->quantity
                ]);

                // Check stock for simple product
                if ($this->product->quantity < $this->quantity) {
                    Log::warning('Insufficient stock for product', [
                        'product_stock' => $this->product->quantity,
                        'requested_quantity' => $this->quantity
                    ]);
                    $this->dispatch('showNotification', [
                        'message' => 'Not enough stock available. Only ' . $this->product->quantity . ' items left in stock.',
                        'type' => 'error'
                    ]);
                    return;
                }
                // Add simple product to cart
                $cartService->addItem($this->product, $this->quantity);
            }

            Log::info('Product added to cart successfully', [
                'product_id' => $this->product->id,
                'quantity' => $this->quantity,
                'has_variant' => $variant ? 'yes' : 'no'
            ]);

            // Reset quantity to 1 after successful add to cart
            $this->quantity = 1;

            // Emit events
            $this->dispatch('cartUpdated');
            $this->dispatch('showNotification', [
                'message' => 'Product added to cart successfully!',
                'type' => 'success'
            ]);

            // Redirect to cart page after successful add to cart
            return redirect()->route('cart.index');

        } catch (Exception $e) {
            Log::error('Error adding product to cart', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('showNotification', [
                'message' => 'Error adding product to cart: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function checkWishlistStatus()
    {
        $this->isInWishlist = false;
        if (Auth::check()) {
            $this->isInWishlist = Auth::user()->wishlist()
                ->where('product_id', $this->product->id)
                ->exists();
        }
    }

    public function toggleWishlist()
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
            $existingWishlist = $user->wishlist()->where('product_id', $this->product->id)->first();

            if ($existingWishlist) {
                // Remove from wishlist
                $existingWishlist->delete();
                $this->isInWishlist = false;
                $message = 'Product removed from wishlist!';
            } else {
                // Add to wishlist
                $user->wishlist()->create([
                    'product_id' => $this->product->id
                ]);
                $this->isInWishlist = true;
                $message = 'Product added to wishlist!';
            }

            // Emit events
            $this->dispatch('wishlistUpdated');
            $this->dispatch('showNotification', [
                'message' => $message,
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'An error occurred while updating your wishlist.',
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        $relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('active', true)
            ->with('media')
            ->take(4)
            ->get();

        // Convert related products prices to current currency
        $this->convertRelatedProductsPrices($relatedProducts);

        return view('livewire.product-show', [
            'relatedProducts' => $relatedProducts
        ]);
    }

    protected function convertRelatedProductsPrices($relatedProducts)
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert all related product prices
            foreach ($relatedProducts as $product) {
                if ($product->price) {
                    $product->converted_price = $currencyService->convertFromUSD($product->price, $this->currencyCode);
                }
            }
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }
}
