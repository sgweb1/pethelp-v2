<?php

namespace Tests\Feature;

use App\Services\LocationSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Testy funkcjonalności lokalnego Nominatim.
 *
 * Sprawdzają poprawność działania systemu geocodingu z fallback-iem
 * z lokalnego na zewnętrzne API.
 *
 * @package Tests\Feature
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class LocalNominatimTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Serwis wyszukiwania lokalizacji.
     *
     * @var LocationSearchService
     */
    private LocationSearchService $locationService;

    /**
     * Setup dla testów.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->locationService = app(LocationSearchService::class);
    }

    /**
     * Test API endpoint dla wyszukiwania lokalizacji.
     */
    public function test_location_search_api_endpoint(): void
    {
        // Mock HTTP response dla external Nominatim
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => 'Warszawa, Mazowieckie, Polska',
                    'lat' => '52.2319581',
                    'lon' => '21.0067249',
                    'address' => [
                        'city' => 'Warszawa',
                        'state' => 'Mazowieckie',
                        'country' => 'Polska'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/location/search', [
            'query' => 'Warszawa',
            'limit' => 5
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'display_name',
                             'lat',
                             'lon'
                         ]
                     ],
                     'meta' => [
                         'query',
                         'limit',
                         'count',
                         'source'
                     ]
                 ]);
    }

    /**
     * Test API endpoint dla reverse geocoding.
     */
    public function test_reverse_geocoding_api_endpoint(): void
    {
        // Mock HTTP response dla external Nominatim
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Warszawa, Mazowieckie, Polska',
                'lat' => '52.2319581',
                'lon' => '21.0067249',
                'address' => [
                    'city' => 'Warszawa',
                    'state' => 'Mazowieckie',
                    'country' => 'Polska'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/api/location/reverse', [
            'lat' => 52.2319581,
            'lon' => 21.0067249
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'display_name',
                         'lat',
                         'lon'
                     ],
                     'meta' => [
                         'lat',
                         'lon',
                         'source'
                     ]
                 ]);
    }

    /**
     * Test status endpoint.
     */
    public function test_nominatim_status_endpoint(): void
    {
        $response = $this->getJson('/api/location/status');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'local_nominatim' => [
                             'enabled',
                             'url',
                             'healthy',
                             'status'
                         ],
                         'fallback' => [
                             'enabled',
                             'url'
                         ],
                         'active_source',
                         'configuration' => [
                             'cache_ttl',
                             'rate_limit_delay'
                         ]
                     ]
                 ]);
    }

    /**
     * Test fallback z lokalnego na zewnętrzny Nominatim.
     */
    public function test_fallback_from_local_to_external(): void
    {
        // Ustaw konfigurację na lokalny Nominatim
        config(['app.nominatim_local_enabled' => true]);
        config(['app.nominatim_local_url' => 'http://localhost:8080']);
        config(['app.nominatim_fallback_enabled' => true]);

        // Mock nieudany lokalny request i udany zewnętrzny
        Http::fake([
            'localhost:8080/*' => Http::response('Connection refused', 500),
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => 'Kraków, Małopolskie, Polska',
                    'lat' => '50.0469432',
                    'lon' => '19.9370471',
                    'address' => [
                        'city' => 'Kraków',
                        'state' => 'Małopolskie',
                        'country' => 'Polska'
                    ]
                ]
            ], 200)
        ]);

        $results = $this->locationService->searchLocations('Kraków', 5);

        $this->assertNotEmpty($results);
        $this->assertEquals('Kraków, Małopolskie, Polska', $results[0]['display_name']);
    }

    /**
     * Test używania lokalnego Nominatim gdy jest dostępny.
     */
    public function test_uses_local_nominatim_when_available(): void
    {
        // Ustaw konfigurację na lokalny Nominatim
        config(['app.nominatim_local_enabled' => true]);
        config(['app.nominatim_local_url' => 'http://localhost:8080']);

        // Mock lokalny i zewnętrzny Nominatim
        Http::fake([
            'localhost:8080/status' => Http::response('OK', 200),
            'localhost:8080/search*' => Http::response([
                [
                    'display_name' => 'Gdańsk, Pomorskie, Polska (LOCAL)',
                    'lat' => '54.3610255',
                    'lon' => '18.6335814',
                    'address' => [
                        'city' => 'Gdańsk',
                        'state' => 'Pomorskie',
                        'country' => 'Polska'
                    ]
                ]
            ], 200),
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => 'Gdańsk, Pomorskie, Polska (EXTERNAL)',
                    'lat' => '54.3610255',
                    'lon' => '18.6335814'
                ]
            ], 200)
        ]);

        $results = $this->locationService->searchLocations('Gdańsk', 5);

        $this->assertNotEmpty($results);
        // Sprawdź że używa lokalnego (contains LOCAL)
        $this->assertStringContainsString('LOCAL', $results[0]['display_name']);
    }

    /**
     * Test konfiguracji rate limiting dla lokalnego vs zewnętrznego.
     */
    public function test_rate_limiting_configuration(): void
    {
        // Test lokalny - krótkie opóźnienie
        config(['app.nominatim_local_enabled' => true]);
        config(['app.nominatim_rate_limit_delay' => 50]);

        $startTime = microtime(true);

        Http::fake([
            'localhost:8080/status' => Http::response('OK', 200),
            'localhost:8080/search*' => Http::response([
                [
                    'display_name' => 'Test Location',
                    'lat' => '52.0',
                    'lon' => '21.0'
                ]
            ], 200)
        ]);

        $this->locationService->searchLocations('Test', 1);

        $executionTime = (microtime(true) - $startTime) * 1000; // milliseconds

        // Lokalny powinien być szybszy (max 200ms z overhead)
        $this->assertLessThan(200, $executionTime);
    }

    /**
     * Test walidacji API requests.
     */
    public function test_api_validation(): void
    {
        // Test pustego query
        $response = $this->postJson('/api/location/search', [
            'query' => '',
            'limit' => 5
        ]);

        $response->assertStatus(422);

        // Test niepoprawnych współrzędnych
        $response = $this->postJson('/api/location/reverse', [
            'lat' => 200, // Niepoprawna szerokość
            'lon' => 21.0
        ]);

        $response->assertStatus(422);

        // Test zbyt dużego limitu
        $response = $this->postJson('/api/location/search', [
            'query' => 'Warszawa',
            'limit' => 100 // Powyżej maksimum
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test cache'owania wyników.
     */
    public function test_caching_behavior(): void
    {
        config(['app.nominatim_cache_ttl' => 3600]); // 1 hour

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => 'Wrocław, Dolnośląskie, Polska',
                    'lat' => '51.1089776',
                    'lon' => '17.0326689'
                ]
            ], 200)
        ]);

        // Pierwsze wywołanie
        $results1 = $this->locationService->searchLocations('Wrocław', 1);

        // Drugie wywołanie powinno użyć cache
        $results2 = $this->locationService->searchLocations('Wrocław', 1);

        $this->assertEquals($results1, $results2);

        // Sprawdź że HTTP został wywołany tylko raz (cache działa)
        Http::assertSentCount(1);
    }

    /**
     * Test formatowania polskich adresów.
     */
    public function test_polish_address_formatting(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'display_name' => 'Marszałkowska 1, Śródmieście, Warszawa, Mazowieckie, Polska',
                    'lat' => '52.2319581',
                    'lon' => '21.0067249',
                    'address' => [
                        'road' => 'Marszałkowska',
                        'house_number' => '1',
                        'suburb' => 'Śródmieście',
                        'city' => 'Warszawa',
                        'state' => 'Mazowieckie',
                        'country' => 'Polska',
                        'postcode' => '00-026'
                    ]
                ]
            ], 200)
        ]);

        $results = $this->locationService->searchLocations('Marszałkowska 1 Warszawa', 1);

        $this->assertNotEmpty($results);

        $result = $results[0];

        // Sprawdź obecność polskich elementów
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('state', $result);
        $this->assertArrayHasKey('country', $result);

        $this->assertEquals('Warszawa', $result['city']);
        $this->assertEquals('Mazowieckie', $result['state']);
        $this->assertEquals('Polska', $result['country']);
    }

    /**
     * Test obsługi błędów sieciowych.
     */
    public function test_network_error_handling(): void
    {
        // Mock timeout
        Http::fake([
            '*' => function () {
                throw new \Exception('Connection timeout');
            }
        ]);

        $results = $this->locationService->searchLocations('Warszawa', 5);

        // Powinien zwrócić pustą tablicę zamiast rzucić wyjątek
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }
}