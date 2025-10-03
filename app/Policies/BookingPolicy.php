<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

/**
 * Policy autoryzacji dla modelu Booking.
 *
 * Określa uprawnienia użytkowników do zarządzania rezerwacjami.
 * Administrator ma pełne uprawnienia, właściciele i opiekunowie
 * mają ograniczony dostęp do własnych rezerwacji.
 */
class BookingPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę rezerwacji.
     *
     * Uprawnienia:
     * - Admin: wszystkie rezerwacje
     * - Owner: własne rezerwacje jako właściciel
     * - Sitter: własne rezerwacje jako opiekun
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @return bool True jeśli użytkownik ma uprawnienia
     */
    public function viewAny(User $user): bool
    {
        // Admin może przeglądać wszystkie rezerwacje
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Właściciele i opiekunowie mogą przeglądać własne rezerwacje
        // Filtrowanie po stronie query w resource
        return in_array($user->profile?->role, ['owner', 'sitter', 'both']);
    }

    /**
     * Określa czy użytkownik może wyświetlić szczegóły rezerwacji.
     *
     * Uprawnienia:
     * - Admin: dowolna rezerwacja
     * - Owner/Sitter: tylko jeśli jest właścicielem lub opiekunem w rezerwacji
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do wyświetlenia
     * @return bool True jeśli użytkownik ma uprawnienia
     */
    public function view(User $user, Booking $booking): bool
    {
        // Admin ma pełny dostęp
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Właściciel lub opiekun rezerwacji może ją wyświetlić
        return $booking->owner_id === $user->id || $booking->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może tworzyć nowe rezerwacje.
     *
     * Uprawnienia:
     * - Wszyscy zalogowani użytkownicy (owner może tworzyć rezerwacje)
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @return bool True jeśli użytkownik może tworzyć rezerwacje
     */
    public function create(User $user): bool
    {
        // Każdy zalogowany użytkownik może tworzyć rezerwacje
        return true;
    }

    /**
     * Określa czy użytkownik może edytować rezerwację.
     *
     * Uprawnienia:
     * - Admin: dowolna rezerwacja
     * - Owner: własna rezerwacja przed datą rozpoczęcia
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do edycji
     * @return bool True jeśli użytkownik może edytować
     */
    public function update(User $user, Booking $booking): bool
    {
        // Admin może edytować dowolną rezerwację
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Właściciel może edytować własną rezerwację przed datą rozpoczęcia
        if ($booking->owner_id === $user->id && $booking->start_date->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Określa czy użytkownik może usunąć rezerwację.
     *
     * Uprawnienia:
     * - Tylko administrator
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do usunięcia
     * @return bool True jeśli użytkownik może usunąć
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Tylko admin może usuwać rezerwacje
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może przywrócić usuniętą rezerwację.
     *
     * Uprawnienia:
     * - Tylko administrator
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do przywrócenia
     * @return bool True jeśli użytkownik może przywrócić
     */
    public function restore(User $user, Booking $booking): bool
    {
        // Tylko admin może przywracać rezerwacje
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może permanentnie usunąć rezerwację.
     *
     * Uprawnienia:
     * - Tylko administrator
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do permanentnego usunięcia
     * @return bool True jeśli użytkownik może permanentnie usunąć
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        // Tylko admin może permanentnie usuwać rezerwacje
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może anulować rezerwację.
     *
     * Custom metoda dla akcji anulowania rezerwacji.
     * Uprawnienia:
     * - Admin: dowolna rezerwacja
     * - Owner/Sitter: własna rezerwacja
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do anulowania
     * @return bool True jeśli użytkownik może anulować
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Admin może anulować dowolną rezerwację
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Owner lub sitter mogą anulować własne rezerwacje
        return $booking->owner_id === $user->id || $booking->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może zakończyć rezerwację.
     *
     * Custom metoda dla akcji zakończenia rezerwacji.
     * Uprawnienia:
     * - Admin: dowolna rezerwacja
     * - Sitter: rezerwacja gdzie jest opiekunem
     *
     * @param  User  $user  Aktualnie zalogowany użytkownik
     * @param  Booking  $booking  Rezerwacja do zakończenia
     * @return bool True jeśli użytkownik może zakończyć
     */
    public function complete(User $user, Booking $booking): bool
    {
        // Admin może zakończyć dowolną rezerwację
        if ($user->profile?->role === 'admin') {
            return true;
        }

        // Opiekun może zakończyć własną rezerwację
        return $booking->sitter_id === $user->id;
    }
}
