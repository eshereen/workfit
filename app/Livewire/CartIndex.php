<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Services\CountryCurrencyService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class CartIndex extends Component
{
    public $cartItems = [];
    public $cartCount = 0;
    public $subtotal = 0;
    public $total = 0;
    public $shipping = 0;
    public $tax = 0;
    public $currencyCode = 'USD';
    public $currencySymbol = '$';
    public $isAutoDetected = false;

    public function mount()
    {
        Log::info('CartIndex component mounted - Starting');

        try {
            $this->loadCurrencyInfo();
            $this->loadCart();
            Log::info('CartIndex component mounted - Cart and currency loaded successfully');
        } catch (\Exception $e) {
            Log::error('CartIndex component mounted - Error loading cart or currency', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

            Log::info('Currency info loaded', [
                'currencyCode' => $this->currencyCode,
                'currencySymbol' => $this->currencySymbol,
                'isAutoDetected' => $this->isAutoDetected
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading currency info', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function loadCart()
    {
        Log::info('Loading cart...');

        try {
            Log::info('Getting CartService instance...');
            $cartService = app(CartService::class);
            Log::info('CartService resolved', ['class' => get_class($cartService)]);

            Log::info('Getting cart items...');
            $cartItems = $cartService->getCart();
            // Convert collection to array for modification
            $this->cartItems = $cartItems->toArray();
            Log::info('Cart items retrieved', ['count' => count($this->cartItems)]);

            Log::info('Getting cart count...');
            $this->cartCount = $cartService->getCount();
            Log::info('Cart count retrieved', ['count' => $this->cartCount]);

            Log::info('Getting cart totals...');
            $this->subtotal = $cartService->getSubtotal();
            $this->shipping = $cartService->getShippingCost();
            $this->tax = $cartService->getTaxAmount();
            $this->total = $cartService->getTotal();

            Log::info('Cart totals from service', [
                'subtotal' => $this->subtotal,
                'shipping' => $this->shipping,
                'tax' => $this->tax,
                'total' => $this->total
            ]);

            // Convert prices to current currency
            $this->convertPricesToCurrency();

            Log::info('Cart loaded successfully', [
                'cartItems' => $this->cartItems,
                'cartCount' => $this->cartCount,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'currencyCode' => $this->currencyCode,
                'currencySymbol' => $this->currencySymbol
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function convertPricesToCurrency()
    {
        if ($this->currencyCode === 'USD') {
            return; // No conversion needed
        }

        try {
            $currencyService = app(CountryCurrencyService::class);

            // Convert cart totals
            $originalSubtotal = $this->subtotal;
            $originalShipping = $this->shipping;
            $originalTax = $this->tax;
            $originalTotal = $this->total;

            $this->subtotal = $currencyService->convertFromUSD($this->subtotal, $this->currencyCode);
            $this->shipping = $currencyService->convertFromUSD($this->shipping, $this->currencyCode);
            $this->tax = $currencyService->convertFromUSD($this->tax, $this->currencyCode);
            $this->total = $currencyService->convertFromUSD($this->total, $this->currencyCode);

            Log::info('Cart totals converted', [
                'currency' => $this->currencyCode,
                'subtotal' => ['original' => $originalSubtotal, 'converted' => $this->subtotal],
                'shipping' => ['original' => $originalShipping, 'converted' => $this->shipping],
                'tax' => ['original' => $originalTax, 'converted' => $this->tax],
                'total' => ['original' => $originalTotal, 'converted' => $this->total]
            ]);

            // Convert individual cart item prices
            Log::info('Starting to convert cart item prices', [
                'cartItemsCount' => count($this->cartItems),
                'currencyCode' => $this->currencyCode
            ]);

            foreach ($this->cartItems as $key => $item) {
                Log::info('Processing cart item', [
                    'key' => $key,
                    'item_name' => $item['name'] ?? 'Unknown',
                    'has_price' => isset($item['price']),
                    'price' => $item['price'] ?? 'No price'
                ]);

                if (isset($item['price'])) {
                    $originalPrice = $item['price'];
                    $convertedPrice = $currencyService->convertFromUSD($originalPrice, $this->currencyCode);
                    $this->cartItems[$key]['converted_price'] = $convertedPrice;

                    Log::info('Cart item price converted', [
                        'item_name' => $item['name'],
                        'original_price' => $originalPrice,
                        'converted_price' => $convertedPrice,
                        'currency' => $this->currencyCode,
                        'key' => $key
                    ]);

                    // Verify the conversion was stored
                    Log::info('Verification - stored converted price', [
                        'key' => $key,
                        'stored_price' => $this->cartItems[$key]['converted_price'] ?? 'Not stored'
                    ]);
                }
            }

            Log::info('Finished converting cart item prices', [
                'final_cart_items' => $this->cartItems
            ]);

            Log::info('Prices converted to currency', [
                'currencyCode' => $this->currencyCode,
                'subtotal' => $this->subtotal,
                'total' => $this->total
            ]);
        } catch (\Exception $e) {
            Log::error('Error converting prices to currency', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[On('currencyChanged')]
    public function refreshCurrency()
    {
        Log::info('Currency changed event received, refreshing...');
        $this->loadCurrencyInfo();
        $this->loadCart(); // Reload cart to get fresh prices and convert them
    }

    #[On('cartUpdated')]
    public function refreshCart()
    {
        Log::info('Refreshing cart...');
        $this->loadCart();
    }

    public function updateQuantity($rowId, $newQuantity)
    {
        Log::info('updateQuantity called', [
            'rowId' => $rowId,
            'newQuantity' => $newQuantity,
            'current_cart_items_count' => count($this->cartItems)
        ]);

        if ($newQuantity < 1) {
            Log::info('Quantity less than 1, returning');
            $this->dispatch('showNotification', [
                'message' => 'Quantity cannot be less than 1',
                'type' => 'warning'
            ]);
            return;
        }

        try {
            // Find the item to be updated for validation
            $itemToUpdate = collect($this->cartItems)->firstWhere('rowId', $rowId);
            if (!$itemToUpdate) {
                Log::warning('Item not found for quantity update', ['rowId' => $rowId]);
                $this->dispatch('showNotification', [
                    'message' => 'Item not found in cart',
                    'type' => 'error'
                ]);
                return;
            }

            Log::info('Item found for quantity update', [
                'rowId' => $rowId,
                'item_name' => $itemToUpdate['name'],
                'old_quantity' => $itemToUpdate['quantity'],
                'new_quantity' => $newQuantity
            ]);

            Log::info('Getting CartService...');
            $cartService = app(CartService::class);

            if (!$cartService) {
                throw new \Exception('CartService could not be resolved');
            }

            Log::info('CartService resolved successfully', [
                'cartServiceClass' => get_class($cartService)
            ]);

            Log::info('Calling updateQuantity on CartService...');
            $result = $cartService->updateQuantity($rowId, $newQuantity);

            if ($result) {
                Log::info('Quantity updated successfully in service', [
                    'rowId' => $rowId,
                    'newQuantity' => $newQuantity
                ]);

                // Update the local cart item quantity immediately for better UX
                foreach ($this->cartItems as $key => $item) {
                    if ($item['rowId'] === $rowId) {
                        $this->cartItems[$key]['quantity'] = $newQuantity;
                        break;
                    }
                }

                // Reload cart to get updated totals
                $this->loadCart();

                $this->dispatch('showNotification', [
                    'message' => 'Quantity updated successfully!',
                    'type' => 'success'
                ]);

                // Dispatch cart updated event
                $this->dispatch('cartUpdated');
            } else {
                Log::error('CartService updateQuantity returned false', ['rowId' => $rowId]);
                $this->dispatch('showNotification', [
                    'message' => 'Failed to update quantity',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating quantity', [
                'rowId' => $rowId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('showNotification', [
                'message' => 'Error updating quantity: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function decreaseQuantity($rowId)
    {
        Log::info('decreaseQuantity called', ['rowId' => $rowId]);

        // Find the current item
        $item = collect($this->cartItems)->firstWhere('rowId', $rowId);
        if (!$item) {
            Log::warning('Item not found for quantity decrease', ['rowId' => $rowId]);
            $this->dispatch('showNotification', [
                'message' => 'Item not found in cart',
                'type' => 'error'
            ]);
            return;
        }

        $currentQuantity = $item['quantity'];
        $newQuantity = $currentQuantity - 1;

        Log::info('Decreasing quantity', [
            'rowId' => $rowId,
            'item_name' => $item['name'],
            'current_quantity' => $currentQuantity,
            'new_quantity' => $newQuantity
        ]);

        if ($newQuantity < 1) {
            Log::info('Quantity would be less than 1, removing item instead');
            $this->removeItem($rowId);
            return;
        }

        $this->updateQuantity($rowId, $newQuantity);
    }

    public function increaseQuantity($rowId)
    {
        Log::info('increaseQuantity called', ['rowId' => $rowId]);

        // Find the current item
        $item = collect($this->cartItems)->firstWhere('rowId', $rowId);
        if (!$item) {
            Log::warning('Item not found for quantity increase', ['rowId' => $rowId]);
            $this->dispatch('showNotification', [
                'message' => 'Item not found in cart',
                'type' => 'error'
            ]);
            return;
        }

        $currentQuantity = $item['quantity'];
        $newQuantity = $currentQuantity + 1;

        Log::info('Increasing quantity', [
            'rowId' => $rowId,
            'item_name' => $item['name'],
            'current_quantity' => $currentQuantity,
            'new_quantity' => $newQuantity
        ]);

        $this->updateQuantity($rowId, $newQuantity);
    }

    public function removeItem($rowId)
    {
        Log::info('removeItem called', [
            'rowId' => $rowId,
            'cartItemsCount' => count($this->cartItems)
        ]);

        try {
            // Find the item to be removed for logging
            $itemToRemove = collect($this->cartItems)->firstWhere('rowId', $rowId);
            if ($itemToRemove) {
                Log::info('Item found for removal', [
                    'rowId' => $rowId,
                    'item_name' => $itemToRemove['name'],
                    'item_price' => $itemToRemove['price']
                ]);
            } else {
                Log::warning('Item not found for removal', ['rowId' => $rowId]);
                $this->dispatch('showNotification', [
                    'message' => 'Item not found in cart',
                    'type' => 'error'
                ]);
                return;
            }

            $cartService = app(CartService::class);
            $result = $cartService->removeItem($rowId);

            if ($result) {
                Log::info('Item removed successfully from CartService', ['rowId' => $rowId]);

                // Clear the current cart items to force a fresh reload
                $this->cartItems = [];
                $this->cartCount = 0;
                $this->subtotal = 0;
                $this->shipping = 0;
                $this->tax = 0;
                $this->total = 0;

                // Reload cart to get updated data
                $this->loadCart();

                $this->dispatch('showNotification', [
                    'message' => 'Item removed from cart!',
                    'type' => 'success'
                ]);

                // Also dispatch cart updated event
                $this->dispatch('cartUpdated');
            } else {
                Log::error('CartService removeItem returned false', ['rowId' => $rowId]);
                $this->dispatch('showNotification', [
                    'message' => 'Failed to remove item from cart',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error removing item from cart', [
                'rowId' => $rowId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('showNotification', [
                'message' => 'Error removing item: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearCart()
    {
        try {
            $cartService = app(CartService::class);
            $cartService->clearCart();
            $this->loadCart();

            $this->dispatch('showNotification', [
                'message' => 'Cart cleared successfully!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Error clearing cart: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        Log::info('CartIndex render called', [
            'cartCount' => $this->cartCount,
            'cartItemsCount' => is_countable($this->cartItems) ? count($this->cartItems) : 'not countable',
            'currencyCode' => $this->currencyCode,
            'currencySymbol' => $this->currencySymbol
        ]);
        return view('livewire.cart-index');
    }

    // Method for testing currency conversion
    public function testCurrencyConversion()
    {
        Log::info('Testing currency conversion...');
        $this->loadCurrencyInfo();

        // Test a simple conversion first
        $currencyService = app(CountryCurrencyService::class);
        $testAmount = 10.00;
        $convertedAmount = $currencyService->convertFromUSD($testAmount, $this->currencyCode);

        Log::info('Test conversion result', [
            'original_amount' => $testAmount,
            'currency_code' => $this->currencyCode,
            'converted_amount' => $convertedAmount
        ]);

        // Now convert cart prices
        $this->convertPricesToCurrency();

        $this->dispatch('showNotification', [
            'message' => "Currency conversion test completed. Test: $10.00 USD = {$this->currencySymbol}{$convertedAmount} {$this->currencyCode}. Check logs for details.",
            'type' => 'info'
        ]);
    }

    // Method for testing remove item functionality
    public function testRemoveItem()
    {
        if (empty($this->cartItems)) {
            $this->dispatch('showNotification', [
                'message' => 'No items in cart to test removal',
                'type' => 'warning'
            ]);
            return;
        }

        // Get the first item's rowId for testing
        $firstItem = reset($this->cartItems);
        $testRowId = $firstItem['rowId'];

        Log::info('Testing remove item functionality', [
            'test_row_id' => $testRowId,
            'item_name' => $firstItem['name']
        ]);

        $this->dispatch('showNotification', [
            'message' => "Testing removal of: {$firstItem['name']} (Row ID: {$testRowId})",
            'type' => 'info'
        ]);

        // Actually remove the item
        $this->removeItem($testRowId);
    }

    // Method for testing quantity update functionality
    public function testUpdateQuantity()
    {
        if (empty($this->cartItems)) {
            $this->dispatch('showNotification', [
                'message' => 'No items in cart to test quantity update',
                'type' => 'warning'
            ]);
            return;
        }

        // Get the first item's rowId for testing
        $firstItem = reset($this->cartItems);
        $testRowId = $firstItem['rowId'];
        $currentQuantity = $firstItem['quantity'];
        $newQuantity = $currentQuantity + 1;

        Log::info('Testing quantity update functionality', [
            'test_row_id' => $testRowId,
            'item_name' => $firstItem['name'],
            'current_quantity' => $currentQuantity,
            'new_quantity' => $newQuantity
        ]);

        $this->dispatch('showNotification', [
            'message' => "Testing quantity update: {$firstItem['name']} from {$currentQuantity} to {$newQuantity}",
            'type' => 'info'
        ]);

        // Actually update the quantity
        $this->updateQuantity($testRowId, $newQuantity);
    }

    // Method for testing increase quantity functionality
    public function testIncreaseQuantity()
    {
        if (empty($this->cartItems)) {
            $this->dispatch('showNotification', [
                'message' => 'No items in cart to test increase quantity',
                'type' => 'warning'
            ]);
            return;
        }

        // Get the first item's rowId for testing
        $firstItem = reset($this->cartItems);
        $testRowId = $firstItem['rowId'];
        $currentQuantity = $firstItem['quantity'];

        Log::info('Testing increase quantity functionality', [
            'test_row_id' => $testRowId,
            'item_name' => $firstItem['name'],
            'current_quantity' => $currentQuantity
        ]);

        $this->dispatch('showNotification', [
            'message' => "Testing increase quantity: {$firstItem['name']} from {$currentQuantity}",
            'type' => 'info'
        ]);

        // Actually increase the quantity
        $this->increaseQuantity($testRowId);
    }

    // Method for testing decrease quantity functionality
    public function testDecreaseQuantity()
    {
        if (empty($this->cartItems)) {
            $this->dispatch('showNotification', [
                'message' => 'No items in cart to test decrease quantity',
                'type' => 'warning'
            ]);
            return;
        }

        // Get the first item's rowId for testing
        $firstItem = reset($this->cartItems);
        $testRowId = $firstItem['rowId'];
        $currentQuantity = $firstItem['quantity'];

        Log::info('Testing decrease quantity functionality', [
            'test_row_id' => $testRowId,
            'item_name' => $firstItem['name'],
            'current_quantity' => $currentQuantity
        ]);

        $this->dispatch('showNotification', [
            'message' => "Testing decrease quantity: {$firstItem['name']} from {$currentQuantity}",
            'type' => 'info'
        ]);

        // Actually decrease the quantity
        $this->decreaseQuantity($testRowId);
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
