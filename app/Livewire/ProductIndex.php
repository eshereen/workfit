<?php

namespace App\Livewire;

use Exception;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use App\Services\BestSellerService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductIndex extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q', except: '')]
    public $search = '';
    
    #[Url(as: 'sort', except: 'newest')]
    public $sortBy = 'newest';
    
    public $category = ''; // Category ID
    public $categoryModel = null; // Category model for display
    public $categorySlug = null; // SEO-friendly category slug
    public $subcategory = ''; // Subcategory ID
    public $subcategoryModel = null; // Subcategory model
    public $subcategorySlug = null; // SEO-friendly subcategory slug
    public $wishlistProductIds = [];
    public $currencyCode = 'USD';
    public $currencySymbol = '$';
    public $isAutoDetected = false;
    public $useBestSellerLogic = false;
    public $disableEagerLoading = false; // Optimization: Disable eager loading for grids below the fold

    // Cart modal properties
    public $showVariantModal = false;
    public $selectedProduct = null;
    public $selectedVariantId = null;
    public $selectedVariant = null;
    public $quantity = 1;

    // Removed: public $products = null; - conflicts with view variable

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

        $this->loadCurrencyInfo();
        // Force a re-render so paginated items get fresh conversion
        $this->dispatch('$refresh');

       
    }

    #[On('currency-changed')]
    public function handleCurrencyChanged($currencyCode = null)
    {
        $this->loadCurrencyInfo();
    }

    #[On('global-currency-changed')]
    public function handleGlobalCurrencyChanged($currencyCode = null)
    {
        $this->loadCurrencyInfo();
    }

    // Alternative method to handle currency changes
    public function handleCurrencyChange($currencyCode = null)
    {
        Log::info('Manual currency change triggered', ['currency' => $currencyCode]);
        $this->loadCurrencyInfo();

        // If modal is open, re-convert variant prices
        if ($this->showVariantModal && $this->selectedProduct) {
            $this->convertVariantPrices();
        }
    }

    public $passedProducts = null;

    public function mount($products = null, $category = null, $categorySlug = null, $subcategory = null, $subcategorySlug = null)
    {
        try {
            $this->passedProducts = $products;
            
            // If category is a model object, extract its properties
            if ($category instanceof Category) {
                $this->categoryModel = $category;
                $this->category = $category->id;
                $this->categorySlug = $category->slug;
            }
            // If categorySlug is provided, resolve it to category ID for SEO-friendly URLs
            elseif ($categorySlug) {
                $this->categorySlug = $categorySlug;
                $categoryModel = Category::where('slug', $categorySlug)->first();
                if ($categoryModel) {
                    $this->categoryModel = $categoryModel;
                    $this->category = $categoryModel->id;
                }
            } elseif ($category) {
                // Otherwise use the category ID directly
                $this->category = $category;
            }

            // Handle subcategory
            if ($subcategory) {
                $this->subcategory = $subcategory;
                $this->subcategoryModel = \App\Models\Subcategory::find($subcategory);
            }
            if ($subcategorySlug) {
                $this->subcategorySlug = $subcategorySlug;
                $subcategoryModel = \App\Models\Subcategory::where('slug', $subcategorySlug)->first();
                if ($subcategoryModel) {
                    $this->subcategoryModel = $subcategoryModel;
                    $this->subcategory = $subcategoryModel->id;
                }
            }
            
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

            Log::info('ProductIndex: Loading currency info', [
                'currency_info' => $currencyInfo
            ]);

            $this->currencyCode = $currencyInfo['currency_code'];
            $this->currencySymbol = $currencyInfo['currency_symbol'];
            $this->isAutoDetected = $currencyInfo['is_auto_detected'];

          

            // Convert product prices to current currency
            $this->convertProductPrices();
        } catch (Exception $e) {
            Log::error('ProductIndex: Error loading currency info', ['error' => $e->getMessage()]);
        }
    }

    protected function convertProductPrices($products = null)
    {
        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert all product prices in the collection
            $productsToConvert = $products;
            if ($productsToConvert) {
                foreach ($productsToConvert as $product) {
                    //  set converted_price for consistency (even for USD)
                    if ($product->price) {
                        if ($this->currencyCode === 'USD') {
                            $product->converted_price = $product->price;
                        } else {
                            $product->converted_price = $currencyService->convertFromUSD($product->price, $this->currencyCode);
                        }
                    }

                    // Always set converted_compare_price for consistency (even for USD)
                    if ($product->compare_price && $product->compare_price > 0) {
                        if ($this->currencyCode === 'USD') {
                            $product->converted_compare_price = $product->compare_price;
                        } else {
                            $product->converted_compare_price = $currencyService->convertFromUSD($product->compare_price, $this->currencyCode);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('ProductIndex: Error in convertProductPrices', [
                'error' => $e->getMessage(),
                'currency_code' => $this->currencyCode
            ]);
            // Fallback: set original prices if conversion fails
            if ($productsToConvert) {
                foreach ($productsToConvert as $product) {
                    if ($product->price && !isset($product->converted_price)) {
                        $product->converted_price = $product->price;
                    }
                    if ($product->compare_price && $product->compare_price > 0 && !isset($product->converted_compare_price)) {
                        $product->converted_compare_price = $product->compare_price;
                    }
                }
            }
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
                        $originalPrice = $variant->price;
                        $variant->converted_price = $currencyService->convertFromUSD($originalPrice, $this->currencyCode);
                        Log::info('ProductIndex: Variant price converted', [
                            'variant_id' => $variant->id,
                            'original' => $originalPrice,
                            'converted' => $variant->converted_price
                        ]);
                    }
                    return $variant;
                });
            }
        } catch (Exception $e) {
            Log::error('ProductIndex: Conversion error', ['error' => $e->getMessage()]);
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
        // Ensure we have the latest currency info
        $this->loadCurrencyInfo();

        // Get product with variants - no caching to ensure fresh currency conversion
        $this->selectedProduct = Product::with(['variantsOptimized'])
            ->select('id', 'name', 'slug', 'price', 'compare_price')
            ->find($productId);

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

      


        // Ensure the selected variant has converted prices
        if ($this->selectedVariant && $this->currencyCode !== 'USD') {
            $currencyService = app(CountryCurrencyService::class);

            // If variant has its own price, convert it
            if ($this->selectedVariant->price) {
                $originalPrice = $this->selectedVariant->price;
                $this->selectedVariant->converted_price = $currencyService->convertFromUSD($originalPrice, $this->currencyCode);

               
            } else {
                // If variant has no price, use product price
                if ($this->selectedProduct->price) {
                    $originalPrice = $this->selectedProduct->price;
                    $this->selectedVariant->converted_price = $currencyService->convertFromUSD($originalPrice, $this->currencyCode);

                    Log::info('ProductIndex: Using product price for variant', [
                        'variant_id' => $variantId,
                        'product_price' => $originalPrice,
                        'converted_price' => $this->selectedVariant->converted_price,
                        'currency_code' => $this->currencyCode
                    ]);
                }
            }
        }

        // Reset quantity if it exceeds stock or if stock is invalid
        if ($this->selectedVariant) {
            $stock = $this->selectedVariant->stock;

            // If stock is negative or zero, set quantity to 0 (out of stock)
            if ($stock <= 0) {
                $this->quantity = 0;
              
            }
            // If quantity exceeds stock, reset to stock level
            elseif ($this->quantity > $stock) {
                $this->quantity = $stock;
              
            }
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
                Log::warning('Product not found for cart addition', ['product_id' => $productId]);
                $this->dispatch('showNotification', [
                    'message' => 'Product not found.',
                    'type' => 'error'
                ]);
                return;
            }

            // Check stock before adding to cart
            if ($product->quantity < $quantity) {
                if ($product->quantity <= 0) {
                    $this->dispatch('showNotification', [
                        'message' => 'This product is currently out of stock.',
                        'type' => 'error'
                    ]);
                } else {
                    $this->dispatch('showNotification', [
                        'message' => 'Only ' . $product->quantity . ' items available in stock.',
                        'type' => 'error'
                    ]);
                }
                return;
            }

            $cartService->addItem($product, $quantity);

          

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
        try {
            // Use passed products if available (from homepage sections)
            if ($this->passedProducts && $this->passedProducts->isNotEmpty()) {
                $productsToDisplay = $this->passedProducts;
                // Convert currency for passed products
                $this->convertProductPricesOptimized($productsToDisplay);
            } else {
                // Build query without caching to ensure accurate pagination
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

               
                if (!request()->routeIs('home')) {
                    $with[] = 'subcategory:id,name,slug';
                }
               
                $with[] = 'variants:id,product_id,color,size,price,stock';

                $query = Product::with($with)
                    ->select('id', 'name', 'slug', 'description', 'price', 'compare_price', 'category_id', 'subcategory_id', 'active', 'featured', 'created_at')
                    ->where('products.active', true);

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

                if ($this->subcategory) {
                    $query->where('subcategory_id', $this->subcategory);
                }

                // Apply best seller logic if enabled (only on first page for accurate pagination)
                $currentPage = request()->get('page', 1);
                if ($this->useBestSellerLogic && $this->sortBy === 'newest' && $currentPage == 1) {
                    $bestSellerService = app(BestSellerService::class);
                    $perPage = request()->routeIs('home') ? 8 : 40;

                    // Get products with best seller priority for first page only
                    $products = $bestSellerService->getProductsWithBestSellerPriority(
                        clone $query,
                        $perPage,
                        $this->category
                    );

                    // For first page, create simple paginator
                    $productsToDisplay = new \Illuminate\Pagination\LengthAwarePaginator(
                        $products,
                        (clone $query)->count(), // Get total count for pagination
                        $perPage,
                        $currentPage,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                } else {
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
                            $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                            break;
                    }

                    $perPage = request()->routeIs('home') ? 8 : 40;
                    $productsToDisplay = $query->paginate($perPage);
                }
            }

          

          
            if (!$productsToDisplay) {
                // Only fallback if completely null - run query directly
                $productsToDisplay = Product::with([
                    'category:id,name,slug',
                    'media' => function ($query) {
                        $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                              ->whereIn('collection_name', ['main_image', 'product_images'])
                              ->whereNotNull('disk')
                              ->orderBy('collection_name', 'asc')
                              ->orderBy('id', 'asc');
                    },
                    'variants:id,product_id,color,size,price,stock'
                ])
                ->select('id', 'name', 'slug', 'description', 'price', 'compare_price', 'category_id', 'subcategory_id', 'active', 'featured', 'created_at')
                ->where('products.active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(request()->routeIs('home') ? 8 : 40);
            }

            // Convert product prices to current currency (optimized) - only if not already converted
            if (!($this->passedProducts && $this->passedProducts->isNotEmpty())) {
                $this->convertProductPricesOptimized($productsToDisplay);
            }

            // Pre-compute variants data to avoid N+1 queries
            $this->precomputeVariantsData($productsToDisplay);

            return view('livewire.product-index', [
                'products' => $productsToDisplay
            ]);
        } catch (\Exception $e) {
            // Log error and return fallback products query
            Log::error('ProductIndex render error: ' . $e->getMessage());

            // Return a basic query as fallback to maintain pagination
            $fallbackProducts = Product::with([
                'category:id,name,slug',
                'media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->whereIn('collection_name', ['main_image', 'product_images'])
                          ->whereNotNull('disk');
                },
                'variants:id,product_id,color,size,price,stock'
            ])
            ->select('id', 'name', 'slug', 'description', 'price', 'compare_price', 'category_id', 'subcategory_id', 'active', 'featured', 'created_at')
            ->where('products.active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

            return view('livewire.product-index', [
                'products' => $fallbackProducts
            ]);
        }
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
        try {
            $currencyService = app(CountryCurrencyService::class);

            // Get the collection to transform
            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
                $collection = $products->getCollection();
            } else {
                $collection = $products;
            }

            // Transform the collection
            $collection->transform(function ($product) use ($currencyService) {
                // Always set converted_price for consistency (even for USD)
                if ($product->price) {
                    if ($this->currencyCode === 'USD') {
                        $product->converted_price = $product->price;
                    } else {
                        $product->converted_price = $currencyService->convertFromUSD($product->price, $this->currencyCode);
                    }
                }

                // Always set converted_compare_price for consistency (even for USD)
                if ($product->compare_price && $product->compare_price > 0) {
                    if ($this->currencyCode === 'USD') {
                        $product->converted_compare_price = $product->compare_price;
                    } else {
                        $product->converted_compare_price = $currencyService->convertFromUSD($product->compare_price, $this->currencyCode);
                    }
                }

                return $product;
            });

            // If it was a paginator, set the transformed collection back
            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
                $products->setCollection($collection);
            }
        } catch (Exception $e) {
            Log::error('ProductIndex: Error converting prices', [
                'error' => $e->getMessage(),
                'currency_code' => $this->currencyCode
            ]);
            // Fallback: set original prices if conversion fails
            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
                $collection = $products->getCollection();
            } else {
                $collection = $products;
            }

            $collection->transform(function ($product) {
                if ($product->price && !isset($product->converted_price)) {
                    $product->converted_price = $product->price;
                }
                if ($product->compare_price && $product->compare_price > 0 && !isset($product->converted_compare_price)) {
                    $product->converted_compare_price = $product->compare_price;
                }
                return $product;
            });

            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
                $products->setCollection($collection);
            }
        }
    }

    /**
     * Pre-compute variants data to avoid N+1 queries
     */
    protected function precomputeVariantsData($products)
    {
        // Get the collection to transform
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
            $collection = $products->getCollection();
        } else {
            $collection = $products;
        }

        // Transform the collection
        $collection->transform(function ($product) {
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

        // If it was a paginator, set the transformed collection back
        if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator || $products instanceof \Illuminate\Pagination\Paginator) {
            $products->setCollection($collection);
        }
    }

    protected function checkCurrencyChange()
    {
        try {
            // Use the already cached currency info from the service
            $currencyService = app(CountryCurrencyService::class);
            $currentInfo = $currencyService->getCurrentCurrencyInfo();

            if ($this->currencyCode !== $currentInfo['currency_code']) {
             

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

    /**
     * Check if a product is a best seller
     */
    public function isBestSeller($productId)
    {
        if (!$this->useBestSellerLogic) {
            return false;
        }

        $bestSellerService = app(BestSellerService::class);
        return $bestSellerService->isBestSeller($productId, $this->category);
    }
}
