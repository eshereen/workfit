<?php
return [
    'api_key' => env('PAYMOB_API_KEY'),
    'auth_endpoint' => env('PAYMOB_AUTH_ENDPOINT', 'https://accept.paymob.com/api/auth/tokens'),
    'order_endpoint' => env('PAYMOB_ORDER_ENDPOINT', 'https://accept.paymob.com/api/ecommerce/orders'),
    'payment_key_endpoint' => env('PAYMOB_PAYMENT_KEY_ENDPOINT', 'https://accept.paymob.com/api/acceptance/payment_keys'),
    'iframe_url' => env('PAYMOB_IFRAME_URL'),
    'integration_id_card' => env('PAYMOB_INTEGRATION_ID_CARD'),
    'hmac' => env('PAYMOB_HMAC_KEY'),
];
