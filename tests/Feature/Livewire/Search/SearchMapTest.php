<?php

use App\Livewire\Search\SearchMap;
use App\Models\{User, UserProfile, Service, ServiceCategory, Location};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = ServiceCategory::factory()->create([
        'name' => 'Dog Walking',
        'slug' => 'dog-walking',
        'is_active' => true,
    ]);

    $this->sitter = User::factory()->create(['name' => 'Map Sitter']);

    UserProfile::create([
        'user_id' => $this->sitter->id,
        'role' => 'sitter',
        'first_name' => 'Map',
        'last_name' => 'Sitter',
    ]);

    // Create location with coordinates
    Location::create([
        'user_id' => $this->sitter->id,
        'city' => 'Warsaw',
        'street' => 'Map St 123',
        'postal_code' => '00-001',
        'latitude' => 52.2297,
        'longitude' => 21.0122,
    ]);

    $this->service = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Map Dog Walking',
        'description' => 'Dog walking on map',
        'is_active' => true,
    ]);
});

test('component renders successfully', function () {
    Livewire::test(SearchMap::class)
        ->assertStatus(200)
        ->assertSet('show_map', false)
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('location_detected', false)
        ->assertSet('radius', 10);
});

test('can toggle map visibility', function () {
    Livewire::test(SearchMap::class)
        ->assertSet('show_map', false)
        ->call('toggleMap')
        ->assertSet('show_map', true)
        ->assertDispatched('initialize-map');
});

test('updates filters correctly', function () {
    $filters = [
        'search_term' => 'dog',
        'category_id' => $this->category->id,
        'radius' => 15,
    ];

    Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters)
        ->assertSet('filters', $filters)
        ->assertSet('radius', 15);
});

test('sets location correctly', function () {
    $lat = 52.2297;
    $lng = 21.0122;
    $address = 'Warsaw, Poland';

    Livewire::test(SearchMap::class)
        ->call('setLocation', $lat, $lng, $address)
        ->assertSet('latitude', $lat)
        ->assertSet('longitude', $lng)
        ->assertSet('location_detected', true)
        ->assertDispatched('location-detected', [
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
        ]);
});

test('dispatches detect location event', function () {
    Livewire::test(SearchMap::class)
        ->call('detectLocation')
        ->assertDispatched('detect-location');
});

test('returns map services with location data', function () {
    $filters = [
        'search_term' => 'dog',
        'category_id' => $this->category->id,
    ];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    $mapServices = $component->get('mapServices');
    expect($mapServices->count())->toBeGreaterThan(0);
    expect($mapServices->first()->id)->toBe($this->service->id);
});

test('returns empty collection when no filters', function () {
    $component = Livewire::test(SearchMap::class);
    $mapServices = $component->get('mapServices');
    expect($mapServices->count())->toBe(0);
});

test('filters services by search term on map', function () {
    // Create another service that shouldn't match
    $otherService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Cat Grooming',
        'description' => 'Cat grooming service',
        'is_active' => true,
    ]);

    $filters = ['search_term' => 'dog'];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    $mapServices = $component->get('mapServices');
    expect($mapServices->contains('id', $this->service->id))->toBeTrue();
    expect($mapServices->contains('id', $otherService->id))->toBeFalse();
});

test('filters services by category on map', function () {
    $otherCategory = ServiceCategory::factory()->create([
        'name' => 'Cat Sitting',
        'slug' => 'cat-sitting',
        'is_active' => true,
    ]);

    $otherService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $otherCategory->id,
        'title' => 'Cat Service',
        'is_active' => true,
    ]);

    $filters = ['category_id' => $this->category->id];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    $mapServices = $component->get('mapServices');
    expect($mapServices->contains('id', $this->service->id))->toBeTrue();
    expect($mapServices->contains('id', $otherService->id))->toBeFalse();
});

test('limits map services to 50 for performance', function () {
    // Create many services
    Service::factory()->count(60)->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);

    $filters = ['category_id' => $this->category->id];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    $mapServices = $component->get('mapServices');
    expect($mapServices->count())->toBeLessThanOrEqual(50);
});

