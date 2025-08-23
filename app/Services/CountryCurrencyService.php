<?php

namespace App\Services;

use App\Models\Country;
use Exception;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class CountryCurrencyService
{
    public function detectCountry()
    {
        $ip = request()->ip();
        Log::info("CountryCurrencyService: Detecting country for IP: {$ip}");

        $location = Location::get($ip);
        Log::info("CountryCurrencyService: Location result", ['location' => $location]);

        if ($location) {
            $result = [
                'country_code' => $location->countryCode,
                'country_name' => $location->countryName,
                'currency_code' => $this->mapCountryToCurrency($location->countryCode)
            ];
            Log::info("CountryCurrencyService: Detection successful", $result);
            return $result;
        }

        Log::warning("CountryCurrencyService: Could not detect location for IP: {$ip}");
        return null;
    }

    public function getPreferredCurrency()
    {
        // First, check if user has manually selected a currency
        if (Session::has('preferred_currency')) {
            return Session::get('preferred_currency');
        }

        // Then check if user has a preferred country
        if (Session::has('preferred_country_id')) {
            $country = Country::find(Session::get('preferred_country_id'));
            if ($country) {
                return $country->currency_code;
            }
        }

        // Finally, fall back to IP detection
        $detected = $this->detectCountry();
        if ($detected && $detected['currency_code']) {
            return $detected['currency_code'];
        }

        // Default to USD
        return 'USD';
    }

    public function setPreferredCurrency($currencyCode)
    {
        // Clear all currency-related session data to force a fresh start
        Session::forget('preferred_currency');
        Session::forget('preferred_country_id');
        Session::forget('currency_initialized');
        Session::forget('detected_country');
        Session::forget('detected_currency');

        // Set the new currency preference
        Session::put('preferred_currency', $currencyCode);
        Session::put('currency_initialized', true);

        Log::info("CountryCurrencyService: Currency changed to {$currencyCode}", [
            'session_cleared' => true,
            'new_currency' => $currencyCode
        ]);
    }

    public function setPreferredCountry($countryId)
    {
        $country = Country::find($countryId);
        if ($country) {
            Session::put('preferred_country_id', $countryId);
            Session::put('preferred_currency', $country->currency_code);
        }
    }

    public function getCurrentCurrencyInfo()
    {
        $currencyCode = $this->getPreferredCurrency();
        $country = null;

        // Find the country for this currency
        if (Session::has('preferred_country_id')) {
            $country = Country::find(Session::get('preferred_country_id'));
        } else {
            // Try to find a country with this currency
      
            $country = Country::select('id','code','currency_code','currency_sympol')->where('currency_code', $currencyCode)->first();
        }

        return [
            'currency_code' => $currencyCode,
            'currency_symbol' => $this->getCurrencySymbol($currencyCode),
            'country' => $country,
            'is_auto_detected' => !Session::has('preferred_currency'),
        ];
    }

    public function convertFromUSD($amount, $currencyCode)
    {
        if (!$currencyCode || $currencyCode === 'USD') {
            return $amount;
        }

        try {
            // Cache exchange rate for 1 hour
            $rate = Cache::remember("usd_to_{$currencyCode}", now()->addHour(), function () use ($currencyCode) {
                return $this->fetchExchangeRate($currencyCode);
            });

            if ($rate && is_numeric($rate)) {
                $converted = round($amount * $rate, 2);
                Log::info("Currency conversion: {$amount} USD to {$currencyCode} = {$converted} (rate: {$rate})");
                return $converted;
            }
        } catch (Exception $e) {
            Log::error("Currency conversion error: " . $e->getMessage());
        }

        Log::warning("Currency conversion failed for {$currencyCode}, returning original amount: {$amount}");
        return $amount;
    }

    protected function fetchExchangeRate($currencyCode)
    {
        try {
            // Try multiple free exchange rate APIs for reliability
            $rate = $this->fetchFromExchangeRateAPI($currencyCode);

            if ($rate) {
                return $rate;
            }

            // Fallback to hardcoded rates for common currencies
            return $this->getFallbackRate($currencyCode);

        } catch (Exception $e) {
            Log::error("Failed to fetch exchange rate for {$currencyCode}: " . $e->getMessage());
            return $this->getFallbackRate($currencyCode);
        }
    }

    protected function fetchFromExchangeRateAPI($currencyCode)
    {
        try {
            // Using a free exchange rate API
            $response = Http::timeout(5)->get("https://api.exchangerate-api.com/v4/latest/USD");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates'][$currencyCode])) {
                    return $data['rates'][$currencyCode];
                }
            }
        } catch (Exception $e) {
            Log::warning("Exchange rate API failed: " . $e->getMessage());
        }

        return null;
    }

    protected function getFallbackRate($currencyCode)
    {
        // Fallback rates (updated periodically) - these are approximate
        $fallbackRates = [
            'EGP' => 48.60,    // Egyptian Pound
            'GBP' => 0.79,    // British Pound
            'EUR' => 0.92,    // Euro
            'AED' => 3.67,    // UAE Dirham
            'SAR' => 3.75,    // Saudi Riyal
            'CAD' => 1.35,    // Canadian Dollar
            'AUD' => 1.52,    // Australian Dollar
            'JPY' => 150.0,   // Japanese Yen
            'CHF' => 0.88,    // Swiss Franc
            'INR' => 83.0,    // Indian Rupee
            'BRL' => 4.95,    // Brazilian Real
            'MXN' => 17.0,    // Mexican Peso
            'KRW' => 1350.0,  // South Korean Won
            'SGD' => 1.35,    // Singapore Dollar
            'HKD' => 7.82,    // Hong Kong Dollar
        ];

        return $fallbackRates[$currencyCode] ?? 1.0;
    }

    public function getCurrencySymbol($currencyCode)
    {
        $symbols = [
            'USD' => '$',
            'EGP' => 'E£',
            'GBP' => '£',
            'EUR' => '€',
            'AED' => 'د.إ',
            'SAR' => 'ر.س',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY' => '¥',
            'CHF' => 'CHF',
        ];

        return $symbols[$currencyCode] ?? $currencyCode;
    }

    public function getCountryCurrency($countryId)
    {
        $country = Country::find($countryId);
        return $country ? $country->currency_code : 'USD';
    }

    public function getCountryCurrencyByCode($countryCode)
    {
        $country = Country::select('id','code','currency_code','currency_sympol')->where('code', $countryCode)->first();
        return $country ? $country->currency_code : 'USD';
    }

    public function convertCartToCurrency($cartData, $countryId = null)
    {
        if ($countryId) {
            $currencyCode = $this->getCountryCurrency($countryId);
        } else {
            $currencyCode = $this->getPreferredCurrency();
        }

        if ($currencyCode === 'USD') {
            return $cartData;
        }

        $converted = [];
        foreach ($cartData as $key => $value) {
            if (is_numeric($value) && in_array($key, ['subtotal', 'tax_amount', 'shipping_amount', 'total'])) {
                $converted[$key] = $this->convertFromUSD($value, $currencyCode);
            } else {
                $converted[$key] = $value;
            }
        }

        return $converted;
    }

    private function mapCountryToCurrency($countryCode)
    {
        // Extended mapping for more countries
        $map = [
            'EG' => 'EGP',
            'US' => 'USD',
            'GB' => 'GBP',
            'EU' => 'EUR',
            'AE' => 'AED',
            'SA' => 'SAR',
            'CA' => 'CAD',
            'AU' => 'AUD',
            'JP' => 'JPY',
            'CH' => 'CHF',
            'IN' => 'INR',
            'BR' => 'BRL',
            'MX' => 'MXN',
            'KR' => 'KRW',
            'SG' => 'SGD',
            'HK' => 'HKD',
        ];

        return $map[$countryCode] ?? 'USD';
    }
}
