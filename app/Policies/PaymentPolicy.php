<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

/**
 * Policy autoryzacji dla modelu Payment.
 *
 * Określa uprawnienia użytkowników do przeglądania i zarządzania płatnościami.
 * Resource płatności jest READ-ONLY dla większości operacji.
 * Tylko administrator ma dostęp do pełnego zarządzania płatnościami.
 */
class PaymentPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę płatności.
     *
     * Uprawnienia:
     * - Tylko administrator
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @return bool True jeśli użytkownik ma uprawnienia
     */
    public function viewAny(User $user): bool
    {
        // Tylko admin może przeglądać wszystkie płatności
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może wyświetlić szczegóły płatności.
     *
     * Uprawnienia:
     * - Admin: dowolna płatność
     * - User: tylko własne płatności
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do wyświetlenia
     * @return bool True jeśli użytkownik ma uprawnienia
     */
    public function view(User $user, Payment $payment): bool
    {
        // Admin ma pełny dostęp
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Użytkownik może wyświetlić tylko własne płatności
        return $payment->user_id === $user->id;
    }

    /**
     * Określa czy użytkownik może tworzyć nowe płatności.
     *
     * Uprawnienia:
     * - System tylko (automatyczne tworzenie)
     * - Ręczne tworzenie zabronione
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @return bool Zawsze false - płatności tworzy system
     */
    public function create(User $user): bool
    {
        // Płatności są tworzone automatycznie przez system
        // Ręczne tworzenie jest zabronione
        return false;
    }

    /**
     * Określa czy użytkownik może edytować płatność.
     *
     * Uprawnienia:
     * - Tylko administrator (ograniczone)
     * - Tylko status i notatki administracyjne
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do edycji
     * @return bool True jeśli użytkownik może edytować
     */
    public function update(User $user, Payment $payment): bool
    {
        // Tylko admin może edytować płatności (z ograniczeniami)
        // Edycja ograniczona do statusu i notatek
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może usunąć płatność.
     *
     * Uprawnienia:
     * - Nigdy (płatności nie można usunąć ze względów prawnych)
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do usunięcia
     * @return bool Zawsze false - płatności nie można usunąć
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Płatności nie można usunąć ze względów audytowych i prawnych
        return false;
    }

    /**
     * Określa czy użytkownik może przywrócić usuniętą płatność.
     *
     * Uprawnienia:
     * - N/A (płatności nie są usuwane)
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do przywrócenia
     * @return bool Zawsze false
     */
    public function restore(User $user, Payment $payment): bool
    {
        // N/A - płatności nie są usuwane
        return false;
    }

    /**
     * Określa czy użytkownik może permanentnie usunąć płatność.
     *
     * Uprawnienia:
     * - Nigdy (płatności nie można permanentnie usunąć)
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do permanentnego usunięcia
     * @return bool Zawsze false
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        // Płatności nigdy nie są permanentnie usuwane
        return false;
    }

    /**
     * Określa czy użytkownik może wykonać zwrot płatności.
     *
     * Custom metoda dla akcji zwrotu płatności.
     * Uprawnienia:
     * - Tylko administrator
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do zwrotu
     * @return bool True jeśli użytkownik może wykonać zwrot
     */
    public function refund(User $user, Payment $payment): bool
    {
        // Tylko admin może wykonywać zwroty
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może ręcznie oznaczyć płatność jako opłaconą.
     *
     * Custom metoda dla akcji ręcznego potwierdzenia płatności.
     * Uprawnienia:
     * - Tylko administrator (admin override)
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Payment  $payment  Płatność do potwierdzenia
     * @return bool True jeśli użytkownik może oznaczyć jako opłaconą
     */
    public function markAsPaid(User $user, Payment $payment): bool
    {
        // Tylko admin może ręcznie potwierdzać płatności
        return $user->profile?->role === 'admin';
    }
}
