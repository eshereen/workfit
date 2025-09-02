<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected $cartKey = 'shopping_cart';

    public function __construct()
    {
        Log::info('CartService initialized with session-based storage');
    }

    protected function getCartKey()
    {
        return $this->cartKey;
    }

    public function getCart()
    {
        $cart = Session::get($this->getCartKey(), collect());
        Log::info('Getting cart from session', ['count' => $cart->count()]);

        // Ensure we return a collection of arrays, not objects
        $cartArray = $cart->map(function($item) {
            return (array) $item;
        });

        return $cartArray->sortBy('name');
    }

    public function addItem(Product $product, $quantity = 1, $size = null, $color = null)
    {
        try {
            $cart = $this->getCart();

            $options = [
                'image' => $product->getFirstMediaUrl('main_image'),
                'size' => $size,
                'color' => $color,
                'slug' => $product->slug
            ];

            // Check if product already exists in cart
            $existingItem = $cart->firstWhere('id', $product->id);

            if ($existingItem) {
                // Update existing item quantity
                $existingItem['quantity'] += $quantity;
                $cart = $cart->map(function($item) use ($existingItem) {
                    if ($item['id'] === $existingItem['id']) {
                        return $existingItem;
                    }
                    return $item;
                });
            } else {
                // Add new item
                $newItem = [
                    'id' => $product->id,
                    'rowId' => uniqid('item_'),
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'attributes' => $options,
                    'associatedModel' => $product
                ];
                $cart->push($newItem);
            }

            // Save cart to session
            Session::put($this->getCartKey(), $cart);

            Log::info('Product added to cart', [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'cart_count' => $this->getCount(),
                'session_id' => session()->getId()
            ]);

            return $cart->last();
        } catch (Exception $e) {
            Log::error('Error adding product to cart', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function updateQuantity($rowId, $quantity)
    {
        try {
            Log::info('CartService updateQuantity called', [
                'rowId' => $rowId,
                'newQuantity' => $quantity
            ]);

            $cart = $this->getCart();
            Log::info('Cart before quantity update', ['count' => $cart->count()]);

            // Find the item to be updated for logging
            $itemToUpdate = $cart->firstWhere('rowId', $rowId);
            if (!$itemToUpdate) {
                Log::warning('Item not found for quantity update', ['rowId' => $rowId]);
                return false;
            }

            Log::info('Item found for quantity update', [
                'rowId' => $rowId,
                'item_name' => $itemToUpdate['name'],
                'old_quantity' => $itemToUpdate['quantity'],
                'new_quantity' => $quantity
            ]);

            // Create a new collection with the updated item
            $updatedCart = $cart->map(function($item) use ($rowId, $quantity) {
                if ($item['rowId'] === $rowId) {
                    $item['quantity'] = $quantity;
                }
                return $item;
            });

            // Update the session
            Session::put($this->getCartKey(), $updatedCart);

            Log::info('Quantity updated successfully', [
                'rowId' => $rowId,
                'newQuantity' => $quantity,
                'cart_count_after' => $updatedCart->count()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Error updating quantity', [
                'rowId' => $rowId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function removeItem($rowId)
    {
        try {
            Log::info('CartService removeItem called', ['rowId' => $rowId]);

            $cart = $this->getCart();
            Log::info('Cart before removal', ['count' => $cart->count()]);

            // Find the item to be removed for logging
            $itemToRemove = $cart->firstWhere('rowId', $rowId);
            if ($itemToRemove) {
                Log::info('Item found for removal', [
                    'rowId' => $rowId,
                    'item_name' => $itemToRemove['name'],
                    'item_price' => $itemToRemove['price']
                ]);
            } else {
                Log::warning('Item not found in cart', ['rowId' => $rowId]);
                return false;
            }

            $cart = $cart->reject(function($item) use ($rowId) {
                return $item['rowId'] === $rowId;
            });

            Log::info('Cart after removal', ['count' => $cart->count()]);

            Session::put($this->getCartKey(), $cart);

            Log::info('Item removed successfully', ['rowId' => $rowId]);

            return true;
        } catch (Exception $e) {
            Log::error('Error removing item from cart', [
                'rowId' => $rowId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function clearCart()
    {
        Session::forget($this->getCartKey());
        Log::info('Cart cleared');
    }

    public function getSubtotal()
    {
        $cart = $this->getCart();
        return $cart->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getTotal()
    {
        return $this->getSubtotal() + $this->getShippingCost() + $this->getTaxAmount();
    }

    public function getCount()
    {
        $cart = $this->getCart();
        return $cart->sum('quantity');
    }

    public function getShippingCost()
    {
        return $this->isEmpty() ? 0 : 10.00; // Example flat rate
    }

    public function getTaxAmount()
    {
        return $this->isEmpty() ? 0 : $this->getSubtotal() * 0.1; // Example 10% tax
    }

    public function isEmpty()
    {
        return $this->getCart()->isEmpty();
    }

    public function addItemWithVariant(Product $product, ProductVariant $variant, $quantity = 1)
    {
        try {
            $cart = $this->getCart();

            $options = [
                'image' => $product->getFirstMediaUrl('main_image'),
                'size' => $variant->size,
                'color' => $variant->color,
                'color_code' => $variant->color_code,
                'slug' => $product->slug,
                'variant_id' => $variant->id
            ];

            // Use variant price if available, otherwise product price
            $price = $variant->price ?? $product->price;

            // Check if product with same variant already exists in cart
            $existingItem = $cart->firstWhere('id', $product->id . '-' . $variant->id);

            if ($existingItem) {
                // Update existing item quantity
                $existingItem['quantity'] += $quantity;
                $cart = $cart->map(function($item) use ($existingItem) {
                    if ($item['id'] === $existingItem['id']) {
                        return $existingItem;
                    }
                    return $item;
                });

                Log::info('Product with variant quantity updated in cart', [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'old_quantity' => $existingItem['quantity'] - $quantity,
                    'new_quantity' => $existingItem['quantity'],
                    'cart_count' => $this->getCount()
                ]);
            } else {
                // Add new item
                $newItem = [
                    'id' => $product->id . '-' . $variant->id,
                    'rowId' => uniqid('variant_'),
                    'name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'attributes' => $options,
                    'associatedModel' => $product
                ];

                $cart->push($newItem);

                Log::info('Product with variant added to cart', [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'cart_count' => $this->getCount()
                ]);
            }

            Session::put($this->getCartKey(), $cart);

            return $existingItem ?? $newItem;
        } catch (Exception $e) {
            Log::error('Error adding product with variant to cart', [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
