<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShippingTaxService
{
    protected $countryCurrencyService;

    public function __construct(CountryCurrencyService $countryCurrencyService)
    {
        $this->countryCurrencyService = $countryCurrencyService;
    }

    /**
     * Get shipping cost for a country
     */
    public function getShippingCost($countryCode = null, $subtotal = 0, $weight = 0, $method = 'flat_rate')
    {
        try {
            // Get current country if not provided
            if (!$countryCode) {
                $countryInfo = $this->countryCurrencyService->getCurrentCurrencyInfo();
                $countryCode = $countryInfo['country_code'] ?? 'US';
            }

            // Get shipping rates for the country
            $rates = $this->getShippingRates($countryCode);

            if (!$rates) {
                Log::warning('No shipping rates found for country', ['country_code' => $countryCode]);
                return 0;
            }

            // Calculate shipping based on method
            switch ($method) {
                case 'free_shipping':
                    return $this->calculateFreeShipping($subtotal, $rates);

                case 'weight_based':
                    return $this->calculateWeightBasedShipping($weight, $rates);

                case 'flat_rate':
                default:
                    return $this->calculateFlatRateShipping($rates);
            }
        } catch (\Exception $e) {
            Log::error('Error calculating shipping cost', [
                'country_code' => $countryCode,
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get tax amount for a country
     */
    public function getTaxAmount($countryCode = null, $subtotal = 0, $shippingCost = 0, $stateCode = null)
    {
        try {
            // Get current country if not provided
            if (!$countryCode) {
                $countryInfo = $this->countryCurrencyService->getCurrentCurrencyInfo();
                $countryCode = $countryInfo['country_code'] ?? 'US';
            }

            // Check if tax is enabled
            if (!config('tax.enabled', true)) {
                return 0;
            }

            // Get tax rates for the country
            $taxRates = $this->getTaxRates($countryCode, $stateCode);

            if (!$taxRates) {
                Log::warning('No tax rates found for country', ['country_code' => $countryCode]);
                return 0;
            }

            // Calculate taxable amount
            $taxableAmount = $subtotal;

            // Add shipping to taxable amount if configured
            if (config('tax.rules.tax_shipping', true)) {
                $taxableAmount += $shippingCost;
            }

            // Calculate tax
            $taxAmount = $this->calculateTax($taxableAmount, $taxRates);

            // Apply rounding rules
            return $this->roundTaxAmount($taxAmount);

        } catch (\Exception $e) {
            Log::error('Error calculating tax amount', [
                'country_code' => $countryCode,
                'state_code' => $stateCode,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get available shipping methods for a country
     */
    public function getAvailableShippingMethods($countryCode = null)
    {
        try {
            if (!$countryCode) {
                $countryInfo = $this->countryCurrencyService->getCurrentCurrencyInfo();
                $countryCode = $countryInfo['country_code'] ?? 'US';
            }

            $methods = config('shipping.methods', []);
            $availableMethods = [];

            foreach ($methods as $methodKey => $method) {
                if ($method['enabled']) {
                    $availableMethods[$methodKey] = [
                        'name' => $method['name'],
                        'description' => $method['description'],
                        'cost' => $this->getShippingCost($countryCode, 0, 0, $methodKey),
                    ];
                }
            }

            return $availableMethods;

        } catch (\Exception $e) {
            Log::error('Error getting shipping methods', [
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get shipping rates for a country
     */
    protected function getShippingRates($countryCode)
    {
        $cacheKey = "shipping_rates_{$countryCode}";

        return Cache::remember($cacheKey, 3600, function () use ($countryCode) {
            $rates = config('shipping.rates', []);

            // Return country-specific rates or default rates
            return $rates[$countryCode] ?? $rates['default'] ?? null;
        });
    }

    /**
     * Get tax rates for a country
     */
    protected function getTaxRates($countryCode, $stateCode = null)
    {
        $cacheKey = "tax_rates_{$countryCode}_{$stateCode}";

        return Cache::remember($cacheKey, 3600, function () use ($countryCode, $stateCode) {
            $rates = config('tax.rates', []);
            $countryRates = $rates[$countryCode] ?? $rates['default'] ?? null;

            if (!$countryRates) {
                return null;
            }

            // Handle country-specific tax structures
            switch ($countryCode) {
                case 'US':
                    return $this->getUSTaxRates($countryRates, $stateCode);

                case 'CA':
                    return $this->getCATaxRates($countryRates, $stateCode);

                default:
                    return $countryRates;
            }
        });
    }

    /**
     * Get US tax rates (federal + state)
     */
    protected function getUSTaxRates($countryRates, $stateCode)
    {
        $federalRate = $countryRates['federal_rate'] ?? 0;
        $stateRate = 0;

        if ($stateCode && isset($countryRates['state_rates'][$stateCode])) {
            $stateRate = $countryRates['state_rates'][$stateCode];
        } else {
            $stateRate = $countryRates['default_state_rate'] ?? 0;
        }

        return [
            'total_rate' => $federalRate + $stateRate,
            'federal_rate' => $federalRate,
            'state_rate' => $stateRate,
        ];
    }

    /**
     * Get Canada tax rates (GST + provincial)
     */
    protected function getCATaxRates($countryRates, $provinceCode)
    {
        $gstRate = $countryRates['federal_rate'] ?? 0;
        $provincialRate = 0;

        if ($provinceCode && isset($countryRates['provincial_rates'][$provinceCode])) {
            $provincialRate = $countryRates['provincial_rates'][$provinceCode];
        } else {
            $provincialRate = $countryRates['default_provincial_rate'] ?? 0;
        }

        return [
            'total_rate' => $gstRate + $provincialRate,
            'gst_rate' => $gstRate,
            'provincial_rate' => $provincialRate,
        ];
    }

    /**
     * Calculate flat rate shipping
     */
    protected function calculateFlatRateShipping($rates)
    {
        return $rates['flat_rate'] ?? 0;
    }

    /**
     * Calculate free shipping
     */
    protected function calculateFreeShipping($subtotal, $rates)
    {
        $threshold = $rates['free_shipping_threshold'] ?? 0;

        if ($subtotal >= $threshold) {
            return 0; // Free shipping
        }

        // Fall back to flat rate if threshold not met
        return $this->calculateFlatRateShipping($rates);
    }

    /**
     * Calculate weight-based shipping
     */
    protected function calculateWeightBasedShipping($weight, $rates)
    {
        $weightBasedRates = $rates['weight_based'] ?? [];
        $baseRate = $weightBasedRates['base_rate'] ?? 0;
        $perPound = $weightBasedRates['per_pound'] ?? 0;

        return $baseRate + ($weight * $perPound);
    }

    /**
     * Calculate tax amount
     */
    protected function calculateTax($taxableAmount, $taxRates)
    {
        $rate = $taxRates['total_rate'] ?? $taxRates['rate'] ?? 0;
        return $taxableAmount * $rate;
    }

    /**
     * Round tax amount according to rules
     */
    protected function roundTaxAmount($taxAmount)
    {
        $roundingMethod = config('tax.rules.rounding_method', 'round');
        $decimalPlaces = config('tax.rules.decimal_places', 2);
        $minimumAmount = config('tax.rules.minimum_tax_amount', 0.01);

        switch ($roundingMethod) {
            case 'ceil':
                $rounded = ceil($taxAmount * pow(10, $decimalPlaces)) / pow(10, $decimalPlaces);
                break;
            case 'floor':
                $rounded = floor($taxAmount * pow(10, $decimalPlaces)) / pow(10, $decimalPlaces);
                break;
            case 'round':
            default:
                $rounded = round($taxAmount, $decimalPlaces);
                break;
        }

        // Apply minimum tax amount
        if ($rounded > 0 && $rounded < $minimumAmount) {
            $rounded = $minimumAmount;
        }

        return $rounded;
    }

    /**
     * Check if product is tax exempt
     */
    public function isProductTaxExempt(Product $product)
    {
        $exemptions = config('tax.exemptions', []);

        // Check product categories
        if ($product->category && in_array($product->category->name, $exemptions['product_categories'] ?? [])) {
            return true;
        }

        // Check product types (you can add a product_type field to your products table)
        // if (in_array($product->product_type, $exemptions['product_types'] ?? [])) {
        //     return true;
        // }

        return false;
    }

    /**
     * Get tax registration number for a country
     */
    public function getTaxRegistrationNumber($countryCode)
    {
        $registrationNumbers = config('tax.registration_numbers', []);
        return $registrationNumbers[$countryCode] ?? null;
    }

    /**
     * Clear shipping and tax caches
     */
    public function clearCaches()
    {
        $countries = Country::where('active', true)->pluck('code');

        foreach ($countries as $countryCode) {
            Cache::forget("shipping_rates_{$countryCode}");
            Cache::forget("tax_rates_{$countryCode}_");
        }
    }
}
