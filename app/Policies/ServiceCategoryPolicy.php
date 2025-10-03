<?php

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do kategorii usług.
 *
 * Kategorie usług mogą być zarządzane wyłącznie przez administratorów.
 * Są to kluczowe elementy struktury aplikacji, które wpływają na
 * organizację wszystkich usług w systemie.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServiceCategoryPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę kategorii.
     *
     * Tylko administratorzy mają dostęp do zarządzania kategoriami usług.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły kategorii.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  ServiceCategory  $serviceCategory  Kategoria do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć nowe kategorie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może edytować kategorię.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  ServiceCategory  $serviceCategory  Kategoria do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać kategorię.
     *
     * Kategorie można usuwać tylko jeśli nie mają przypisanych usług.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  ServiceCategory  $serviceCategory  Kategoria do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte kategorie.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  ServiceCategory  $serviceCategory  Kategoria do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć kategorię.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  ServiceCategory  $serviceCategory  Kategoria do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->isAdmin();
    }
}
