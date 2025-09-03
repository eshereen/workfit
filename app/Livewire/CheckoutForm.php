<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Country;
use App\Services\PaymentMethodResolver;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Log;

class CheckoutForm extends Component
{
    // Customer Information
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $phoneNumber = '';

    // Billing Address fields
    public $billingCountry = null;
    public $billingState = '';
    public $billingCity = '';
    public $billingAddress = '';
    public $billingBuildingNumber = '';

    // Shipping Address fields
    public $shippingCountry = null;
    public $shippingState = '';
    public $shippingCity = '';
    public $shippingAddress = '';
    public $shippingBuildingNumber = '';
    public $useBillingForShipping = false;

    // Payment fields
    public $paymentMethods = [];
    public $selectedPaymentMethod = null;
    public $paypalPaymentType = 'credit_card';
    public $creditCardAvailable = false;

    // Currency info
    public $currentCurrency = 'USD';
    public $currentSymbol = '$';

        // Loyalty points discount
    public $loyaltyDiscount = 0;
    public $loyaltyPointsApplied = 0;

    public function mount()
    {
        $this->loadInitialData();

        // Check stock availability and show warnings
        $this->checkStockAvailability();
    }

    protected function loadInitialData()
    {
        // Get current country from session or default
        $countryCode = session('checkout_country', 'EG');

        // Find country and set defaults
        $country = Country::where('code', $countryCode)->first();
        if ($country) {
            $this->billingCountry = $country->id;
            $this->shippingCountry = $country->id;
        }

        // Load payment methods for current country
        $this->updatePaymentMethods($countryCode);

        // Load currency info only if no manual currency is set
        if (!session('currency_initialized', false)) {
            $this->updateCurrencyInfo($countryCode);
        } else {
            // Just load the current currency without changing it
            $currencyService = app(CountryCurrencyService::class);
            $currencyInfo = $currencyService->getCurrentCurrencyInfo();
            $this->currentCurrency = $currencyInfo['currency_code'];
            $this->currentSymbol = $currencyInfo['currency_symbol'];
        }
    }

    public function updatedBillingCountry($countryId)
    {
        $this->handleCountryChange($countryId, 'billing');
    }

    public function updatedShippingCountry($countryId)
    {
        $this->handleCountryChange($countryId, 'shipping');
    }

    public function updatedUseBillingForShipping($value)
    {
        if ($value) {
            // Copy all billing information to shipping
            $this->shippingCountry = $this->billingCountry;
            $this->shippingState = $this->billingState;
            $this->shippingCity = $this->billingCity;
            $this->shippingAddress = $this->billingAddress;
            $this->shippingBuildingNumber = $this->billingBuildingNumber;

            if ($this->billingCountry) {
                $this->handleCountryChange($this->billingCountry, 'shipping_from_billing');
            }
        }
    }

