<?php

namespace App\Policies;

use App\Models\PetType;
use App\Models\User;

/**
 * Policy zarządzająca uprawnieniami do typów zwierząt.
 *
 * Typy zwierząt mogą być zarządzane wyłącznie przez administratorów.
 * Są to kluczowe elementy struktury aplikacji, które wpływają na
 * klasyfikację zwierząt i organizację usług w systemie.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PetTypePolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę typów zwierząt.
     *
     * Tylko administratorzy mają dostęp do zarządzania typami zwierząt.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły typu zwierzęcia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  PetType  $petType  Typ zwierzęcia do przeglądania
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function view(User $user, PetType $petType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może tworzyć nowe typy zwierząt.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może edytować typ zwierzęcia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  PetType  $petType  Typ zwierzęcia do edycji
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function update(User $user, PetType $petType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może usuwać typ zwierzęcia.
     *
     * Typy można usuwać tylko jeśli nie mają przypisanych zwierząt.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  PetType  $petType  Typ zwierzęcia do usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function delete(User $user, PetType $petType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może przywracać usunięte typy zwierząt.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  PetType  $petType  Typ zwierzęcia do przywrócenia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function restore(User $user, PetType $petType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć typ zwierzęcia.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  PetType  $petType  Typ zwierzęcia do trwałego usunięcia
     * @return bool True jeśli użytkownik jest administratorem
     */
    public function forceDelete(User $user, PetType $petType): bool
    {
        return $user->isAdmin();
    }
}
