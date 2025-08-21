<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Country;
use App\Services\PaymentMethodResolver;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Log;

class CheckoutForm extends Component
{
    // Address fields
    public $billingCountry = null;
    public $shippingCountry = null;
    public $useBillingForShipping = false;

    // Payment fields
    public $paymentMethods = [];
    public $selectedPaymentMethod = null;
    public $paypalPaymentType = 'paypal_account';
    public $creditCardAvailable = false;

    // Currency info
    public $currentCurrency = 'USD';
    public $currentSymbol = '$';

    public function mount()
    {
        $this->loadInitialData();
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
            $this->shippingCountry = $this->billingCountry;
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

            // Set default payment method
            if (!empty($this->paymentMethods)) {
                $this->selectedPaymentMethod = $this->paymentMethods[0];
            }

            // Reset PayPal payment type if credit card not available
            if (!$this->creditCardAvailable) {
                $this->paypalPaymentType = 'paypal_account';
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
            $this->selectedPaymentMethod = 'paypal';
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

    public function render()
    {
        return view('livewire.checkout-form');
    }
}
