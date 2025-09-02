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
use Illuminate\Support\Facades\Cache;

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
            // Cache wishlist for better performance
            $this->wishlistProductIds = cache()->remember(
                'user_wishlist_' . Auth::id(),
                600, // 10 minutes cache
                function () {
                    return Auth::user()->wishlist()->pluck('product_id')->toArray();
                }
            );
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

            // Check currency change once on mount
            $this->checkCurrencyChange();
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

            // Convert variant prices with bulk processing
            if ($this->selectedProduct->variants) {
                $this->selectedProduct->variants->transform(function ($variant) use ($currencyService) {
                    if ($variant->price) {
                        $variant->converted_price = $currencyService->convertFromUSD($variant->price, $this->currencyCode);
                    }
                    return $variant;
                });
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

            // Clear wishlist cache
            cache()->forget('user_wishlist_' . Auth::id());

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
        // Cache product with variants for better performance
        $this->selectedProduct = cache()->remember(
            'product_with_variants_' . $productId,
            300, // 5 minutes cache
            function () use ($productId) {
                return Product::with(['variantsOptimized'])
                    ->select('id', 'name', 'slug', 'price', 'compare_price')
                    ->find($productId);
            }
        );

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
        Log::info('addSimpleProductToCart called', [
            'product_id' => $productId,
            'quantity' => $quantity
        ]);



        try {
            $cartService = app(CartService::class);
            $product = Product::find($productId);

            if (!$product) {
                Log::warning('Product not found for cart addition', ['product_id' => $productId]);
                $this->dispatch('showNotification', [
                    'message' => 'Product not found.',
                    'type' => 'error'
                ]);
                return;
            }

            $cartService->addItem($product, $quantity);

            Log::info('Simple product added to cart successfully', [
                'product_id' => $productId,
                'product_name' => $product->name,
                'quantity' => $quantity
            ]);

            $this->dispatch('cartUpdated');
            $this->dispatch('showNotification', [
                'message' => 'Product added to cart successfully!',
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            Log::error('Error adding simple product to cart', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('showNotification', [
                'message' => 'Error adding product to cart: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        // Build cache key for this specific query
        $cacheKey = $this->buildCacheKey();

        // Try to get from cache first with shorter cache time for better performance
        $cacheTime = request()->routeIs('home') ? 60 : 180; // Shorter cache for home page
        $products = cache()->remember($cacheKey, $cacheTime, function () {
            // Optimized eager loading with specific selects
            $with = [
                'category:id,name,slug',
                'media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->whereIn('collection_name', ['main_image', 'product_images'])
                          ->whereNotNull('disk')
                          ->orderBy('collection_name', 'asc')
                          ->orderBy('id', 'asc');
                }
            ];

            // Always load variants for product index pages to avoid N+1 queries
            if (!request()->routeIs('home')) {
                $with[] = 'subcategory:id,name,slug';
            }

            // Always load variants to prevent N+1 queries
            $with[] = 'variants:id,product_id,color,size,price,stock';

            $query = Product::with($with)
                ->select('id', 'name', 'slug', 'description', 'price', 'compare_price', 'category_id', 'subcategory_id', 'active', 'featured', 'created_at')
                ->where('active', true);

            // Optimized search with full-text search if available
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->category) {
                $query->where('category_id', $this->category);
            }

            // Optimized sorting
            switch ($this->sortBy) {
                case 'price_low':
                    $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $perPage = request()->routeIs('home') ? 8 : 12;
            return $query->paginate($perPage);
        });

        // Convert product prices to current currency (optimized)
        $this->convertProductPricesOptimized($products);

        // Pre-compute variants data to avoid N+1 queries
        $this->precomputeVariantsData($products);

        return view('livewire.product-index', [
            'products' => $products
        ]);
    }

        /**
     * Build a unique cache key for the current query
     */
    protected function buildCacheKey()
    {
        $params = [
            'search' => $this->search,
            'sort' => $this->sortBy,
            'category' => $this->category,
            'page' => request()->get('page', 1),
            'per_page' => request()->routeIs('home') ? 8 : 12,
            'route' => request()->route()->getName(),
            'currency' => $this->currencyCode
        ];

        $cacheKey = 'products_index_' . md5(serialize($params));

        // Track cache keys for cleanup
        $keys = Cache::get('product_index_cache_keys', []);
        if (!in_array($cacheKey, $keys)) {
            $keys[] = $cacheKey;
            Cache::put('product_index_cache_keys', $keys, 3600);
        }

        return $cacheKey;
    }

        /**
     * Optimized price conversion with bulk processing
     */
    protected function convertProductPricesOptimized($products)
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Bulk convert all prices at once
            $products->getCollection()->transform(function ($product) use ($currencyService) {
                if ($product->price) {
                    $product->converted_price = $currencyService->convertFromUSD($product->price, $this->currencyCode);
                }
                if ($product->compare_price && $product->compare_price > 0) {
                    $product->converted_compare_price = $currencyService->convertFromUSD($product->compare_price, $this->currencyCode);
                }
                return $product;
            });
        } catch (Exception $e) {
            // Handle conversion error silently
        }
    }

    /**
     * Pre-compute variants data to avoid N+1 queries
     */
    protected function precomputeVariantsData($products)
    {
        $products->getCollection()->transform(function ($product) {
            // Add computed properties to avoid individual queries
            $product->has_variants = $product->variants && $product->variants->isNotEmpty();
            $product->variants_count = $product->variants ? $product->variants->count() : 0;

            // Pre-compute unique colors if variants exist
            if ($product->has_variants) {
                $product->unique_colors = $product->variants->unique('color')->pluck('color');
                $product->first_variant = $product->variants->first();
            }

            return $product;
        });
    }

    protected function checkCurrencyChange()
    {
        try {
            // Use the already cached currency info from the service
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

                // Clear product cache when currency changes
                $this->clearProductCache();
            }
        } catch (Exception $e) {
            // Handle error silently
        }
    }

    /**
     * Clear product cache when currency changes
     */
    protected function clearProductCache()
    {
        // Clear all product index caches
        $keys = Cache::get('product_index_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('product_index_cache_keys');

        // Clear currency cache to force refresh
        Cache::forget("currency_info_{$this->currencyCode}");
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
     * Safely get media URL with error handling
     */
    protected function getSafeMediaUrl($product, $collectionName = 'main_image', $conversionName = '')
    {
        try {
            if (!$product->media || $product->media->isEmpty()) {
                return null;
            }

            $media = $product->media->where('collection_name', $collectionName)->first();
            if (!$media || !$media->disk) {
                return null;
            }

            return $product->getFirstMediaUrl($collectionName, $conversionName);
        } catch (Exception $e) {
            Log::warning('Failed to get media URL', [
                'product_id' => $product->id,
                'collection' => $collectionName,
                'conversion' => $conversionName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
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
