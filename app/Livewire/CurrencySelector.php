<?php

namespace App\Livewire;

use Exception;
use App\Services\CountryCurrencyService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CurrencySelector extends Component
{
    public $currentCurrency = 'USD';
    public $currentSymbol = '$';
    public $isAutoDetected = false;
    public $detectedCountry = '';
    public $showDropdown = false;

    public function mount()
    {
        $this->loadCurrencyInfo();
        $this->showDropdown = false;
    }

    public function loadCurrencyInfo()
    {
        try {
            $currencyService = app(CountryCurrencyService::class);
            $currencyInfo = $currencyService->getCurrentCurrencyInfo();

            $this->currentCurrency = $currencyInfo['currency_code'];
            $this->currentSymbol = $currencyInfo['currency_symbol'];
            $this->isAutoDetected = $currencyInfo['is_auto_detected'];
            $this->detectedCountry = Session::get('detected_country', '');
        } catch (Exception $e) {
            // Use defaults if currency service fails
        }
    }

    public function changeCurrency($currencyCode)
    {
        Log::info('🔔 CurrencySelector: changeCurrency method called!', [
            'currency_code' => $currencyCode,
            'current_currency' => $this->currentCurrency,
            'method' => 'wire:click'
        ]);

        try {
            $currencyService = app(CountryCurrencyService::class);
            $currencyService->setPreferredCurrency($currencyCode);

            // Store old values for comparison
            $oldCurrency = $this->currentCurrency;
            $oldSymbol = $this->currentSymbol;

            $this->loadCurrencyInfo();
            $this->showDropdown = false;

            Log::info('CurrencySelector: Property update check', [
                'old_currency' => $oldCurrency,
                'new_currency' => $this->currentCurrency,
                'old_symbol' => $oldSymbol,
                'new_symbol' => $this->currentSymbol,
                'properties_changed' => $oldCurrency !== $this->currentCurrency
            ]);

            // Dispatch event to refresh other components
            $this->dispatch('currencyChanged', $currencyCode);

            // Show notification
            $this->dispatch('showNotification', [
                'message' => "Currency changed to {$this->currentCurrency}",
                'type' => 'success'
            ]);

            // Dispatch multiple events for better compatibility
            $this->dispatch('currency-changed', $currencyCode);
            $this->dispatch('global-currency-changed', $currencyCode);

            // Log the currency change
            Log::info('CurrencySelector: Currency changed successfully (Alpine.js)', [
                'new_currency' => $this->currentCurrency,
                'new_symbol' => $this->currentSymbol,
                'events_dispatched' => ['currencyChanged', 'currency-changed', 'global-currency-changed']
            ]);

            // Add console logging for debugging
            $this->js("
                console.log('✅ Currency changed to: {$this->currentCurrency} ({$this->currentSymbol}) via Alpine.js');
            ");

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Failed to change currency',
                'type' => 'error'
            ]);
        }
    }

    public function setShowDropdown($value)
    {
        $this->showDropdown = $value;
    }

    public function debugSession()
    {
        $sessionData = [
            'preferred_currency' => Session::get('preferred_currency'),
            'currency_initialized' => Session::get('currency_initialized'),
            'detected_country' => Session::get('detected_country'),
            'detected_currency' => Session::get('detected_currency'),
            'preferred_country_id' => Session::get('preferred_country_id'),
        ];

        Log::info('CurrencySelector: Session debug', $sessionData);

        $this->dispatch('showNotification', [
            'message' => 'Session: ' . json_encode($sessionData),
            'type' => 'info'
        ]);
    }

    public function resetToDetected()
    {
        try {
            $detectedCurrency = Session::get('detected_currency', 'USD');
            $currencyService = app(CountryCurrencyService::class);
            $currencyService->setPreferredCurrency($detectedCurrency);

            $this->loadCurrencyInfo();
            $this->showDropdown = false;

            // Dispatch event to refresh other components
            $this->dispatch('currencyChanged', $detectedCurrency);

            // Show notification
            $this->dispatch('showNotification', [
                'message' => 'Currency reset to detected location',
                'type' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Failed to reset currency',
                'type' => 'error'
            ]);
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    #[On('currencyChanged')]
    public function handleCurrencyChange()
    {
        $this->showDropdown = false;
        $this->loadCurrencyInfo();
    }

        #[On('country-changed')]
    public function updateCurrencyForCountry($countryCode)
    {
        try {
            Log::info('CurrencySelector: Updating currency for country change', ['country_code' => $countryCode]);

            // Get the currency for the new country
            $currencyService = app(CountryCurrencyService::class);
            $currencyCode = $currencyService->getCountryCurrencyByCode($countryCode);

            if ($currencyCode && $currencyCode !== $this->currentCurrency) {
                Log::info('CurrencySelector: Setting currency for country', [
                    'country_code' => $countryCode,
                    'currency_code' => $currencyCode,
                    'old_currency' => $this->currentCurrency
                ]);

                // Update the currency
                $currencyService->setPreferredCurrency($currencyCode);
                $this->loadCurrencyInfo();

                Log::info('CurrencySelector: Currency updated successfully', [
                    'country_code' => $countryCode,
                    'new_currency' => $this->currentCurrency,
                    'new_symbol' => $this->currentSymbol
                ]);
            } else {
                Log::info('CurrencySelector: No currency change needed', [
                    'country_code' => $countryCode,
                    'current_currency' => $this->currentCurrency
                ]);
            }
        } catch (Exception $e) {
            Log::error('CurrencySelector: Error updating currency for country', [
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
        }
    }

    #[On('currency-changed')]
    public function handleCurrencyChanged($currencyCode)
    {
        Log::info('CurrencySelector: Received currency-changed event', ['currency_code' => $currencyCode]);
        $this->updateToCurrency($currencyCode);
    }

    #[On('global-currency-changed')]
    public function handleGlobalCurrencyChanged($currencyCode)
    {
        Log::info('CurrencySelector: Received global-currency-changed event', ['currency_code' => $currencyCode]);
        $this->updateToCurrency($currencyCode);
    }

    #[On('$refresh')]
    public function handleRefresh()
    {
        Log::info('CurrencySelector: Received $refresh event - reloading currency info');
        $this->loadCurrencyInfo();
        Log::info('CurrencySelector: After refresh', [
            'current_currency' => $this->currentCurrency,
            'current_symbol' => $this->currentSymbol
        ]);
    }

    // Listen for country-changed event
    #[On('country-changed')]
    public function handleCountryChanged($countryCode)
    {
        try {
            Log::info('CurrencySelector: Received country-changed event', ['country_code' => $countryCode]);

            if ($countryCode) {
                $this->updateCurrencyForCountry($countryCode);
            }
        } catch (Exception $e) {
            Log::error('CurrencySelector: Error handling country-changed event', [
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
        }
    }

        // Removed duplicate event handler

    // Method to update currency directly from external call
    public function updateToCurrency($currencyCode)
    {
        Log::info('CurrencySelector: updateToCurrency called', ['currency_code' => $currencyCode]);

        try {
            $currencyService = app(CountryCurrencyService::class);
            $currencyService->setPreferredCurrency($currencyCode);
            $this->loadCurrencyInfo();

            Log::info('CurrencySelector: Currency updated successfully', [
                'new_currency' => $this->currentCurrency,
                'new_symbol' => $this->currentSymbol
            ]);
        } catch (Exception $e) {
            Log::error('CurrencySelector: Error updating currency', [
                'currency_code' => $currencyCode,
                'error' => $e->getMessage()
            ]);
        }
    }





    public function render()
    {
        return view('livewire.currency-selector');
    }
}
