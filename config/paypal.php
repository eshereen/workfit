<?php
/**
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'),

    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'webhook_id'        => env('PAYPAL_SANDBOX_WEBHOOK_ID', ''),
        'app_id'            => 'APP-80W284485P519543T',
    ],

    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'webhook_id'        => env('PAYPAL_LIVE_WEBHOOK_ID', ''),
        'app_id'            => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    // Modern PayPal settings
    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'CAPTURE'),
    'currency'       => env('PAYPAL_CURRENCY', 'USD'),
    'locale'         => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true),

    // Webhook configuration
    'webhook_url'    => env('PAYPAL_WEBHOOK_URL', ''),
    'return_url'     => env('PAYPAL_RETURN_URL', ''),
    'cancel_url'     => env('PAYPAL_CANCEL_URL', ''),

    // Supported currencies for PayPal
    'supported_currencies' => [
        'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SGD', 'HKD', 'NZD'
    ],

    // Application context settings
    'application_context' => [
        'shipping_preference' => 'NO_SHIPPING',
        'user_action' => 'PAY_NOW',
        'landing_page' => 'BILLING',
        'locale' => 'en-US',
    ],
];