    protected function handleCountryChange($countryId, $source = 'unknown')
    {
        if (!$countryId) return;

        $country = Country::find($countryId);
        if (!$country) return;

        // Update session
        session(['checkout_country' => $country->code]);

        // Update payment methods
        $this->updatePaymentMethods($country->code);

        // Update currency
        $this->updateCurrencyInfo($country->code);

                // Dispatch events to other components
        $this->dispatch('country-changed', $country->code);
        $this->dispatch('currency-changed', $this->currentCurrency);
        $this->dispatch('global-currency-changed', $this->currentCurrency);

        // Force refresh of the CurrencySelector component specifically
        $this->dispatch('$refresh')->to('currency-selector');

        // Force refresh currency selector and update session
        $this->js("
            console.log('🚀 CheckoutForm: Updating currency to: {$this->currentCurrency}');

            // Dispatch browser events that all components can listen to
            window.dispatchEvent(new CustomEvent('livewire-currency-changed', {
                detail: { currency: '{$this->currentCurrency}', symbol: '{$this->currentSymbol}' }
            }));
            window.dispatchEvent(new CustomEvent('livewire-country-changed', {
                detail: { countryCode: '{$country->code}', currency: '{$this->currentCurrency}' }
            }));

            console.log('📡 Browser events dispatched for currency: {$this->currentCurrency}');

            // Update session currency via AJAX
            fetch('/currency/change', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    currency: '{$this->currentCurrency}'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Currency updated in session:', data);

                                // Find and refresh the currency selector component
                if (window.Livewire) {
                    // Try multiple selectors to find the currency component
                    let currencySelector = document.querySelector('.currency-selector[wire\\\\:id]');
                    if (!currencySelector) {
                        currencySelector = document.querySelector('[wire\\\\:id*=\"currency-selector\"]');
                    }
                    if (!currencySelector) {
                        currencySelector = document.querySelector('[x-data*=\"open\"][wire\\\\:id]');
                    }

                    console.log('🔍 Looking for CurrencySelector component...');
                    console.log('Found currency selector:', currencySelector ? 'YES' : 'NO');

                    if (currencySelector) {
                        const wireId = currencySelector.getAttribute('wire:id');
                        console.log('📍 Found CurrencySelector with wire:id:', wireId);

                        try {
                            const livewireComponent = window.Livewire.find(wireId);
                            console.log('🔗 Livewire component found:', livewireComponent ? 'YES' : 'NO');

                            if (livewireComponent) {
                                console.log('🔄 Refreshing CurrencySelector component');
                                livewireComponent.\$refresh();

                                // Also call updateToCurrency directly
                                setTimeout(() => {
                                    console.log('📞 Calling updateToCurrency with: {$this->currentCurrency}');
                                    livewireComponent.call('updateToCurrency', '{$this->currentCurrency}');
                                }, 200);

                                // Double-check with another method
                                setTimeout(() => {
                                    console.log('📞 Second attempt: handleCurrencyChanged');
                                    livewireComponent.call('handleCurrencyChanged', '{$this->currentCurrency}');
                                }, 400);
                            }
                        } catch (e) {
                            console.error('❌ Error refreshing CurrencySelector:', e);
                        }
                    } else {
                        console.error('⚠️ CurrencySelector component not found at all');
                        console.log('Available Livewire components:', window.Livewire.all());
                    }

                    // Also refresh order summary component
                    const orderSummary = document.querySelector('[wire\\\\:id*=\"order-summary\"]');
                    if (orderSummary) {
                        const orderWireId = orderSummary.getAttribute('wire:id');
                        try {
                            const orderLivewireComponent = window.Livewire.find(orderWireId);
                            if (orderLivewireComponent) {
                                console.log('🔄 Refreshing OrderSummary component');
                                orderLivewireComponent.\$refresh();
                            }
                        } catch (e) {
                            console.error('Error refreshing OrderSummary component:', e);
                        }
                    }

                    // Also refresh cart-wishlist-counts component if exists
                    const cartComponent = document.querySelector('[wire\\\\:id*=\"cart-wishlist-counts\"]');
                    if (cartComponent) {
                        const cartWireId = cartComponent.getAttribute('wire:id');
                        try {
                            const cartLivewireComponent = window.Livewire.find(cartWireId);
                            if (cartLivewireComponent) {
                                console.log('🔄 Refreshing cart component for currency update');
                                cartLivewireComponent.\$refresh();
                            }
                        } catch (e) {
                            console.error('Error refreshing cart component:', e);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('❌ Error updating currency:', error);
            });
        ");

        Log::info('CheckoutForm: Country change complete', [
            'country_code' => $country->code,
            'payment_methods' => $this->paymentMethods,
            'selected_method' => $this->selectedPaymentMethod,
            'currency' => $this->currentCurrency,
            'events_dispatched' => ['country-changed', 'currency-changed']
        ]);
    }

    protected function updatePaymentMethods($countryCode)
    {
        try {
            $resolver = app(PaymentMethodResolver::class);
            $availableMethods = $resolver->availableForCountry($countryCode);

            $this->paymentMethods = array_map(fn($m) => $m->value, $availableMethods);
            $this->creditCardAvailable = $resolver->isCreditCardAvailableForCountry($countryCode);

            // Don't set default payment method here - let PaymentMethodsSelector handle it
            // The PaymentMethodsSelector will dispatch an event to set this value
            Log::info('CheckoutForm: Payment methods loaded, waiting for PaymentMethodsSelector to set selection', [
                'available_methods' => $this->paymentMethods
            ]);

            // Reset PayPal payment type if credit card not available
            if (!$this->creditCardAvailable) {
                $this->paypalPaymentType = 'credit_card';
            }

        } catch (Exception $e) {

            // Fallback to default methods
            $this->paymentMethods = ['paypal'];
            // Don't set selectedPaymentMethod here - let PaymentMethodsSelector handle it
            $this->creditCardAvailable = false;
        }
    }

        protected function updateCurrencyInfo($countryCode)
    {
        try {
            $currencyService = app(CountryCurrencyService::class);

            // When country changes, always update currency based on country
            // This allows country changes to override manual selections
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                $currencyService->setPreferredCountry($country->id);
            }

            // Get updated currency info
            $currencyInfo = $currencyService->getCurrentCurrencyInfo();
            $this->currentCurrency = $currencyInfo['currency_code'];
            $this->currentSymbol = $currencyInfo['currency_symbol'];

        } catch (Exception $e) {

            // Keep current currency on error
        }
    }

    public function getCountriesProperty()
    {
        return Country::orderBy('name')->get();
    }

    // Validation rules for all form fields
    protected $rules = [
        'firstName' => 'required|string|min:2|max:50',
        'lastName' => 'required|string|min:2|max:50',
        'email' => 'required|email|max:255',
        'phoneNumber' => 'required|string|min:10|max:20',

        'billingCountry' => 'required|exists:countries,id',
        'billingState' => 'required|string|min:2|max:100',
        'billingCity' => 'required|string|min:2|max:100',
        'billingAddress' => 'required|string|min:5|max:255',
        'billingBuildingNumber' => 'nullable|string|max:50',

        'shippingCountry' => 'required_if:useBillingForShipping,false|exists:countries,id',
        'shippingState' => 'required_if:useBillingForShipping,false|string|min:2|max:100',
        'shippingCity' => 'required_if:useBillingForShipping,false|string|min:2|max:100',
        'shippingAddress' => 'required_if:useBillingForShipping,false|string|min:5|max:255',
        'shippingBuildingNumber' => 'nullable|string|max:50',

        'selectedPaymentMethod' => 'required|string|in:paypal,paymob,cash_on_delivery',
    ];

    // Custom validation messages
    protected $messages = [
        'firstName.required' => 'First name is required',
        'lastName.required' => 'Last name is required',
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'phoneNumber.required' => 'Phone number is required',
        'phoneNumber.min' => 'Phone number must be at least 10 characters',

        'billingCountry.required' => 'Billing country is required',
        'billingState.required' => 'State/Province is required',
        'billingCity.required' => 'City is required',
        'billingAddress.required' => 'Billing address is required',

        'shippingCountry.required_if' => 'Shipping country is required',
        'shippingState.required_if' => 'State/Province is required',
        'shippingCity.required_if' => 'City is required',
        'shippingAddress.required_if' => 'Shipping address is required',

        'selectedPaymentMethod.required' => 'Please select a payment method',
        'selectedPaymentMethod.in' => 'Please select a valid payment method',
    ];

    // Real-time validation for specific fields
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Listen for payment method selection from PaymentMethodsSelector
    #[On('payment-method-selected')]
    public function handlePaymentMethodSelected($method)
    {
        $this->selectedPaymentMethod = $method;
    }

    // Listen for PayPal payment type change
    #[On('paypal-payment-type-changed')]
    public function handlePayPalPaymentTypeChanged($type)
    {
        // Always set to credit card for simplified flow
        $this->paypalPaymentType = 'credit_card';
    }

    // Listen for loyalty points events
    #[On('country-changed')]
    public function handleCountryChanged($countryCode)
    {
        $this->updatePaymentMethods($countryCode);
        $this->updateCurrencyInfo($countryCode);
    }

        #[On('loyaltyPointsApplied')]
    public function handleLoyaltyPointsApplied($data)
    {
        $this->loyaltyPointsApplied = $data['points'];
        // Convert loyalty discount from USD to local currency using service container
        $loyaltyDiscountUSD = $data['value'];
        $currencyService = app(CountryCurrencyService::class);
        $this->loyaltyDiscount = $currencyService->convertFromUSD($loyaltyDiscountUSD, $this->currentCurrency);
    }

    #[On('loyaltyPointsRemoved')]
    public function handleLoyaltyPointsRemoved()
    {
        $this->loyaltyPointsApplied = 0;
        $this->loyaltyDiscount = 0;
    }

    #[On('loyaltyPointsUpdated')]
    public function handleLoyaltyPointsUpdated($data)
    {
        // This is just a preview update, don't change the actual applied points
        // The discount will be shown in the order summary but not stored until applied
    }

    // Method to validate all form data
    public function validateForm()
    {
        return $this->validate();
    }

    // Method to handle form submission
    public function submitForm()
    {
        try {
            // Validate the form
            $this->validate();

            // Validate stock availability before proceeding
            $this->validateStockAvailability();

            // Store form data in session
            $sessionData = [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'phone_number' => $this->phoneNumber,
                'billing_country_id' => $this->billingCountry,
                'billing_state' => $this->billingState,
                'billing_city' => $this->billingCity,
                'billing_address' => $this->billingAddress,
                'billing_building_number' => $this->billingBuildingNumber,
                'shipping_country_id' => $this->useBillingForShipping ? $this->billingCountry : $this->shippingCountry,
                'shipping_state' => $this->useBillingForShipping ? $this->billingState : $this->shippingState,
                'shipping_city' => $this->useBillingForShipping ? $this->billingCity : $this->shippingCity,
                'shipping_address' => $this->useBillingForShipping ? $this->billingAddress : $this->shippingAddress,
                'shipping_building_number' => $this->useBillingForShipping ? $this->billingBuildingNumber : $this->shippingBuildingNumber,
                'use_billing_for_shipping' => $this->useBillingForShipping,
                'payment_method' => $this->selectedPaymentMethod,
                'paypal_payment_type' => $this->paypalPaymentType,
                'currency' => $this->currentCurrency,
                'loyalty_discount' => $this->loyaltyDiscount,
                'loyalty_points_applied' => $this->loyaltyPointsApplied,
            ];

            // Clear any previous session data and set new data
            session(['checkout_data' => $sessionData]);

            // Submit form via JavaScript with session data
            $this->js('
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "' . route('checkout.process') . '";

                const csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "' . csrf_token() . '";
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            ');

        } catch (Exception $e) {

            // Check if it's a stock-related error
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'out of stock') !== false || strpos($errorMessage, 'Only') !== false) {
                // Show specific stock error with action buttons
                $this->dispatch('showStockError', [
                    'message' => $errorMessage,
                    'type' => 'error',
                    'showCartButton' => true
                ]);
            } else {
                // Show generic error
                $this->dispatch('showNotification', [
                    'message' => 'An error occurred while submitting the form. Please try again.',
                    'type' => 'error'
                ]);
            }
        }
    }

    /**
     * Check stock availability and show warnings to users
     */
    protected function checkStockAvailability()
    {
        $cart = app(\App\Services\CartService::class)->getCart();
        $stockIssues = [];

        foreach ($cart as $item) {
            $productId = null;
            if (strpos($item['id'], '-') !== false) {
                $parts = explode('-', $item['id']);
                $productId = (int) $parts[0];
            } else {
                $productId = (int) $item['id'];
            }

            $variantId = $item['attributes']['variant_id'] ?? null;
            $requestedQuantity = $item['quantity'];

            if ($variantId) {
                // Check variant stock
                $variant = \App\Models\ProductVariant::find($variantId);
                if (!$variant) {
                    $stockIssues[] = "Product variant not found: {$variantId}";
                    continue;
                }

                if ($variant->stock < $requestedQuantity) {
                    $product = \App\Models\Product::find($productId);
                    $availableStock = $variant->stock;

                    if ($availableStock <= 0) {
                        $stockIssues[] = "❌ Product '{$product->name}' (Size: {$variant->size}, Color: {$variant->color}) is out of stock.";
                    } else {
                        $stockIssues[] = "⚠️ Only {$availableStock} items available for '{$product->name}' (Size: {$variant->size}, Color: {$variant->color}).";
                    }
                }
            } else {
                // Check product stock for simple products
                $product = \App\Models\Product::find($productId);
                if (!$product) {
                    $stockIssues[] = "Product not found: {$productId}";
                    continue;
                }

                if ($product->quantity < $requestedQuantity) {
                    $availableStock = $product->quantity;

                    if ($availableStock <= 0) {
                        $stockIssues[] = "❌ Product '{$product->name}' is out of stock.";
                    } else {
                        $stockIssues[] = "⚠️ Only {$availableStock} items available for '{$product->name}'.";
                    }
                }
            }
        }

        // Show stock warnings if any issues found
        if (!empty($stockIssues)) {
            $message = "Stock Issues Found:\n" . implode("\n", $stockIssues);
            $this->dispatch('showStockError', [
                'message' => $message,
                'type' => 'warning',
                'showCartButton' => true
            ]);
        }
    }

    /**
     * Validate stock availability for all cart items
     */
    protected function validateStockAvailability()
    {
        $cart = app(\App\Services\CartService::class)->getCart();

        foreach ($cart as $item) {
            $productId = null;
            if (strpos($item['id'], '-') !== false) {
                $parts = explode('-', $item['id']);
                $productId = (int) $parts[0];
            } else {
                $productId = (int) $item['id'];
            }

            $variantId = $item['attributes']['variant_id'] ?? null;
            $requestedQuantity = $item['quantity'];

            if ($variantId) {
                // Check variant stock
                $variant = \App\Models\ProductVariant::find($variantId);
                if (!$variant) {
                    throw new \Exception("Product variant not found: {$variantId}");
                }

                                if ($variant->stock < $requestedQuantity) {
                    $product = \App\Models\Product::find($productId);
                    $availableStock = $variant->stock;

                    if ($availableStock <= 0) {
                        throw new \Exception("❌ Product '{$product->name}' (Size: {$variant->size}, Color: {$variant->color}) is currently out of stock. Please remove it from your cart or try a different variant.");
                    } else {
                        throw new \Exception("⚠️ Only {$availableStock} items available for '{$product->name}' (Size: {$variant->size}, Color: {$variant->color}). Please reduce the quantity in your cart.");
                    }
                }
            } else {
                // Check product stock for simple products
                $product = \App\Models\Product::find($productId);
                if (!$product) {
                    throw new \Exception("Product not found: {$productId}");
                }

                                if ($product->quantity < $requestedQuantity) {
                    $availableStock = $product->quantity;

                    if ($availableStock <= 0) {
                        throw new \Exception("❌ Product '{$product->name}' is currently out of stock. Please remove it from your cart.");
                    } else {
                        throw new \Exception("⚠️ Only {$availableStock} items available for '{$product->name}'. Please reduce the quantity in your cart.");
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.checkout-form');
    }
}
