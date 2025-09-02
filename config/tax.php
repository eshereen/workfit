<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains tax settings that can be localized per country.
    | Tax rates, rules, and exemptions are defined here.
    |
    */

    'enabled' => env('TAX_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Tax Calculation Method
    |--------------------------------------------------------------------------
    |
    | How taxes are calculated: 'percentage' or 'fixed'
    |
    */
    'calculation_method' => env('TAX_CALCULATION_METHOD', 'percentage'),

    /*
    |--------------------------------------------------------------------------
    | Country-Specific Tax Rates
    |--------------------------------------------------------------------------
    |
    | Tax rates for different countries and regions.
    | Use country codes as keys (ISO 3166-1 alpha-2).
    |
    */
    'rates' => [
        'US' => [
            'federal_rate' => 0.00, // No federal sales tax
            'state_rates' => [
                'CA' => 0.075, // California
                'NY' => 0.085, // New York
                'TX' => 0.0625, // Texas
                'FL' => 0.06, // Florida
                'IL' => 0.0625, // Illinois
                'PA' => 0.06, // Pennsylvania
                'OH' => 0.0575, // Ohio
                'GA' => 0.04, // Georgia
                'NC' => 0.0475, // North Carolina
                'MI' => 0.06, // Michigan
            ],
            'default_state_rate' => 0.06,
        ],
        'CA' => [
            'federal_rate' => 0.05, // GST
            'provincial_rates' => [
                'ON' => 0.08, // Ontario (HST)
                'QC' => 0.09975, // Quebec (QST + GST)
                'BC' => 0.12, // British Columbia (PST + GST)
                'AB' => 0.05, // Alberta (GST only)
                'NS' => 0.15, // Nova Scotia (HST)
                'NB' => 0.15, // New Brunswick (HST)
                'MB' => 0.12, // Manitoba (PST + GST)
                'SK' => 0.11, // Saskatchewan (PST + GST)
                'PE' => 0.15, // Prince Edward Island (HST)
                'NL' => 0.15, // Newfoundland and Labrador (HST)
            ],
            'default_provincial_rate' => 0.10,
        ],
        'GB' => [
            'vat_rate' => 0.20, // Standard VAT rate
            'reduced_rates' => [
                'reduced' => 0.05, // Reduced rate for some goods
                'zero' => 0.00, // Zero rate for some goods
            ],
        ],
        'AU' => [
            'gst_rate' => 0.10, // Goods and Services Tax
        ],
        'EG' => [
            'vat_rate' => 0.14, // Value Added Tax
        ],
        'EU' => [
            'standard_vat' => 0.21, // Standard EU VAT rate
            'reduced_vat' => 0.10, // Reduced EU VAT rate
            'super_reduced_vat' => 0.05, // Super reduced EU VAT rate
            'zero_vat' => 0.00, // Zero EU VAT rate
        ],
        // Default rates for other countries
        'default' => [
            'rate' => 0.10, // 10% default tax rate
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Exemptions
    |--------------------------------------------------------------------------
    |
    | Products or categories that are tax exempt.
    |
    */
    'exemptions' => [
        'product_categories' => [
            'books',
            'medical_supplies',
            'educational_materials',
        ],
        'product_types' => [
            'digital_products',
            'services',
            'gift_cards',
        ],
        'customer_types' => [
            'business',
            'tax_exempt',
            'diplomatic',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Rules
    |--------------------------------------------------------------------------
    |
    | Global tax rules and regulations.
    |
    */
    'rules' => [
        'tax_shipping' => true, // Whether to tax shipping costs
        'tax_discounts' => false, // Whether to tax discount amounts
        'rounding_method' => 'round', // round, ceil, floor
        'decimal_places' => 2,
        'minimum_tax_amount' => 0.01,
        'tax_inclusive_pricing' => false, // Whether prices include tax
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Zones
    |--------------------------------------------------------------------------
    |
    | Define tax zones for different regions.
    |
    */
    'zones' => [
        'domestic' => [
            'name' => 'Domestic',
            'countries' => ['US'],
            'tax_shipping' => true,
        ],
        'north_america' => [
            'name' => 'North America',
            'countries' => ['US', 'CA', 'MX'],
            'tax_shipping' => true,
        ],
        'europe' => [
            'name' => 'Europe',
            'countries' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CH'],
            'tax_shipping' => true,
        ],
        'asia_pacific' => [
            'name' => 'Asia Pacific',
            'countries' => ['AU', 'JP', 'KR', 'SG', 'NZ'],
            'tax_shipping' => false,
        ],
        'middle_east' => [
            'name' => 'Middle East',
            'countries' => ['EG', 'SA', 'AE', 'QA', 'KW'],
            'tax_shipping' => true,
        ],
        'international' => [
            'name' => 'International',
            'countries' => [], // All other countries
            'tax_shipping' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Registration Numbers
    |--------------------------------------------------------------------------
    |
    | Tax registration numbers for different countries.
    |
    */
    'registration_numbers' => [
        'US' => [
            'type' => 'EIN',
            'number' => env('US_EIN_NUMBER', ''),
        ],
        'CA' => [
            'type' => 'GST/HST',
            'number' => env('CA_GST_NUMBER', ''),
        ],
        'GB' => [
            'type' => 'VAT',
            'number' => env('GB_VAT_NUMBER', ''),
        ],
        'EU' => [
            'type' => 'VAT',
            'number' => env('EU_VAT_NUMBER', ''),
        ],
    ],
];
