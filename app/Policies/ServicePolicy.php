<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

/**
 * Policy dla zarządzania uprawnieniami do usług opiekunów.
 *
 * Definiuje kto może przeglądać, tworzyć, edytować i usuwać usługi.
 * Usługi są publiczne do przeglądania, ale tylko opiekunowie mogą je tworzyć
 * i zarządzać swoimi własnymi usługami.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class ServicePolicy
{
    /**
     * Określa czy użytkownik może przeglądać listę usług.
     *
     * Wszystkie usługi są publiczne - każdy może je przeglądać.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True - zawsze dostępne
     */
    public function viewAny(User $user): bool
    {
        // Wszyscy (publiczne)
        return true;
    }

    /**
     * Określa czy użytkownik może przeglądać szczegóły usługi.
     *
     * Szczegóły usług są publiczne.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do wyświetlenia
     * @return bool True - zawsze dostępne
     */
    public function view(User $user, Service $service): bool
    {
        // Wszyscy (publiczne)
        return true;
    }

    /**
     * Określa czy użytkownik może tworzyć nowe usługi.
     *
     * Tylko opiekunowie (sitter lub both) mogą tworzyć usługi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @return bool True jeśli użytkownik jest opiekunem
     */
    public function create(User $user): bool
    {
        // Admin + Sitters (tylko role: sitter lub both)
        return $user->isAdmin() || $user->isSitter();
    }

    /**
     * Określa czy użytkownik może edytować usługę.
     *
     * Admin może edytować wszystkie usługi.
     * Właściciel usługi (sitter) może edytować tylko swoje usługi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do edycji
     * @return bool True jeśli użytkownik może edytować
     */
    public function update(User $user, Service $service): bool
    {
        // Admin + Właściciel usługi (sitter)
        return $user->isAdmin() || $service->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może usunąć usługę.
     *
     * Admin może usuwać wszystkie usługi.
     * Właściciel usługi może usunąć tylko swoją usługę.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do usunięcia
     * @return bool True jeśli użytkownik może usunąć
     */
    public function delete(User $user, Service $service): bool
    {
        // Admin + Właściciel usługi
        return $user->isAdmin() || $service->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może przywrócić usuniętą usługę.
     *
     * Tylko admin może przywracać usługi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do przywrócenia
     * @return bool True jeśli użytkownik może przywrócić
     */
    public function restore(User $user, Service $service): bool
    {
        // Admin
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może trwale usunąć usługę.
     *
     * Tylko admin może wykonać hard delete usługi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do trwałego usunięcia
     * @return bool True jeśli użytkownik może trwale usunąć
     */
    public function forceDelete(User $user, Service $service): bool
    {
        // Admin
        return $user->isAdmin();
    }

    /**
     * Określa czy użytkownik może aktywować usługę.
     *
     * Custom metoda dla akcji aktywacji usługi.
     * Admin może aktywować wszystkie usługi.
     * Właściciel może aktywować tylko swoją usługę.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do aktywacji
     * @return bool True jeśli użytkownik może aktywować
     */
    public function activate(User $user, Service $service): bool
    {
        // Admin + Właściciel usługi (custom)
        return $user->isAdmin() || $service->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może dezaktywować usługę.
     *
     * Custom metoda dla akcji dezaktywacji usługi.
     * Admin może dezaktywować wszystkie usługi.
     * Właściciel może dezaktywować tylko swoją usługę.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do dezaktywacji
     * @return bool True jeśli użytkownik może dezaktywować
     */
    public function deactivate(User $user, Service $service): bool
    {
        // Admin + Właściciel usługi (custom)
        return $user->isAdmin() || $service->sitter_id === $user->id;
    }

    /**
     * Określa czy użytkownik może wyróżnić usługę (feature).
     *
     * Custom metoda dla akcji wyróżnienia usługi.
     * Tylko admin może wyróżniać usługi.
     *
     * @param  User  $user  Użytkownik wykonujący akcję
     * @param  Service  $service  Usługa do wyróżnienia
     * @return bool True jeśli użytkownik może wyróżnić
     */
    public function feature(User $user, Service $service): bool
    {
        // Admin tylko (custom)
        return $user->isAdmin();
    }
}
