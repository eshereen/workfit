<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cart Database Table
    |--------------------------------------------------------------------------
    |
    | This is the database table that will be used to store cart items.
    |
    */
    'table' => 'shopping_cart',

    /*
    |--------------------------------------------------------------------------
    | Cart Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection that will be used to store cart items.
    |
    */
    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Cart Storage
    |--------------------------------------------------------------------------
    |
    | This is the storage driver that will be used to store cart items.
    | Supported: "database", "session"
    |
    */
    'storage' => 'session',

    /*
    |--------------------------------------------------------------------------
    | Cart Session Key
    |--------------------------------------------------------------------------
    |
    | This is the session key that will be used to store cart items.
    |
    */
    'session_key' => '88uuiioo99888',

    /*
    |--------------------------------------------------------------------------
    | Cart Cookie
    |--------------------------------------------------------------------------
    |
    | This is the cookie that will be used to store cart items.
    |
    */
    'cookie' => 'laravel_cart',

    /*
    |--------------------------------------------------------------------------
    | Cart Cookie Expiration
    |--------------------------------------------------------------------------
    |
    | This is the cookie expiration time in minutes.
    |
    */
    'cookie_expiration' => 525600,

    /*
    |--------------------------------------------------------------------------
    | Cart Format Numbers
    |--------------------------------------------------------------------------
    |
    | This is the format numbers option.
    |
    */
    'format_numbers' => env('SHOPPING_FORMAT_VALUES', false),

    /*
    |--------------------------------------------------------------------------
    | Cart Decimals
    |--------------------------------------------------------------------------
    |
    | This is the decimals option.
    |
    */
    'decimals' => env('SHOPPING_DECIMALS', 0),

    /*
    |--------------------------------------------------------------------------
    | Cart Decimal Point
    |--------------------------------------------------------------------------
    |
    | This is the decimal point option.
    |
    */
    'dec_point' => env('SHOPPING_DEC_POINT', '.'),

    /*
    |--------------------------------------------------------------------------
    | Cart Thousands Separator
    |--------------------------------------------------------------------------
    |
    | This is the thousands separator option.
    |
    */
    'thousands_sep' => env('SHOPPING_THOUSANDS_SEP', ','),
];
