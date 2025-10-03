<?php

namespace App\Policies;

use App\Models\AdminLog;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do logów aktywności administratorów.
 *
 * Logi mogą być przeglądane wyłącznie przez administratorów.
 * Nie można tworzyć, edytować ani usuwać logów - są tworzone automatycznie
 * przez system i są niemodyfikowalne dla celów audytu.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminLogPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę logów.
     *
     * Tylko administratorzy mają dostęp do logów aktywności.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły logu.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  AdminLog  $adminLog  Log do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, AdminLog $adminLog): bool
    {
        return $user->profile?->role === 'admin';
    }

    /**
     * Określa czy użytkownik może tworzyć logi przez panel admina.
     *
     * Logi są tworzone automatycznie przez system, nie można ich
     * tworzyć ręcznie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool Zawsze false - logi tworzone automatycznie
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może edytować log.
     *
     * Logi są niemodyfikowalne dla zachowania integralności audytu.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  AdminLog  $adminLog  Log do edycji
     * @return bool Zawsze false - logi są niemodyfikowalne
     */
    public function update(User $user, AdminLog $adminLog): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może usuwać logi.
     *
     * Logi są nieusuwalne dla zachowania pełnego audytu.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  AdminLog  $adminLog  Log do usunięcia
     * @return bool Zawsze false - logi są nieusuwalne
     */
    public function delete(User $user, AdminLog $adminLog): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może przywracać usunięte logi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  AdminLog  $adminLog  Log do przywrócenia
     * @return bool Zawsze false - logi nie podlegają usuwaniu/przywracaniu
     */
    public function restore(User $user, AdminLog $adminLog): bool
    {
        return false;
    }

    /**
     * Określa czy użytkownik może trwale usunąć log.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  AdminLog  $adminLog  Log do trwałego usunięcia
     * @return bool Zawsze false - logi są nieusuwalne
     */
    public function forceDelete(User $user, AdminLog $adminLog): bool
    {
        return false;
    }
}
