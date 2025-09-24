<?php

namespace Tests\Feature;

use App\Models\MapItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedSearchFiltersTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create test data for comprehensive filter testing
        $this->createTestMapItems();
    }

    /** @test */
    public function test_content_type_filter_returns_only_pet_sitters()
    {
        $response = $this->getJson('/api/search?content_type=pet_sitter&limit=50');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertEquals('pet_sitter', $item['content_type']);
        }
    }

    /** @test */
    public function test_location_filter_returns_items_from_specified_city()
    {
        // Create test data for specific city
        \App\Models\MapItem::factory()->create([
            'city' => 'Kraków',
            'content_type' => 'pet_sitter',
            'status' => 'published'
        ]);

        $response = $this->getJson('/api/search?location=Kraków&limit=50');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        $this->assertNotEmpty($items, 'Should return items for Kraków');

        foreach ($items as $item) {
            $this->assertStringContainsStringIgnoringCase('Kraków', $item['location']['city']);
        }
    }

    /** @test */
    public function test_price_range_filter_returns_items_within_range()
    {
        $minPrice = 20;
        $maxPrice = 50;

        $response = $this->getJson("/api/search?min_price={$minPrice}&max_price={$maxPrice}&limit=50");

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $price = (float) $item['price']['from'];
            $this->assertGreaterThanOrEqual($minPrice, $price);
            $this->assertLessThanOrEqual($maxPrice, $price);
        }
    }

    /** @test */
    public function test_rating_filter_returns_items_with_minimum_rating()
    {
        $minRating = 4.0;

        $response = $this->getJson("/api/search?min_rating={$minRating}&limit=50");

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertGreaterThanOrEqual($minRating, $item['quality']['rating']);
        }
    }

    /** @test */
    public function test_featured_only_filter_returns_only_featured_items()
    {
        $response = $this->getJson('/api/search?featured_only=1&limit=50');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertTrue($item['flags']['featured']);
        }
    }

    /** @test */
    public function test_geographic_bounds_filter_returns_items_within_bounds()
    {
        // Bounds for Olsztyn area: [south, west, north, east]
        $bounds = [53.7, 20.4, 53.8, 20.5];

        $response = $this->getJson('/api/search?bounds[]='.implode('&bounds[]=', $bounds).'&limit=50');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $lat = $item['location']['coordinates']['lat'];
            $lng = $item['location']['coordinates']['lng'];

            $this->assertGreaterThanOrEqual($bounds[0], $lat); // south
            $this->assertLessThanOrEqual($bounds[2], $lat);    // north
            $this->assertGreaterThanOrEqual($bounds[1], $lng); // west
            $this->assertLessThanOrEqual($bounds[3], $lng);    // east
        }
    }

    /** @test */
    public function test_search_term_filter_returns_relevant_items()
    {
        $searchTerm = 'kot';

        $response = $this->getJson("/api/search?search_term={$searchTerm}&limit=50");

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $searchContent = strtolower($item['title'].' '.$item['description'].' '.$item['category']['name']);
            $this->assertStringContainsString(strtolower($searchTerm), $searchContent);
        }
    }

    /** @test */
    public function test_multiple_filters_work_together()
    {
        $response = $this->getJson('/api/search?'.http_build_query([
            'content_type' => 'pet_sitter',
            'location' => 'Olsztyn',
            'min_price' => 25,
            'max_price' => 45,
            'min_rating' => 4.0,
            'limit' => 50,
        ]));

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            // All filters should be satisfied
            $this->assertEquals('pet_sitter', $item['content_type']);
            $this->assertStringContainsStringIgnoringCase('Olsztyn', $item['location']['city']);
            $this->assertGreaterThanOrEqual(25, (float) $item['price']['from']);
            $this->assertLessThanOrEqual(45, (float) $item['price']['from']);
            $this->assertGreaterThanOrEqual(4.0, $item['quality']['rating']);
        }
    }

    /** @test */
    public function test_map_format_returns_markers_with_correct_structure()
    {
        $response = $this->getJson('/api/search?content_type=pet_sitter&format=map&limit=50');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'markers' => [
                        '*' => [
                            'id',
                            'lat',
                            'lng',
                            'title',
                            'category',
                            'icon',
                            'color',
                            'price',
                            'currency',
                            'featured',
                            'urgent',
                            'rating',
                            'content_type',
                        ],
                    ],
                    'bounds',
                    'clusters',
                ],
            ]);

        $markers = $response->json('data.markers');

        foreach ($markers as $marker) {
            $this->assertEquals('pet_sitter', $marker['content_type']);
            $this->assertIsNumeric($marker['lat']);
            $this->assertIsNumeric($marker['lng']);
            $this->assertIsNumeric($marker['rating']);
        }
    }

    /** @test */
    public function test_sorting_by_price_works_correctly()
    {
        // Test ascending price sort
        $response = $this->getJson('/api/search?sort_by=price_low&limit=10');
        $response->assertStatus(200);

        $items = $response->json('data.items');
        $prices = array_map(fn ($item) => (float) $item['price']['from'], $items);

        $sortedPrices = $prices;
        sort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices);

        // Test descending price sort
        $response = $this->getJson('/api/search?sort_by=price_high&limit=10');
        $response->assertStatus(200);

        $items = $response->json('data.items');
        $prices = array_map(fn ($item) => (float) $item['price']['from'], $items);

        $sortedPricesDesc = $prices;
        rsort($sortedPricesDesc);
        $this->assertEquals($sortedPricesDesc, $prices);
    }

    /** @test */
    public function test_sorting_by_rating_works_correctly()
    {
        $response = $this->getJson('/api/search?sort_by=rating&limit=10');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        $ratings = array_map(fn ($item) => $item['quality']['rating'], $items);

        // Should be sorted from highest to lowest rating
        $sortedRatingsDesc = $ratings;
        rsort($sortedRatingsDesc);
        $this->assertEquals($sortedRatingsDesc, $ratings);
    }

    /** @test */
    public function test_invalid_filters_are_handled_gracefully()
    {
        $response = $this->getJson('/api/search?'.http_build_query([
            'content_type' => 'invalid_type',
            'min_price' => -10,
            'max_price' => 'invalid',
            'min_rating' => 10, // Invalid rating > 5
            'limit' => 2000, // Too high limit
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content_type', 'min_price', 'max_price', 'min_rating', 'limit']);
    }

    /** @test */
    public function test_data_consistency_between_list_and_map_format()
    {
        $filters = [
            'content_type' => 'pet_sitter',
            'location' => 'Olsztyn',
            'limit' => 20,
        ];

        // Get data in list format
        $listResponse = $this->getJson('/api/search?'.http_build_query(array_merge($filters, ['format' => 'list'])));
        $listResponse->assertStatus(200);
        $listItems = $listResponse->json('data.items');

        // Get data in map format
        $mapResponse = $this->getJson('/api/search?'.http_build_query(array_merge($filters, ['format' => 'map'])));
        $mapResponse->assertStatus(200);
        $mapMarkers = $mapResponse->json('data.markers');

        // Should have same number of items
        $this->assertCount(count($listItems), $mapMarkers);

        // Should have same IDs
        $listIds = array_map(fn ($item) => $item['id'], $listItems);
        $mapIds = array_map(fn ($marker) => $marker['id'], $mapMarkers);

        sort($listIds);
        sort($mapIds);

        $this->assertEquals($listIds, $mapIds);
    }

    /** @test */
    public function test_cache_performance_with_repeated_requests()
    {
        $filters = ['content_type' => 'pet_sitter', 'location' => 'Olsztyn'];

        // First request - should populate cache
        $start1 = microtime(true);
        $response1 = $this->getJson('/api/search?'.http_build_query($filters));
        $time1 = microtime(true) - $start1;

        $response1->assertStatus(200);

        // Second request - should use cache (should be faster)
        $start2 = microtime(true);
        $response2 = $this->getJson('/api/search?'.http_build_query($filters));
        $time2 = microtime(true) - $start2;

        $response2->assertStatus(200);

        // Cache should improve performance (allow for timing variations)
        $this->assertLessThan($time1 * 3, $time2 * 10); // Very lenient timing check

        // Results should be identical
        $this->assertEquals($response1->json('data'), $response2->json('data'));
    }

    /**
     * Create test data for comprehensive filter testing
     */
    private function createTestMapItems(): void
    {
        // Create pet sitters in Olsztyn with various prices and ratings
        MapItem::factory()->count(15)->create([
            'content_type' => 'pet_sitter',
            'city' => 'Olsztyn',
            'latitude' => 53.7766839,
            'longitude' => 20.476507,
            'status' => 'published',
            'price_from' => fake()->randomFloat(2, 20, 60),
            'rating_avg' => fake()->randomFloat(1, 3.5, 5.0),
            'is_featured' => fake()->boolean(30), // 30% featured
            'title' => fake()->randomElement([
                'Opieka nad kotami',
                'Spacery z psami',
                'Pet sitting w domu',
                'Opieka nad zwierzętami',
            ]),
            'category_name' => fake()->randomElement([
                'Opieka w domu właściciela',
                'Pet Sitter - Psy',
                'Pet Sitter - Koty',
            ]),
        ]);

        // Create services in different cities
        MapItem::factory()->count(10)->create([
            'content_type' => 'service',
            'city' => fake()->randomElement(['Warszawa', 'Kraków', 'Gdańsk']),
            'status' => 'published',
            'price_from' => fake()->randomFloat(2, 50, 150),
            'rating_avg' => fake()->randomFloat(1, 3.0, 5.0),
        ]);

        // Create some items outside Olsztyn but in general area
        MapItem::factory()->count(5)->create([
            'content_type' => 'pet_sitter',
            'city' => 'Warszawa',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'status' => 'published',
            'price_from' => fake()->randomFloat(2, 30, 80),
            'rating_avg' => fake()->randomFloat(1, 4.0, 5.0),
        ]);

        // Create some draft/inactive items that shouldn't appear in results
        MapItem::factory()->count(3)->create([
            'status' => 'draft',
        ]);
    }
}
