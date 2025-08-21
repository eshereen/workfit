<?php

namespace App\Livewire;

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

        public function mount()
    {
        // Basic debug to see if component is even loading
        error_log('CheckoutForm: Component mount() called');

        Log::info('CheckoutForm: Component mounting');
        $this->loadInitialData();

        Log::info('CheckoutForm: Mount completed', [
            'selected_payment_method' => $this->selectedPaymentMethod,
            'paypal_payment_type' => $this->paypalPaymentType,
            'current_currency' => $this->currentCurrency
        ]);

        Log::info('CheckoutForm: Component mounted successfully');
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

            Log::info('CheckoutForm: Using existing manual currency', [
                'currency' => $this->currentCurrency,
                'symbol' => $this->currentSymbol
            ]);
        }

        Log::info('CheckoutForm: Initial data loaded', [
            'country_code' => $countryCode,
            'payment_methods' => $this->paymentMethods,
            'currency' => $this->currentCurrency
        ]);
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

        Log::info('CheckoutForm: Country changed', [
            'source' => $source,
            'country_id' => $countryId,
            'country_code' => $country->code,
            'country_name' => $country->name
        ]);

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

            Log::info('CheckoutForm: Payment methods updated', [
                'country_code' => $countryCode,
                'methods' => $this->paymentMethods,
                'credit_card_available' => $this->creditCardAvailable,
                'selected_method' => $this->selectedPaymentMethod
            ]);

        } catch (\Exception $e) {
            Log::error('CheckoutForm: Error updating payment methods', [
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);

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
                Log::info('CheckoutForm: Updating currency based on country change', [
                    'country_code' => $countryCode,
                    'country_name' => $country->name
                ]);

                $currencyService->setPreferredCountry($country->id);
            }

            // Get updated currency info
            $currencyInfo = $currencyService->getCurrentCurrencyInfo();
            $this->currentCurrency = $currencyInfo['currency_code'];
            $this->currentSymbol = $currencyInfo['currency_symbol'];

            Log::info('CheckoutForm: Currency updated', [
                'country_code' => $countryCode,
                'currency_code' => $this->currentCurrency,
                'currency_symbol' => $this->currentSymbol
            ]);

        } catch (\Exception $e) {
            Log::error('CheckoutForm: Error updating currency', [
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);

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
        Log::info('CheckoutForm: Received payment-method-selected event', [
            'method' => $method,
            'previous_selection' => $this->selectedPaymentMethod,
            'component_id' => $this->getId()
        ]);

        $this->selectedPaymentMethod = $method;

        Log::info('CheckoutForm: Payment method updated', [
            'new_method' => $this->selectedPaymentMethod,
            'is_cod' => $this->selectedPaymentMethod === 'cash_on_delivery',
            'is_paymob' => $this->selectedPaymentMethod === 'paymob',
            'is_paypal' => $this->selectedPaymentMethod === 'paypal'
        ]);
    }

    // Listen for PayPal payment type change
    #[On('paypal-payment-type-changed')]
    public function handlePayPalPaymentTypeChanged($type)
    {
        // Always set to credit card for simplified flow
        $this->paypalPaymentType = 'credit_card';
        Log::info('CheckoutForm: PayPal payment type set to credit card (simplified flow)', ['requested_type' => $type, 'actual_type' => $this->paypalPaymentType]);
    }

    // Method to validate all form data
    public function validateForm()
    {
        return $this->validate();
    }

    // Method to handle form submission
    public function submitForm()
    {
        Log::info('CheckoutForm: submitForm method called');

        try {
            // Validate the form
            Log::info('CheckoutForm: Starting form validation');
            $this->validate();
            Log::info('CheckoutForm: Form validation passed');

            // Debug: Log current form values before session storage
            Log::info('CheckoutForm: Current form values before session storage', [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'phoneNumber' => $this->phoneNumber,
                'billingCountry' => $this->billingCountry,
                'billingState' => $this->billingState,
                'billingCity' => $this->billingCity,
                'billingAddress' => $this->billingAddress,
                'selectedPaymentMethod' => $this->selectedPaymentMethod,
                'useBillingForShipping' => $this->useBillingForShipping,
            ]);

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
            ];

            Log::info('CheckoutForm: Form data being stored in session', [
                'payment_method' => $this->selectedPaymentMethod,
                'is_cod' => $this->selectedPaymentMethod === 'cash_on_delivery',
                'is_paymob' => $this->selectedPaymentMethod === 'paymob',
                'is_paypal' => $this->selectedPaymentMethod === 'paypal',
                'session_data' => $sessionData
            ]);

            Log::info('CheckoutForm: Storing session data', ['session_data' => $sessionData]);
            session(['checkout_data' => $sessionData]);
            Log::info('CheckoutForm: Session data stored successfully');

            // Debug: Log the form data being stored
            Log::info('CheckoutForm: Storing form data in session', [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'payment_method' => $this->selectedPaymentMethod,
                'billing_country_id' => $this->billingCountry,
                'use_billing_for_shipping' => $this->useBillingForShipping,
                'all_form_data' => [ // Added for debugging
                    'firstName' => $this->firstName,
                    'lastName' => $this->lastName,
                    'email' => $this->email,
                    'phoneNumber' => $this->phoneNumber,
                    'billingCountry' => $this->billingCountry,
                    'billingState' => $this->billingState,
                    'billingCity' => $this->billingCity,
                    'billingAddress' => $this->billingAddress,
                    'billingBuildingNumber' => $this->billingBuildingNumber,
                    'shippingCountry' => $this->shippingCountry,
                    'shippingState' => $this->shippingState,
                    'shippingCity' => $this->shippingCity,
                    'shippingAddress' => $this->shippingAddress,
                    'shippingBuildingNumber' => $this->shippingBuildingNumber,
                    'useBillingForShipping' => $this->useBillingForShipping,
                    'selectedPaymentMethod' => $this->selectedPaymentMethod,
                    'paypalPaymentType' => $this->paypalPaymentType,
                    'currentCurrency' => $this->currentCurrency,
                ]
            ]);

            // Debug: Log the form data being stored
            Log::info('CheckoutForm: Storing form data in session', [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'payment_method' => $this->selectedPaymentMethod,
                'billing_country_id' => $this->billingCountry,
                'use_billing_for_shipping' => $this->useBillingForShipping
            ]);

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

            Log::info('CheckoutForm: Triggering form submission via JavaScript');

        } catch (\Exception $e) {
            // Log the error
            Log::error('CheckoutForm submission error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting the form. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.checkout-form');
    }
}
