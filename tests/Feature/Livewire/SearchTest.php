<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Search;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $sitter1;
    protected User $sitter2;
    protected User $sitter3;
    protected ServiceCategory $dogWalkingCategory;
    protected ServiceCategory $catSittingCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create service categories
        $this->dogWalkingCategory = ServiceCategory::factory()->create([
            'name' => 'Dog Walking',
            'slug' => 'dog-walking',
        ]);

        $this->catSittingCategory = ServiceCategory::factory()->create([
            'name' => 'Cat Sitting',
            'slug' => 'cat-sitting',
        ]);

        // Create sitters with profiles and locations
        $this->sitter1 = User::factory()->create(['name' => 'annakowalska']);
        UserProfile::create([
            'user_id' => $this->sitter1->id,
            'role' => 'sitter',
            'first_name' => 'Anna',
            'last_name' => 'Kowalska',
        ]);
        Location::factory()->create([
            'user_id' => $this->sitter1->id,
            'name' => 'Dom',
            'city' => 'Warszawa',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_primary' => true,
        ]);

        $this->sitter2 = User::factory()->create(['name' => 'piotrnowak']);
        UserProfile::create([
            'user_id' => $this->sitter2->id,
            'role' => 'sitter',
            'first_name' => 'Piotr',
            'last_name' => 'Nowak',
        ]);
        Location::factory()->create([
            'user_id' => $this->sitter2->id,
            'name' => 'Praca',
            'city' => 'Warszawa',
            'latitude' => 52.2500,
            'longitude' => 21.0300,
            'is_primary' => true,
        ]);

        $this->sitter3 = User::factory()->create(['name' => 'mariawisniewska']);
        UserProfile::create([
            'user_id' => $this->sitter3->id,
            'role' => 'sitter',
            'first_name' => 'Maria',
            'last_name' => 'Wiśniewska',
        ]);
        Location::factory()->create([
            'user_id' => $this->sitter3->id,
            'name' => 'Dom',
            'city' => 'Kraków',
            'latitude' => 50.0647,
            'longitude' => 19.9450,
            'is_primary' => true,
        ]);

        // Create services
        Service::factory()->create([
            'sitter_id' => $this->sitter1->id,
            'category_id' => $this->dogWalkingCategory->id,
            'title' => 'Wyprowadzanie psów w Warszawie',
            'price_per_hour' => 25.00,
            'pet_types' => ['psy'],
        ]);

        Service::factory()->create([
            'sitter_id' => $this->sitter2->id,
            'category_id' => $this->dogWalkingCategory->id,
            'title' => 'Opieka nad psami',
            'price_per_hour' => 30.00,
            'pet_types' => ['psy'],
        ]);

        Service::factory()->create([
            'sitter_id' => $this->sitter3->id,
            'category_id' => $this->catSittingCategory->id,
            'title' => 'Opieka nad kotami',
            'price_per_hour' => 20.00,
            'pet_types' => ['koty'],
        ]);
    }

    public function test_component_loads_with_default_values(): void
    {
        Livewire::test(Search::class)
            ->assertSet('search_term', '')
            ->assertSet('location', '')
            ->assertSet('service_type', '')
            ->assertSet('sort_by', 'relevance')
            ->assertStatus(200);
    }

    public function test_can_search_by_location(): void
    {
        $livewire = Livewire::test(Search::class)
            ->set('location', 'Warszawa');

        // Get services and verify count
        $services = $livewire->get('services');
        $this->assertCount(2, $services->items()); // 2 services in Warsaw

        $livewire->assertSee('Wyprowadzanie psów w Warszawie')
                ->assertSee('Opieka nad psami');
    }

    public function test_can_filter_by_category(): void
    {
        $livewire = Livewire::test(Search::class)
            ->set('category_id', $this->dogWalkingCategory->id);

        $services = $livewire->get('services');
        $this->assertCount(2, $services->items()); // 2 dog walking services

        $livewire->assertSee('Wyprowadzanie psów w Warszawie')
                ->assertSee('Opieka nad psami')
                ->assertDontSee('Opieka nad kotami');
    }

    public function test_can_filter_by_price_range(): void
    {
        $livewire = Livewire::test(Search::class)
            ->set('max_price', 25);

        $services = $livewire->get('services');
        $this->assertCount(2, $services->items()); // Anna (25.00) and Maria (20.00)

        $livewire->assertSee('Wyprowadzanie psów w Warszawie') // 25.00
                ->assertSee('Opieka nad kotami') // 20.00
                ->assertDontSee('Opieka nad psami'); // 30.00
    }

    public function test_can_clear_filters(): void
    {
        Livewire::test(Search::class)
            ->set('location', 'Warszawa')
            ->set('category_id', $this->dogWalkingCategory->id)
            ->set('max_price', 25)
            ->call('clearFilters')
            ->assertSet('location', '')
            ->assertSet('category_id', '')
            ->assertSet('max_price', '');
    }

    public function test_can_detect_location(): void
    {
        Livewire::test(Search::class)
            ->call('detectLocation')
            ->assertDispatched('detect-location');
    }

    public function test_can_set_location_coordinates(): void
    {
        Livewire::test(Search::class)
            ->call('setLocation', 52.2297, 21.0122, 'Warszawa, Poland')
            ->assertSet('latitude', 52.2297)
            ->assertSet('longitude', 21.0122)
            ->assertSet('location', 'Warszawa, Poland')
            ->assertSet('location_detected', true);
    }

    public function test_search_handles_empty_results(): void
    {
        $livewire = Livewire::test(Search::class)
            ->set('location', 'Nieistniejące Miasto');

        $services = $livewire->get('services');
        $this->assertCount(0, $services->items());
    }

    public function test_active_filters_count(): void
    {
        $livewire = Livewire::test(Search::class)
            ->set('location', 'Warszawa')
            ->set('category_id', $this->dogWalkingCategory->id)
            ->set('max_price', 25);

        $this->assertEquals(3, $livewire->get('activeFiltersCount'));
    }
}