<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test sprawdzający czy linki w navbar działają poprawnie.
 */
class NavbarLinksTest extends TestCase
{
    /**
     * Test sprawdza czy strona główna się ładuje i zawiera poprawne linki.
     */
    public function test_homepage_navbar_links(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Sprawdzamy czy strona zawiera tekst przycisków
        $response->assertSee('Zaloguj się');
        $response->assertSee('Zarejestruj się');

        // Sprawdzamy czy HTML zawiera poprawne href-y
        $content = $response->getContent();

        $this->assertStringContainsString(
            'href="'.route('login').'"',
            $content,
            'Navbar powinien zawierać link do strony logowania'
        );

        $this->assertStringContainsString(
            'href="'.route('register').'"',
            $content,
            'Navbar powinien zawierać link do strony rejestracji'
        );
    }

    /**
     * Test sprawdza czy linki generują poprawne URL-e.
     */
    public function test_route_urls(): void
    {
        // Test czy ruty są zdefiniowane i generują poprawne URL-e
        $loginUrl = route('login');
        $registerUrl = route('register');

        $this->assertStringEndsWith('/login', $loginUrl);
        $this->assertStringEndsWith('/register', $registerUrl);

        // Sprawdzamy czy URL-e są względne lub pełne
        $this->assertTrue(
            str_contains($loginUrl, '/login'),
            'URL logowania powinien zawierać /login'
        );

        $this->assertTrue(
            str_contains($registerUrl, '/register'),
            'URL rejestracji powinien zawierać /register'
        );
    }
}
