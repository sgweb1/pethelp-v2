<?php

use App\Livewire\Search\SearchResults;
use App\Models\{User, UserProfile, Service, ServiceCategory, Location};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test data
    $this->category = ServiceCategory::factory()->create([
        'name' => 'Dog Walking',
        'slug' => 'dog-walking',
        'is_active' => true,
    ]);

    $this->sitter = User::factory()->create(['name' => 'John Doe']);

    UserProfile::create([
        'user_id' => $this->sitter->id,
        'role' => 'sitter',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'is_verified' => true,
        'experience_years' => 5,
        'has_insurance' => true,
    ]);

    Location::create([
        'user_id' => $this->sitter->id,
        'city' => 'Warsaw',
        'street' => 'Main St 123',
        'postal_code' => '00-001',
        'latitude' => 52.2297,
        'longitude' => 21.0122,
    ]);

    $this->service = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Professional Dog Walking',
        'description' => 'Experienced dog walker in Warsaw',
        'price_per_hour' => 50,
        'price_per_day' => 200,
        'is_active' => true,
    ]);
});

test('component renders successfully', function () {
    Livewire::test(SearchResults::class)
        ->assertStatus(200);
});

test('displays services when no filters applied', function () {
    Livewire::test(SearchResults::class)
        ->assertSeeHtml($this->service->title);
});

test('updates results when filters are applied', function () {
    $filters = [
        'search_term' => 'dog',
        'location' => 'Warsaw',
        'category_id' => $this->category->id,
    ];

    Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters)
        ->assertSet('filters', $filters)
        ->assertSeeHtml($this->service->title);
});

test('filters by search term correctly', function () {
    // Create another service that shouldn't match
    $otherService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Cat Grooming Service',
        'description' => 'Professional cat grooming',
        'is_active' => true,
    ]);

    $filters = ['search_term' => 'dog'];

    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->contains('id', $this->service->id))->toBeTrue();
    expect($services->contains('id', $otherService->id))->toBeFalse();
});

test('filters by location correctly', function () {
    // Create sitter in different city
    $otherSitter = User::factory()->create();
    UserProfile::create([
        'user_id' => $otherSitter->id,
        'role' => 'sitter',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);
    Location::create([
        'user_id' => $otherSitter->id,
        'city' => 'Krakow',
        'street' => 'Other St 456',
        'postal_code' => '30-001',
    ]);
    $otherService = Service::factory()->create([
        'sitter_id' => $otherSitter->id,
        'category_id' => $this->category->id,
        'title' => 'Another Dog Walking',
        'is_active' => true,
    ]);

    $filters = ['location' => 'Warsaw'];

    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->contains('id', $this->service->id))->toBeTrue();
    expect($services->contains('id', $otherService->id))->toBeFalse();
});

test('filters by category correctly', function () {
    $otherCategory = ServiceCategory::factory()->create([
        'name' => 'Cat Sitting',
        'slug' => 'cat-sitting',
        'is_active' => true,
    ]);

    $otherService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $otherCategory->id,
        'title' => 'Cat Sitting Service',
        'is_active' => true,
    ]);

    $filters = ['category_id' => $this->category->id];

    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->contains('id', $this->service->id))->toBeTrue();
    expect($services->contains('id', $otherService->id))->toBeFalse();
});

test('filters by verified sitters only', function () {
    // Create unverified sitter
    $unverifiedSitter = User::factory()->create();
    UserProfile::create([
        'user_id' => $unverifiedSitter->id,
        'role' => 'sitter',
        'first_name' => 'Unverified',
        'last_name' => 'Sitter',
        'is_verified' => false,
    ]);
    $unverifiedService = Service::factory()->create([
        'sitter_id' => $unverifiedSitter->id,
        'category_id' => $this->category->id,
        'title' => 'Unverified Service',
        'is_active' => true,
    ]);

    $filters = ['verified_only' => true];

    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->contains('id', $this->service->id))->toBeTrue();
    expect($services->contains('id', $unverifiedService->id))->toBeFalse();
});

test('pagination works correctly', function () {
    // Create multiple services to test pagination
    Service::factory()->count(15)->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);

    $component = Livewire::test(SearchResults::class);

    $services = $component->get('services');
    expect($services->count())->toBe(12); // Default per page
    expect($services->hasMorePages())->toBeTrue();
});

test('results count is calculated correctly', function () {
    // Create additional services
    Service::factory()->count(5)->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);

    $component = Livewire::test(SearchResults::class);
    expect($component->get('results_count'))->toBe(6); // 1 + 5 created
});

test('resets page when filters are updated', function () {
    Livewire::test(SearchResults::class)
        ->set('page', 2)
        ->call('updateFilters', ['search_term' => 'test'])
        ->assertSet('page', 1);
});

test('sorting works correctly', function () {
    // Create services with different prices
    $expensiveService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Expensive Service',
        'price_per_hour' => 100,
        'is_active' => true,
    ]);

    $cheapService = Service::factory()->create([
        'sitter_id' => $this->sitter->id,
        'category_id' => $this->category->id,
        'title' => 'Cheap Service',
        'price_per_hour' => 25,
        'is_active' => true,
    ]);

    // Test price low to high sorting
    $filters = ['sort_by' => 'price_low'];
    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->first()->id)->toBe($cheapService->id);
});

test('handles empty results gracefully', function () {
    // Filter that should return no results
    $filters = ['search_term' => 'nonexistent service'];

    $component = Livewire::test(SearchResults::class)
        ->call('updateFilters', $filters);

    $services = $component->get('services');
    expect($services->count())->toBe(0);
    expect($component->get('results_count'))->toBe(0);
});
