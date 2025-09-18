<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Location;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Tworzymy użytkowników opiekunów
        $sitters = [
            [
                'name' => 'Anna Kowalska',
                'email' => 'anna.kowalska@example.com',
                'phone' => '+48 123 456 789',
                'city' => 'Warszawa',
                'latitude' => 52.2297,
                'longitude' => 21.0122,
                'services' => [
                    [
                        'category' => 'opieka-w-domu',
                        'title' => 'Profesjonalna opieka nad psami w domu',
                        'description' => 'Oferuję kompleksową opiekę nad Twoim psem w zaciszu jego własnego domu. Doświadczenie 5 lat.',
                        'price_per_hour' => 25,
                        'price_per_day' => 150,
                        'pet_types' => ['dog', 'cat'],
                        'pet_sizes' => ['small', 'medium'],
                        'service_types' => ['home_service']
                    ]
                ]
            ],
            [
                'name' => 'Marek Nowak',
                'email' => 'marek.nowak@example.com',
                'phone' => '+48 987 654 321',
                'city' => 'Kraków',
                'latitude' => 50.0647,
                'longitude' => 19.9450,
                'services' => [
                    [
                        'category' => 'spacery',
                        'title' => 'Spacery z psami - Kraków centrum',
                        'description' => 'Codzienne spacery z psami w centrum Krakowa. Elastyczne godziny, dostępny również w weekendy.',
                        'price_per_hour' => 20,
                        'price_per_day' => null,
                        'pet_types' => ['dog'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service']
                    ]
                ]
            ],
            [
                'name' => 'Katarzyna Wiśniewska',
                'email' => 'katarzyna.wisniewska@example.com',
                'phone' => '+48 555 666 777',
                'city' => 'Gdańsk',
                'latitude' => 54.3520,
                'longitude' => 18.6466,
                'services' => [
                    [
                        'category' => 'opieka-u-opiekuna',
                        'title' => 'Opieka nad kotami u mnie w domu',
                        'description' => 'Mam duży dom z ogrodem, idealny dla kotów. Doświadczenie z różnymi rasami kotów.',
                        'price_per_hour' => null,
                        'price_per_day' => 80,
                        'pet_types' => ['cat', 'rabbit'],
                        'pet_sizes' => ['small'],
                        'service_types' => ['sitter_home']
                    ]
                ]
            ],
            [
                'name' => 'Tomasz Kaczmarek',
                'email' => 'tomasz.kaczmarek@example.com',
                'phone' => '+48 333 444 555',
                'city' => 'Wrocław',
                'latitude' => 51.1079,
                'longitude' => 17.0385,
                'services' => [
                    [
                        'category' => 'transport-weterynaryjny',
                        'title' => 'Transport zwierząt do weterynarza',
                        'description' => 'Bezpieczny transport zwierząt do klinik weterynaryjnych. Samochód przystosowany do przewozu zwierząt.',
                        'price_per_hour' => 40,
                        'price_per_day' => null,
                        'pet_types' => ['dog', 'cat', 'rabbit'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service']
                    ]
                ]
            ],
            [
                'name' => 'Agnieszka Lewandowska',
                'email' => 'agnieszka.lewandowska@example.com',
                'phone' => '+48 777 888 999',
                'city' => 'Poznań',
                'latitude' => 52.4064,
                'longitude' => 16.9252,
                'services' => [
                    [
                        'category' => 'pielegnacja',
                        'title' => 'Pielęgnacja i strzyżenie psów',
                        'description' => 'Profesjonalna pielęgnacja, mycie i strzyżenie psów wszystkich ras. 10 lat doświadczenia.',
                        'price_per_hour' => 60,
                        'price_per_day' => null,
                        'pet_types' => ['dog'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service', 'sitter_home']
                    ]
                ]
            ]
        ];

        foreach ($sitters as $sitterData) {
            // Sprawdzamy czy użytkownik już istnieje
            $user = User::where('email', $sitterData['email'])->first();

            if (!$user) {
                // Tworzymy użytkownika
                $user = User::create([
                    'name' => $sitterData['name'],
                    'email' => $sitterData['email'],
                    'email_verified_at' => now(),
                    'password' => bcrypt('password')
                ]);
            }

            // Pomijamy tworzenie profilu - tabela nie istnieje

            // Sprawdzamy czy lokalizacja już istnieje
            $location = Location::where('user_id', $user->id)->first();

            if (!$location) {
                // Tworzymy lokalizację
                $location = Location::create([
                    'user_id' => $user->id,
                    'name' => 'Główna lokalizacja',
                    'city' => $sitterData['city'],
                    'street' => 'ul. Przykładowa 1',
                    'postal_code' => '00-001',
                    'country' => 'Polska',
                    'latitude' => $sitterData['latitude'],
                    'longitude' => $sitterData['longitude'],
                    'is_primary' => true
                ]);
            }

            // Tworzymy usługi
            foreach ($sitterData['services'] as $serviceData) {
                $category = ServiceCategory::where('slug', $serviceData['category'])->first();

                if ($category) {
                    // Sprawdzamy czy usługa już istnieje
                    $existingService = Service::where('sitter_id', $user->id)
                        ->where('title', $serviceData['title'])
                        ->first();

                    if (!$existingService) {
                        Service::create([
                            'sitter_id' => $user->id,
                            'category_id' => $category->id,
                            'title' => $serviceData['title'],
                            'description' => $serviceData['description'],
                            'price_per_hour' => $serviceData['price_per_hour'],
                            'price_per_day' => $serviceData['price_per_day'],
                            'pet_types' => $serviceData['pet_types'],
                            'pet_sizes' => $serviceData['pet_sizes'],
                            'home_service' => in_array('home_service', $serviceData['service_types']),
                            'sitter_home' => in_array('sitter_home', $serviceData['service_types']),
                            'max_pets' => 3,
                            'is_active' => true
                        ]);
                    }
                }
            }
        }

        $this->command->info('Testowe dane zostały utworzone pomyślnie!');
        $this->command->info('Utworzono ' . count($sitters) . ' opiekunów z usługami.');
    }
}