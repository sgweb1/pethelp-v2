<?php

return [
    'name' => env('APP_NAME', 'PetHelp'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('USE_NGROK', false) ? env('NGROK_DOMAIN', env('APP_URL', 'http://localhost')) : env('LOCAL_DOMAIN', env('APP_URL', 'http://localhost')),
    'timezone' => env('APP_TIMEZONE', 'Europe/Warsaw'),
    'locale' => env('APP_LOCALE', 'pl'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'pl_PL'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    // ===== KONFIGURACJA LOKALNEGO NOMINATIM =====

    /**
     * Włącza używanie lokalnego Nominatim zamiast zewnętrznego API.
     */
    'nominatim_local_enabled' => env('NOMINATIM_LOCAL_ENABLED', false),

    /**
     * URL do lokalnej instancji Nominatim.
     */
    'nominatim_local_url' => env('NOMINATIM_LOCAL_URL', 'http://localhost:8080'),

    /**
     * Włącza fallback do zewnętrznego API w przypadku awarii lokalnego.
     */
    'nominatim_fallback_enabled' => env('NOMINATIM_FALLBACK_ENABLED', true),

    /**
     * TTL cache dla wyników geocodingu (w sekundach).
     */
    'nominatim_cache_ttl' => env('NOMINATIM_CACHE_TTL', 86400),

    /**
     * Opóźnienie między requestami do API (w milisekundach).
     * Dla lokalnego Nominatim może być krótsze.
     */
    'nominatim_rate_limit_delay' => env('NOMINATIM_RATE_LIMIT_DELAY', 100),
];
