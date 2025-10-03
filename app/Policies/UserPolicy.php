<?php

namespace App\Policies;

use App\Models\User;

/**
 * Policy dla autoryzacji operacji na użytkownikach.
 *
 * Definiuje uprawnienia dla różnych ról użytkowników w systemie PetHelp.
 * Administratorzy mają pełne uprawnienia do zarządzania wszystkimi użytkownikami,
 * podczas gdy zwykli użytkownicy mogą tylko przeglądać i edytować własne profile
 * (z ograniczeniami - nie mogą zmieniać krytycznych pól takich jak rola czy status premium).
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 * @see \App\Models\User
 * @see \App\Filament\Resources\Users\UserResource
 */
class UserPolicy
{
    /**
     * Sprawdza czy użytkownik może przeglądać listę użytkowników.
     *
     * Tylko administratorzy mają dostęp do pełnej listy użytkowników
     * w panelu administracyjnym.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący uzyskać dostęp
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * if ($user->can('viewAny', User::class)) {
     *     // Pokaż listę wszystkich użytkowników
     * }
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może przeglądać konkretnego użytkownika.
     *
     * Administratorzy mogą przeglądać profile wszystkich użytkowników.
     * Zwykli użytkownicy mogą przeglądać tylko własny profil.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący uzyskać dostęp
     * @param  User  $model  Użytkownik, którego profil ma być wyświetlony
     * @return bool True jeśli użytkownik jest adminem lub przegląda własny profil
     *
     * @example
     * if ($user->can('view', $targetUser)) {
     *     // Pokaż szczegóły profilu
     * }
     */
    public function view(User $user, User $model): bool
    {
        // Admin może widzieć wszystkich
        if ($user->isAdmin()) {
            return true;
        }

        // Użytkownik może widzieć własny profil
        return $user->id === $model->id;
    }

    /**
     * Sprawdza czy użytkownik może tworzyć nowych użytkowników.
     *
     * Tylko administratorzy mogą tworzyć nowe konta użytkowników
     * przez panel administracyjny.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący utworzyć użytkownika
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * if ($user->can('create', User::class)) {
     *     // Pokaż formularz tworzenia użytkownika
     * }
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może edytować dane użytkownika.
     *
     * Administratorzy mogą edytować wszystkich użytkowników bez ograniczeń.
     * Zwykli użytkownicy mogą edytować tylko własny profil, ale nie mogą
     * zmieniać krytycznych pól takich jak:
     * - rola (role)
     * - status premium (premium_until)
     * - status weryfikacji (email_verified_at, is_verified)
     *
     * @param  User  $user  Zalogowany użytkownik próbujący edytować
     * @param  User  $model  Użytkownik, który ma być edytowany
     * @return bool True jeśli użytkownik ma uprawnienia do edycji
     *
     * @example
     * if ($user->can('update', $targetUser)) {
     *     // Pokaż formularz edycji (z ograniczeniami dla zwykłych użytkowników)
     * }
     */
    public function update(User $user, User $model): bool
    {
        // Admin może edytować wszystkich
        if ($user->isAdmin()) {
            return true;
        }

        // Użytkownik może edytować własny profil (z ograniczeniami)
        // Ograniczenia będą obsługiwane na poziomie formularza w Filament
        return $user->id === $model->id;
    }

    /**
     * Sprawdza czy użytkownik może usunąć użytkownika (soft delete).
     *
     * Tylko administratorzy mogą usuwać użytkowników.
     * Administrator nie może usunąć samego siebie jako zabezpieczenie.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący usunąć
     * @param  User  $model  Użytkownik, który ma być usunięty
     * @return bool True jeśli użytkownik jest adminem i nie próbuje usunąć siebie
     *
     * @example
     * if ($user->can('delete', $targetUser)) {
     *     // Wykonaj soft delete użytkownika
     * }
     */
    public function delete(User $user, User $model): bool
    {
        // Tylko admin może usuwać
        if (! $user->isAdmin()) {
            return false;
        }

        // Admin nie może usunąć samego siebie
        return $user->id !== $model->id;
    }

    /**
     * Sprawdza czy użytkownik może przywrócić usuniętego użytkownika.
     *
     * Tylko administratorzy mogą przywracać użytkowników usuniętych
     * przez soft delete.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący przywrócić
     * @param  User  $model  Użytkownik, który ma być przywrócony
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * if ($user->can('restore', $deletedUser)) {
     *     // Przywróć usuniętego użytkownika
     * }
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może permanentnie usunąć użytkownika z bazy.
     *
     * Tylko administratorzy mogą wykonywać trwałe usunięcie użytkownika.
     * To działanie jest nieodwracalne i powinno być wykonywane z ostrożnością.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący permanentnie usunąć
     * @param  User  $model  Użytkownik, który ma być permanentnie usunięty
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * if ($user->can('forceDelete', $user)) {
     *     // Wykonaj permanentne usunięcie (zgodnie z GDPR)
     * }
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może przyznać status premium innemu użytkownikowi.
     *
     * Tylko administratorzy mogą zarządzać statusem premium użytkowników.
     * Ta metoda jest używana przez Custom Action w UserResource.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący przyznać premium
     * @param  User  $model  Użytkownik, który ma otrzymać premium
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * Action::make('grant_premium')
     *     ->visible(fn(User $record) => auth()->user()->can('grantPremium', $record))
     */
    public function grantPremium(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może zweryfikować konto użytkownika.
     *
     * Tylko administratorzy mogą weryfikować konta użytkowników.
     * Weryfikacja oznacza potwierdzenie tożsamości i dokumentów użytkownika.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący zweryfikować konto
     * @param  User  $model  Użytkownik, którego konto ma być zweryfikowane
     * @return bool True jeśli użytkownik ma uprawnienia administratora
     *
     * @example
     * Action::make('verify_account')
     *     ->visible(fn(User $record) => auth()->user()->can('verifyAccount', $record))
     */
    public function verifyAccount(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Sprawdza czy użytkownik może zbanować innego użytkownika.
     *
     * Tylko administratorzy mogą banować użytkowników.
     * Administrator nie może zbanować samego siebie.
     *
     * @param  User  $user  Zalogowany użytkownik próbujący zbanować
     * @param  User  $model  Użytkownik, który ma być zbanowany
     * @return bool True jeśli użytkownik jest adminem i nie próbuje zbanować siebie
     *
     * @example
     * Action::make('ban_user')
     *     ->visible(fn(User $record) => auth()->user()->can('banUser', $record))
     */
    public function banUser(User $user, User $model): bool
    {
        // Tylko admin może banować
        if (! $user->isAdmin()) {
            return false;
        }

        // Admin nie może zbanować samego siebie
        return $user->id !== $model->id;
    }
}
