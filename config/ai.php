<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfiguracja AI Assistant System
    |--------------------------------------------------------------------------
    |
    | Ustawienia dla systemu AI Assistant w Pet Sitter Wizard.
    | Zawiera konfigurację Ollama, cache, fallback rules i inne parametry.
    |
    */

    'ollama' => [
        'enabled' => env('AI_OLLAMA_ENABLED', false),
        'host' => env('AI_OLLAMA_HOST', 'localhost'),
        'port' => env('AI_OLLAMA_PORT', 11434),
        'model' => env('AI_OLLAMA_MODEL', 'llama3.2'),
        'timeout' => env('AI_OLLAMA_TIMEOUT', 30), // sekundy
        'max_retries' => env('AI_OLLAMA_RETRIES', 3),
    ],

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'ttl' => env('AI_CACHE_TTL', 3600), // sekundy
        'prefix' => 'ai_assistant:',
        'store' => env('AI_CACHE_STORE', 'redis'), // redis lub file
    ],

    'fallback' => [
        'enabled' => true,
        'use_templates' => true,
        'use_rules' => true,
    ],

    'market_data' => [
        'enabled' => true,
        'cache_ttl' => 86400, // 24 godziny
        'default_city' => 'Warsaw',
        'cities' => [
            'Warszawa' => [
                'multiplier' => 1.3,
                'currency' => 'PLN',
                'competition_level' => 'high',
                'market_size' => 'large',
                'growth_potential' => 'medium',
            ],
            'Kraków' => [
                'multiplier' => 1.2,
                'currency' => 'PLN',
                'competition_level' => 'medium',
                'market_size' => 'medium-large',
                'growth_potential' => 'high',
            ],
            'Gdańsk' => [
                'multiplier' => 1.15,
                'currency' => 'PLN',
                'competition_level' => 'medium',
                'market_size' => 'medium',
                'growth_potential' => 'high',
            ],
            'Wrocław' => [
                'multiplier' => 1.1,
                'currency' => 'PLN',
                'competition_level' => 'medium',
                'market_size' => 'medium',
                'growth_potential' => 'high',
            ],
            'Poznań' => [
                'multiplier' => 1.1,
                'currency' => 'PLN',
                'competition_level' => 'medium',
                'market_size' => 'medium',
                'growth_potential' => 'medium',
            ],
            'Łódź' => [
                'multiplier' => 0.95,
                'currency' => 'PLN',
                'competition_level' => 'low',
                'market_size' => 'medium',
                'growth_potential' => 'medium',
            ],
            'Other' => [
                'multiplier' => 1.0,
                'currency' => 'PLN',
                'competition_level' => 'low',
                'market_size' => 'small',
                'growth_potential' => 'medium',
            ],
        ],
    ],

    'services' => [
        'base_prices' => [
            'dog_walking' => 25.0,
            'pet_sitting' => 40.0,
            'overnight_care' => 80.0,
            'pet_feeding' => 20.0,
            'pet_grooming' => 60.0,
            'vet_transport' => 50.0,
            'pet_training' => 70.0,
            'emergency_care' => 100.0,
        ],
        'popular_services' => [
            'dog_walking',
            'pet_sitting',
            'overnight_care',
        ],
        'service_labels' => [
            'dog_walking' => 'Wyprowadzanie psów',
            'pet_sitting' => 'Opieka dzienna',
            'overnight_care' => 'Opieka nocna',
            'pet_feeding' => 'Karmienie zwierząt',
            'pet_grooming' => 'Pielęgnacja',
            'vet_transport' => 'Transport do weterynarza',
            'pet_training' => 'Trening zwierząt',
            'emergency_care' => 'Opieka awaryjna',
        ],
    ],

    'templates' => [
        'bio_length' => [
            'min' => 100,
            'max' => 500,
            'recommended' => 250,
        ],
        'variables' => [
            'name', 'experience_years', 'pet_types', 'services',
            'location', 'availability', 'special_skills'
        ],
    ],

    'rules' => [
        'max_services_suggestion' => 5,
        'min_price_variance' => 0.8, // 80% - 120% od base price
        'max_price_variance' => 1.2,
        'experience_bonus_multiplier' => 0.1, // 10% na rok doświadczenia
        'max_experience_bonus' => 0.5, // max 50% bonus
    ],

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'level' => env('AI_LOG_LEVEL', 'info'),
        'channels' => ['single', 'stack'],
    ],

    'performance' => [
        'async_requests' => env('AI_ASYNC_REQUESTS', false),
        'batch_processing' => env('AI_BATCH_PROCESSING', true),
        'memory_limit' => env('AI_MEMORY_LIMIT', '256M'),
    ],
];