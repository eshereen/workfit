<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use App\Models\Coupon;
use App\Enums\CouponType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

    // Loyalty points discount properties
    public $loyaltyDiscount = 0;
    public $loyaltyPointsApplied = 0;
    public $finalTotal = 0;
    public $couponDiscount = 0;
    public $appliedCouponCode = null;

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

        #[On('loyaltyPointsApplied')]
    public function handleLoyaltyPointsApplied($data)
    {
        Log::info('OrderSummary: Received loyaltyPointsApplied event', $data);

        $this->loyaltyPointsApplied = $data['points'];
        // Convert loyalty discount from USD to local currency using service container as fallback
        $loyaltyDiscountUSD = $data['value'];
        $currencyService = $this->currencyService ?? app(CountryCurrencyService::class);
        $this->loyaltyDiscount = $currencyService->convertFromUSD($loyaltyDiscountUSD, $this->currencyCode);
        $this->calculateFinalTotal();
    }

    #[On('loyaltyPointsRemoved')]
    public function handleLoyaltyPointsRemoved()
    {
        Log::info('OrderSummary: Received loyaltyPointsRemoved event');

        $this->loyaltyPointsApplied = 0;
        $this->loyaltyDiscount = 0;
        $this->calculateFinalTotal();
    }

        #[On('loyaltyPointsUpdated')]
    public function handleLoyaltyPointsUpdated($data)
    {
        Log::info('OrderSummary: Received loyaltyPointsUpdated event', $data);

        // This is just a preview update, don't change the actual applied points
        // Convert loyalty discount from USD to local currency for preview
        $loyaltyDiscountUSD = $data['value'];
        $currencyService = $this->currencyService ?? app(CountryCurrencyService::class);
        $this->loyaltyDiscount = $currencyService->convertFromUSD($loyaltyDiscountUSD, $this->currencyCode);
        $this->calculateFinalTotal();
    }

    protected function calculateFinalTotal()
    {
        $this->finalTotal = max(0, $this->total - $this->loyaltyDiscount - $this->couponDiscount);

        Log::info('OrderSummary: Final total calculated', [
            'original_total' => $this->total,
            'loyalty_discount' => $this->loyaltyDiscount,
            'final_total' => $this->finalTotal
        ]);
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
                $this->finalTotal = 0;
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

            // Apply coupon from session if present
            $this->applyCouponFromSession();

            // Calculate final total with any existing loyalty/coupon discount
            $this->calculateFinalTotal();

            Log::info('OrderSummary: Data loaded', [
                'currency' => $this->currencyCode,
                'symbol' => $this->currencySymbol,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'loyalty_discount' => $this->loyaltyDiscount,
                'final_total' => $this->finalTotal,
                'item_count' => count($this->cartItems)
            ]);

        } catch (Exception $e) {
            Log::error('OrderSummary: Error loading data', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function applyCouponFromSession(): void
    {
        $this->couponDiscount = 0;
        $this->appliedCouponCode = null;

        $savedCode = Session::get('applied_coupon_code');
        if (!$savedCode) {
            return;
        }

        $coupon = Coupon::where('code', $savedCode)->first();
        if (!$coupon || !$coupon->isValid()) {
            Session::forget(['applied_coupon_code', 'applied_coupon_id']);
            return;
        }

        // Base prices in USD
        $baseSubtotal = $this->cartService->getSubtotal();
        $baseShipping = $this->cartService->getShippingCost();
        $baseTax = $this->cartService->getTaxAmount();
        $baseTotal = $baseSubtotal + $baseShipping + $baseTax;

        if ($coupon->type === CouponType::Percentage) {
            // Percentage on local total
            $this->couponDiscount = $this->total * ((float) $coupon->value / 100);
        } else {
            // Fixed in USD -> convert to local
            $discountUSD = (float) $coupon->calculateDiscount($baseSubtotal);
            $this->couponDiscount = $this->currencyService->convertFromUSD($discountUSD, $this->currencyCode);
        }

        $this->couponDiscount = round(max(0, $this->couponDiscount), 2);
        $this->appliedCouponCode = $coupon->code;
    }

    public function render()
    {
        return view('livewire.order-summary');
    }
}
