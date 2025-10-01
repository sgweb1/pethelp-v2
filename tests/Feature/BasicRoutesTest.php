<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uproszczony test podstawowych rut aplikacji.
 *
 * Sprawdza dostępność najważniejszych publicznych i chronionych rut
 * bez szczegółowej analizy wszystkich możliwych kombinacji.
 */
class BasicRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test podstawowych publicznych rut.
     */
    public function test_public_routes(): void
    {
        $publicRoutes = [
            '/' => 200,
            '/login' => 200,
            '/register' => 200,
        ];

        foreach ($publicRoutes as $route => $expectedStatus) {
            $response = $this->get($route);

            $this->assertEquals(
                $expectedStatus,
                $response->getStatusCode(),
                "Ruta {$route} powinna zwrócić kod {$expectedStatus}, ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test głównych chronionych rut (powinny przekierować do logowania).
     */
    public function test_protected_routes_redirect(): void
    {
        $protectedRoutes = [
            '/profil',
            '/profile',
            '/profil/powiadomienia',
            '/subscription/plans',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);

            $this->assertTrue(
                in_array($response->getStatusCode(), [302, 401]),
                "Ruta {$route} powinna przekierować niezalogowanego użytkownika (302/401), ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test chronionych rut dla zalogowanego użytkownika.
     */
    public function test_protected_routes_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $protectedRoutes = [
            '/profil' => 200,
            '/profile' => 200,
            '/subscription/plans' => 200,
        ];

        foreach ($protectedRoutes as $route => $expectedStatus) {
            $response = $this->get($route);

            $this->assertEquals(
                $expectedStatus,
                $response->getStatusCode(),
                "Ruta {$route} powinna zwrócić kod {$expectedStatus} dla zalogowanego użytkownika, ale zwróciła {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test navbar linków - sprawdza czy przyciski mają poprawne URL-e.
     */
    public function test_navbar_links(): void
    {
        $response = $this->get('/');

        // Sprawdzamy czy strona główna zawiera linki do logowania i rejestracji
        $response->assertSee('Zaloguj się');
        $response->assertSee('Zarejestruj się');

        // Sprawdzamy czy HTML zawiera poprawne href
        $content = $response->getContent();
        $this->assertStringContainsString('href="'.route('login').'"', $content);
        $this->assertStringContainsString('href="'.route('register').'"', $content);
    }

    /**
     * Test podstawowych API endpoints.
     */
    public function test_api_endpoints(): void
    {
        $apiRoutes = [
            '/api/map/data' => [200, 404], // 404 jeśli brak danych
            '/api/map/categories' => [200, 404],
        ];

        foreach ($apiRoutes as $route => $allowedStatuses) {
            $response = $this->get($route);

            $this->assertTrue(
                in_array($response->getStatusCode(), $allowedStatuses),
                "API {$route} powinno zwrócić jeden z kodów [".implode(', ', $allowedStatuses)."] ale zwróciło {$response->getStatusCode()}"
            );
        }
    }
}
