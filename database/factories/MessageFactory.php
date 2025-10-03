<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Message.
 *
 * Generuje wiadomości w ramach konwersacji pomiędzy użytkownikami.
 * Wspiera różne stany wiadomości (przeczytane, nieprzeczytane, ukryte).
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Typowe wiadomości dotyczące opieki nad zwierzętami.
     */
    private array $messageTemplates = [
        'Dzień dobry! Czy będzie Pan/Pani dostępny/a w tym terminie?',
        'Bardzo dziękuję za szybką odpowiedź!',
        'Mój pies ma specjalne wymagania żywieniowe. Czy to problem?',
        'Czy moglibyśmy ustalić dokładne godziny?',
        'Świetnie! Potwierdzam rezerwację.',
        'Czy możemy się umówić na spotkanie przed terminem opieki?',
        'Jak przebiega dzień z moim pupilem?',
        'Wszystko przebiega wspaniale! Piesek jest zadowolony.',
        'Proszę o więcej informacji na temat Państwa doświadczenia.',
        'Czy akceptuje Pan/Pani płatność przelewem?',
        'Mój kot wymaga podawania leków o określonych godzinach.',
        'Oczywiście, nie ma problemu. Mam doświadczenie z takimi przypadkami.',
        'Kiedy będę mógł/mogła odebrać swojego pupila?',
        'Do zobaczenia jutro o umówionej godzinie!',
        'Dziękuję za wspaniałą opiekę nad moim zwierzakiem!',
    ];

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'content' => fake()->randomElement($this->messageTemplates),
            'is_read' => fake()->boolean(60), // 60% wiadomości przeczytanych
            'read_at' => null,
            'is_hidden' => false,
        ];
    }

    /**
     * Wiadomość przeczytana.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Wiadomość nieprzeczytana.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Wiadomość ukryta przez moderację.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hidden' => true,
            'hidden_reason' => fake()->sentence(),
            'hidden_by' => User::factory(),
            'hidden_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Wiadomość z konkretnymi danymi nadawcy i konwersacji.
     */
    public function forConversation(Conversation $conversation, User $sender): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
        ]);
    }

    /**
     * Wiadomość niedawna (ostatni tydzień).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Wiadomość stara.
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-6 months', '-1 month'),
        ]);
    }

    /**
     * Wiadomość z długą treścią.
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(3, true),
        ]);
    }

    /**
     * Wiadomość z krótką treścią.
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->sentence(3),
        ]);
    }

    /**
     * Sekwencja wiadomości w ramach jednej konwersacji.
     */
    public function createSequence(Conversation $conversation, array $senders, int $count = 5): array
    {
        $messages = [];
        $lastMessageTime = fake()->dateTimeBetween('-1 month', 'now');

        for ($i = 0; $i < $count; $i++) {
            $sender = $senders[$i % count($senders)];
            $lastMessageTime = (clone $lastMessageTime)->modify('+'.rand(5, 120).' minutes');

            $messages[] = Message::factory()
                ->forConversation($conversation, $sender)
                ->create([
                    'created_at' => $lastMessageTime,
                    'is_read' => $i < $count - 2, // Ostatnie 2 wiadomości nieprzeczytane
                ]);
        }

        return $messages;
    }
}
