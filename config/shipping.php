<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains shipping settings that can be localized per country.
    | Shipping methods, rates, and rules are defined here.
    |
    */

    'default_method' => env('SHIPPING_DEFAULT_METHOD', 'flat_rate'),

    /*
    |--------------------------------------------------------------------------
    | Shipping Methods
    |--------------------------------------------------------------------------
    |
    | Available shipping methods and their configurations.
    |
    */
    'methods' => [
        'flat_rate' => [
            'name' => 'Flat Rate Shipping',
            'description' => 'Standard shipping with fixed rate',
            'enabled' => true,
        ],
        'free_shipping' => [
            'name' => 'Free Shipping',
            'description' => 'Free shipping on orders above threshold',
            'enabled' => true,
        ],
        'weight_based' => [
            'name' => 'Weight Based Shipping',
            'description' => 'Shipping cost based on package weight',
            'enabled' => false,
        ],
        'local_pickup' => [
            'name' => 'Local Pickup',
            'description' => 'Pick up from our store',
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Country-Specific Shipping Rates
    |--------------------------------------------------------------------------
    |
    | Shipping rates and rules for different countries.
    | Use country codes as keys (ISO 3166-1 alpha-2).
    |
    */
    'rates' => [
        'US' => [
            'flat_rate' => 5.99,
            'free_shipping_threshold' => 50.00,
            'weight_based' => [
                'base_rate' => 3.99,
                'per_pound' => 1.50,
            ],
        ],
        'CA' => [
            'flat_rate' => 12.99,
            'free_shipping_threshold' => 75.00,
            'weight_based' => [
                'base_rate' => 8.99,
                'per_pound' => 2.50,
            ],
        ],
        'GB' => [
            'flat_rate' => 8.99,
            'free_shipping_threshold' => 60.00,
            'weight_based' => [
                'base_rate' => 5.99,
                'per_pound' => 1.75,
            ],
        ],
        'AU' => [
            'flat_rate' => 15.99,
            'free_shipping_threshold' => 100.00,
            'weight_based' => [
                'base_rate' => 12.99,
                'per_pound' => 3.00,
            ],
        ],
        'EG' => [
            'flat_rate' => 25.00,
            'free_shipping_threshold' => 500.00,
            'weight_based' => [
                'base_rate' => 20.00,
                'per_pound' => 5.00,
            ],
        ],
        // Default rates for other countries
        'default' => [
            'flat_rate' => 10.00,
            'free_shipping_threshold' => 75.00,
            'weight_based' => [
                'base_rate' => 7.99,
                'per_pound' => 2.00,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Rules
    |--------------------------------------------------------------------------
    |
    | Global shipping rules and restrictions.
    |
    */
    'rules' => [
        'max_weight' => 50, // Maximum weight in pounds
        'max_dimensions' => [
            'length' => 48,
            'width' => 48,
            'height' => 48,
        ],
        'restricted_items' => [
            'hazardous_materials',
            'perishables',
            'fragile_items',
        ],
        'delivery_times' => [
            'standard' => '3-5 business days',
            'express' => '1-2 business days',
            'overnight' => 'Next business day',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Zones
    |--------------------------------------------------------------------------
    |
    | Define shipping zones for different regions.
    |
    */
    'zones' => [
        'domestic' => [
            'name' => 'Domestic',
            'countries' => ['US'],
            'priority' => 1,
        ],
        'north_america' => [
            'name' => 'North America',
            'countries' => ['US', 'CA', 'MX'],
            'priority' => 2,
        ],
        'europe' => [
            'name' => 'Europe',
            'countries' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CH'],
            'priority' => 3,
        ],
        'asia_pacific' => [
            'name' => 'Asia Pacific',
            'countries' => ['AU', 'JP', 'KR', 'SG', 'NZ'],
            'priority' => 4,
        ],
        'middle_east' => [
            'name' => 'Middle East',
            'countries' => ['EG', 'SA', 'AE', 'QA', 'KW'],
            'priority' => 5,
        ],
        'international' => [
            'name' => 'International',
            'countries' => [], // All other countries
            'priority' => 6,
        ],
    ],
];
