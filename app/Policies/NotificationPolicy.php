<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Policy zarządzająca uprawnieniami do powiadomień systemowych.
 *
 * Powiadomienia mogą być przeglądane i zarządzane wyłącznie przez administratorów.
 * Tworzenie powiadomień odbywa się automatycznie przez system lub masowo przez akcje,
 * dlatego panel admina służy głównie do przeglądania i moderacji.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class NotificationPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę powiadomień.
     *
     * Tylko administratorzy mają dostęp do panelu powiadomień.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły powiadomienia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  DatabaseNotification  $databaseNotification  Powiadomienie do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, DatabaseNotification $databaseNotification): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć powiadomienia przez panel admina.
     *
     * Tworzenie powiadomień odbywa się automatycznie przez system
     * lub masowo przez specjalne akcje, więc panel admina nie pozwala
     * na ręczne tworzenie pojedynczych powiadomień.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool Zawsze false - powiadomienia tworzone automatycznie lub masowo
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może edytować powiadomienie.
     *
     * Administratorzy mogą oznaczać powiadomienia jako przeczytane/nieprzeczytane.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  DatabaseNotification  $databaseNotification  Powiadomienie do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, DatabaseNotification $databaseNotification): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać powiadomienia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  DatabaseNotification  $databaseNotification  Powiadomienie do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, DatabaseNotification $databaseNotification): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte powiadomienia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  DatabaseNotification  $databaseNotification  Powiadomienie do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, DatabaseNotification $databaseNotification): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć powiadomienie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  DatabaseNotification  $databaseNotification  Powiadomienie do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, DatabaseNotification $databaseNotification): bool
    {
        return $user->isAdmin();
    }
}
