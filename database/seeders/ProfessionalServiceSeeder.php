<?php

namespace Database\Seeders;

use App\Models\AdvertisementCategory;
use App\Models\ProfessionalService;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfessionalServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Sprawdzamy czy mamy użytkowników i kategorie
        $users = User::limit(5)->get();
        $categories = AdvertisementCategory::limit(3)->get();

        if ($users->isEmpty()) {
            $this->command->warn('Brak użytkowników - pomijam tworzenie ProfessionalServices');

            return;
        }

        if ($categories->isEmpty()) {
            $this->command->warn('Brak kategorii ogłoszeń - pomijam tworzenie ProfessionalServices');

            return;
        }

        $services = [
            // Usługi w Warszawie
            [
                'user_id' => $users->first()->id,
                'advertisement_category_id' => $categories->first()->id,
                'business_name' => 'Klinika Weterynaryjna VetExpert',
                'contact_person' => 'Dr Anna Kowalska',
                'description' => 'Nowoczesna klinika weterynaryjna z pełnym wyposażeniem diagnostycznym. Specjalizujemy się w medycynie wewnętrznej, chirurgii i stomatologii zwierząt.',
                'services_offered' => 'Konsultacje, szczepienia, chirurgia, diagnostyka, stomatologia, usługi ratunkowe',
                'base_price' => 80.00,
                'hourly_rate' => 120.00,
                'city' => 'Warszawa',
                'voivodeship' => 'mazowieckie',
                'full_address' => 'ul. Marszałkowska 150, 00-061 Warszawa',
                'latitude' => 52.2297,
                'longitude' => 21.0122,
                'service_radius_km' => 30,
                'phone' => '+48 22 123 45 67',
                'email' => 'kontakt@vetexpert.pl',
                'website' => 'https://vetexpert.pl',
                'specializations' => json_encode(['medycyna wewnętrzna', 'chirurgia', 'stomatologia']),
                'experience_years' => 8,
                'is_insured' => true,
                'is_licensed' => true,
                'status' => 'published',
                'is_featured' => true,
                'accepts_online_booking' => true,
                'offers_emergency_services' => true,
                'average_rating' => 4.8,
                'review_count' => 120,
                'view_count' => 1250,
                'contact_count' => 85,
            ],
            [
                'user_id' => $users->skip(1)->first()->id,
                'advertisement_category_id' => $categories->first()->id,
                'business_name' => 'Salon Groomerski Perfect Paws',
                'contact_person' => 'Katarzyna Nowak',
                'description' => 'Profesjonalny salon fryzjerski dla psów wszystkich ras. Strzyżenie, mycie, pielęgnacja pazurów, czyszczenie uszu. Używamy tylko wysokiej jakości kosmetyków.',
                'services_offered' => 'Pełna pielęgnacja, kąpiele, skracanie pazurów, czyszczenie uszu, czyszczenie zębów',
                'base_price' => 60.00,
                'hourly_rate' => 80.00,
                'city' => 'Warszawa',
                'voivodeship' => 'mazowieckie',
                'full_address' => 'ul. Nowy Świat 25, 00-029 Warszawa',
                'latitude' => 52.2319,
                'longitude' => 21.0067,
                'service_radius_km' => 20,
                'phone' => '+48 22 234 56 78',
                'email' => 'kontakt@perfectpaws.pl',
                'website' => 'https://perfectpaws.pl',
                'specializations' => json_encode(['strzyżenie wystawowe', 'strzyżenie rasowe']),
                'experience_years' => 5,
                'is_insured' => true,
                'is_licensed' => false,
                'status' => 'published',
                'accepts_online_booking' => true,
                'offers_emergency_services' => false,
                'average_rating' => 4.9,
                'review_count' => 89,
                'view_count' => 980,
                'contact_count' => 156,
            ],

            // Usługi w Krakowie
            [
                'user_id' => $users->skip(2)->first()->id,
                'advertisement_category_id' => $categories->first()->id,
                'business_name' => 'Szpital Weterynaryjny Animal Care',
                'contact_person' => 'Dr Marek Wiśniewski',
                'description' => '24-godzinny szpital weterynaryjny z oddziałem intensywnej opieki, blokiem operacyjnym i pełną diagnostyką obrazową.',
                'services_offered' => 'Usługi ratunkowe, chirurgia, intensywna opieka, diagnostyka, radiologia, ultrasonografia',
                'base_price' => 100.00,
                'hourly_rate' => 150.00,
                'city' => 'Kraków',
                'voivodeship' => 'małopolskie',
                'full_address' => 'ul. Floriańska 55, 31-019 Kraków',
                'latitude' => 50.0647,
                'longitude' => 19.9450,
                'service_radius_km' => 40,
                'phone' => '+48 12 123 45 67',
                'email' => 'emergency@animalcare-krakow.pl',
                'website' => 'https://animalcare-krakow.pl',
                'specializations' => json_encode(['medycyna ratunkowa', 'intensywna opieka', 'zaawansowana chirurgia']),
                'experience_years' => 12,
                'is_insured' => true,
                'is_licensed' => true,
                'status' => 'published',
                'is_featured' => true,
                'accepts_online_booking' => true,
                'offers_emergency_services' => true,
                'average_rating' => 4.6,
                'review_count' => 156,
                'view_count' => 3240,
                'contact_count' => 289,
            ],
            [
                'user_id' => $users->skip(3)->first()->id,
                'advertisement_category_id' => $categories->skip(1)->first()->id,
                'business_name' => 'Centrum Szkoleniowe Dog Academy',
                'contact_person' => 'Tomasz Kaczmarek',
                'description' => 'Profesjonalne szkolenia psów - od podstawowych komend po zaawansowany trening behawioralny. Kursy grupowe i indywidualne.',
                'services_offered' => 'Podstawowe szkolenie, zaawansowane szkolenie, terapia behawioralna, zajęcia grupowe, sesje indywidualne',
                'base_price' => 50.00,
                'hourly_rate' => 80.00,
                'city' => 'Kraków',
                'voivodeship' => 'małopolskie',
                'full_address' => 'ul. Dietla 50, 31-039 Kraków',
                'latitude' => 50.0600,
                'longitude' => 19.9500,
                'service_radius_km' => 25,
                'phone' => '+48 12 234 56 78',
                'email' => 'szkolenia@dogacademy.pl',
                'website' => 'https://dogacademy.pl',
                'specializations' => json_encode(['problemy behawioralne', 'terapia agresji', 'szkolenie szczeniąt']),
                'experience_years' => 6,
                'is_insured' => true,
                'is_licensed' => true,
                'status' => 'published',
                'accepts_online_booking' => true,
                'offers_emergency_services' => false,
                'average_rating' => 4.8,
                'review_count' => 134,
                'view_count' => 1670,
                'contact_count' => 178,
            ],

            // Usługi w Gdańsku
            [
                'user_id' => $users->skip(4)->first()->id,
                'advertisement_category_id' => $categories->first()->id,
                'business_name' => 'Przychodnia Weterynaryjna VetGdańsk',
                'contact_person' => 'Dr Agnieszka Lewandowska',
                'description' => 'Przychodnia weterynaryjna z tradycjami oferująca kompleksową opiekę nad zwierzętami. Specjalizujemy się w dermatologii i ortopedii.',
                'services_offered' => 'Konsultacje, szczepienia, podstawowa chirurgia, dermatologia, ortopedia',
                'base_price' => 70.00,
                'hourly_rate' => 90.00,
                'city' => 'Gdańsk',
                'voivodeship' => 'pomorskie',
                'full_address' => 'ul. Długa 80, 80-831 Gdańsk',
                'latitude' => 54.3520,
                'longitude' => 18.6466,
                'service_radius_km' => 35,
                'phone' => '+48 58 123 45 67',
                'email' => 'recepcja@vetgdansk.pl',
                'website' => 'https://vetgdansk.pl',
                'specializations' => json_encode(['dermatologia', 'ortopedia']),
                'experience_years' => 10,
                'is_insured' => true,
                'is_licensed' => true,
                'status' => 'published',
                'accepts_online_booking' => true,
                'offers_emergency_services' => false,
                'average_rating' => 4.4,
                'review_count' => 78,
                'view_count' => 980,
                'contact_count' => 67,
            ],

            // Dodatkowe usługi specjalistyczne
            [
                'user_id' => $users->first()->id,
                'advertisement_category_id' => $categories->skip(2)->first()->id,
                'business_name' => 'Pet Taxi Wrocław',
                'contact_person' => 'Piotr Kowal',
                'description' => 'Bezpieczny transport zwierząt po całym mieście. Specjalistyczne pojazdy przystosowane do przewozu zwierząt różnej wielkości.',
                'services_offered' => 'Transport do weterynarza, transport na lotnisko, transport do groomera, transport ratunkowy',
                'base_price' => 30.00,
                'hourly_rate' => 40.00,
                'city' => 'Wrocław',
                'voivodeship' => 'dolnośląskie',
                'full_address' => 'ul. Kazimierza Wielkiego 25, 50-077 Wrocław',
                'latitude' => 51.1000,
                'longitude' => 17.0300,
                'service_radius_km' => 50,
                'phone' => '+48 71 234 56 78',
                'email' => 'transport@pettaxi-wroclaw.pl',
                'website' => 'https://pettaxi-wroclaw.pl',
                'specializations' => json_encode(['transport ratunkowy', 'transport długodystansowy']),
                'experience_years' => 4,
                'is_insured' => true,
                'is_licensed' => true,
                'status' => 'published',
                'accepts_online_booking' => true,
                'offers_emergency_services' => true,
                'average_rating' => 4.3,
                'review_count' => 67,
                'view_count' => 890,
                'contact_count' => 123,
            ],
        ];

        foreach ($services as $service) {
            ProfessionalService::create($service);
        }

        $this->command->info('Utworzono '.count($services).' profesjonalnych usług weterynaryjnych.');
    }
}
