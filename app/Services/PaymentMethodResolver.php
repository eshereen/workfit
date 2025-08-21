<?php
namespace App\Services;

use App\Enums\PaymentMethod;
use App\Payments\Gateways\PaypalGateway;
use Illuminate\Support\Facades\Log;

class PaymentMethodResolver
{
    public function availableForCountry(string $countryCode): array
    {
        try {
            Log::info('PaymentMethodResolver: Checking country', ['countryCode' => $countryCode]);

            $countryCode = strtoupper($countryCode);

            if ($countryCode === 'EG') {
                Log::info('PaymentMethodResolver: Egypt detected, returning Paymob/COD');
                return [PaymentMethod::PAYMOB, PaymentMethod::COD];
            }

            // GCC countries - prefer PayPal
            $gcc = ['AE', 'SA', 'KW', 'QA', 'OM', 'BH'];
            if (in_array($countryCode, $gcc, true)) {
                Log::info('PaymentMethodResolver: GCC country detected, returning PayPal');
                return [PaymentMethod::PAYPAL];
            }

            // Rest of world - PayPal
            Log::info('PaymentMethodResolver: Rest of world, returning PayPal');
            return [PaymentMethod::PAYPAL];
        } catch (\Exception $e) {
            Log::error('PaymentMethodResolver: Error in availableForCountry', [
                'countryCode' => $countryCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Return default methods in case of error
            return [PaymentMethod::PAYPAL];
        }
    }

    public function isPayPalAvailableForCurrency(string $currency): bool
    {
        // PayPal supports major currencies
        $supportedCurrencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SGD', 'HKD', 'NZD'];
        return in_array(strtoupper($currency), $supportedCurrencies);
    }

    public function isCreditCardAvailableForCountry(string $countryCode): bool
    {
        try {
            // Credit cards through PayPal are available in most countries except Egypt
            // and some restricted countries
            $restrictedCountries = ['EG', 'IR', 'KP', 'CU', 'SY'];
            $isAvailable = !in_array(strtoupper($countryCode), $restrictedCountries);

            Log::info('PaymentMethodResolver: Credit card availability check', [
                'countryCode' => $countryCode,
                'isAvailable' => $isAvailable
            ]);

            return $isAvailable;
        } catch (\Exception $e) {
            Log::error('PaymentMethodResolver: Error in isCreditCardAvailableForCountry', [
                'countryCode' => $countryCode,
                'error' => $e->getMessage()
            ]);
            // Default to false in case of error
            return false;
        }
    }
}
