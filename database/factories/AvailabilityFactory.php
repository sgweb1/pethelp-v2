<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Availability.
 *
 * Generuje sloty dostępności dla opiekunów zwierząt.
 * Wspiera różne typy slotów czasowych i powtarzające się dostępności.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+3 months');
        $timeSlot = fake()->randomElement(['morning', 'afternoon', 'evening', 'all_day', 'custom']);

        [$startTime, $endTime] = $this->getTimeRangeForSlot($timeSlot);

        return [
            'sitter_id' => User::factory(),
            'service_id' => null,
            'service_type' => fake()->randomElement(['home_service', 'sitter_home', 'walking', null]),
            'time_slot' => $timeSlot,
            'available_services' => null,
            'available_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => true,
            'is_recurring' => false,
            'recurring_days' => null,
            'recurring_end_date' => null,
            'recurring_weeks' => null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Zwraca zakres godzin dla danego slotu czasowego.
     */
    private function getTimeRangeForSlot(string $timeSlot): array
    {
        return match ($timeSlot) {
            'morning' => ['08:00', '12:00'],
            'afternoon' => ['12:00', '17:00'],
            'evening' => ['17:00', '21:00'],
            'all_day' => ['08:00', '20:00'],
            'overnight' => ['20:00', '08:00'],
            'custom' => [
                fake()->time('H:i', '18:00'),
                fake()->time('H:i', '23:59'),
            ],
            default => ['09:00', '17:00'],
        };
    }

    /**
     * Dostępność na rano (8:00-12:00).
     */
    public function morning(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_slot' => 'morning',
            'start_time' => '08:00',
            'end_time' => '12:00',
        ]);
    }

    /**
     * Dostępność na popołudnie (12:00-17:00).
     */
    public function afternoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_slot' => 'afternoon',
            'start_time' => '12:00',
            'end_time' => '17:00',
        ]);
    }

    /**
     * Dostępność na wieczór (17:00-21:00).
     */
    public function evening(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_slot' => 'evening',
            'start_time' => '17:00',
            'end_time' => '21:00',
        ]);
    }

    /**
     * Dostępność przez cały dzień (8:00-20:00).
     */
    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_slot' => 'all_day',
            'start_time' => '08:00',
            'end_time' => '20:00',
        ]);
    }

    /**
     * Dostępność na nocleg (20:00-8:00).
     */
    public function overnight(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_slot' => 'overnight',
            'start_time' => '20:00',
            'end_time' => '08:00',
            'service_type' => 'sitter_home',
        ]);
    }

    /**
     * Dostępność powtarzająca się.
     */
    public function recurring(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('now', '+1 month');
            $endDate = (clone $startDate)->modify('+'.rand(4, 12).' weeks');

            return [
                'is_recurring' => true,
                'recurring_days' => fake()->randomElements([1, 2, 3, 4, 5, 6, 0], rand(2, 5)),
                'recurring_end_date' => $endDate,
                'recurring_weeks' => rand(4, 12),
            ];
        });
    }

    /**
     * Dostępność dla konkretnej usługi.
     */
    public function forService(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_id' => Service::factory(),
        ]);
    }

    /**
     * Dostępność dla usługi w domu klienta.
     */
    public function homeService(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'home_service',
        ]);
    }

    /**
     * Dostępność dla usługi w domu opiekuna.
     */
    public function sitterHome(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'sitter_home',
        ]);
    }

    /**
     * Dostępność dla spacerów.
     */
    public function walking(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => 'walking',
            'time_slot' => fake()->randomElement(['morning', 'afternoon', 'evening']),
        ]);
    }

    /**
     * Niedostępność (blokada kalendarza).
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'time_slot' => 'vacation',
            'notes' => 'Urlop',
        ]);
    }

    /**
     * Urlop - blokada na wiele dni.
     */
    public function vacation(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('+1 week', '+2 months');
            $endDate = (clone $startDate)->modify('+'.rand(3, 14).' days');

            return [
                'is_available' => false,
                'time_slot' => 'vacation',
                'available_date' => $startDate,
                'vacation_end_date' => $endDate,
                'start_time' => '00:00',
                'end_time' => '23:59',
                'notes' => 'Urlop',
            ];
        });
    }

    /**
     * Dostępność na najbliższe dni (tydzień).
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_date' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }

    /**
     * Dostępność z notatkami.
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => fake()->sentence(),
        ]);
    }

    /**
     * Dostępność dla opiekuna w dni powszednie.
     */
    public function weekdays(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurring_days' => [1, 2, 3, 4, 5], // Pon-Pt
            'recurring_end_date' => now()->addMonths(3),
            'recurring_weeks' => 12,
        ]);
    }

    /**
     * Dostępność dla opiekuna w weekendy.
     */
    public function weekends(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurring_days' => [0, 6], // Sob-Niedz
            'recurring_end_date' => now()->addMonths(3),
            'recurring_weeks' => 12,
        ]);
    }
}
