<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Booking.
 *
 * Generuje realistyczne dane zleceń pomiędzy właścicielami a opiekunami zwierząt.
 * Obsługuje różne statusy zleceń, daty rozłożone w czasie i odpowiednie ceny.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+2 months');
        $endDate = (clone $startDate)->modify('+'.rand(1, 14).' days');

        // Cena bazowa zależna od długości pobytu
        $days = $startDate->diff($endDate)->days;
        $pricePerDay = fake()->numberBetween(50, 200);
        $totalPrice = $pricePerDay * max($days, 1);

        return [
            'owner_id' => User::factory(),
            'sitter_id' => User::factory(),
            'service_id' => Service::factory(),
            'pet_id' => Pet::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled']),
            'total_price' => $totalPrice,
            'special_instructions' => fake()->optional(0.4)->paragraph(),
        ];
    }

    /**
     * Stan zlecenia oczekującego na potwierdzenie.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'confirmed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Stan zlecenia potwierdzonego.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now()->subDays(rand(1, 30)),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Stan zlecenia w trakcie realizacji.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = now()->subDays(rand(1, 7));
            $endDate = now()->addDays(rand(1, 7));

            return [
                'status' => 'in_progress',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'confirmed_at' => $startDate->copy()->subDays(rand(3, 14)),
                'cancelled_at' => null,
            ];
        });
    }

    /**
     * Stan zlecenia zakończonego.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = fake()->dateTimeBetween('-6 months', '-1 day');
            $startDate = (clone $endDate)->modify('-'.rand(1, 14).' days');

            return [
                'status' => 'completed',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'confirmed_at' => (clone $startDate)->modify('-'.rand(3, 14).' days'),
                'cancelled_at' => null,
            ];
        });
    }

    /**
     * Stan zlecenia anulowanego.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now()->subDays(rand(1, 60)),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Zlecenie z bliskiej przeszłości (ostatni miesiąc).
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-1 month', 'now');
            $endDate = (clone $startDate)->modify('+'.rand(1, 7).' days');

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Zlecenie z przyszłości.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('now', '+2 months');
            $endDate = (clone $startDate)->modify('+'.rand(1, 14).' days');

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => fake()->randomElement(['pending', 'confirmed']),
            ];
        });
    }

    /**
     * Zlecenie krótkoterminowe (1-3 dni).
     */
    public function shortTerm(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-3 months', '+1 month');
            $endDate = (clone $startDate)->modify('+'.rand(1, 3).' days');

            $days = $startDate->diff($endDate)->days;
            $pricePerDay = fake()->numberBetween(80, 150);
            $totalPrice = $pricePerDay * max($days, 1);

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $totalPrice,
            ];
        });
    }

    /**
     * Zlecenie długoterminowe (7-14 dni).
     */
    public function longTerm(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-3 months', '+1 month');
            $endDate = (clone $startDate)->modify('+'.rand(7, 14).' days');

            $days = $startDate->diff($endDate)->days;
            $pricePerDay = fake()->numberBetween(60, 120); // Niższa cena za dzień przy dłuższym okresie
            $totalPrice = $pricePerDay * max($days, 1);

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $totalPrice,
            ];
        });
    }
}
