<?php

use App\Livewire\Search\SearchFilters;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test service categories
    ServiceCategory::factory()->create([
        'name' => 'Dog Walking',
        'slug' => 'dog-walking',
        'icon' => '🐕',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    ServiceCategory::factory()->create([
        'name' => 'Cat Sitting',
        'slug' => 'cat-sitting',
        'icon' => '🐱',
        'is_active' => true,
        'sort_order' => 2,
    ]);
});

test('component renders successfully', function () {
    Livewire::test(SearchFilters::class)
        ->assertStatus(200)
        ->assertSee('Szukaj opiekuna lub usługi')
        ->assertSee('Lokalizacja')
        ->assertSee('Kategoria usługi')
        ->assertSee('Typ zwierzęcia');
});

test('can initialize component with parameters', function () {
    Livewire::test(SearchFilters::class)
        ->set('search_term', 'test')
        ->set('location', 'Warsaw')
        ->set('category_id', '1')
        ->set('pet_type', 'dog')
        ->assertSet('search_term', 'test')
        ->assertSet('location', 'Warsaw')
        ->assertSet('category_id', '1')
        ->assertSet('pet_type', 'dog');
});

test('can update search term and dispatch event', function () {
    Livewire::test(SearchFilters::class)
        ->set('search_term', 'dog walker')
        ->assertSet('search_term', 'dog walker')
        ->assertDispatched('filters-updated');
});

test('can update location and dispatch event', function () {
    Livewire::test(SearchFilters::class)
        ->set('location', 'Kraków')
        ->assertSet('location', 'Kraków')
        ->assertDispatched('filters-updated');
});

test('can select category and dispatch event', function () {
    $category = ServiceCategory::first();

    Livewire::test(SearchFilters::class)
        ->set('category_id', $category->id)
        ->assertSet('category_id', $category->id)
        ->assertDispatched('filters-updated');
});

test('can select pet type and dispatch event', function () {
    Livewire::test(SearchFilters::class)
        ->set('pet_type', 'cat')
        ->assertSet('pet_type', 'cat')
        ->assertDispatched('filters-updated');
});

test('can set price range and dispatch event', function () {
    Livewire::test(SearchFilters::class)
        ->set('min_price', '50')
        ->set('max_price', '200')
        ->assertSet('min_price', '50')
        ->assertSet('max_price', '200')
        ->assertDispatched('filters-updated');
});

test('can toggle advanced filters', function () {
    Livewire::test(SearchFilters::class)
        ->assertSet('show_filters', false)
        ->call('\$toggle', 'show_filters')
        ->assertSet('show_filters', true);
});

test('can clear all filters', function () {
    Livewire::test(SearchFilters::class)
        ->set('search_term', 'test')
        ->set('location', 'Warsaw')
        ->set('category_id', '1')
        ->set('pet_type', 'dog')
        ->set('min_price', '50')
        ->set('verified_only', true)
        ->call('clearFilters')
        ->assertSet('search_term', '')
        ->assertSet('location', '')
        ->assertSet('category_id', '')
        ->assertSet('pet_type', '')
        ->assertSet('min_price', '')
        ->assertSet('verified_only', false)
        ->assertDispatched('filters-updated');
});

test('calculates active filters count correctly', function () {
    $component = Livewire::test(SearchFilters::class);

    // No filters
    expect($component->get('active_filters_count'))->toBe(0);

    // Add some filters
    $component
        ->set('search_term', 'test')
        ->set('location', 'Warsaw')
        ->set('verified_only', true);

    expect($component->get('active_filters_count'))->toBe(3);

    // Add price range (counts as one filter)
    $component
        ->set('min_price', '50')
        ->set('max_price', '200');

    expect($component->get('active_filters_count'))->toBe(4);
});

test('can set filters via listener', function () {
    $filters = [
        'search_term' => 'test search',
        'location' => 'Gdansk',
        'category_id' => '2',
        'pet_type' => 'cat',
        'verified_only' => true,
    ];

    Livewire::test(SearchFilters::class)
        ->call('setFilters', $filters)
        ->assertSet('search_term', 'test search')
        ->assertSet('location', 'Gdansk')
        ->assertSet('category_id', '2')
        ->assertSet('pet_type', 'cat')
        ->assertSet('verified_only', true);
});

test('can reset filters via listener', function () {
    Livewire::test(SearchFilters::class)
        ->set('search_term', 'test')
        ->set('location', 'Warsaw')
        ->call('resetFilters')
        ->assertSet('search_term', '')
        ->assertSet('location', '');
});

test('displays categories correctly', function () {
    Livewire::test(SearchFilters::class)
        ->assertSee('🐕 Dog Walking')
        ->assertSee('🐱 Cat Sitting');
});

test('advanced filters work correctly', function () {
    Livewire::test(SearchFilters::class)
        ->set('pet_size', 'large')
        ->set('service_type', 'home_service')
        ->set('min_rating', '4')
        ->set('available_date', '2025-12-25')
        ->set('experience_years', '5')
        ->set('has_insurance', true)
        ->assertSet('pet_size', 'large')
        ->assertSet('service_type', 'home_service')
        ->assertSet('min_rating', '4')
        ->assertSet('available_date', '2025-12-25')
        ->assertSet('experience_years', '5')
        ->assertSet('has_insurance', true)
        ->assertDispatched('filters-updated');
});

test('radius slider works correctly', function () {
    Livewire::test(SearchFilters::class)
        ->assertSet('radius', 10) // default
        ->set('radius', 25)
        ->assertSet('radius', 25)
        ->assertDispatched('filters-updated');
});

test('sort by selection works correctly', function () {
    Livewire::test(SearchFilters::class)
        ->assertSet('sort_by', 'relevance') // default
        ->set('sort_by', 'price_low')
        ->assertSet('sort_by', 'price_low')
        ->assertDispatched('filters-updated');
});
