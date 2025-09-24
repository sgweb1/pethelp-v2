<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchApiTest extends TestCase
{
    /** @test */
    public function test_search_api_returns_valid_response()
    {
        $response = $this->getJson('/api/search?limit=5');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'title',
                            'content_type',
                            'location' => [
                                'city',
                                'coordinates' => [
                                    'lat',
                                    'lng',
                                ],
                            ],
                            'price' => [
                                'from',
                                'currency',
                            ],
                            'quality' => [
                                'rating',
                            ],
                            'flags' => [
                                'featured',
                                'urgent',
                            ],
                        ],
                    ],
                    'pagination',
                ],
                'meta',
            ]);
    }

    /** @test */
    public function test_content_type_filter_works()
    {
        $response = $this->getJson('/api/search?content_type=pet_sitter&limit=10');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertEquals('pet_sitter', $item['content_type']);
        }
    }

    /** @test */
    public function test_location_filter_works()
    {
        $response = $this->getJson('/api/search?location=Olsztyn&limit=10');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertStringContainsStringIgnoringCase('Olsztyn', $item['location']['city']);
        }
    }

    /** @test */
    public function test_price_filter_works()
    {
        $minPrice = 25;
        $maxPrice = 50;

        $response = $this->getJson("/api/search?min_price={$minPrice}&max_price={$maxPrice}&limit=10");

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            if (isset($item['price']['from'])) {
                $price = (float) $item['price']['from'];
                $this->assertGreaterThanOrEqual($minPrice, $price);
                $this->assertLessThanOrEqual($maxPrice, $price);
            }
        }
    }

    /** @test */
    public function test_rating_filter_works()
    {
        $minRating = 4.0;

        $response = $this->getJson("/api/search?min_rating={$minRating}&limit=10");

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            if (isset($item['quality']['rating'])) {
                $this->assertGreaterThanOrEqual($minRating, $item['quality']['rating']);
            }
        }
    }

    /** @test */
    public function test_featured_filter_works()
    {
        $response = $this->getJson('/api/search?featured_only=1&limit=10');

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertTrue($item['flags']['featured']);
        }
    }

    /** @test */
    public function test_map_format_returns_markers()
    {
        $response = $this->getJson('/api/search?format=map&limit=10');

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
                            'content_type',
                        ],
                    ],
                    'bounds',
                    'clusters',
                ],
            ]);
    }

    /** @test */
    public function test_invalid_content_type_returns_validation_error()
    {
        $response = $this->getJson('/api/search?content_type=invalid_type');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content_type']);
    }

    /** @test */
    public function test_invalid_price_returns_validation_error()
    {
        $response = $this->getJson('/api/search?min_price=-10&max_price=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_price', 'max_price']);
    }

    /** @test */
    public function test_search_with_multiple_filters()
    {
        $response = $this->getJson('/api/search?'.http_build_query([
            'content_type' => 'pet_sitter',
            'location' => 'Olsztyn',
            'min_price' => 20,
            'max_price' => 60,
            'limit' => 10,
        ]));

        $response->assertStatus(200);
        $items = $response->json('data.items');

        foreach ($items as $item) {
            $this->assertEquals('pet_sitter', $item['content_type']);
            $this->assertStringContainsStringIgnoringCase('Olsztyn', $item['location']['city']);

            if (isset($item['price']['from'])) {
                $price = (float) $item['price']['from'];
                $this->assertGreaterThanOrEqual(20, $price);
                $this->assertLessThanOrEqual(60, $price);
            }
        }
    }

    /** @test */
    public function test_data_consistency_between_list_and_map()
    {
        $filters = [
            'content_type' => 'pet_sitter',
            'location' => 'Olsztyn',
            'limit' => 5,
        ];

        // Get list format
        $listResponse = $this->getJson('/api/search?'.http_build_query(array_merge($filters, ['format' => 'list'])));
        $listResponse->assertStatus(200);
        $listItems = $listResponse->json('data.items');

        // Get map format
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
}
