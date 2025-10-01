<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventLocation;
use App\Models\EventType;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Najpierw sprawdźmy czy mamy typy wydarzeń
        $adoptionType = EventType::where('slug', 'adoption')->first();
        $trainingType = EventType::where('slug', 'training')->first();
        $socialType = EventType::where('slug', 'social')->first();
        $educationalType = EventType::where('slug', 'educational')->first();
        $charitableType = EventType::where('slug', 'charitable')->first();

        // Jeśli nie ma typów, tworzymy je
        if (! $adoptionType) {
            $adoptionType = EventType::create([
                'name' => 'Adopcje',
                'slug' => 'adoption',
                'description' => 'Wydarzenia związane z adopcją zwierząt',
                'color' => '#ef4444',
                'icon' => 'heart',
                'is_active' => true,
            ]);
        }

        if (! $trainingType) {
            $trainingType = EventType::create([
                'name' => 'Szkolenia',
                'slug' => 'training',
                'description' => 'Szkolenia i warsztaty dla właścicieli zwierząt',
                'color' => '#3b82f6',
                'icon' => 'academic-cap',
                'is_active' => true,
            ]);
        }

        if (! $socialType) {
            $socialType = EventType::create([
                'name' => 'Spotkania',
                'slug' => 'social',
                'description' => 'Spotkania właścicieli zwierząt',
                'color' => '#10b981',
                'icon' => 'users',
                'is_active' => true,
            ]);
        }

        if (! $educationalType) {
            $educationalType = EventType::create([
                'name' => 'Edukacyjne',
                'slug' => 'educational',
                'description' => 'Wykłady i prezentacje edukacyjne',
                'color' => '#f59e0b',
                'icon' => 'book-open',
                'is_active' => true,
            ]);
        }

        if (! $charitableType) {
            $charitableType = EventType::create([
                'name' => 'Charytatywne',
                'slug' => 'charitable',
                'description' => 'Wydarzenia charytatywne na rzecz zwierząt',
                'color' => '#8b5cf6',
                'icon' => 'gift',
                'is_active' => true,
            ]);
        }

        $events = [
            // Wydarzenia w Warszawie
            [
                'title' => 'Dzień Adopcji w Parku Łazienkowskim',
                'description' => 'Przyjdź i poznaj zwierzęta szukające domu! Schronisko "Przyjazny Dom" prezentuje psy i koty gotowe do adopcji. Na miejscu będzie weterynarz i behawioralista, którzy pomogą wybrać idealnego pupila.',
                'event_type_id' => $adoptionType->id,
                'start_date' => Carbon::now()->addDays(7)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(16, 0),
                'max_participants' => 100,
                'current_participants' => 23,
                'registration_deadline' => Carbon::now()->addDays(5),
                'price' => 0,
                'is_free' => true,
                'organizer_name' => 'Schronisko Przyjazny Dom',
                'organizer_email' => 'kontakt@przyjazndom.pl',
                'organizer_phone' => '+48 22 123 45 67',
                'location' => [
                    'name' => 'Park Łazienkowski',
                    'address' => 'Agrykola 1, 00-460 Warszawa',
                    'city' => 'Warszawa',
                    'latitude' => 52.2148,
                    'longitude' => 21.0314,
                    'venue_type' => 'outdoor',
                    'capacity' => 100,
                ],
            ],
            [
                'title' => 'Szkolenie: Podstawy Psiej Psychologii',
                'description' => 'Warsztat dla właścicieli psów o podstawach psiej psychologii. Dowiesz się jak myśli Twój pies, jak komunikować się z nim skutecznie i jak budować pozytywną relację.',
                'event_type_id' => $trainingType->id,
                'start_date' => Carbon::now()->addDays(10)->setTime(14, 0),
                'end_date' => Carbon::now()->addDays(10)->setTime(17, 0),
                'max_participants' => 20,
                'current_participants' => 12,
                'registration_deadline' => Carbon::now()->addDays(8),
                'price' => 150,
                'is_free' => false,
                'organizer_name' => 'Centrum Szkoleniowe DogMind',
                'organizer_email' => 'szkolenia@dogmind.pl',
                'organizer_phone' => '+48 22 234 56 78',
                'location' => [
                    'name' => 'Centrum Konferencyjne "Pałac Kultury"',
                    'address' => 'plac Defilad 1, 00-901 Warszawa',
                    'city' => 'Warszawa',
                    'latitude' => 52.2319,
                    'longitude' => 21.0067,
                    'venue_type' => 'indoor',
                    'capacity' => 20,
                ],
            ],
            [
                'title' => 'Spotkanie Właścicieli Kotów Rasy Maine Coon',
                'description' => 'Miesięczne spotkanie pasjonatów i właścicieli kotów rasy Maine Coon. Wymiana doświadczeń, porady pielęgnacyjne, prezentacja nowych kotów w rodzinach członków.',
                'event_type_id' => $socialType->id,
                'start_date' => Carbon::now()->addDays(14)->setTime(16, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(18, 0),
                'max_participants' => 30,
                'current_participants' => 8,
                'registration_deadline' => Carbon::now()->addDays(12),
                'price' => 20,
                'is_free' => false,
                'organizer_name' => 'Klub Maine Coon Warszawa',
                'organizer_email' => 'mainecoon.warszawa@gmail.com',
                'organizer_phone' => '+48 22 345 67 89',
                'location' => [
                    'name' => 'Kawiarnia "Cat Cafe Warszawa"',
                    'address' => 'ul. Nowy Świat 18, 00-497 Warszawa',
                    'city' => 'Warszawa',
                    'latitude' => 52.2297,
                    'longitude' => 21.0122,
                    'venue_type' => 'indoor',
                    'capacity' => 30,
                ],
            ],

            // Wydarzenia w Krakowie
            [
                'title' => 'Kiermasz Charytatywny "Pomagamy Zwierzakom"',
                'description' => 'Kiermasz rzeczy używanych i rękodzieła na rzecz bezdomnych zwierząt. Cały dochód zostanie przekazany schroniskom w Krakowie. Będzie można kupić akcesoria dla zwierząt, książki i pamiątki.',
                'event_type_id' => $charitableType->id,
                'start_date' => Carbon::now()->addDays(5)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(5)->setTime(15, 0),
                'max_participants' => 200,
                'current_participants' => 67,
                'registration_deadline' => null,
                'price' => 0,
                'is_free' => true,
                'organizer_name' => 'Fundacja "Łapy w Potrzebie"',
                'organizer_email' => 'fundacja@lapywpotrzebie.pl',
                'organizer_phone' => '+48 12 123 45 67',
                'location' => [
                    'name' => 'Rynek Główny',
                    'address' => 'Rynek Główny, 31-042 Kraków',
                    'city' => 'Kraków',
                    'latitude' => 50.0614,
                    'longitude' => 19.9365,
                    'venue_type' => 'outdoor',
                    'capacity' => 200,
                ],
            ],
            [
                'title' => 'Wykład: Żywienie Starszych Psów',
                'description' => 'Edukacyjny wykład weterynarza o specyfice żywienia psów seniorów. Porady dotyczące diety, suplementacji i pielęgnacji starszych czworonogów.',
                'event_type_id' => $educationalType->id,
                'start_date' => Carbon::now()->addDays(20)->setTime(18, 0),
                'end_date' => Carbon::now()->addDays(20)->setTime(20, 0),
                'max_participants' => 50,
                'current_participants' => 31,
                'registration_deadline' => Carbon::now()->addDays(18),
                'price' => 25,
                'is_free' => false,
                'organizer_name' => 'Dr Anna Kowalczyk - VetExpert',
                'organizer_email' => 'dr.kowalczyk@vetexpert.pl',
                'organizer_phone' => '+48 12 234 56 78',
                'location' => [
                    'name' => 'Uniwersytet Rolniczy - Wydział Weterynaryjny',
                    'address' => 'al. Mickiewicza 24/28, 30-059 Kraków',
                    'city' => 'Kraków',
                    'latitude' => 50.0689,
                    'longitude' => 19.9544,
                    'venue_type' => 'indoor',
                    'capacity' => 50,
                ],
            ],

            // Wydarzenia w Gdańsku
            [
                'title' => 'Spacer z Psami nad Morzem',
                'description' => 'Grupowy spacer z psami po plaży w Jelitkowo. Świetna okazja do socjalizacji psów i poznania innych właścicieli. Po spacerze wspólne ognisko na plaży.',
                'event_type_id' => $socialType->id,
                'start_date' => Carbon::now()->addDays(3)->setTime(15, 0),
                'end_date' => Carbon::now()->addDays(3)->setTime(18, 0),
                'max_participants' => 25,
                'current_participants' => 18,
                'registration_deadline' => Carbon::now()->addDays(1),
                'price' => 0,
                'is_free' => true,
                'organizer_name' => 'Grupa "Gdańskie Łapki"',
                'organizer_email' => 'gdanskelapki@gmail.com',
                'organizer_phone' => '+48 58 123 45 67',
                'location' => [
                    'name' => 'Plaża w Jelitkowo',
                    'address' => 'ul. Kapliczna, 80-336 Gdańsk',
                    'city' => 'Gdańsk',
                    'latitude' => 54.4372,
                    'longitude' => 18.5794,
                    'venue_type' => 'outdoor',
                    'capacity' => 25,
                ],
            ],
            [
                'title' => 'Warsztaty Agility dla Początkujących',
                'description' => 'Pierwszy kontakt z agility - sportiem dla psów. Podstawowe przeszkody, nauka współpracy z psem, elementy treningu. Wymagany pies w wieku min. 12 miesięcy.',
                'event_type_id' => $trainingType->id,
                'start_date' => Carbon::now()->addDays(12)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(12)->setTime(13, 0),
                'max_participants' => 15,
                'current_participants' => 9,
                'registration_deadline' => Carbon::now()->addDays(10),
                'price' => 80,
                'is_free' => false,
                'organizer_name' => 'Klub Sportowy "Agility Gdańsk"',
                'organizer_email' => 'agility@gdansk.pl',
                'organizer_phone' => '+48 58 234 56 78',
                'location' => [
                    'name' => 'Tor Agility - Park Oliwski',
                    'address' => 'ul. Opacka 15, 80-462 Gdańsk',
                    'city' => 'Gdańsk',
                    'latitude' => 54.4056,
                    'longitude' => 18.5710,
                    'venue_type' => 'outdoor',
                    'capacity' => 15,
                ],
            ],

            // Wydarzenia we Wrocławiu
            [
                'title' => 'Dzień Otwartych Drzwi - Schronisko dla Zwierząt',
                'description' => 'Poznaj pracy schroniska od środka! Oprowadzanie po budynku, spotkanie ze zwierzętami, możliwość volontariatu. Dla rodzin z dziećmi - warsztaty edukacyjne.',
                'event_type_id' => $educationalType->id,
                'start_date' => Carbon::now()->addDays(8)->setTime(11, 0),
                'end_date' => Carbon::now()->addDays(8)->setTime(16, 0),
                'max_participants' => 80,
                'current_participants' => 34,
                'registration_deadline' => Carbon::now()->addDays(6),
                'price' => 0,
                'is_free' => true,
                'organizer_name' => 'Schronisko dla Bezdomnych Zwierząt Wrocław',
                'organizer_email' => 'schronisko@wroclaw.pl',
                'organizer_phone' => '+48 71 123 45 67',
                'location' => [
                    'name' => 'Schronisko dla Zwierząt',
                    'address' => 'ul. Ślężna 114, 53-302 Wrocław',
                    'city' => 'Wrocław',
                    'latitude' => 51.0919,
                    'longitude' => 17.0257,
                    'venue_type' => 'mixed',
                    'capacity' => 80,
                ],
            ],

            // Wydarzenia w Poznaniu
            [
                'title' => 'Bieg z Psem "Poznań Dog Run"',
                'description' => 'Pierwszy w Poznaniu bieg z psami! Trasa 5km przez Park Cytadela. Nagrody dla najszybszych zespołów człowiek-pies. Wymagana rejestracja wraz z psem.',
                'event_type_id' => $socialType->id,
                'start_date' => Carbon::now()->addDays(25)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(25)->setTime(12, 0),
                'max_participants' => 100,
                'current_participants' => 42,
                'registration_deadline' => Carbon::now()->addDays(23),
                'price' => 35,
                'is_free' => false,
                'organizer_name' => 'Running Team Poznań',
                'organizer_email' => 'dogrun@poznan.pl',
                'organizer_phone' => '+48 61 123 45 67',
                'location' => [
                    'name' => 'Park Cytadela - Start/Meta',
                    'address' => 'Park Cytadela, 61-663 Poznań',
                    'city' => 'Poznań',
                    'latitude' => 52.4210,
                    'longitude' => 16.9474,
                    'venue_type' => 'outdoor',
                    'capacity' => 100,
                ],
            ],
            [
                'title' => 'Konkurs Piękności Psów "Miss & Mister Pies 2025"',
                'description' => 'Konkurs piękności dla psów wszystkich ras. Kategorie: psy rasowe, mieszańce, psy senior, najładniejsza para. Jury składa się z hodowców i ekspertów kynologicznych.',
                'event_type_id' => $socialType->id,
                'start_date' => Carbon::now()->addDays(30)->setTime(13, 0),
                'end_date' => Carbon::now()->addDays(30)->setTime(17, 0),
                'max_participants' => 60,
                'current_participants' => 27,
                'registration_deadline' => Carbon::now()->addDays(28),
                'price' => 50,
                'is_free' => false,
                'organizer_name' => 'Poznański Klub Kynologiczny',
                'organizer_email' => 'pkk@poznan.pl',
                'organizer_phone' => '+48 61 234 56 78',
                'location' => [
                    'name' => 'Hala Sportowa Arena',
                    'address' => 'ul. Roosevelta 1, 61-623 Poznań',
                    'city' => 'Poznań',
                    'latitude' => 52.4001,
                    'longitude' => 16.9158,
                    'venue_type' => 'indoor',
                    'capacity' => 60,
                ],
            ],
        ];

        // Sprawdzamy czy mamy użytkowników
        $users = \App\Models\User::limit(3)->get();
        if ($users->isEmpty()) {
            $this->command->warn('Brak użytkowników - pomijam tworzenie Events');

            return;
        }

        foreach ($events as $eventData) {
            // Tworzymy wydarzenie
            $event = Event::create([
                'organizer_id' => $users->random()->id,
                'title' => $eventData['title'],
                'description' => $eventData['description'],
                'event_type_id' => $eventData['event_type_id'],
                'start_date' => $eventData['start_date'],
                'end_date' => $eventData['end_date'],
                'max_participants' => $eventData['max_participants'],
                'price' => $eventData['price'],
                'status' => 'published',
            ]);

            // Najpierw tworzymy lokalizację
            $location = Location::create([
                'name' => $eventData['location']['name'] ?? $eventData['title'].' - lokalizacja',
                'description' => 'Lokalizacja wydarzenia: '.$eventData['title'],
                'address' => $eventData['location']['address'],
                'city' => $eventData['location']['city'],
                'postal_code' => '00-000', // placeholder
                'country' => 'PL',
                'latitude' => $eventData['location']['latitude'],
                'longitude' => $eventData['location']['longitude'],
                'is_active' => true,
            ]);

            // Potem łączymy event z lokalizacją
            EventLocation::create([
                'event_id' => $event->id,
                'location_id' => $location->id,
                'is_primary' => true,
                'notes' => $eventData['location']['name'] ?? null,
            ]);
        }

        $this->command->info('Utworzono '.count($events).' wydarzeń z lokalizacjami.');
    }
}
