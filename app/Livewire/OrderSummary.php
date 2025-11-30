<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use App\Models\Country;
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

    #[On('shipping-country-changed')]
    public function handleShippingCountryChanged($id = null)
    {
        Log::info('OrderSummary: Received shipping-country-changed event', ['id' => $id]);

        // Ensure session has the latest ID from the event payload
        if ($id) {
            Session::put('checkout_shipping_country_id', $id);
        }

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
        // max(0, ...) prevents negative totals, round(..., 2) ensures currency precision
        $this->finalTotal = round(max(0, $this->total - $this->loyaltyDiscount - $this->couponDiscount), 2);

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

            // Get shipping country ID from session (fallback to country code)
            $shippingCountryId = Session::get('checkout_shipping_country_id');
            if (!$shippingCountryId) {
                $countryCode = Session::get('checkout_country');
                if ($countryCode) {
                    $country = Country::where('code', $countryCode)->first();
                    if ($country) {
                        $shippingCountryId = $country->id;
                    }
                }
            }

            // Get billing country ID from session (for tax calculation)
            // Tax is calculated based on billing country, not shipping country
            $billingCountryId = Session::get('checkout_billing_country_id');
            if (!$billingCountryId) {
                // Try to get from checkout_data (form submission)
                $checkoutData = Session::get('checkout_data');
                if ($checkoutData && isset($checkoutData['billing_country_id'])) {
                    $billingCountryId = $checkoutData['billing_country_id'];
                } else {
                    // Fallback: use shipping country if billing not set yet
                    $billingCountryId = $shippingCountryId;
                }
            }

            // Get base prices in USD
            $baseSubtotal = $this->cartService->getSubtotal();
            // Tax is calculated based on billing country
            $baseTaxAmount = $this->cartService->getTaxAmount($billingCountryId);
            $baseShippingAmount = $this->cartService->getShippingCost($shippingCountryId);
            $baseTotal = $this->cartService->getTotal($shippingCountryId);

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
                'tax_amount' => $this->taxAmount,
                'shipping_amount' => $this->shippingAmount,
                'total' => $this->total,
                'coupon_discount' => $this->couponDiscount,
                'loyalty_discount' => $this->loyaltyDiscount,
                'final_total' => $this->finalTotal,
                'billing_country_id' => $billingCountryId,
                'shipping_country_id' => $shippingCountryId,
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

        // Get shipping country ID from session (fallback to country code)
        $shippingCountryId = Session::get('checkout_shipping_country_id');
        if (!$shippingCountryId) {
            $countryCode = Session::get('checkout_country');
            if ($countryCode) {
                $country = Country::where('code', $countryCode)->first();
                if ($country) {
                    $shippingCountryId = $country->id;
                }
            }
        }

        // Get billing country ID for tax calculation
        $billingCountryId = Session::get('checkout_billing_country_id');
        if (!$billingCountryId) {
            $checkoutData = Session::get('checkout_data');
            if ($checkoutData && isset($checkoutData['billing_country_id'])) {
                $billingCountryId = $checkoutData['billing_country_id'];
            } else {
                $billingCountryId = $shippingCountryId;
            }
        }

        // Base prices in USD - use subtotal without compare_price for eligible items
        $baseSubtotalWithoutComparePrice = $this->cartService->getSubtotalWithoutComparePrice();
        $baseSubtotal = $this->cartService->getSubtotal();
        $baseShipping = $this->cartService->getShippingCost($shippingCountryId);
        $baseTax = $this->cartService->getTaxAmount($billingCountryId);
        $baseTotal = $baseSubtotal + $baseShipping + $baseTax;

        // Convert eligible subtotal to local currency
        $eligibleSubtotal = $this->currencyService->convertFromUSD($baseSubtotalWithoutComparePrice, $this->currencyCode);

        if ($coupon->type === CouponType::Percentage) {
            // Percentage discount on eligible subtotal only (items without compare_price)
            // Note: Shipping and tax are NOT included in percentage discount calculation
            $this->couponDiscount = $eligibleSubtotal * ((float) $coupon->value / 100);
        } else {
            // Fixed in USD -> calculate on eligible subtotal only, then convert to local
            $discountUSD = (float) $coupon->calculateDiscount($baseSubtotalWithoutComparePrice);
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
