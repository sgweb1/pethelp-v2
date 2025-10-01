<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Kontroler szybkiego logowania dla środowiska deweloperskiego.
 *
 * Umożliwia szybkie logowanie się jako różni użytkownicy podczas testowania.
 * Dostępny tylko w środowisku lokalnym dla bezpieczeństwa.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class QuickLoginController extends Controller
{
    /**
     * Konstruktor - sprawdza czy jesteśmy w środowisku lokalnym.
     *
     * @throws \Exception Gdy próba użycia w środowisku produkcyjnym
     */
    public function __construct()
    {
        if (config('app.env') !== 'local') {
            abort(404, 'Quick login is only available in local environment');
        }
    }

    /**
     * Pobiera listę użytkowników do szybkiego logowania.
     *
     * Zwraca dane JSON z aktualnymi użytkownikami z bazy danych,
     * pogrupowane według typów (właściciele, opiekunowie, admini).
     *
     * @return array Lista użytkowników
     */
    public function getUsers(): array
    {
        // Pobierz przykładowych użytkowników różnych typów
        $users = [
            'owner' => $this->getOwnerUsers(),
            'sitter' => $this->getSitterUsers(),
            'admin' => $this->getAdminUsers(),
            'regular' => $this->getRegularUsers(),
        ];

        return $users;
    }

    /**
     * Szybkie logowanie się jako wybrany użytkownik.
     *
     * @param  int  $userId  ID użytkownika do zalogowania
     * @return RedirectResponse Przekierowanie do dashboardu
     */
    public function loginAs(int $userId): RedirectResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Użytkownik nie został znaleziony.');
        }

        Auth::login($user);

        // Przekieruj na odpowiednią stronę w zależności od typu użytkownika
        $redirectRoute = $this->getRedirectRoute($user);

        return redirect($redirectRoute)
            ->with('success', 'Zalogowano jako: '.$user->name.' ('.$user->email.')');
    }

    /**
     * Logowanie jako pierwszy dostępny właściciel zwierząt.
     */
    public function loginAsOwner(): RedirectResponse
    {
        $owner = User::whereHas('pets')->first();

        if (! $owner) {
            // Stwórz testowego właściciela jeśli nie ma żadnego
            $owner = $this->createTestOwner();
        }

        return $this->loginAs($owner->id);
    }

    /**
     * Logowanie jako pierwszy dostępny opiekun (sitter).
     */
    public function loginAsSitter(): RedirectResponse
    {
        $sitter = User::whereHas('services')->first();

        if (! $sitter) {
            // Stwórz testowego opiekuna jeśli nie ma żadnego
            $sitter = $this->createTestSitter();
        }

        return $this->loginAs($sitter->id);
    }

    /**
     * Logowanie jako pierwszy dostępny użytkownik.
     */
    public function loginAsUser(): RedirectResponse
    {
        $user = User::first();

        if (! $user) {
            // Stwórz testowego użytkownika jeśli nie ma żadnego
            $user = $this->createTestUser();
        }

        return $this->loginAs($user->id);
    }

    /**
     * Pobiera użytkowników będących właścicielami zwierząt.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getOwnerUsers()
    {
        return User::whereHas('pets')
            ->with(['pets:id,owner_id,name,type'])
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'pets_count' => $user->pets->count(),
                    'description' => 'Właściciel '.$user->pets->count().' zwierząt',
                ];
            });
    }

    /**
     * Pobiera użytkowników będących opiekunami (mających usługi).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getSitterUsers()
    {
        return User::whereHas('services')
            ->with(['services:id,user_id,category'])
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'services_count' => $user->services->count(),
                    'description' => 'Opiekun z '.$user->services->count().' usługami',
                ];
            });
    }

    /**
     * Pobiera użytkowników z uprawnieniami administratora.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAdminUsers()
    {
        return User::where('email', 'like', '%admin%')
            ->orWhere('email', 'like', '%test%')
            ->limit(2)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'description' => 'Administrator/Test',
                ];
            });
    }

    /**
     * Pobiera zwykłych użytkowników (bez zwierząt i usług).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRegularUsers()
    {
        return User::whereDoesntHave('pets')
            ->whereDoesntHave('services')
            ->limit(2)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'description' => 'Zwykły użytkownik',
                ];
            });
    }

    /**
     * Określa odpowiedni route dla przekierowania po logowaniu.
     *
     * @param  User  $user  Zalogowany użytkownik
     * @return string Route do przekierowania
     */
    private function getRedirectRoute(User $user): string
    {
        // Jeśli ma usługi - idź do panelu opiekuna
        if ($user->services()->count() > 0) {
            return route('profile.dashboard');
        }

        // Jeśli ma zwierzęta - idź do panelu właściciela
        if ($user->pets()->count() > 0) {
            return route('profile.dashboard');
        }

        // Domyślnie dashboard
        return route('profile.dashboard');
    }

    /**
     * Tworzy testowego właściciela zwierząt.
     *
     * @return User Utworzony użytkownik
     */
    private function createTestOwner(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'test-owner@pethelp.local'],
            [
                'name' => 'Jan Testowy',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Dodaj testowego psa jeśli nie istnieje
        if (! $user->pets()->where('name', 'Burek')->exists()) {
            $user->pets()->create([
                'name' => 'Burek',
                'pet_type_id' => 1, // Zakładając że 1 = pies
                'breed' => 'Mieszaniec',
                'age' => 3,
                'size' => 'medium',
                'gender' => 'male',
                'description' => 'Przyjazny i energiczny pies testowy',
            ]);
        }

        return $user;
    }

    /**
     * Tworzy testowego opiekuna zwierząt.
     *
     * @return User Utworzony użytkownik
     */
    private function createTestSitter(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'test-sitter@pethelp.local'],
            [
                'name' => 'Anna Testowa',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Dodaj testową usługę jeśli nie istnieje
        if (! $user->services()->where('title', 'Spacery z psami - TEST')->exists()) {
            // Znajdź kategorię "spacery" lub użyj pierwszej dostępnej
            $walkingCategory = \App\Models\ServiceCategory::where('slug', 'spacery')->first()
                ?? \App\Models\ServiceCategory::first();

            if ($walkingCategory) {
                $user->services()->create([
                    'category_id' => $walkingCategory->id,
                    'title' => 'Spacery z psami - TEST',
                    'slug' => 'spacery-z-psami-test',
                    'description' => 'Testowa usługa spacerów z psami',
                    'price_per_hour' => 25.00,
                    'city' => 'Warszawa',
                    'is_active' => true,
                ]);
            }
        }

        return $user;
    }

    /**
     * Tworzy testowego użytkownika.
     *
     * @return User Utworzony użytkownik
     */
    private function createTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test-user@pethelp.local'],
            [
                'name' => 'Użytkownik Testowy',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
