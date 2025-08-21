<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class OrderSummary extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $taxAmount = 0;
    public $shippingAmount = 0;
    public $total = 0;
    public $currencyCode = 'USD';
    public $currencySymbol = '$';

    protected $cartService;
    protected $currencyService;

    public function boot(CartService $cartService, CountryCurrencyService $currencyService)
    {
        $this->cartService = $cartService;
        $this->currencyService = $currencyService;
    }

    public function mount()
    {
        Log::info('OrderSummary: Component mounting');
        $this->loadOrderData();
    }

    #[On('currency-changed')]
    public function handleCurrencyChanged($currencyCode)
    {
        Log::info('OrderSummary: Received currency-changed event', ['currency_code' => $currencyCode]);
        $this->loadOrderData();
    }

    #[On('global-currency-changed')]
    public function handleGlobalCurrencyChanged($currencyCode)
    {
        Log::info('OrderSummary: Received global-currency-changed event', ['currency_code' => $currencyCode]);
        $this->loadOrderData();
    }

    #[On('currencyChanged')]
    public function handleCurrencyChangedEvent($currencyCode)
    {
        Log::info('OrderSummary: Received currencyChanged event', ['currency_code' => $currencyCode]);
        $this->loadOrderData();
    }

    #[On('country-changed')]
    public function handleCountryChanged($countryCode)
    {
        Log::info('OrderSummary: Received country-changed event', ['country_code' => $countryCode]);
        $this->loadOrderData();
    }

    #[On('$refresh')]
    public function handleRefresh()
    {
        Log::info('OrderSummary: Received $refresh event');
        $this->loadOrderData();
    }

    protected function loadOrderData()
    {
        try {
            $cart = $this->cartService->getCart();

            if ($cart->isEmpty()) {
                $this->cartItems = [];
                $this->subtotal = 0;
                $this->taxAmount = 0;
                $this->shippingAmount = 0;
                $this->total = 0;
                return;
            }

            // Get current currency preference
            $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();
            $this->currencyCode = $currencyInfo['currency_code'];
            $this->currencySymbol = $currencyInfo['currency_symbol'];

            // Get base prices in USD
            $baseSubtotal = $this->cartService->getSubtotal();
            $baseTaxAmount = $this->cartService->getTaxAmount();
            $baseShippingAmount = $this->cartService->getShippingCost();
            $baseTotal = $this->cartService->getTotal();

            // Convert prices to preferred currency
            $this->subtotal = $this->currencyService->convertFromUSD($baseSubtotal, $this->currencyCode);
            $this->taxAmount = $this->currencyService->convertFromUSD($baseTaxAmount, $this->currencyCode);
            $this->shippingAmount = $this->currencyService->convertFromUSD($baseShippingAmount, $this->currencyCode);
            $this->total = $this->currencyService->convertFromUSD($baseTotal, $this->currencyCode);

            // Convert cart item prices
            $this->cartItems = [];
            foreach ($cart as $item) {
                $convertedPrice = $this->currencyService->convertFromUSD($item['price'], $this->currencyCode);
                $this->cartItems[] = array_merge($item, [
                    'converted_price' => $convertedPrice
                ]);
            }

            Log::info('OrderSummary: Data loaded', [
                'currency' => $this->currencyCode,
                'symbol' => $this->currencySymbol,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'item_count' => count($this->cartItems)
            ]);

        } catch (\Exception $e) {
            Log::error('OrderSummary: Error loading data', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.order-summary');
    }
}
