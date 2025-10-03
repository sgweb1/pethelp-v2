<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory dla modelu Payment.
 *
 * Generuje płatności dla zleceń i subskrypcji.
 * Obsługuje różne statusy płatności, metody płatności i prowizje.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Definiuje domyślny stan modelu.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 50, 500);
        $commission = round($amount * 0.15, 2); // 15% prowizji

        return [
            'booking_id' => Booking::factory(),
            'user_id' => User::factory(),
            'subscription_plan_id' => null,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed']),
            'amount' => $amount,
            'original_amount' => $amount,
            'proration_credit' => 0,
            'commission' => $commission,
            'payment_method' => fake()->randomElement(['card', 'blik', 'transfer']),
            'external_id' => fake()->uuid(),
            'gateway_response' => null,
            'processed_at' => null,
            'metadata' => null,
        ];
    }

    /**
     * Płatność za zlecenie.
     */
    public function forBooking(): static
    {
        return $this->state(function (array $attributes) {
            $booking = Booking::factory()->create();

            return [
                'booking_id' => $booking->id,
                'user_id' => $booking->owner_id,
                'subscription_plan_id' => null,
                'amount' => $booking->total_price,
                'original_amount' => $booking->total_price,
                'commission' => round($booking->total_price * 0.15, 2),
            ];
        });
    }

    /**
     * Płatność za subskrypcję.
     */
    public function forSubscription(): static
    {
        return $this->state(function (array $attributes) {
            $plan = SubscriptionPlan::factory()->create();

            return [
                'booking_id' => null,
                'subscription_plan_id' => $plan->id,
                'amount' => $plan->price,
                'original_amount' => $plan->price,
                'commission' => 0, // Subskrypcje bez prowizji
            ];
        });
    }

    /**
     * Płatność oczekująca.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
        ]);
    }

    /**
     * Płatność w trakcie przetwarzania.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'processed_at' => null,
        ]);
    }

    /**
     * Płatność zakończona sukcesem.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'processed_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'gateway_response' => [
                'transaction_id' => fake()->uuid(),
                'status' => 'success',
                'message' => 'Payment completed successfully',
            ],
        ]);
    }

    /**
     * Płatność nieudana.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'processed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'gateway_response' => [
                'status' => 'failed',
                'error_code' => fake()->randomElement(['insufficient_funds', 'card_declined', 'timeout']),
                'message' => 'Payment failed',
            ],
        ]);
    }

    /**
     * Płatność zwrócona.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'processed_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'gateway_response' => [
                'transaction_id' => fake()->uuid(),
                'status' => 'refunded',
                'refund_date' => now()->format('Y-m-d H:i:s'),
                'message' => 'Payment refunded successfully',
            ],
        ]);
    }

    /**
     * Płatność kartą.
     */
    public function byCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
            'metadata' => [
                'card_last4' => fake()->numerify('####'),
                'card_brand' => fake()->randomElement(['Visa', 'Mastercard', 'Maestro']),
            ],
        ]);
    }

    /**
     * Płatność BLIK-iem.
     */
    public function byBlik(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'blik',
            'metadata' => [
                'blik_code' => fake()->numerify('######'),
            ],
        ]);
    }

    /**
     * Płatność przelewem bankowym.
     */
    public function byTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'transfer',
            'metadata' => [
                'bank_name' => fake()->randomElement(['PKO BP', 'mBank', 'ING', 'Santander']),
            ],
        ]);
    }

    /**
     * Płatność z proracją (dla subskrypcji).
     */
    public function withProration(): static
    {
        return $this->state(function (array $attributes) {
            $originalAmount = $attributes['original_amount'] ?? fake()->randomFloat(2, 50, 200);
            $prorationCredit = round($originalAmount * fake()->randomFloat(2, 0.1, 0.4), 2);
            $finalAmount = $originalAmount - $prorationCredit;

            return [
                'original_amount' => $originalAmount,
                'proration_credit' => $prorationCredit,
                'amount' => $finalAmount,
            ];
        });
    }

    /**
     * Płatność z niższą prowizją (promocja).
     */
    public function withReducedCommission(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? fake()->randomFloat(2, 50, 500);
            $commission = round($amount * 0.10, 2); // 10% zamiast 15%

            return [
                'commission' => $commission,
                'metadata' => [
                    'commission_rate' => '10%',
                    'promotion' => 'reduced_commission',
                ],
            ];
        });
    }

    /**
     * Płatność niedawna.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'processed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
