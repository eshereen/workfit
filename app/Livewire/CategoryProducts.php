<?php

namespace App\Livewire;

use Exception;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryProducts extends Component
{
    use WithPagination;

    public $categorySlug;
    public $category;
    public $selectedCategories = [];
    public $selectedSubcategories = [];
    public $search = '';
    public $sortBy = 'newest';
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

    #[On('filtersChanged')]
    public function updateFilters($filters)
    {
        $this->selectedCategories = $filters['categories'] ?? [];
        $this->selectedSubcategories = $filters['subcategories'] ?? [];
        $this->resetPage();
    }

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
    #[On('currency-changed')]
    #[On('global-currency-changed')]
    public function refreshCurrency()
    {
        Log::info('Currency change event received in CategoryProducts');
        $this->loadCurrencyInfo();
        $this->dispatch('$refresh');
    }

    public function mount($categorySlug = null)
    {
        $this->categorySlug = $categorySlug;
        $this->loadCategory();
        $this->loadWishlist();
        $this->loadCurrencyInfo();
    }

    public function loadCategory()
    {
        if ($this->categorySlug) {
            $this->category = Category::where('slug', $this->categorySlug)
                ->where('categories.active', true)
                ->first();
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
        } catch (Exception $e) {
            // Use defaults if currency service fails
        }
    }

    public function convertPrice($amount)
    {
        try {
            $currencyService = app(CountryCurrencyService::class);
            return $currencyService->convertFromUSD($amount, $this->currencyCode);
        } catch (Exception $e) {
            return $amount;
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
                $existingWishlist->delete();
                $message = 'Product removed from wishlist!';
                $this->wishlistProductIds = array_filter($this->wishlistProductIds, function($id) use ($productId) {
                    return $id != $productId;
                });
            } else {
                $user->wishlist()->create(['product_id' => $productId]);
                $message = 'Product added to wishlist!';
                $this->wishlistProductIds[] = $productId;
            }

            $this->dispatch('wishlistUpdated');
            $this->dispatch('showNotification', [
                'message' => $message,
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'An error occurred while updating your wishlist: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function openVariantModal($productId)
    {
        try {
            // Enhanced debug logging for live server
            \Log::info('CategoryProducts: openVariantModal STARTED', [
                'product_id' => $productId,
                'csrf_token' => csrf_token(),
                'session_id' => session()->getId(),
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip(),
                'livewire_request' => request()->hasHeader('X-Livewire'),
                'csrf_header' => request()->header('X-CSRF-TOKEN'),
                'all_headers' => request()->headers->all(),
                'method' => request()->method(),
                'url' => request()->url()
            ]);

            $this->selectedProduct = Product::with('variants')->find($productId);

            if (!$this->selectedProduct) {
                \Log::error('CategoryProducts: Product not found', ['product_id' => $productId]);
                $this->dispatch('showNotification', [
                    'message' => 'Product not found.',
                    'type' => 'error'
                ]);
                return;
            }

            $this->selectedVariantId = null;
            $this->selectedVariant = null;
            $this->quantity = 1;
            $this->showVariantModal = true;

            \Log::info('CategoryProducts: openVariantModal COMPLETED successfully', [
                'product_id' => $productId,
                'product_name' => $this->selectedProduct->name,
                'variants_count' => $this->selectedProduct->variants->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('CategoryProducts: openVariantModal EXCEPTION', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('showNotification', [
                'message' => 'Unable to load product options. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariantId = $variantId;
        $this->selectedVariant = $this->selectedProduct->variants->find($variantId);

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

            $this->showVariantModal = false;
            $this->selectedProduct = null;
            $this->selectedVariantId = null;
            $this->selectedVariant = null;
            $this->quantity = 1;

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
            // Debug logging for live server
            \Log::info('CategoryProducts: addSimpleProductToCart called', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'csrf_token' => csrf_token(),
                'session_id' => session()->getId()
            ]);

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
        $query = Product::with(['category', 'subcategory', 'media', 'variants'])
            ->where('products.active', true);

        // If a specific category is selected, filter by it
        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        // Apply category filter
        if ($this->selectedCategories) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        // Apply subcategory filter
        if ($this->selectedSubcategories) {
            $query->whereIn('subcategory_id', $this->selectedSubcategories);
        }

        // Apply search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Apply sorting
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

        return view('livewire.category-products', [
            'products' => $products,
            'category' => $this->category
        ]);
    }

    public function getColorCode($colorName)
    {
        $colors = config('colors');
        return $colors[$colorName] ?? '#808080';
    }

    public function getContrastColor($hexColor)
    {
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}
