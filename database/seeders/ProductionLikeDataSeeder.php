<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Location;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder generujący dane produkcyjne - setki rekordów.
 *
 * Tworzy realistyczny dataset symulujący stronę działającą od kilku miesięcy:
 * - 150-200 użytkowników (opiekuni + właściciele)
 * - 300-400 usług
 * - 250-350 zwierząt
 * - 600-800 zleceń z różnymi statusami
 * - 400-600 recenzji
 * - 100-150 konwersacji z wiadomościami
 * - Dostępności w kalendarzach opiekunów
 * - Płatności powiązane ze zleceniami
 *
 * Dane są rozłożone w czasie (ostatnie 6 miesięcy) dla realizmu.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ProductionLikeDataSeeder extends Seeder
{
    private array $polishCities = [
        ['name' => 'Warszawa', 'lat' => 52.2297, 'lng' => 21.0122],
        ['name' => 'Kraków', 'lat' => 50.0647, 'lng' => 19.9450],
        ['name' => 'Wrocław', 'lat' => 51.1079, 'lng' => 17.0385],
        ['name' => 'Poznań', 'lat' => 52.4064, 'lng' => 16.9252],
        ['name' => 'Gdańsk', 'lat' => 54.3520, 'lng' => 18.6466],
        ['name' => 'Łódź', 'lat' => 51.7592, 'lng' => 19.4560],
        ['name' => 'Katowice', 'lat' => 50.2649, 'lng' => 19.0238],
        ['name' => 'Szczecin', 'lat' => 53.4285, 'lng' => 14.5528],
        ['name' => 'Bydgoszcz', 'lat' => 53.1235, 'lng' => 18.0084],
        ['name' => 'Lublin', 'lat' => 51.2465, 'lng' => 22.5684],
    ];

    private array $petNames = [
        'dog' => ['Rex', 'Bella', 'Max', 'Luna', 'Charlie', 'Daisy', 'Rocky', 'Lola', 'Buddy', 'Lucy', 'Duke', 'Molly', 'Zeus', 'Sadie', 'Bear'],
        'cat' => ['Whiskers', 'Mittens', 'Shadow', 'Simba', 'Luna', 'Oliver', 'Chloe', 'Leo', 'Bella', 'Milo', 'Kitty', 'Tiger', 'Smokey'],
        'rabbit' => ['Fluffy', 'Snowball', 'Thumper', 'Cotton', 'Bugs', 'Oreo', 'Peter', 'Flopsy'],
        'bird' => ['Tweety', 'Rio', 'Kiwi', 'Sunny', 'Blue', 'Coco'],
    ];

    private array $dogBreeds = [
        'Golden Retriever', 'Labrador', 'Owczarek niemiecki', 'Buldog francuski',
        'Beagle', 'Poodle', 'Husky', 'Chihuahua', 'Yorkshire Terrier', 'Boxer',
        'Rottweiler', 'Cocker Spaniel', 'Border Collie', 'Jack Russell Terrier',
    ];

    private array $catBreeds = [
        'Perski', 'Maine Coon', 'Brytyjski krótkowłosy', 'Syberyjski',
        'Ragdoll', 'Syjamski', 'Sfinks', 'Bengalski', 'Rosyjski niebieski',
    ];

    /**
     * Uruchamia seeder.
     */
    public function run(): void
    {
        $this->command->info('🚀 Rozpoczynam generowanie danych produkcyjnych...');

        DB::transaction(function () {
            // Tworzenie użytkowników
            $this->command->info('👥 Tworzenie użytkowników...');
            $sitters = $this->createSitters(100); // 100 opiekunów
            $owners = $this->createOwners(100); // 100 właścicieli
            $this->command->info("✅ Utworzono {$sitters->count()} opiekunów i {$owners->count()} właścicieli");

            // Tworzenie lokalizacji dla opiekunów
            $this->command->info('📍 Tworzenie lokalizacji...');
            $this->createLocationsForSitters($sitters);
            $this->command->info('✅ Lokalizacje utworzone');

            // Tworzenie usług
            $this->command->info('💼 Tworzenie usług...');
            $services = $this->createServices($sitters);
            $this->command->info("✅ Utworzono {$services->count()} usług");

            // Tworzenie zwierząt
            $this->command->info('🐾 Tworzenie zwierząt...');
            $pets = $this->createPets($owners);
            $this->command->info("✅ Utworzono {$pets->count()} zwierząt");

            // Tworzenie dostępności
            $this->command->info('📅 Tworzenie dostępności...');
            $this->createAvailabilities($sitters);
            $this->command->info('✅ Dostępności utworzone');

            // Tworzenie zleceń
            $this->command->info('📋 Tworzenie zleceń...');
            $bookings = $this->createBookings($owners, $sitters, $services, $pets);
            $this->command->info("✅ Utworzono {$bookings->count()} zleceń");

            // Tworzenie płatności
            $this->command->info('💳 Tworzenie płatności...');
            $payments = $this->createPayments($bookings);
            $this->command->info("✅ Utworzono {$payments->count()} płatności");

            // Tworzenie recenzji
            $this->command->info('⭐ Tworzenie recenzji...');
            $reviews = $this->createReviews($bookings);
            $this->command->info("✅ Utworzono {$reviews->count()} recenzji");

            // Tworzenie konwersacji i wiadomości
            $this->command->info('💬 Tworzenie konwersacji i wiadomości...');
            [$conversations, $messages] = $this->createConversationsAndMessages($bookings);
            $this->command->info("✅ Utworzono {$conversations->count()} konwersacji i {$messages} wiadomości");
        });

        $this->command->info('');
        $this->command->info('🎉 Dane produkcyjne zostały wygenerowane pomyślnie!');
        $this->showStatistics();
    }

    /**
     * Tworzy opiekunów z profilami.
     */
    private function createSitters(int $count): \Illuminate\Support\Collection
    {
        $sitters = collect();

        for ($i = 0; $i < $count; $i++) {
            $user = User::factory()->create();

            UserProfile::factory()->create([
                'user_id' => $user->id,
                'role' => 'sitter',
                'is_verified' => fake()->boolean(85), // 85% zweryfikowanych
                'verified_at' => fake()->boolean(85) ? now()->subDays(rand(1, 180)) : null,
            ]);

            $sitters->push($user);
        }

        return $sitters;
    }

    /**
     * Tworzy właścicieli z profilami.
     */
    private function createOwners(int $count): \Illuminate\Support\Collection
    {
        $owners = collect();

        for ($i = 0; $i < $count; $i++) {
            $user = User::factory()->create();

            UserProfile::factory()->create([
                'user_id' => $user->id,
                'role' => 'owner',
                'is_verified' => fake()->boolean(70), // 70% zweryfikowanych
                'verified_at' => fake()->boolean(70) ? now()->subDays(rand(1, 180)) : null,
            ]);

            $owners->push($user);
        }

        return $owners;
    }

    /**
     * Tworzy lokalizacje dla opiekunów.
     */
    private function createLocationsForSitters(\Illuminate\Support\Collection $sitters): void
    {
        foreach ($sitters as $sitter) {
            $city = fake()->randomElement($this->polishCities);

            // Małe losowe przesunięcie dla różnorodności lokalizacji w mieście
            $latOffset = fake()->randomFloat(4, -0.05, 0.05);
            $lngOffset = fake()->randomFloat(4, -0.05, 0.05);

            Location::factory()->create([
                'user_id' => $sitter->id,
                'city' => $city['name'],
                'latitude' => $city['lat'] + $latOffset,
                'longitude' => $city['lng'] + $lngOffset,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Tworzy usługi dla opiekunów.
     */
    private function createServices(\Illuminate\Support\Collection $sitters): \Illuminate\Support\Collection
    {
        $services = collect();
        $categories = ServiceCategory::all();

        foreach ($sitters as $sitter) {
            // Każdy opiekun oferuje 2-5 usług
            $serviceCount = rand(2, 5);

            for ($i = 0; $i < $serviceCount; $i++) {
                $category = $categories->random();

                $service = Service::factory()->create([
                    'sitter_id' => $sitter->id,
                    'category_id' => $category->id,
                    'is_active' => fake()->boolean(90), // 90% aktywnych
                ]);

                $services->push($service);
            }
        }

        return $services;
    }

    /**
     * Tworzy zwierzęta dla właścicieli.
     */
    private function createPets(\Illuminate\Support\Collection $owners): \Illuminate\Support\Collection
    {
        $pets = collect();
        $petTypeMap = ['dog' => 1, 'cat' => 2, 'bird' => 3, 'rabbit' => 4];

        foreach ($owners as $owner) {
            // Każdy właściciel ma 1-4 zwierzęta
            $petCount = rand(1, 4);

            for ($i = 0; $i < $petCount; $i++) {
                $petType = fake()->randomElement(['dog', 'cat', 'bird', 'rabbit']);
                $petName = fake()->randomElement($this->petNames[$petType]);

                $breed = match ($petType) {
                    'dog' => fake()->randomElement($this->dogBreeds),
                    'cat' => fake()->randomElement($this->catBreeds),
                    'rabbit' => 'Królik',
                    'bird' => 'Papuga',
                    default => 'Inne',
                };

                $pet = Pet::factory()->create([
                    'owner_id' => $owner->id,
                    'name' => $petName,
                    'pet_type_id' => $petTypeMap[$petType],
                    'breed' => $breed,
                    'is_active' => true,
                ]);

                $pets->push($pet);
            }
        }

        return $pets;
    }

    /**
     * Tworzy dostępności dla opiekunów.
     */
    private function createAvailabilities(\Illuminate\Support\Collection $sitters): void
    {
        foreach ($sitters as $sitter) {
            // Każdy opiekun ma 10-30 slotów dostępności
            $slotCount = rand(10, 30);

            for ($i = 0; $i < $slotCount; $i++) {
                Availability::factory()->create([
                    'sitter_id' => $sitter->id,
                ]);
            }

            // Dodaj kilka powtarzających się dostępności
            $recurringCount = rand(2, 5);
            for ($i = 0; $i < $recurringCount; $i++) {
                Availability::factory()->recurring()->create([
                    'sitter_id' => $sitter->id,
                ]);
            }
        }
    }

    /**
     * Tworzy zlecenia.
     */
    private function createBookings(
        \Illuminate\Support\Collection $owners,
        \Illuminate\Support\Collection $sitters,
        \Illuminate\Support\Collection $services,
        \Illuminate\Support\Collection $pets
    ): \Illuminate\Support\Collection {
        $bookings = collect();

        // Rozkład statusów zleceń (realistyczny)
        $statusDistribution = [
            'completed' => 50, // 50% ukończonych
            'confirmed' => 15, // 15% potwierdzonych
            'in_progress' => 10, // 10% w trakcie
            'pending' => 15, // 15% oczekujących
            'cancelled' => 10, // 10% anulowanych
        ];

        // Generujemy 700 zleceń
        for ($i = 0; $i < 700; $i++) {
            $owner = $owners->random();
            $sitter = $sitters->random();

            // Sprawdzamy czy opiekun ma usługi
            $sitterServices = Service::where('sitter_id', $sitter->id)->get();
            if ($sitterServices->isEmpty()) {
                continue;
            }

            $service = $sitterServices->random();
            $ownerPets = Pet::where('owner_id', $owner->id)->get();

            if ($ownerPets->isEmpty()) {
                continue;
            }

            $pet = $ownerPets->random();

            // Wybór statusu według rozkładu
            $status = $this->getWeightedRandomStatus($statusDistribution);

            // Tworzenie zlecenia z odpowiednim stanem
            $factory = match ($status) {
                'completed' => Booking::factory()->completed(),
                'confirmed' => Booking::factory()->confirmed(),
                'in_progress' => Booking::factory()->inProgress(),
                'pending' => Booking::factory()->pending(),
                'cancelled' => Booking::factory()->cancelled(),
            };

            $booking = $factory->create([
                'owner_id' => $owner->id,
                'sitter_id' => $sitter->id,
                'service_id' => $service->id,
                'pet_id' => $pet->id,
            ]);

            $bookings->push($booking);
        }

        return $bookings;
    }

    /**
     * Tworzy płatności dla zleceń.
     */
    private function createPayments(\Illuminate\Support\Collection $bookings): \Illuminate\Support\Collection
    {
        $payments = collect();

        // Płatności tylko dla zleceń które nie są pending
        $paidBookings = $bookings->whereNotIn('status', ['pending']);

        foreach ($paidBookings as $booking) {
            $paymentStatus = match ($booking->status) {
                'completed', 'confirmed', 'in_progress' => 'completed',
                'cancelled' => fake()->randomElement(['refunded', 'completed']),
                default => 'pending',
            };

            $payment = Payment::factory()->create([
                'booking_id' => $booking->id,
                'user_id' => $booking->owner_id,
                'amount' => $booking->total_price,
                'original_amount' => $booking->total_price,
                'commission' => round($booking->total_price * 0.15, 2),
                'status' => $paymentStatus,
                'processed_at' => $paymentStatus === 'completed' ? now()->subDays(rand(1, 180)) : null,
            ]);

            $payments->push($payment);
        }

        return $payments;
    }

    /**
     * Tworzy recenzje dla ukończonych zleceń.
     */
    private function createReviews(\Illuminate\Support\Collection $bookings): \Illuminate\Support\Collection
    {
        $reviews = collect();

        $completedBookings = $bookings->where('status', 'completed');

        foreach ($completedBookings as $booking) {
            // 80% ukończonych zleceń ma recenzję od właściciela
            if (fake()->boolean(80)) {
                // Właściciel ocenia opiekuna
                $ownerReview = Review::factory()->positive()->create([
                    'booking_id' => $booking->id,
                    'reviewer_id' => $booking->owner_id,
                    'reviewee_id' => $booking->sitter_id,
                ]);

                $reviews->push($ownerReview);
            }
        }

        return $reviews;
    }

    /**
     * Tworzy konwersacje i wiadomości.
     */
    private function createConversationsAndMessages(\Illuminate\Support\Collection $bookings): array
    {
        $conversations = collect();
        $messageCount = 0;

        // Wybieramy 120 losowych zleceń do stworzenia konwersacji
        $bookingsWithConversations = $bookings->random(min(120, $bookings->count()));

        foreach ($bookingsWithConversations as $booking) {
            $conversation = Conversation::factory()->create([
                'user_one_id' => min($booking->owner_id, $booking->sitter_id),
                'user_two_id' => max($booking->owner_id, $booking->sitter_id),
                'booking_id' => $booking->id,
                'subject' => "Zlecenie #{$booking->id}",
            ]);

            $conversations->push($conversation);

            // Każda konwersacja ma 3-15 wiadomości
            $messagesInConversation = rand(3, 15);
            $lastMessageTime = fake()->dateTimeBetween('-3 months', 'now');

            for ($i = 0; $i < $messagesInConversation; $i++) {
                $isOwnerSender = $i % 2 === 0;
                $senderId = $isOwnerSender ? $booking->owner_id : $booking->sitter_id;

                $lastMessageTime = (clone $lastMessageTime)->modify('+'.rand(10, 360).' minutes');

                Message::factory()->create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $senderId,
                    'is_read' => $i < $messagesInConversation - 3, // Ostatnie 3 nieprzeczytane
                    'read_at' => $i < $messagesInConversation - 3 ? $lastMessageTime : null,
                    'created_at' => $lastMessageTime,
                ]);

                $messageCount++;
            }

            // Aktualizuj last_message_at w konwersacji
            $conversation->update(['last_message_at' => $lastMessageTime]);
        }

        return [$conversations, $messageCount];
    }

    /**
     * Zwraca losowy status według wag.
     */
    private function getWeightedRandomStatus(array $distribution): string
    {
        $rand = rand(1, 100);
        $sum = 0;

        foreach ($distribution as $status => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $status;
            }
        }

        return 'pending';
    }

    /**
     * Wyświetla statystyki wygenerowanych danych.
     */
    private function showStatistics(): void
    {
        $this->command->table(
            ['Model', 'Ilość'],
            [
                ['Użytkownicy (Opiekunowie)', User::whereHas('profile', fn ($q) => $q->where('role', 'sitter'))->count()],
                ['Użytkownicy (Właściciele)', User::whereHas('profile', fn ($q) => $q->where('role', 'owner'))->count()],
                ['Usługi', Service::count()],
                ['Zwierzęta', Pet::count()],
                ['Zlecenia', Booking::count()],
                ['- Ukończone', Booking::where('status', 'completed')->count()],
                ['- Potwierdzone', Booking::where('status', 'confirmed')->count()],
                ['- W trakcie', Booking::where('status', 'in_progress')->count()],
                ['- Oczekujące', Booking::where('status', 'pending')->count()],
                ['- Anulowane', Booking::where('status', 'cancelled')->count()],
                ['Płatności', Payment::count()],
                ['Recenzje', Review::count()],
                ['Konwersacje', Conversation::count()],
                ['Wiadomości', Message::count()],
                ['Dostępności', Availability::count()],
            ]
        );
    }
}
