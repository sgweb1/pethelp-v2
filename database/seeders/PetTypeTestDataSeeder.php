<?php

namespace Database\Seeders;

use App\Models\MapItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetTypeTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Tworzenie danych testowych dla różnych typów zwierząt...');

        // Sprawdzamy czy mamy użytkowników
        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('Brak użytkowników - tworzę testowych...');
            $users = User::factory(10)->create();
        }

        $testData = [
            // PET SITTERS - PSY
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1001,
                'latitude' => 52.2297,
                'longitude' => 21.0122,
                'city' => 'Warszawa',
                'voivodeship' => 'mazowieckie',
                'full_address' => 'Warszawa, ul. Marszałkowska 10',
                'title' => 'Spacery z psami - doświadczony dog walker',
                'description_short' => 'Profesjonalne spacery z psami wszystkich rozmiarów w Warszawie',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad psami',
                'price_from' => 25.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.5,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'spacery', 'walker']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1002,
                'latitude' => 50.0647,
                'longitude' => 19.9450,
                'city' => 'Kraków',
                'voivodeship' => 'małopolskie',
                'full_address' => 'Kraków, ul. Floriańska 15',
                'title' => 'Trener psów - szkolenie behawioralne',
                'description_short' => 'Profesjonalne szkolenie psów, korekta zachowań nieporządanych',
                'content_type' => 'pet_sitter',
                'category_name' => 'Szkolenie psów',
                'price_from' => 80.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.9,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'trener', 'szkolenie']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1003,
                'latitude' => 51.1079,
                'longitude' => 17.0385,
                'city' => 'Wrocław',
                'voivodeship' => 'dolnośląskie',
                'full_address' => 'Wrocław, ul. Świdnicka 25',
                'title' => 'Dog sitting - opieka u mnie lub u Ciebie',
                'description_short' => 'Kompleksowa opieka nad psami - feeding, spacery, zabawa',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad psami',
                'price_from' => 35.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.7,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'sitting', 'opieka']),
            ],

            // PET SITTERS - KOTY
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1004,
                'latitude' => 52.4064,
                'longitude' => 16.9252,
                'city' => 'Poznań',
                'voivodeship' => 'wielkopolskie',
                'full_address' => 'Poznań, ul. Półwiejska 8',
                'title' => 'Opieka nad kotami - miłośniczka kotów',
                'description_short' => 'Profesjonalna opieka nad kotami, karma, zabawa, czyszczenie kuwety',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad kotami',
                'price_from' => 20.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.6,
                'search_keywords' => json_encode(['kot', 'koty', 'cat', 'opieka', 'sitting']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1005,
                'latitude' => 54.3520,
                'longitude' => 18.6466,
                'city' => 'Gdańsk',
                'voivodeship' => 'pomorskie',
                'full_address' => 'Gdańsk, ul. Długa 20',
                'title' => 'Cat sitter - wizyta w domu kota',
                'description_short' => 'Codzienne wizyty u kotów podczas nieobecności właścicieli',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad kotami',
                'price_from' => 15.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.8,
                'search_keywords' => json_encode(['kot', 'koty', 'cat', 'sitter', 'wizyta']),
            ],

            // PET SITTERS - INNE ZWIERZĘTA
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1006,
                'latitude' => 53.4289,
                'longitude' => 14.5530,
                'city' => 'Szczecin',
                'voivodeship' => 'zachodniopomorskie',
                'full_address' => 'Szczecin, ul. Bogusława 12',
                'title' => 'Opieka nad ptakami - specjalista egzotyki',
                'description_short' => 'Profesjonalna opieka nad papugami, kanarkami i innymi ptakami',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad ptakami',
                'price_from' => 30.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.3,
                'search_keywords' => json_encode(['ptak', 'ptaki', 'bird', 'papuga', 'kanarek']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'PetSitter',
                'mappable_id' => 1007,
                'latitude' => 51.7592,
                'longitude' => 19.4560,
                'city' => 'Łódź',
                'voivodeship' => 'łódzkie',
                'full_address' => 'Łódź, ul. Piotrkowska 100',
                'title' => 'Gryzonie i króliki - opieka specjalistyczna',
                'description_short' => 'Opieka nad królikami, świnkami morskimi, chomików, szczurów',
                'content_type' => 'pet_sitter',
                'category_name' => 'Opieka nad gryzoniami',
                'price_from' => 25.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.4,
                'search_keywords' => json_encode(['królik', 'chomik', 'świnka', 'gryzoń', 'rabbit']),
            ],

            // SERVICES - DLA PSÓW
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'Service',
                'mappable_id' => 2001,
                'latitude' => 52.2297,
                'longitude' => 21.0122,
                'city' => 'Warszawa',
                'voivodeship' => 'mazowieckie',
                'full_address' => 'Warszawa, ul. Nowy Świat 30',
                'title' => 'Weterynarz specjalista psów',
                'description_short' => 'Gabinet weterynaryjny specializujący się w chorobach psów',
                'content_type' => 'service',
                'category_name' => 'Weterynarz dla psów',
                'price_from' => 120.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.7,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'weterynarz', 'vet']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'Service',
                'mappable_id' => 2002,
                'latitude' => 50.0647,
                'longitude' => 19.9450,
                'city' => 'Kraków',
                'voivodeship' => 'małopolskie',
                'full_address' => 'Kraków, ul. Karmelicka 20',
                'title' => 'Grooming salon - pielęgnacja psów',
                'description_short' => 'Profesjonalne strzyżenie, kąpiel i pielęgnacja psów wszystkich ras',
                'content_type' => 'service',
                'category_name' => 'Grooming psów',
                'price_from' => 60.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.5,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'grooming', 'strzyżenie']),
            ],

            // SERVICES - DLA KOTÓW
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'Service',
                'mappable_id' => 2003,
                'latitude' => 52.4064,
                'longitude' => 16.9252,
                'city' => 'Poznań',
                'voivodeship' => 'wielkopolskie',
                'full_address' => 'Poznań, ul. Święty Marcin 15',
                'title' => 'Klinika weterynaryjna - specjalista kotów',
                'description_short' => 'Gabinet weterynaryjny specializujący się w medycynie kotów',
                'content_type' => 'service',
                'category_name' => 'Weterynarz dla kotów',
                'price_from' => 100.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.8,
                'search_keywords' => json_encode(['kot', 'koty', 'cat', 'weterynarz', 'vet']),
            ],

            // SUPPLIES - RÓŻNE TYPY
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'Shop',
                'mappable_id' => 3001,
                'latitude' => 54.3520,
                'longitude' => 18.6466,
                'city' => 'Gdańsk',
                'voivodeship' => 'pomorskie',
                'full_address' => 'Gdańsk, ul. Długa 45',
                'title' => 'ZooMarket - karma dla psów',
                'description_short' => 'Wysokiej jakości karma i przysmaki dla psów wszystkich rozmiarów',
                'content_type' => 'supplies',
                'category_name' => 'Karma dla psów',
                'price_from' => 15.00,
                'status' => 'published',
                'is_featured' => 0,
                'rating_avg' => 4.2,
                'search_keywords' => json_encode(['pies', 'psy', 'dog', 'karma', 'food']),
            ],
            [
                'user_id' => $users->random()->id,
                'mappable_type' => 'Shop',
                'mappable_id' => 3002,
                'latitude' => 51.1079,
                'longitude' => 17.0385,
                'city' => 'Wrocław',
                'voivodeship' => 'dolnośląskie',
                'full_address' => 'Wrocław, ul. Świdnicka 40',
                'title' => 'Sklep FelixCat - akcesoria dla kotów',
                'description_short' => 'Zabawki, drapaki, kuwety i inne akcesoria dla kotów',
                'content_type' => 'supplies',
                'category_name' => 'Akcesoria dla kotów',
                'price_from' => 20.00,
                'status' => 'published',
                'is_featured' => 1,
                'rating_avg' => 4.4,
                'search_keywords' => json_encode(['kot', 'koty', 'cat', 'akcesoria', 'zabawki']),
            ],
        ];

        foreach ($testData as $item) {
            MapItem::create($item);
        }

        $this->command->info('Utworzono '.count($testData).' pozycji testowych dla różnych typów zwierząt.');

        // Pokaż statystyki
        $stats = [
            'Psy' => MapItem::where('search_keywords', 'LIKE', '%pies%')->orWhere('search_keywords', 'LIKE', '%dog%')->count(),
            'Koty' => MapItem::where('search_keywords', 'LIKE', '%kot%')->orWhere('search_keywords', 'LIKE', '%cat%')->count(),
            'Ptaki' => MapItem::where('search_keywords', 'LIKE', '%ptak%')->orWhere('search_keywords', 'LIKE', '%bird%')->count(),
            'Gryzonie' => MapItem::where('search_keywords', 'LIKE', '%gryzoń%')->orWhere('search_keywords', 'LIKE', '%rabbit%')->count(),
        ];

        foreach ($stats as $type => $count) {
            $this->command->info("$type: $count pozycji");
        }
    }
}
