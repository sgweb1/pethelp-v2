<?php

return [
    /*
    |--------------------------------------------------------------------------
    | InFakt API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfiguracja API InFakt dla automatycznego fakturowania subskrypcji.
    | Rejestracja: https://app.infakt.pl/app/register
    |
    */

    // Środowisko API (sandbox lub production)
    'environment' => env('INFAKT_ENVIRONMENT', 'sandbox'),

    // Klucze API z panelu InFakt
    'api_key' => env('INFAKT_API_KEY'),
    'company_id' => env('INFAKT_COMPANY_ID'),

    // URL API
    'api_url' => [
        'sandbox' => env('INFAKT_API_URL_SANDBOX', 'https://api.sandbox-infakt.pl/api/v3'),
        'production' => env('INFAKT_API_URL_PRODUCTION', 'https://api.infakt.pl/v3')
    ],

    /*
    |--------------------------------------------------------------------------
    | Ustawienia fakturowania
    |--------------------------------------------------------------------------
    */

    // Domyślne ustawienia faktury
    'invoice_defaults' => [
        'currency' => 'PLN',
        'language' => 'pl',
        'payment_method' => 'transfer',
        'payment_date' => 14, // dni na płatność
        'tax_rate' => 23, // VAT 23%
        'bank_name' => env('INFAKT_BANK_NAME', 'Bank Testowy'),
        'bank_account' => env('INFAKT_BANK_ACCOUNT', '12 3456 7890 1234 5678 9012 3456'),
    ],

    // Automatyczne operacje
    'auto_send_email' => env('INFAKT_AUTO_SEND_EMAIL', true),
    'auto_mark_paid' => env('INFAKT_AUTO_MARK_PAID', true), // Oznacz jako opłacone po PayU

    // Prefiks numeracji faktur
    'invoice_prefix' => env('INFAKT_INVOICE_PREFIX', 'PETHELP'),

    /*
    |--------------------------------------------------------------------------
    | Mapowanie planów subskrypcji
    |--------------------------------------------------------------------------
    */

    // Opisy usług dla różnych planów
    'service_descriptions' => [
        'free-monthly' => 'Subskrypcja Darmowa - miesięczna',
        'free-yearly' => 'Subskrypcja Darmowa - roczna',
        'starter-monthly' => 'Subskrypcja Starter - miesięczna',
        'starter-yearly' => 'Subskrypcja Starter - roczna (20% rabatu)',
        'pro-monthly' => 'Subskrypcja Pro - miesięczna',
        'pro-yearly' => 'Subskrypcja Pro - roczna (25% rabatu)',
        'business-monthly' => 'Subskrypcja Business - miesięczna',
        'business-yearly' => 'Subskrypcja Business - roczna (30% rabatu)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks i powiadomienia
    |--------------------------------------------------------------------------
    */

    'webhook_url' => env('APP_URL') . '/api/infakt/webhook',
    'webhook_secret' => env('INFAKT_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Logowanie i debugowanie
    |--------------------------------------------------------------------------
    */

    'log_requests' => env('INFAKT_LOG_REQUESTS', true),
    'debug_mode' => env('INFAKT_DEBUG', false),
];