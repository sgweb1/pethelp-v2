<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Serwis zarządzający subskrypcjami użytkowników.
 *
 * Obsługuje tworzenie, aktualizację, anulowanie subskrypcji oraz
 * rozliczenie niewykorzystanych kwot przy zmianie planów.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class SubscriptionService
{
    /**
     * Tworzy nową płatność za subskrypcję z rozliczeniem proporcjonalnym.
     *
     * Oblicza kwotę do zapłaty uwzględniając niewykorzystaną wartość
     * z obecnej subskrypcji (jeśli istnieje).
     *
     * @param  User  $user  Użytkownik kupujący subskrypcję
     * @param  SubscriptionPlan  $newPlan  Nowy plan subskrypcji
     * @param  string  $paymentMethod  Metoda płatności
     * @return Payment Utworzona płatność
     *
     * @throws \Exception Gdy wystąpi błąd podczas tworzenia płatności
     */
    public function createSubscriptionPayment(User $user, SubscriptionPlan $newPlan, string $paymentMethod): Payment
    {
        return DB::transaction(function () use ($user, $newPlan, $paymentMethod) {
            $currentSubscription = $user->activeSubscription;
            $prorationData = $this->calculateProration($user, $newPlan);

            Log::info('Tworzenie płatności za subskrypcję', [
                'user_id' => $user->id,
                'new_plan' => $newPlan->slug,
                'current_plan' => $currentSubscription?->subscriptionPlan->slug,
                'proration_data' => $prorationData,
            ]);

            // Utwórz płatność za subskrypcję (booking_id = null dla płatności subskrypcji)
            $payment = Payment::create([
                'booking_id' => null,
                'user_id' => $user->id,
                'subscription_plan_id' => $newPlan->id,
                'status' => 'pending',
                'amount' => $prorationData['amount_to_charge'],
                'original_amount' => $newPlan->price,
                'proration_credit' => $prorationData['credit_amount'],
                'commission' => 0, // Brak prowizji od płatności subskrypcji
                'payment_method' => $paymentMethod,
                'external_id' => $this->generateSubscriptionPaymentId(),
                'metadata' => [
                    'type' => 'subscription',
                    'plan_change' => $prorationData['is_plan_change'],
                    'proration_details' => $prorationData,
                ],
            ]);

            return $payment;
        });
    }

    /**
     * Oblicza proporcjonalne rozliczenie przy zmianie planu subskrypcji.
     *
     * Uwzględnia niewykorzystaną wartość z obecnej subskrypcji
     * i oblicza faktyczną kwotę do zapłaty.
     *
     * @param  User  $user  Użytkownik
     * @param  SubscriptionPlan  $newPlan  Nowy plan
     * @return array Dane dotyczące rozliczenia
     */
    public function calculateProration(User $user, SubscriptionPlan $newPlan): array
    {
        $currentSubscription = $user->activeSubscription;

        if (! $currentSubscription) {
            // Brak aktywnej subskrypcji - pełna cena nowego planu
            return [
                'is_plan_change' => false,
                'current_plan' => null,
                'new_plan' => $newPlan->slug,
                'credit_amount' => 0,
                'amount_to_charge' => $newPlan->price,
                'period_extension' => false,
                'new_end_date' => $this->calculateNewEndDate($newPlan),
            ];
        }

        // Sprawdź czy to ten sam plan (przedłużenie)
        if ($currentSubscription->subscription_plan_id === $newPlan->id) {
            return $this->calculatePeriodExtension($currentSubscription, $newPlan);
        }

        // Zmiana planu - oblicz proporcjonalne rozliczenie
        return $this->calculatePlanChange($currentSubscription, $newPlan);
    }

    /**
     * Oblicza przedłużenie okresu dla tego samego planu.
     *
     * @param  Subscription  $currentSubscription  Obecna subskrypcja
     * @param  SubscriptionPlan  $plan  Plan subskrypcji
     * @return array Dane rozliczenia
     */
    private function calculatePeriodExtension(Subscription $currentSubscription, SubscriptionPlan $plan): array
    {
        $period = $plan->billing_period === 'yearly' ? 12 : 1;
        $newEndDate = $currentSubscription->ends_at->addMonths($period);

        return [
            'is_plan_change' => false,
            'current_plan' => $plan->slug,
            'new_plan' => $plan->slug,
            'credit_amount' => 0,
            'amount_to_charge' => $plan->price,
            'period_extension' => true,
            'current_end_date' => $currentSubscription->ends_at,
            'new_end_date' => $newEndDate,
            'extended_days' => $currentSubscription->ends_at->diffInDays($newEndDate),
        ];
    }

    /**
     * Oblicza rozliczenie proporcjonalne przy zmianie planu.
     *
     * @param  Subscription  $currentSubscription  Obecna subskrypcja
     * @param  SubscriptionPlan  $newPlan  Nowy plan
     * @return array Dane rozliczenia
     */
    private function calculatePlanChange(Subscription $currentSubscription, SubscriptionPlan $newPlan): array
    {
        $now = now();
        $daysRemaining = $now->diffInDays($currentSubscription->ends_at);
        $totalDaysInPeriod = $currentSubscription->starts_at->diffInDays($currentSubscription->ends_at);

        // Oblicz niewykorzystaną wartość z obecnej subskrypcji
        $unusedRatio = $daysRemaining / $totalDaysInPeriod;
        $creditAmount = $currentSubscription->price * $unusedRatio;

        // Oblicz kwotę do zapłaty (nowy plan minus kredyt)
        $amountToCharge = max(0, $newPlan->price - $creditAmount);

        Log::info('Obliczanie proration dla zmiany planu', [
            'current_plan' => $currentSubscription->subscriptionPlan->slug,
            'new_plan' => $newPlan->slug,
            'days_remaining' => $daysRemaining,
            'total_days' => $totalDaysInPeriod,
            'unused_ratio' => $unusedRatio,
            'credit_amount' => $creditAmount,
            'amount_to_charge' => $amountToCharge,
        ]);

        return [
            'is_plan_change' => true,
            'current_plan' => $currentSubscription->subscriptionPlan->slug,
            'new_plan' => $newPlan->slug,
            'days_remaining' => $daysRemaining,
            'total_days_in_period' => $totalDaysInPeriod,
            'unused_ratio' => round($unusedRatio * 100, 2),
            'credit_amount' => round($creditAmount, 2),
            'amount_to_charge' => round($amountToCharge, 2),
            'period_extension' => false,
            'new_end_date' => $this->calculateNewEndDate($newPlan, $now),
        ];
    }

    /**
     * Przetwarza płatność subskrypcji i aktywuje nowy plan.
     *
     * Po udanej płatności natychmiast przełącza użytkownika na nowy plan
     * lub przedłuża istniejący.
     *
     * @param  Payment  $payment  Płatność do przetworzenia
     * @return bool Czy operacja się powiodła
     */
    public function processSubscriptionPayment(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            // Szukaj user_id najpierw w payment->user_id, potem w metadata, potem w gateway_response
            $userId = $payment->user_id
                ?? $payment->metadata['user_id'] ?? null
                ?? $payment->gateway_response['user_id'] ?? null;

            $user = User::find($userId);
            $newPlan = SubscriptionPlan::find($payment->subscription_plan_id);

            if (! $user || ! $newPlan) {
                Log::error('Nie można znaleźć użytkownika lub planu dla płatności subskrypcji', [
                    'payment_id' => $payment->id,
                ]);

                return false;
            }

            // Oznacz płatność jako zakończoną
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_response' => [
                    'transaction_id' => 'SUB_'.now()->format('YmdHis').'_'.str()->random(8),
                    'status' => 'success',
                    'processed_at' => now()->toISOString(),
                ],
            ]);

            $prorationData = $payment->metadata['proration_details'] ?? [];

            if ($prorationData['period_extension'] ?? false) {
                // Przedłużenie istniejącego planu
                return $this->extendCurrentSubscription($user, $newPlan, $prorationData);
            } else {
                // Nowy plan lub zmiana planu
                return $this->activateNewSubscription($user, $newPlan, $prorationData);
            }
        });
    }

    /**
     * Przedłuża istniejącą subskrypcję.
     *
     * @param  User  $user  Użytkownik
     * @param  SubscriptionPlan  $plan  Plan subskrypcji
     * @param  array  $prorationData  Dane rozliczenia
     * @return bool Czy operacja się powiodła
     */
    private function extendCurrentSubscription(User $user, SubscriptionPlan $plan, array $prorationData): bool
    {
        $currentSubscription = $user->activeSubscription;

        if (! $currentSubscription) {
            return false;
        }

        $period = $plan->billing_period === 'yearly' ? 12 : 1;
        $newEndDate = $currentSubscription->ends_at->copy()->addMonths($period);
        $newNextBilling = $newEndDate->copy();

        $updated = $currentSubscription->update([
            'ends_at' => $newEndDate,
            'next_billing_at' => $newNextBilling,
            'last_payment_at' => now(),
            'status' => Subscription::STATUS_ACTIVE,
            'metadata' => array_merge($currentSubscription->metadata ?? [], [
                'last_extension' => now()->toISOString(),
                'extension_period' => $period.'_months',
            ]),
        ]);

        Log::info('Przedłużono subskrypcję', [
            'user_id' => $user->id,
            'subscription_id' => $currentSubscription->id,
            'plan' => $plan->slug,
            'new_end_date' => $newEndDate->toISOString(),
            'extended_months' => $period,
        ]);

        return $updated;
    }

    /**
     * Aktywuje nową subskrypcję lub zmienia istniejący plan.
     *
     * @param  User  $user  Użytkownik
     * @param  SubscriptionPlan  $newPlan  Nowy plan
     * @param  array  $prorationData  Dane rozliczenia
     * @return bool Czy operacja się powiodła
     */
    private function activateNewSubscription(User $user, SubscriptionPlan $newPlan, array $prorationData): bool
    {
        $currentSubscription = $user->activeSubscription;

        // Anuluj obecną subskrypcję jeśli istnieje
        if ($currentSubscription) {
            $currentSubscription->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'metadata' => array_merge($currentSubscription->metadata ?? [], [
                    'cancellation_reason' => 'Zmiana planu na: '.$newPlan->name,
                    'replaced_by_plan' => $newPlan->slug,
                ]),
            ]);
        }

        // Utwórz nową subskrypcję
        $startsAt = now();
        $endsAt = $prorationData['new_end_date'] ?? $this->calculateNewEndDate($newPlan, $startsAt);

        // Ensure $endsAt is a Carbon instance
        if (is_string($endsAt)) {
            $endsAt = \Carbon\Carbon::parse($endsAt);
        }

        $period = $newPlan->billing_period === 'yearly' ? 12 : 1;
        $nextBilling = $endsAt->copy();

        $newSubscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $newPlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'price' => $newPlan->price,
            'amount' => $newPlan->price,
            'billing_period' => $newPlan->billing_period,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'current_period_start' => $startsAt,
            'current_period_end' => $endsAt,
            'next_billing_at' => $nextBilling,
            'last_payment_at' => now(),
            'metadata' => [
                'activation_date' => $startsAt->toISOString(),
                'proration_applied' => $prorationData['is_plan_change'] ?? false,
                'credit_amount' => $prorationData['credit_amount'] ?? 0,
                'replaced_subscription_id' => $currentSubscription?->id,
            ],
        ]);

        Log::info('Aktywowano nową subskrypcję', [
            'user_id' => $user->id,
            'new_subscription_id' => $newSubscription->id,
            'plan' => $newPlan->slug,
            'starts_at' => $startsAt->toISOString(),
            'ends_at' => $endsAt->toISOString(),
            'proration_data' => $prorationData,
        ]);

        return $newSubscription->exists;
    }

    /**
     * Oblicza datę zakończenia nowej subskrypcji.
     *
     * @param  SubscriptionPlan  $plan  Plan subskrypcji
     * @param  Carbon|null  $startDate  Data rozpoczęcia (domyślnie: teraz)
     * @return Carbon Data zakończenia
     */
    private function calculateNewEndDate(SubscriptionPlan $plan, ?Carbon $startDate = null): Carbon
    {
        $startDate = $startDate ?? now();
        $period = $plan->billing_period === 'yearly' ? 12 : 1;

        return $startDate->copy()->addMonths($period);
    }

    /**
     * Generuje unikalny identyfikator płatności subskrypcji.
     *
     * @return string Identyfikator płatności
     */
    private function generateSubscriptionPaymentId(): string
    {
        return 'SUB_'.now()->format('YmdHis').'_'.str()->random(8);
    }

    /**
     * Pobiera szczegóły rozliczenia dla użytkownika i planu.
     *
     * Metoda pomocnicza do wyświetlania użytkownikowi informacji
     * o kosztach przed dokonaniem płatności.
     *
     * @param  User  $user  Użytkownik
     * @param  SubscriptionPlan  $newPlan  Nowy plan
     * @return array Szczegóły rozliczenia do wyświetlenia
     */
    public function getProrationDetails(User $user, SubscriptionPlan $newPlan): array
    {
        $prorationData = $this->calculateProration($user, $newPlan);
        $currentSubscription = $user->activeSubscription;

        return [
            'current_subscription' => $currentSubscription ? [
                'plan_name' => $currentSubscription->subscriptionPlan->name,
                'price' => $currentSubscription->price,
                'ends_at' => $currentSubscription->ends_at->format('d.m.Y'),
                'days_remaining' => $currentSubscription->days_remaining,
            ] : null,
            'new_plan' => [
                'name' => $newPlan->name,
                'price' => $newPlan->price,
                'billing_period' => $newPlan->billing_period,
            ],
            'proration' => [
                'is_plan_change' => $prorationData['is_plan_change'],
                'is_extension' => $prorationData['period_extension'] ?? false,
                'credit_amount' => $prorationData['credit_amount'],
                'amount_to_charge' => $prorationData['amount_to_charge'],
                'savings' => $prorationData['credit_amount'],
                'new_end_date' => Carbon::parse($prorationData['new_end_date'])->format('d.m.Y'),
            ],
            'summary' => [
                'description' => $this->getProrationDescription($prorationData),
                'total_charge' => $prorationData['amount_to_charge'],
                'immediate_activation' => true,
            ],
        ];
    }

    /**
     * Generuje opis rozliczenia proporcjonalnego.
     *
     * @param  array  $prorationData  Dane rozliczenia
     * @return string Opis dla użytkownika
     */
    private function getProrationDescription(array $prorationData): string
    {
        if ($prorationData['period_extension'] ?? false) {
            $period = $prorationData['extended_days'] ?? 30;

            return "Przedłużenie obecnego planu o {$period} dni.";
        }

        if ($prorationData['is_plan_change']) {
            $credit = $prorationData['credit_amount'];

            return $credit > 0
                ? "Zmiana planu z kredytem {$credit} PLN za niewykorzystany okres."
                : 'Zmiana planu na wyższy - natychmiastowa aktywacja.';
        }

        return 'Aktywacja nowego planu - natychmiastowy dostęp do wszystkich funkcji.';
    }

    /**
     * Anuluje aktywną subskrypcję użytkownika.
     *
     * @param  User  $user  Użytkownik
     * @param  string  $reason  Powód anulowania
     * @return bool Czy operacja się powiodła
     */
    public function cancelSubscription(User $user, string $reason = ''): bool
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return false;
        }

        return $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'metadata' => array_merge($subscription->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_by' => 'user',
            ]),
        ]);
    }
}
