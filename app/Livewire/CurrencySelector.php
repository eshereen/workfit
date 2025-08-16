<?php

namespace App\Livewire;

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
        } catch (\Exception $e) {
            // Use defaults if currency service fails
        }
    }

    public function changeCurrency($currencyCode)
    {
        try {
            $currencyService = app(CountryCurrencyService::class);
            $currencyService->setPreferredCurrency($currencyCode);

            $this->loadCurrencyInfo();
            $this->showDropdown = false;

            // Dispatch event to refresh other components
            $this->dispatch('currencyChanged', $currencyCode);

            // Show notification
            $this->dispatch('showNotification', [
                'message' => "Currency changed to {$currencyCode}",
                'type' => 'success'
            ]);

            // Force a page refresh to ensure all components update
            $this->dispatch('$refresh');

            // Also dispatch a browser event for JavaScript components
            $this->dispatch('currency-changed', $currencyCode);

            // Log the currency change
            Log::info('Currency changed in CurrencySelector', [
                'new_currency' => $currencyCode,
                'event_dispatched' => 'currencyChanged'
            ]);

            // Force a page reload to ensure all components get the new currency
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
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

        } catch (\Exception $e) {
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

    public function render()
    {
        return view('livewire.currency-selector');
    }
}
