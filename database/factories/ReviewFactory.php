<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Review.
 *
 * Generuje realistyczne recenzje pomiędzy właścicielami a opiekunami zwierząt.
 * Wspiera różne statusy moderacji i generuje odpowiednie komentarze.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Pozytywne komentarze do recenzji (rating 4-5).
     */
    private array $positiveComments = [
        'Wspaniała opieka nad moim pupilem! Polecam z całego serca.',
        'Bardzo profesjonalne podejście i widoczna miłość do zwierząt.',
        'Mój pies był zachwycony! Na pewno skorzystam ponownie.',
        'Świetny kontakt, pełna elastyczność. Jestem bardzo zadowolona.',
        'Piesek wrócił szczęśliwy i zmęczony po długich spacerach.',
        'Doskonała komunikacja i zaangażowanie. Gorąco polecam!',
        'Czuję się spokojnie zostawiając mojego pupila pod opieką.',
        'Najlepsza opiekunka jaką znalazłam! Profesjonalizm na najwyższym poziomie.',
        'Kot był zadowolony i spokojny. Widać że ma doświadczenie.',
        'Bardzo miła i cierpliwa osoba. Mój pies ją pokochał!',
    ];

    /**
     * Neutralne komentarze (rating 3).
     */
    private array $neutralComments = [
        'Opieka była w porządku, bez szczególnych uwag.',
        'Wszystko przebiegło zgodnie z ustaleniami.',
        'Standardowa opieka, nic nadzwyczajnego.',
        'Poprawna opieka, ale bez większego zaangażowania.',
        'W porządku, choć oczekiwałam nieco więcej.',
    ];

    /**
     * Negatywne komentarze (rating 1-2).
     */
    private array $negativeComments = [
        'Niestety opieka nie spełniła moich oczekiwań.',
        'Problemy z komunikacją i brakiem elastyczności.',
        'Mój pies wrócił zestresowany i nie chciał jeść.',
        'Nie dotrzymano ustalonych godzin opieki.',
    ];

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);
        $comment = $this->getCommentForRating($rating);

        return [
            'booking_id' => Booking::factory(),
            'reviewer_id' => User::factory(),
            'reviewee_id' => User::factory(),
            'rating' => $rating,
            'comment' => $comment,
            'is_visible' => true,
            'moderation_status' => 'approved',
        ];
    }

    /**
     * Zwraca odpowiedni komentarz dla danego ratingu.
     */
    private function getCommentForRating(int $rating): string
    {
        return match ($rating) {
            5, 4 => fake()->randomElement($this->positiveComments),
            3 => fake()->randomElement($this->neutralComments),
            2, 1 => fake()->randomElement($this->negativeComments),
            default => fake()->sentence(),
        };
    }

    /**
     * Recenzja z wysoką oceną (4-5 gwiazdek).
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->randomElement($this->positiveComments),
            'is_visible' => true,
            'moderation_status' => 'approved',
        ]);
    }

    /**
     * Recenzja z neutralną oceną (3 gwiazdki).
     */
    public function neutral(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 3,
            'comment' => fake()->randomElement($this->neutralComments),
            'is_visible' => true,
            'moderation_status' => 'approved',
        ]);
    }

    /**
     * Recenzja z niską oceną (1-2 gwiazdki).
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'comment' => fake()->randomElement($this->negativeComments),
            'is_visible' => true,
            'moderation_status' => fake()->randomElement(['pending', 'approved']),
        ]);
    }

    /**
     * Recenzja oczekująca na moderację.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'pending',
            'is_visible' => false,
            'moderated_at' => null,
            'moderated_by' => null,
        ]);
    }

    /**
     * Recenzja zaakceptowana przez moderację.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'approved',
            'is_visible' => true,
            'moderated_at' => now()->subDays(rand(1, 30)),
            'moderated_by' => User::factory(),
        ]);
    }

    /**
     * Recenzja odrzucona przez moderację.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'rejected',
            'is_visible' => false,
            'moderated_at' => now()->subDays(rand(1, 30)),
            'moderated_by' => User::factory(),
            'admin_response' => fake()->sentence(),
        ]);
    }

    /**
     * Recenzja od właściciela zwierzęcia o opiekunie.
     */
    public function fromOwner(): static
    {
        return $this->state(function (array $attributes) {
            $booking = Booking::factory()->completed()->create();

            return [
                'booking_id' => $booking->id,
                'reviewer_id' => $booking->owner_id,
                'reviewee_id' => $booking->sitter_id,
            ];
        });
    }

    /**
     * Recenzja od opiekuna o właścicielu zwierzęcia.
     */
    public function fromSitter(): static
    {
        return $this->state(function (array $attributes) {
            $booking = Booking::factory()->completed()->create();

            return [
                'booking_id' => $booking->id,
                'reviewer_id' => $booking->sitter_id,
                'reviewee_id' => $booking->owner_id,
            ];
        });
    }

    /**
     * Recenzja z odpowiedzią administratora.
     */
    public function withAdminResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_response' => fake()->sentence(),
            'moderated_by' => User::factory(),
            'moderated_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    /**
     * Recenzja z niedawnej przeszłości.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
