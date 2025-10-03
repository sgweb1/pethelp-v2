<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do recenzji.
 *
 * Recenzje mogą być moderowane wyłącznie przez administratorów.
 * Tworzenie recenzji odbywa się automatycznie po zakończeniu rezerwacji,
 * dlatego panel admina służy głównie do moderacji i zarządzania.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ReviewPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę recenzji.
     *
     * Tylko administratorzy mają dostęp do panelu moderacji recenzji.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły recenzji.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Review  $review  Recenzja do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć recenzje przez panel admina.
     *
     * Tworzenie recenzji odbywa się automatycznie w aplikacji,
     * więc panel admina nie pozwala na ręczne tworzenie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool Zawsze false - recenzje tworzone automatycznie
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może edytować recenzję (moderacja).
     *
     * Administratorzy mogą moderować recenzje - zmieniać status,
     * dodawać odpowiedzi, ukrywać/pokazywać.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Review  $review  Recenzja do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać recenzje.
     *
     * Tylko administratorzy mogą usuwać recenzje (np. spam, obraźliwe treści).
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Review  $review  Recenzja do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte recenzje.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Review  $review  Recenzja do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć recenzję.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Review  $review  Recenzja do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }
}
