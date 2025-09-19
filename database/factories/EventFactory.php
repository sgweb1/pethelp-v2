<?php

namespace Database\Factories;

use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('now', '+3 months');
        $endsAt = $this->faker->dateTimeBetween($startsAt, $startsAt->format('Y-m-d H:i:s') . ' +8 hours');

        return [
            'user_id' => User::factory(),
            'event_type_id' => EventType::factory(),
            'title' => $this->faker->randomElement([
                'Spacer z psami w parku',
                'Warsztaty posłuszeństwa dla szczeniąt',
                'Piknik adopcyjny zwierząt',
                'Konkurs najpiękniejszego psa',
                'Sesja treningowa agility',
                'Spotkanie miłośników kotów',
                'Akcja kastracji bezdomnych zwierząt',
                'Wystawa psów rasowych',
            ]),
            'description' => $this->faker->paragraphs(3, true),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'max_participants' => $this->faker->optional(0.8)->numberBetween(10, 100),
            'entry_fee' => $this->faker->optional(0.4, 0.00)->randomFloat(2, 5, 50),
            'currency' => 'PLN',
            'is_invitation_only' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['draft', 'published', 'cancelled']),
            'is_featured' => $this->faker->boolean(10),
            'registration_deadline' => $this->faker->optional(0.7)->dateTimeBetween('now', $startsAt),
            'allow_waiting_list' => $this->faker->boolean(30),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }

    public function upcoming(): static
    {
        $startsAt = $this->faker->dateTimeBetween('+1 day', '+2 months');
        $endsAt = $this->faker->dateTimeBetween($startsAt, $startsAt->format('Y-m-d H:i:s') . ' +6 hours');

        return $this->state(fn () => [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'published',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function free(): static
    {
        return $this->state(fn () => ['entry_fee' => null]);
    }

    public function paid(): static
    {
        return $this->state(fn () => ['entry_fee' => $this->faker->randomFloat(2, 5, 50)]);
    }

    public function withLocation(): static
    {
        return $this->has(
            \App\Models\EventLocation::factory(),
            'location'
        );
    }
}
