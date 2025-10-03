<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do wiadomości.
 *
 * Wiadomości mogą być przeglądane i moderowane wyłącznie przez administratorów.
 * Tworzenie wiadomości odbywa się przez system czatu w aplikacji,
 * dlatego panel admina służy głównie do moderacji i przeglądania.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class MessagePolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę wiadomości.
     *
     * Tylko administratorzy mają dostęp do panelu moderacji wiadomości.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły wiadomości.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Message  $message  Wiadomość do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć wiadomości przez panel admina.
     *
     * Tworzenie wiadomości odbywa się automatycznie przez system czatu,
     * więc panel admina nie pozwala na ręczne tworzenie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool Zawsze false - wiadomości tworzone przez czat
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może edytować wiadomość (moderacja).
     *
     * Administratorzy mogą moderować wiadomości - ukrywać je,
     * dodawać powody ukrycia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Message  $message  Wiadomość do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać wiadomości.
     *
     * Tylko administratorzy mogą usuwać wiadomości (np. spam, obraźliwe treści).
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Message  $message  Wiadomość do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte wiadomości.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Message  $message  Wiadomość do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć wiadomość.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Message  $message  Wiadomość do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, Message $message): bool
    {
        return $user->isAdmin();
    }
}