test('only includes services with location coordinates', function () {
    // Create sitter without coordinates
    $sitterNoCoords = User::factory()->create();
    UserProfile::create([
        'user_id' => $sitterNoCoords->id,
        'role' => 'sitter',
        'first_name' => 'No',
        'last_name' => 'Coords',
    ]);
    Location::create([
        'user_id' => $sitterNoCoords->id,
        'city' => 'Warsaw',
        'street' => 'No Coords St',
        'postal_code' => '00-002',
        'latitude' => null,
        'longitude' => null,
    ]);
    $serviceNoCoords = Service::factory()->create([
        'sitter_id' => $sitterNoCoords->id,
        'category_id' => $this->category->id,
        'title' => 'Service Without Coords',
        'is_active' => true,
    ]);

    $filters = ['category_id' => $this->category->id];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    $mapServices = $component->get('mapServices');
    expect($mapServices->contains('id', $this->service->id))->toBeTrue();
    expect($mapServices->contains('id', $serviceNoCoords->id))->toBeFalse();
});

test('responds to map-toggled event', function () {
    $component = Livewire::test(SearchMap::class)
        ->assertSet('show_map', false);

    // Simulate receiving map-toggled event
    $component->call('toggleMap')
        ->assertSet('show_map', true);
});

test('responds to filters-updated event', function () {
    $newFilters = [
        'search_term' => 'updated search',
        'radius' => 20,
    ];

    Livewire::test(SearchMap::class)
        ->call('updateFilters', $newFilters)
        ->assertSet('filters', $newFilters)
        ->assertSet('radius', 20);
});

test('can update map bounds', function () {
    $bounds = [52.0, 20.0, 53.0, 22.0]; // [south, west, north, east]

    Livewire::test(SearchMap::class)
        ->call('updateMapBounds', $bounds)
        ->assertSet('map_bounds', $bounds);
});

test('can update zoom level and toggle clustering', function () {
    Livewire::test(SearchMap::class)
        ->assertSet('cluster_mode', false)
        ->call('updateZoomLevel', 8)
        ->assertSet('zoom_level', 8)
        ->assertSet('cluster_mode', true); // Should enable clustering at zoom < 10

    // Test higher zoom level (no clustering)
    Livewire::test(SearchMap::class)
        ->call('updateZoomLevel', 15)
        ->assertSet('zoom_level', 15)
        ->assertSet('cluster_mode', false);
});

test('can toggle content types', function () {
    $component = Livewire::test(SearchMap::class);

    // Initially should have 'service' selected
    expect($component->get('selected_content_types'))->toContain('service');

    // Toggle off service
    $component->call('toggleContentType', 'service');
    expect($component->get('selected_content_types'))->not->toContain('service');

    // Toggle on event
    $component->call('toggleContentType', 'event');
    expect($component->get('selected_content_types'))->toContain('event');
});

test('returns available content types with proper structure', function () {
    $component = Livewire::test(SearchMap::class);
    $contentTypes = $component->get('availableContentTypes');

    expect($contentTypes)->toBeArray();
    expect($contentTypes)->toHaveKey('service');
    expect($contentTypes['service'])->toHaveKeys(['name', 'icon', 'color']);
    expect($contentTypes['service']['name'])->toBe('Usługi');
});

test('maps search type to content types correctly', function () {
    $filters = ['search_for' => 'events'];

    $component = Livewire::test(SearchMap::class)
        ->call('updateFilters', $filters);

    expect($component->get('selected_content_types'))->toContain('event');
});

test('returns map statistics correctly', function () {
    // Create some MapItem records for testing
    $component = Livewire::test(SearchMap::class);
    $stats = $component->get('mapStatistics');

    expect($stats)->toBeArray();
    expect($stats)->toHaveKeys(['total_items', 'by_content_type', 'featured_count', 'urgent_count']);
    expect($stats['total_items'])->toBeInt();
});

test('handles empty map bounds gracefully', function () {
    Livewire::test(SearchMap::class)
        ->call('updateMapBounds', [])
        ->assertSet('map_bounds', []);
});

test('default zoom level is 12', function () {
    Livewire::test(SearchMap::class)
        ->assertSet('zoom_level', 12);
});

test('default selected content types include service', function () {
    $component = Livewire::test(SearchMap::class);
    expect($component->get('selected_content_types'))->toEqual(['service']);
});
