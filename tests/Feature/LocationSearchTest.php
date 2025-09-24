<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocationSearchTest extends TestCase
{
    /** @test */
    public function test_hierarchical_search_returns_structured_results()
    {
        $response = $this->getJson('/api/locations/search?q=olsztyn&limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'type',
                        'label',
                        'display_name',
                        'coordinates',
                        'data',
                    ],
                ],
                'meta' => [
                    'query',
                    'count',
                    'limit',
                ],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    /** @test */
    public function test_partial_city_name_returns_suggestions()
    {
        $response = $this->getJson('/api/locations/search?q=olszt&limit=10');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Should return Olsztyn suggestion for partial match
        $found = false;
        foreach ($data as $item) {
            if (stripos($item['label'], 'Olsztyn') !== false) {
                $found = true;
                $this->assertEquals('city', $item['type']);
                $this->assertArrayHasKey('coordinates', $item);
                $this->assertCount(2, $item['coordinates']); // [lat, lng]
                break;
            }
        }

        $this->assertTrue($found, 'Olsztyn not found in suggestions for "olszt"');
    }

    /** @test */
    public function test_warsaw_search_returns_hierarchical_results()
    {
        $response = $this->getJson('/api/locations/search?q=warszawa&limit=10');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotEmpty($data);

        // Check if we have city-type results
        $cityResults = array_filter($data, fn ($item) => $item['type'] === 'city');
        $this->assertNotEmpty($cityResults, 'Should have city results for Warszawa');

        foreach ($cityResults as $city) {
            $this->assertStringContainsStringIgnoringCase('warszawa', $city['label']);
            $this->assertIsArray($city['coordinates']);
            $this->assertCount(2, $city['coordinates']);
        }
    }

    /** @test */
    public function test_location_type_classification_is_correct()
    {
        $response = $this->getJson('/api/locations/search?q=kraków&limit=10');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $item) {
            $this->assertContains($item['type'], ['city', 'district', 'village', 'other']);

            if ($item['type'] === 'city') {
                $this->assertStringContainsStringIgnoringCase('kraków', $item['label']);
            }
        }
    }

    /** @test */
    public function test_short_queries_get_expanded_with_polish_cities()
    {
        $response = $this->getJson('/api/locations/search?q=war&limit=10');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Should contain Warszawa from expanded search
        $found = false;
        foreach ($data as $item) {
            if (stripos($item['label'], 'Warszawa') !== false) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Warszawa should be found for partial query "war"');
    }

    /** @test */
    public function test_coordinates_are_valid_for_polish_locations()
    {
        $response = $this->getJson('/api/locations/search?q=gdańsk&limit=5');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $item) {
            if (! empty($item['coordinates'])) {
                [$lat, $lng] = $item['coordinates'];

                // Poland approximate bounds
                $this->assertGreaterThan(49, $lat);   // South of Poland
                $this->assertLessThan(55, $lat);      // North of Poland
                $this->assertGreaterThan(14, $lng);   // West of Poland
                $this->assertLessThan(25, $lng);      // East of Poland
            }
        }
    }

    /** @test */
    public function test_cache_works_for_repeated_queries()
    {
        $query = 'poznań';

        // First request
        $start1 = microtime(true);
        $response1 = $this->getJson("/api/locations/search?q={$query}&limit=5");
        $time1 = microtime(true) - $start1;

        $response1->assertStatus(200);

        // Second request (should use cache)
        $start2 = microtime(true);
        $response2 = $this->getJson("/api/locations/search?q={$query}&limit=5");
        $time2 = microtime(true) - $start2;

        $response2->assertStatus(200);

        // Results should be identical
        $this->assertEquals($response1->json('data'), $response2->json('data'));

        // Second request should be faster (with some tolerance)
        $this->assertLessThan($time1 * 1.5, $time2, 'Cached request should be faster');
    }

    /** @test */
    public function test_empty_or_short_queries_handled_correctly()
    {
        // Empty query
        $response = $this->getJson('/api/locations/search?q=&limit=5');
        $response->assertStatus(422); // Should validate minimum length

        // Single character
        $response = $this->getJson('/api/locations/search?q=a&limit=5');
        $response->assertStatus(422); // Should validate minimum length

        // Valid short query
        $response = $this->getJson('/api/locations/search?q=ol&limit=5');
        $response->assertStatus(200); // Should work for 2+ characters
    }

    /** @test */
    public function test_limit_parameter_is_respected()
    {
        $limit = 3;
        $response = $this->getJson("/api/locations/search?q=warszawa&limit={$limit}");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertLessThanOrEqual($limit, count($data));
        $this->assertEquals($limit, $response->json('meta.limit'));
    }

    /** @test */
    public function test_invalid_parameters_return_validation_errors()
    {
        // Invalid limit
        $response = $this->getJson('/api/locations/search?q=test&limit=100');
        $response->assertStatus(422);

        // No query parameter
        $response = $this->getJson('/api/locations/search?limit=5');
        $response->assertStatus(422);
    }

    /** @test */
    public function test_reverse_geocoding_works()
    {
        // Coordinates for central Warsaw
        $lat = 52.2297;
        $lon = 21.0122;

        $response = $this->getJson("/api/locations/reverse?lat={$lat}&lon={$lon}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'display_name',
                    'lat',
                    'lon',
                    'type',
                    'city',
                    'district',
                    'state',
                    'country',
                ],
            ]);

        $data = $response->json('data');
        $this->assertStringContainsStringIgnoringCase('warszawa', $data['display_name']);
    }

    /** @test */
    public function test_reverse_geocoding_with_invalid_coordinates()
    {
        // Invalid coordinates
        $response = $this->getJson('/api/locations/reverse?lat=999&lon=999');
        $response->assertStatus(422); // Should validate coordinate bounds

        // Missing coordinates
        $response = $this->getJson('/api/locations/reverse?lat=52.2297');
        $response->assertStatus(422); // Should require both lat and lon
    }

    /** @test */
    public function test_location_descriptions_are_user_friendly()
    {
        $response = $this->getJson('/api/locations/search?q=warszawa&limit=10');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $item) {
            // Check if display_name doesn't contain redundant "województwo" repetitions
            $this->assertStringNotContainsString('województwo województwo', $item['display_name']);

            // Verify proper Polish formatting
            if ($item['type'] === 'district' && isset($item['parent_city'])) {
                $this->assertStringContainsString($item['parent_city'], $item['label']);
            }
        }
    }

    /** @test */
    public function test_performance_with_multiple_concurrent_requests()
    {
        $queries = ['warszawa', 'kraków', 'gdańsk', 'wrocław', 'poznań'];
        $startTime = microtime(true);

        $responses = [];
        foreach ($queries as $query) {
            $responses[] = $this->getJson("/api/locations/search?q={$query}&limit=5");
        }

        $totalTime = microtime(true) - $startTime;

        // All requests should succeed
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Should complete within reasonable time (adjust as needed)
        $this->assertLessThan(5.0, $totalTime, 'Multiple location searches should complete quickly');
    }
}
