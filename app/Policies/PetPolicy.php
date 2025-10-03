<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

/**
 * Policy dla zarządzania uprawnieniami do zwierząt.
 *
 * Definiuje kto może przeglądać, tworzyć, edytować i usuwać profile zwierząt.
 * Admini mają pełne uprawnienia, a właściciele mogą zarządzać tylko swoimi zwierzętami.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetPolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę zwierząt.
     *
     * Admin może widzieć wszystkie zwierzęta.
     * Właściciele mogą widzieć tylko własne zwierzęta (filtrowanie w query).
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik ma dostęp
     */
    public function viewAny(User $user): bool
    {
        // Admin + Owner (własne zwierzęta - filtrowane w Resource)
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły zwierzęcia.
     *
     * Admin może widzieć wszystkie zwierzęta.
     * Właściciel może widzieć tylko swoje zwierzęta.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Pet  $pet  Zwierzę do wyświetlenia
     * @return bool True jeśli użytkownik ma dostęp
     */
    public function view(User $user, Pet $pet): bool
    {
        // Admin + Owner zwierzęcia
        return $user->isAdmin() || $pet->owner_id === $user->id;
    }

    /**
     * Określa czy użytkownik może tworzyć nowe profile zwierząt.
     *
     * Admin i wszyscy użytkownicy mogą dodawać zwierzęta.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik może tworzyć
     */
    public function create(User $user): bool
    {
        // Admin + Users (wszyscy mogą dodawać)
        return true;
    }

    /**
     * Określa czy użytkownik może edytować profil zwierzęcia.
     *
     * Admin może edytować wszystkie zwierzęta.
     * Właściciel może edytować tylko swoje zwierzęta.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Pet  $pet  Zwierzę do edycji
     * @return bool True jeśli użytkownik może edytować
     */
    public function update(User $user, Pet $pet): bool
    {
        // Admin + Owner zwierzęcia
        return $user->isAdmin() || $pet->owner_id === $user->id;
    }

    /**
     * Określa czy użytkownik może usunąć profil zwierzęcia.
     *
     * Admin może usuwać wszystkie zwierzęta.
     * Właściciel może usuwać tylko swoje zwierzęta.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Pet  $pet  Zwierzę do usunięcia
     * @return bool True jeśli użytkownik może usunąć
     */
    public function delete(User $user, Pet $pet): bool
    {
        // Admin + Owner zwierzęcia
        return $user->isAdmin() || $pet->owner_id === $user->id;
    }

    /**
     * Określa czy użytkownik może przywrócić usunięty profil zwierzęcia.
     *
     * Admin może przywracać wszystkie zwierzęta.
     * Właściciel może przywracać tylko swoje zwierzęta.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Pet  $pet  Zwierzę do przywrócenia
     * @return bool True jeśli użytkownik może przywrócić
     */
    public function restore(User $user, Pet $pet): bool
    {
        // Admin + Owner
        return $user->isAdmin() || $pet->owner_id === $user->id;
    }

    /**
     * Określa czy użytkownik może trwale usunąć profil zwierzęcia.
     *
     * Tylko admin może wykonać hard delete.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Pet  $pet  Zwierzę do trwałego usunięcia
     * @return bool True jeśli użytkownik może trwale usunąć
     */
    public function forceDelete(User $user, Pet $pet): bool
    {
        // Admin tylko
        return $user->isAdmin();
    }
}
