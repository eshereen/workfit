<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\ProductVariant;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\CartUpdateRequest;

class CartController extends Controller
{
    protected $cartService;
    protected $currencyService;

    public function __construct(CartService $cartService, CountryCurrencyService $currencyService)
    {
        $this->cartService = $cartService;
        $this->currencyService = $currencyService;
    }

    /**
     * Display the shopping cart page
     */
    public function index()
    {
        // Get current currency info
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Just render the view - the Livewire component will handle all cart operations
        return view('cart', compact('currencyInfo'));
    }


    // ▶ Add item to cart (works for guests & logged-in users)
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $this->cartService->addItem(
            $product,
            $request->quantity,
            $request->size,
            $request->color
        );

        return response()->json([
            'success' => true,
            'cartCount' => $this->cartService->getCount(),
            'message' => 'Item added to cart'
        ]);
    }



    /**
     * Update cart item quantity
     */
    public function update(CartUpdateRequest $request, $rowId)
    {
        $this->cartService->updateQuantity($rowId, $request->validated()['quantity']);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cartService->getSubtotal(),
            'total' => $this->cartService->getTotal(),
            'cartCount' => $this->cartService->getCount()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($rowId)
    {
        $this->cartService->removeItem($rowId);

        return response()->json([
            'success' => true,
            'subtotal' => $this->cartService->getSubtotal(),
            'total' => $this->cartService->getTotal(),
            'cartCount' => $this->cartService->getCount()
        ]);
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return redirect()->route('cart.index')
            ->with('success', 'Your cart has been cleared');
    }

    /**
     * Get cart count (for AJAX requests)
     */
    public function count()
    {
        return response()->json([
            'count' => $this->cartService->getCount()
        ]);
    }
    // For simple products
public function quickAdd(Request $request, Product $product)
{
    $request->validate(['quantity' => 'required|integer|min:1|max:10']);

    $this->cartService->addItem($product, $request->quantity);

    return response()->json([
        'success' => true,
        'cartCount' => $this->cartService->getCount(),
        'message' => 'Product added to cart'
    ]);
}

// For products with variants
public function addWithVariant(Request $request, Product $product)
{
    $validated = $request->validate([
        'quantity' => 'required|integer|min:1|max:10',
        'variant_id' => 'required|exists:product_variants,id'
    ]);

    $variant = ProductVariant::findOrFail($validated['variant_id']);

    $this->cartService->addItemWithVariant(
        $product,
        $variant,
        $validated['quantity']
    );

    return response()->json([
        'success' => true,
        'cartCount' => $this->cartService->getCount(),
        'message' => 'Product added to cart'
    ]);
}
}
