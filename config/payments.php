<?php

return [
    'gateways' => [
        'paymob' => [
            'countries' => ['EG'],
            'currency' => 'EGP',
            'description' => 'Paymob - Egypt Payment Gateway',
        ],
        'cod' => [
            'countries' => ['EG'],
            'currency' => 'EGP',
            'description' => 'Cash on Delivery - Egypt',
        ],
        'paypal' => [
            'countries' => ['*'], // All countries except Egypt
            'exclude_countries' => ['EG'],
            'description' => 'PayPal - International Payment Gateway',
        ],
    ],

    'default_currency' => 'USD',
    'egypt_currency' => 'EGP',
];
