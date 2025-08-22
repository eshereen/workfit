<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\CountryCurrencyService;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected $currencyService;
    protected $cartService;

    public function __construct(CountryCurrencyService $currencyService, CartService $cartService)
    {
        $this->currencyService = $currencyService;
        $this->cartService = $cartService;
    }

    /**
     * Change currency and return updated prices
     */
    public function changeCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|max:3'
        ]);

        $currencyCode = strtoupper($request->currency);

        // Set the preferred currency
        $this->currencyService->setPreferredCurrency($currencyCode);

        // Get updated currency info
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Get base prices in USD and convert to new currency
        $baseSubtotal = $this->cartService->getSubtotal();
        $baseTaxAmount = $this->cartService->getTaxAmount();
        $baseShippingAmount = $this->cartService->getShippingCost();
        $baseTotal = $this->cartService->getTotal();

        $convertedData = [
            'subtotal' => $this->currencyService->convertFromUSD($baseSubtotal, $currencyCode),
            'tax_amount' => $this->currencyService->convertFromUSD($baseTaxAmount, $currencyCode),
            'shipping_amount' => $this->currencyService->convertFromUSD($baseShippingAmount, $currencyCode),
            'total' => $this->currencyService->convertFromUSD($baseTotal, $currencyCode),
        ];

        // Check if request expects JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'currencyCode' => $currencyCode,
                'currencySymbol' => $currencyInfo['currency_symbol'],
                'prices' => $convertedData,
                'message' => "Currency changed to {$currencyCode}"
            ]);
        }

        // For regular form submissions, redirect back with success message
        return redirect()->back()->with('success', "Currency changed to {$currencyCode}");
    }

    /**
     * Get currency info for checkout page
     */
    public function getCheckoutCurrency(Request $request)
    {
        $countryName = $request->get('country');

        if ($countryName) {
            // Find country by name and set its currency
            $country = Country::where('name', $countryName)->first();
            if ($country) {
                $this->currencyService->setPreferredCountry($country->id);
            }
        }

        // Get current currency info
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Get base prices in USD and convert to current currency
        $baseSubtotal = $this->cartService->getSubtotal();
        $baseTaxAmount = $this->cartService->getTaxAmount();
        $baseShippingAmount = $this->cartService->getShippingCost();
        $baseTotal = $this->cartService->getTotal();

        $convertedData = [
            'subtotal' => $this->currencyService->convertFromUSD($baseSubtotal, $currencyInfo['currency_code']),
            'tax_amount' => $this->currencyService->convertFromUSD($baseTaxAmount, $currencyInfo['currency_code']),
            'shipping_amount' => $this->currencyService->convertFromUSD($baseShippingAmount, $currencyInfo['currency_code']),
            'total' => $this->currencyService->convertFromUSD($baseTotal, $currencyInfo['currency_code']),
        ];

        return response()->json([
            'success' => true,
            'currencyCode' => $currencyInfo['currency_code'],
            'currencySymbol' => $currencyInfo['currency_symbol'],
            'prices' => $convertedData
        ]);
    }

    /**
     * Update country during checkout (POST method)
     */
    public function updateCheckoutCountry(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
            'country_name' => 'required|string'
        ]);

        $countryId = $request->input('country_id');
        $countryName = $request->input('country_name');

        // Find country and set it as preferred
        $country = Country::find($countryId);
        if ($country) {
            // Set the preferred country
            $this->currencyService->setPreferredCountry($countryId);

            // Store country code in session for payment method selection
            session(['checkout_country' => $country->code]);

            // Store country code in session for Livewire components to pick up
            session(['country_changed' => $country->code]);
            Log::info('Country code stored in session for Livewire components', ['country_code' => $country->code]);

            // Get updated currency info
            $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

            // Get base prices in USD and convert to current currency
            $baseSubtotal = $this->cartService->getSubtotal();
            $baseTaxAmount = $this->cartService->getTaxAmount();
            $baseShippingAmount = $this->cartService->getShippingCost();
            $baseTotal = $this->cartService->getTotal();

            $convertedData = [
                'subtotal' => $this->currencyService->convertFromUSD($baseSubtotal, $currencyInfo['currency_code']),
                'tax_amount' => $this->currencyService->convertFromUSD($baseTaxAmount, $currencyInfo['currency_code']),
                'shipping_amount' => $this->currencyService->convertFromUSD($baseShippingAmount, $currencyInfo['currency_code']),
                'total' => $this->currencyService->convertFromUSD($baseTotal, $currencyInfo['currency_code']),
            ];

            return response()->json([
                'success' => true,
                'country_code' => $country->code,
                'currencyCode' => $currencyInfo['currency_code'],
                'currencySymbol' => $currencyInfo['currency_symbol'],
                'prices' => $convertedData,
                'message' => "Country updated to {$countryName}"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Country not found'
        ], 404);
    }

    /**
     * Get current currency info
     */
    public function getCurrentCurrency()
    {
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        return response()->json([
            'success' => true,
            'currencyCode' => $currencyInfo['currency_code'],
            'currencySymbol' => $currencyInfo['currency_symbol'],
            'isAutoDetected' => $currencyInfo['is_auto_detected'],
            'detectedCountry' => Session::get('detected_country')
        ]);
    }

    /**
     * Reset to detected currency
     */
    public function resetToDetected()
    {
        $detectedCurrency = Session::get('detected_currency', 'USD');
        $this->currencyService->setPreferredCurrency($detectedCurrency);

        return response()->json([
            'success' => true,
            'message' => 'Currency reset to detected location',
            'currencyCode' => $detectedCurrency
        ]);
    }
}
