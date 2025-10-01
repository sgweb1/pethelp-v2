<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Test sprawdzający dostępność wszystkich zdefiniowanych rut w aplikacji.
 *
 * Testuje zarówno ruty publiczne jak i te wymagające autoryzacji,
 * sprawdzając odpowiednie kody odpowiedzi HTTP.
 */
class RoutesAccessibilityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Użytkownik testowy dla rut wymagających autoryzacji.
     */
    private User $user;

    /**
     * Przygotowanie danych testowych.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Tworzymy użytkownika z pełnym profilem
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test dostępności wszystkich rut publicznych.
     */
    public function test_public_routes_are_accessible(): void
    {
        $publicRoutes = [
            // Strona główna
            ['GET', '/', 200],

            // Autentykacja
            ['GET', '/login', 200],
            ['GET', '/register', 200],
            ['GET', '/forgot-password', 200],

            // Publiczne strony
            ['GET', '/search', 200],
            ['GET', '/about', 200],
            ['GET', '/privacy', 200],
            ['GET', '/terms', 200],
            ['GET', '/contact', 200],

            // API publiczne
            ['GET', '/api/map/data', 200],
            ['GET', '/api/map/categories', 200],
            ['GET', '/api/search/autocomplete', 200],
        ];

        foreach ($publicRoutes as [$method, $uri, $expectedStatus]) {
            $response = $this->call($method, $uri);

            $this->assertEquals(
                $expectedStatus,
                $response->getStatusCode(),
                "Ruta {$method} {$uri} powinna zwrócić kod {$expectedStatus}, ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test dostępności rut wymagających autoryzacji (bez logowania - powinny przekierować).
     */
    public function test_protected_routes_redirect_when_not_authenticated(): void
    {
        $protectedRoutes = [
            // Dashboard i profil
            ['GET', '/profil'],
            ['GET', '/profil/edytuj'],
            ['GET', '/profil/powiadomienia'],
            ['GET', '/profil/wiadomości'],
            ['GET', '/profil/recenzje'],
            ['GET', '/profil/rezerwacje'],
            ['GET', '/profil/dostępność'],

            // Usługi
            ['GET', '/profil/usługi'],
            ['GET', '/profil/usługi/create'],

            // Wydarzenia
            ['GET', '/profil/wydarzenia'],
            ['GET', '/profil/wydarzenia/create'],

            // Ogłoszenia
            ['GET', '/profil/ogłoszenia'],
            ['GET', '/profil/ogłoszenia/create'],

            // Zwierzęta
            ['GET', '/profil/zwierzęta'],
            ['GET', '/profil/zwierzęta/create'],

            // Subskrypcje
            ['GET', '/subscription/plans'],
            ['GET', '/subscription/dashboard'],
        ];

        foreach ($protectedRoutes as [$method, $uri]) {
            $response = $this->call($method, $uri);

            // Sprawdzamy czy nastąpiło przekierowanie (302) lub wyświetlenie strony logowania
            $this->assertTrue(
                in_array($response->getStatusCode(), [302, 401]),
                "Ruta {$method} {$uri} powinna przekierować niezalogowanego użytkownika (302/401), ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test dostępności rut wymagających autoryzacji (z zalogowaniem).
     */
    public function test_protected_routes_are_accessible_when_authenticated(): void
    {
        $this->actingAs($this->user);

        $protectedRoutes = [
            // Dashboard i profil
            ['GET', '/profil', 200],
            ['GET', '/profil/edytuj', 200],
            ['GET', '/profil/powiadomienia', 200],
            ['GET', '/profil/recenzje', 200],

            // Usługi - dostępne dla wszystkich zalogowanych
            ['GET', '/profil/usługi', 200],
            ['GET', '/profil/usługi/create', 200],

            // Zwierzęta
            ['GET', '/profil/zwierzęta', 200],
            ['GET', '/profil/zwierzęta/create', 200],

            // Subskrypcje
            ['GET', '/subscription/plans', 200],
            ['GET', '/subscription/dashboard', 200],
        ];

        foreach ($protectedRoutes as [$method, $uri, $expectedStatus]) {
            $response = $this->call($method, $uri);

            $this->assertEquals(
                $expectedStatus,
                $response->getStatusCode(),
                "Ruta {$method} {$uri} powinna zwrócić kod {$expectedStatus} dla zalogowanego użytkownika, ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test API endpoints wymagających autoryzacji.
     */
    public function test_api_routes_with_authentication(): void
    {
        // Test bez autoryzacji
        $apiRoutes = [
            ['GET', '/api/user/profile'],
            ['GET', '/api/user/notifications'],
            ['GET', '/api/user/messages'],
            ['POST', '/api/bookings'],
            ['POST', '/api/reviews'],
        ];

        foreach ($apiRoutes as [$method, $uri]) {
            $response = $this->call($method, $uri);

            $this->assertTrue(
                in_array($response->getStatusCode(), [401, 302]),
                "API {$method} {$uri} powinno zwrócić 401/302 dla niezalogowanego użytkownika, ale zwróciło {$response->getStatusCode()}"
            );
        }

        // Test z autoryzacją
        $this->actingAs($this->user);

        $authenticatedApiRoutes = [
            ['GET', '/api/user/profile', [200, 404]], // 404 jeśli profil nie istnieje
            ['GET', '/api/user/notifications', [200]],
            ['GET', '/api/js-logs', [200]], // Publiczne API dla logów JS
        ];

        foreach ($authenticatedApiRoutes as [$method, $uri, $allowedStatuses]) {
            $response = $this->call($method, $uri);

            $this->assertTrue(
                in_array($response->getStatusCode(), $allowedStatuses),
                "API {$method} {$uri} powinno zwrócić jeden z kodów [".implode(', ', $allowedStatuses)."] ale zwróciło {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test wszystkich zarejestrowanych rut w aplikacji.
     */
    public function test_all_registered_routes(): void
    {
        $routes = Route::getRoutes();
        $testedRoutes = [];
        $failedRoutes = [];

        foreach ($routes as $route) {
            $methods = $route->methods();
            $uri = $route->uri();

            // Pomijamy ruty z parametrami, CSRF, i specjalne ruty
            if (
                str_contains($uri, '{') ||
                str_contains($uri, '_ignition') ||
                str_contains($uri, 'sanctum') ||
                in_array('POST', $methods) ||
                in_array('PUT', $methods) ||
                in_array('PATCH', $methods) ||
                in_array('DELETE', $methods)
            ) {
                continue;
            }

            // Testujemy tylko ruty GET i HEAD
            if (in_array('GET', $methods)) {
                $fullUri = '/'.ltrim($uri, '/');

                try {
                    // Test bez autoryzacji
                    $response = $this->call('GET', $fullUri);
                    $statusCode = $response->getStatusCode();

                    // Akceptujemy kody: 200 (OK), 302 (redirect), 401 (unauthorized), 404 (not found)
                    $acceptableCodes = [200, 302, 401, 404];

                    if (! in_array($statusCode, $acceptableCodes)) {
                        $failedRoutes[] = "GET {$fullUri} - kod: {$statusCode}";
                    }

                    $testedRoutes[] = "GET {$fullUri} - kod: {$statusCode}";

                } catch (\Exception $e) {
                    $failedRoutes[] = "GET {$fullUri} - błąd: ".$e->getMessage();
                }
            }
        }

        // Wyświetlamy podsumowanie
        echo "\n\n=== PODSUMOWANIE TESTÓW RUT ===\n";
        echo 'Przetestowano rut: '.count($testedRoutes)."\n";
        echo 'Nieudanych: '.count($failedRoutes)."\n\n";

        if (! empty($failedRoutes)) {
            echo "NIEUDANE RUTY:\n";
            foreach ($failedRoutes as $failed) {
                echo "❌ {$failed}\n";
            }
        }

        echo "\nWSSYSTKIE PRZETESTOWANE RUTY:\n";
        foreach ($testedRoutes as $tested) {
            $icon = str_contains($tested, '200') ? '✅' : (str_contains($tested, '302') ? '🔄' : '⚠️');
            echo "{$icon} {$tested}\n";
        }

        // Test przechodzi jeśli mniej niż 20% rut zakończonych niepowodzeniem
        $failureRate = count($failedRoutes) / max(1, count($testedRoutes));
        $this->assertLessThan(
            0.2,
            $failureRate,
            'Zbyt wiele rut zakończonych niepowodzeniem: '.count($failedRoutes).'/'.count($testedRoutes)
        );
    }

    /**
     * Test specjalnych rut z parametrami (używając istniejących danych).
     */
    public function test_parameterized_routes_with_existing_data(): void
    {
        $this->actingAs($this->user);

        // Najpierw tworzymy potrzebne dane testowe
        $pet = \App\Models\Pet::factory()->create(['owner_id' => $this->user->id]);
        $service = \App\Models\Service::factory()->create(['user_id' => $this->user->id]);

        $parameterizedRoutes = [
            // Edycja zwierzęcia
            ['GET', "/profil/zwierzęta/{$pet->id}/edit", [200, 404]],

            // Edycja usługi
            ['GET', "/profil/usługi/{$service->id}/edit", [200, 404]],
        ];

        foreach ($parameterizedRoutes as [$method, $uri, $allowedStatuses]) {
            $response = $this->call($method, $uri);

            $this->assertTrue(
                in_array($response->getStatusCode(), $allowedStatuses),
                "Ruta {$method} {$uri} powinna zwrócić jeden z kodów [".implode(', ', $allowedStatuses)."] ale zwróciła {$response->getStatusCode()}"
            );
        }
    }
}
