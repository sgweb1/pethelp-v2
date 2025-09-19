<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRegistrationFactory extends Factory
{
    public function definition(): array
    {
        $registeredAt = $this->faker->dateTimeBetween('-1 month', 'now');

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'rejected', 'cancelled', 'waiting_list']),
            'message' => $this->faker->optional(0.4)->sentence(),
            'organizer_notes' => $this->faker->optional(0.2)->sentence(),
            'registered_at' => $registeredAt,
            'status_updated_at' => $this->faker->optional(0.6)->dateTimeBetween($registeredAt, 'now'),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => 'confirmed',
            'status_updated_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'status_updated_at' => null,
        ]);
    }

    public function onWaitingList(): static
    {
        return $this->state(fn () => [
            'status' => 'waiting_list',
            'status_updated_at' => now(),
        ]);
    }

    public function forEvent(Event $event): static
    {
        return $this->state(fn () => ['event_id' => $event->id]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }
}
