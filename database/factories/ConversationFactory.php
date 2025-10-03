<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Conversation.
 *
 * Generuje konwersacje pomiędzy użytkownikami aplikacji.
 * Może być powiązana z konkretnym zleceniem lub być niezależną rozmową.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_one_id' => User::factory(),
            'user_two_id' => User::factory(),
            'booking_id' => null,
            'subject' => fake()->optional(0.5)->sentence(3),
            'last_message_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Konwersacja powiązana ze zleceniem.
     */
    public function withBooking(): static
    {
        return $this->state(function (array $attributes) {
            $booking = Booking::factory()->create();

            return [
                'user_one_id' => $booking->owner_id,
                'user_two_id' => $booking->sitter_id,
                'booking_id' => $booking->id,
                'subject' => "Zlecenie #{$booking->id}",
            ];
        });
    }

    /**
     * Konwersacja pomiędzy właścicielem a opiekunem.
     */
    public function betweenOwnerAndSitter(): static
    {
        return $this->state(function (array $attributes) {
            $owner = User::factory()->create();
            $sitter = User::factory()->create();

            // Zapewniamy odpowiednią kolejność user_one_id i user_two_id
            $userOneId = min($owner->id, $sitter->id);
            $userTwoId = max($owner->id, $sitter->id);

            return [
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
            ];
        });
    }

    /**
     * Konwersacja z niedawną aktywnością.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_message_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Konwersacja nieaktywna (stara).
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_message_at' => fake()->dateTimeBetween('-6 months', '-2 months'),
        ]);
    }

    /**
     * Konwersacja z tematem.
     */
    public function withSubject(): static
    {
        return $this->state(fn (array $attributes) => [
            'subject' => fake()->sentence(4),
        ]);
    }
}
