<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayU Environment Configuration
    |--------------------------------------------------------------------------
    |
    | This controls which PayU environment your application will communicate
    | with. Set to 'sandbox' for testing and 'secure' for production.
    |
    */
    'environment' => env('PAYU_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | PayU API Configuration
    |--------------------------------------------------------------------------
    |
    | Your PayU merchant account credentials. These can be found in your
    | PayU merchant panel.
    |
    */
    'merchant_id' => env('PAYU_MERCHANT_ID'),
    'secret_key' => env('PAYU_SECRET_KEY'),
    'oauth_client_id' => env('PAYU_OAUTH_CLIENT_ID'),
    'oauth_client_secret' => env('PAYU_OAUTH_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | PayU API URLs
    |--------------------------------------------------------------------------
    |
    | PayU API endpoints for different environments.
    |
    */
    'api_url' => [
        'sandbox' => 'https://secure.snd.payu.com/',
        'secure' => 'https://secure.payu.com/',
    ],

    /*
    |--------------------------------------------------------------------------
    | PayU Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for handling PayU notifications (IPN).
    |
    */
    'notify_url' => env('APP_URL').'/api/payu/notify',
    'continue_url' => env('APP_URL').'/subscription/payment/success',
    'cancel_url' => env('APP_URL').'/subscription/payment/cancel',
    'error_url' => env('APP_URL').'/subscription/payment/cancel',

    /*
    |--------------------------------------------------------------------------
    | Currency and Locale
    |--------------------------------------------------------------------------
    |
    | Default currency and locale settings for PayU payments.
    |
    */
    'currency' => 'PLN',
    'locale' => 'pl_PL',

    /*
    |--------------------------------------------------------------------------
    | Signature Algorithm
    |--------------------------------------------------------------------------
    |
    | Algorithm used for generating PayU signatures.
    |
    */
    'signature_algorithm' => 'SHA256',

    /*
    |--------------------------------------------------------------------------
    | Payment Method Configuration
    |--------------------------------------------------------------------------
    |
    | Default payment method settings.
    |
    */
    'default_payment_method' => 'pbl',

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    |
    | VAT configuration for Polish tax requirements.
    |
    */
    'vat_rate' => 0.23, // 23% VAT
    'include_vat_in_price' => true,
];
