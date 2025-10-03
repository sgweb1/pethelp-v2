<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do zgłoszeń i sporów.
 *
 * Zgłoszenia mogą być przeglądane i zarządzane wyłącznie przez administratorów.
 * Użytkownicy mogą tworzyć zgłoszenia w aplikacji, ale nie przez panel admina.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class DisputePolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę zgłoszeń.
     *
     * Tylko administratorzy mają dostęp do panelu zgłoszeń.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły zgłoszenia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Dispute  $dispute  Zgłoszenie do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, Dispute $dispute): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć zgłoszenia przez panel admina.
     *
     * Tworzenie zgłoszeń odbywa się przez użytkowników w aplikacji,
     * więc panel admina nie pozwala na ręczne tworzenie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool Zawsze false - zgłoszenia tworzone przez użytkowników
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może edytować zgłoszenie (zarządzanie).
     *
     * Administratorzy mogą zarządzać zgłoszeniami - przypisywać do siebie,
     * dodawać notatki, rozwiązywać lub odrzucać.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Dispute  $dispute  Zgłoszenie do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, Dispute $dispute): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać zgłoszenia.
     *
     * Tylko administratorzy mogą usuwać zgłoszenia (np. spam, duplikaty).
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Dispute  $dispute  Zgłoszenie do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, Dispute $dispute): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte zgłoszenia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Dispute  $dispute  Zgłoszenie do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, Dispute $dispute): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć zgłoszenie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Dispute  $dispute  Zgłoszenie do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, Dispute $dispute): bool
    {
        return $user->isAdmin();
    }
}
