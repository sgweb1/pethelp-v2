<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Pet;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Location;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Conversation;
use App\Models\Message;

class ExtendedTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createExtendedUsers();
        $this->createBookingsAndInteractions();
        $this->createReviews();
        $this->createMessages();

        $this->command->info('Rozszerzone dane testowe zostały utworzone pomyślnie!');
    }

    /**
     * Tworzy dodatkowych użytkowników - właścicieli i opiekunów z pełnymi profilami
     */
    private function createExtendedUsers(): void
    {
        // Dodatkowi opiekunowie z różnymi specjalizacjami
        $extendedSitters = [
            [
                'name' => 'Magdalena Dąbrowska',
                'email' => 'magdalena.dabrowska@example.com',
                'phone' => '+48 666 777 888',
                'city' => 'Łódź',
                'latitude' => 51.7592,
                'longitude' => 19.4560,
                'bio' => 'Specjalistka od opieki nad starszymi zwierzętami. 15 lat doświadczenia weterynaryjnego.',
                'services' => [
                    [
                        'category' => 'opieka-w-domu',
                        'title' => 'Opieka nad starszymi zwierzętami',
                        'description' => 'Specjalistyczna opieka nad zwierzętami z problemami zdrowotnymi i starszymi pupilami.',
                        'price_per_hour' => 35,
                        'price_per_day' => 200,
                        'pet_types' => ['dog', 'cat'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service', 'sitter_home']
                    ],
                    [
                        'category' => 'karmienie',
                        'title' => 'Karmienie z podawaniem leków',
                        'description' => 'Profesjonalne karmienie zwierząt wymagających specjalnej diety i podawania lekarstw.',
                        'price_per_hour' => 30,
                        'price_per_day' => null,
                        'pet_types' => ['dog', 'cat', 'rabbit'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service']
                    ]
                ]
            ],
            [
                'name' => 'Piotr Zieliński',
                'email' => 'piotr.zielinski@example.com',
                'phone' => '+48 888 999 111',
                'city' => 'Katowice',
                'latitude' => 50.2649,
                'longitude' => 19.0238,
                'bio' => 'Trener psów i behaviorista. Specjalizuję się w trudnych przypadkach i problemach behawioralnych.',
                'services' => [
                    [
                        'category' => 'spacery',
                        'title' => 'Spacery z treningiem behawioralnym',
                        'description' => 'Spacery połączone z treningiem posłuszeństwa i korektą problemów behawioralnych.',
                        'price_per_hour' => 50,
                        'price_per_day' => null,
                        'pet_types' => ['dog'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                        'service_types' => ['home_service']
                    ],
                    [
                        'category' => 'opieka-u-opiekuna',
                        'title' => 'Obóz treningowy dla psów',
                        'description' => 'Intensywny trening w moim domu z dużym ogrodem. Idealne dla psów z problemami.',
                        'price_per_hour' => null,
                        'price_per_day' => 120,
                        'pet_types' => ['dog'],
                        'pet_sizes' => ['medium', 'large'],
                        'service_types' => ['sitter_home']
                    ]
                ]
            ]
        ];

        foreach ($extendedSitters as $sitterData) {
            $this->createSitterWithServices($sitterData);
        }

        // Dodatkowi właściciele z różnymi zwierzętami
        $extendedOwners = [
            [
                'name' => 'Aleksandra Wojciechowska',
                'email' => 'aleksandra.wojciechowska@example.com',
                'phone' => '+48 111 222 333',
                'city' => 'Łódź',
                'pets' => [
                    ['name' => 'Bruno', 'type' => 'dog', 'breed' => 'Rottweiler', 'gender' => 'male', 'size' => 'large'],
                    ['name' => 'Coco', 'type' => 'cat', 'breed' => 'Brytyjski krótkowłosy', 'gender' => 'female', 'size' => 'medium'],
                    ['name' => 'Fluffy', 'type' => 'rabbit', 'breed' => 'Angora', 'gender' => 'female', 'size' => 'small']
                ]
            ],
            [
                'name' => 'Michał Kowalczyk',
                'email' => 'michal.kowalczyk@example.com',
                'phone' => '+48 444 555 666',
                'city' => 'Katowice',
                'pets' => [
                    ['name' => 'Zeus', 'type' => 'dog', 'breed' => 'Owczarek niemiecki', 'gender' => 'male', 'size' => 'large'],
                    ['name' => 'Lola', 'type' => 'dog', 'breed' => 'Chihuahua', 'gender' => 'female', 'size' => 'small']
                ]
            ],
            [
                'name' => 'Natalia Sikora',
                'email' => 'natalia.sikora@example.com',
                'phone' => '+48 777 888 999',
                'city' => 'Warszawa',
                'pets' => [
                    ['name' => 'Whiskers', 'type' => 'cat', 'breed' => 'Maine Coon', 'gender' => 'male', 'size' => 'large'],
                    ['name' => 'Mittens', 'type' => 'cat', 'breed' => 'Perski', 'gender' => 'female', 'size' => 'medium'],
                    ['name' => 'Rio', 'type' => 'bird', 'breed' => 'Ara', 'gender' => 'male', 'size' => 'medium']
                ]
            ]
        ];

        foreach ($extendedOwners as $ownerData) {
            $this->createOwnerWithPets($ownerData);
        }
    }

    /**
     * Tworzy opiekuna z usługami na podstawie danych
     */
    private function createSitterWithServices($sitterData)
    {
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

        // Tworzymy profil użytkownika jako sitter
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            $nameParts = explode(' ', $sitterData['name']);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            UserProfile::create([
                'user_id' => $user->id,
                'role' => 'sitter',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $sitterData['phone'],
                'bio' => $sitterData['bio'],
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }

        // Sprawdzamy czy lokalizacja już istnieje
        $location = Location::where('user_id', $user->id)->first();

        if (!$location) {
            // Tworzymy lokalizację
            Location::create([
                'user_id' => $user->id,
                'name' => 'Główna lokalizacja',
                'city' => $sitterData['city'],
                'address' => 'ul. Przykładowa 1',
                'postal_code' => '00-001',
                'country' => 'Polska',
                'latitude' => $sitterData['latitude'],
                'longitude' => $sitterData['longitude'],
                'is_active' => true
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
                        'slug' => \Str::slug($serviceData['title']),
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

    /**
     * Tworzy właściciela z zwierzętami
     */
    private function createOwnerWithPets($ownerData)
    {
        $user = User::where('email', $ownerData['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'email_verified_at' => now(),
                'password' => bcrypt('password')
            ]);
        }

        // Tworzymy profil właściciela
        $profile = UserProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            $nameParts = explode(' ', $ownerData['name']);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            UserProfile::create([
                'user_id' => $user->id,
                'role' => 'owner',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $ownerData['phone'],
                'bio' => 'Kochający właściciel zwierząt z ' . $ownerData['city'] . '.',
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }

        // Dodajmy zwierzęta dla właściciela
        if (isset($ownerData['pets'])) {
            $this->createPetsForUser($user, $ownerData['pets']);
        }
    }

    /**
     * Tworzy zwierzęta dla użytkownika (skopiowane z TestDataSeeder)
     */
    private function createPetsForUser($user, $pets)
    {
        // Mapowanie typów zwierząt na ID z tabeli pet_types
        $petTypeMap = [
            'dog' => 1,
            'cat' => 2,
            'bird' => 3,
            'rabbit' => 4,
            'other' => 5
        ];

        foreach ($pets as $petData) {
            $existingPet = Pet::where('owner_id', $user->id)
                              ->where('name', $petData['name'])
                              ->first();

            if (!$existingPet) {
                Pet::create([
                    'owner_id' => $user->id,
                    'name' => $petData['name'],
                    'pet_type_id' => $petTypeMap[$petData['type']] ?? 5, // Domyślnie "inne"
                    'breed' => $petData['breed'],
                    'size' => $petData['size'],
                    'age' => rand(1, 8),
                    'gender' => $petData['gender'],
                    'description' => 'Wspaniały pupil, bardzo przyjazny i energiczny.',
                    'special_needs' => null,
                    'is_active' => true
                ]);
            }
        }
    }

    /**
     * Tworzy zlecenia i interakcje między użytkownikami
     */
    private function createBookingsAndInteractions(): void
    {
        $owners = User::whereHas('profile', fn($q) => $q->where('role', 'owner'))->get();
        $sitters = User::whereHas('profile', fn($q) => $q->where('role', 'sitter'))->get();

        if ($owners->isEmpty() || $sitters->isEmpty()) {
            $this->command->warn('Brak właścicieli lub opiekunów do tworzenia zleceń.');
            return;
        }

        // Różne statusy zleceń do testowania
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

        foreach ($owners as $owner) {
            $ownerPets = Pet::where('owner_id', $owner->id)->get();

            if ($ownerPets->isEmpty()) continue;

            // Tworzymy 2-4 zlecenia dla każdego właściciela
            $bookingCount = rand(2, 4);

            for ($i = 0; $i < $bookingCount; $i++) {
                $sitter = $sitters->random();
                $sitterServices = Service::where('sitter_id', $sitter->id)->get();

                if ($sitterServices->isEmpty()) continue;

                $service = $sitterServices->random();
                $pet = $ownerPets->random();
                $status = $statuses[array_rand($statuses)];

                // Data rozpoczęcia - ostatnie 3 miesiące lub przyszłość
                $startDate = fake()->dateTimeBetween('-3 months', '+2 months');
                $endDate = (clone $startDate)->modify('+' . rand(1, 7) . ' days');

                // Obliczamy cenę
                $days = $startDate->diff($endDate)->days;
                $totalPrice = $service->price_per_day ?
                    $service->price_per_day * $days :
                    ($service->price_per_hour ? $service->price_per_hour * $days * 8 : 100);

                $booking = Booking::create([
                    'owner_id' => $owner->id,
                    'sitter_id' => $sitter->id,
                    'service_id' => $service->id,
                    'pet_id' => $pet->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'total_price' => $totalPrice,
                    'special_instructions' => fake()->optional(0.3)->paragraph(),
                    'confirmed_at' => in_array($status, ['confirmed', 'in_progress', 'completed']) ? now() : null,
                    'cancelled_at' => $status === 'cancelled' ? now() : null,
                    'cancellation_reason' => $status === 'cancelled' ? fake()->sentence() : null,
                ]);

                $this->command->info("Utworzono zlecenie #{$booking->id}: {$owner->name} -> {$sitter->name} (Status: {$status})");
            }
        }
    }

    /**
     * Tworzy recenzje dla ukończonych zleceń
     */
    private function createReviews(): void
    {
        $completedBookings = Booking::where('status', 'completed')->get();

        foreach ($completedBookings as $booking) {
            // Sprawdzamy czy recenzja już istnieje dla tego zlecenia
            $existingReview = Review::where('booking_id', $booking->id)->first();

            if (!$existingReview && rand(1, 100) <= 80) {
                // Losowo decydujemy, czy to właściciel ocenia opiekuna czy odwrotnie
                $isOwnerReviewing = rand(0, 1);

                Review::create([
                    'booking_id' => $booking->id,
                    'reviewer_id' => $isOwnerReviewing ? $booking->owner_id : $booking->sitter_id,
                    'reviewee_id' => $isOwnerReviewing ? $booking->sitter_id : $booking->owner_id,
                    'rating' => rand(3, 5), // Pozytywne recenzje głównie
                    'comment' => fake()->paragraph(rand(1, 2)),
                    'is_visible' => true,
                ]);

                $reviewerRole = $isOwnerReviewing ? 'właściciel' : 'opiekun';
                $this->command->info("Utworzono recenzję dla zlecenia #{$booking->id} (od: {$reviewerRole})");
            }
        }
    }

    /**
     * Tworzy proste wiadomości (uproszczona wersja)
     */
    private function createMessages(): void
    {
        $bookings = Booking::whereIn('status', ['confirmed', 'in_progress', 'completed'])->get();

        foreach ($bookings->take(10) as $booking) { // Tylko pierwsze 10 dla testów
            // Sprawdzamy czy istnieje konwersacja lub tworzymy prostą
            $conversation = Conversation::create([
                'subject' => "Zlecenie #{$booking->id}",
                'last_message_at' => now(),
            ]);

            // 2-5 wiadomości w konwersacji
            $messageCount = rand(2, 5);

            for ($i = 0; $i < $messageCount; $i++) {
                $isOwnerSender = $i % 2 === 0; // Na przemian właściciel i opiekun
                $senderId = $isOwnerSender ? $booking->owner_id : $booking->sitter_id;

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderId,
                    'content' => fake()->paragraph(rand(1, 2)),
                    'read_at' => rand(0, 1) ? now() : null,
                ]);
            }

            $this->command->info("Utworzono konwersację dla zlecenia #{$booking->id}");
        }
    }
}
