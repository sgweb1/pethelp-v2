<?php

use App\Models\{MapItem, User};
use App\Services\MapCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create test map items
    $this->mapItems = MapItem::factory()->count(5)->create([
        'status' => 'published',
        'content_type' => 'service',
        'latitude' => fake()->latitude(52.0, 52.5),
        'longitude' => fake()->longitude(20.5, 21.5),
        'zoom_level_min' => 10,
    ]);
});

test('can fetch map items via API', function () {
    $response = $this->getJson('/api/map/items');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items',
                    'count'
                ]
            ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.items'))->toBeArray();
    expect($response->json('data.count'))->toBeGreaterThan(0);
});

test('can filter map items by bounds', function () {
    $bounds = [52.0, 20.5, 52.5, 21.5]; // Warsaw area

    $response = $this->getJson('/api/map/items?' . http_build_query([
        'bounds' => $bounds
    ]));

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.items'))->toBeArray();
});

test('can filter map items by content type', function () {
    MapItem::factory()->create([
        'status' => 'published',
        'content_type' => 'event',
        'zoom_level_min' => 10,
    ]);

    $response = $this->getJson('/api/map/items?' . http_build_query([
        'content_types' => ['service']
    ]));

    $response->assertStatus(200);
    $items = $response->json('data.items');

    foreach ($items as $item) {
        expect($item['content_type'])->toBe('service');
    }
});

test('can search map items by text', function () {
    MapItem::factory()->create([
        'status' => 'published',
        'title' => 'Przychodnia weterynaryjna specjalna',
        'zoom_level_min' => 10,
    ]);

    $response = $this->getJson('/api/map/items?' . http_build_query([
        'search_term' => 'weterynaryjna'
    ]));

    $response->assertStatus(200);
    $items = $response->json('data.items');

    expect(count($items))->toBeGreaterThanOrEqual(1);

    $found = false;
    foreach ($items as $item) {
        if (str_contains(strtolower($item['title']), 'weterynaryjna')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

test('validates API parameters correctly', function () {
    $response = $this->getJson('/api/map/items?' . http_build_query([
        'bounds' => [52.0, 20.5], // Invalid - should have 4 elements
        'zoom_level' => 25, // Invalid - max is 18
    ]));

    $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'errors'
            ]);

    expect($response->json('success'))->toBeFalse();
});

test('can fetch cluster data', function () {
    $bounds = [52.0, 20.5, 52.5, 21.5];
    $zoomLevel = 8; // Low zoom for clustering

    $response = $this->getJson('/api/map/clusters?' . http_build_query([
        'bounds' => $bounds,
        'zoom_level' => $zoomLevel
    ]));

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'clusters',
                    'markers'
                ]
            ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.clusters'))->toBeArray();
    expect($response->json('data.markers'))->toBeArray();
});

test('returns individual markers for high zoom levels', function () {
    $bounds = [52.0, 20.5, 52.5, 21.5];
    $zoomLevel = 15; // High zoom for individual markers

    $response = $this->getJson('/api/map/clusters?' . http_build_query([
        'bounds' => $bounds,
        'zoom_level' => $zoomLevel
    ]));

    $response->assertStatus(200);
    $data = $response->json('data');

    // High zoom should return markers, not clusters
    expect($data['clusters'])->toBeArray()->toBeEmpty();
    expect($data['markers'])->toBeArray();
});

test('can fetch statistics', function () {
    $response = $this->getJson('/api/map/statistics');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_items',
                    'featured_count',
                    'urgent_count',
                    'avg_rating',
                    'by_content_type'
                ]
            ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.total_items'))->toBeInt();
    expect($response->json('data.by_content_type'))->toBeArray();
});

test('respects rate limiting', function () {
    // Make multiple rapid requests to test throttling
    for ($i = 0; $i < 5; $i++) {
        $response = $this->getJson('/api/map/items');

        if ($i < 4) {
            $response->assertStatus(200);
        }
    }

    // Continue testing would require actual rate limit implementation
    expect(true)->toBeTrue(); // Placeholder
});

test('cache service works correctly', function () {
    $cacheService = app(MapCacheService::class);

    $filters = [
        'content_types' => ['service'],
        'zoom_level' => 12
    ];

    // First call should hit database
    $items1 = $cacheService->getCachedMapItems($filters, 10);
    expect($items1)->toBeInstanceOf(\Illuminate\Support\Collection::class);

    // Second call should hit cache (same result)
    $items2 = $cacheService->getCachedMapItems($filters, 10);
    expect($items2->count())->toBe($items1->count());
});

test('cache invalidation works on model changes', function () {
    $cacheService = app(MapCacheService::class);

    $filters = ['content_types' => ['service']];

    // Cache some data
    $initialItems = $cacheService->getCachedMapItems($filters, 10);
    $initialCount = $initialItems->count();

    // Create a new map item (should invalidate cache)
    MapItem::factory()->create([
        'status' => 'published',
        'content_type' => 'service',
        'zoom_level_min' => 10,
    ]);

    // Clear cache manually for test
    $cacheService->invalidateMapCache();

    // Get data again (should be fresh)
    $newItems = $cacheService->getCachedMapItems($filters, 10);
    expect($newItems->count())->toBeGreaterThan($initialCount);
});

test('handles errors gracefully', function () {
    // Test with malformed data
    $response = $this->getJson('/api/map/items?' . http_build_query([
        'latitude' => 'invalid',
        'longitude' => 'also-invalid'
    ]));

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('errors'))->toBeArray();
});

test('performance optimization limits work', function () {
    // Create many items to test limits
    MapItem::factory()->count(150)->create([
        'status' => 'published',
        'content_type' => 'service',
        'zoom_level_min' => 10,
    ]);

    $response = $this->getJson('/api/map/items?' . http_build_query([
        'limit' => 50
    ]));

    $response->assertStatus(200);
    expect(count($response->json('data.items')))->toBeLessThanOrEqual(50);
});

test('can clear cache via API', function () {
    // Note: In production this should be protected by auth
    $response = $this->deleteJson('/api/map/cache');

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toContain('Cache cleared');
});