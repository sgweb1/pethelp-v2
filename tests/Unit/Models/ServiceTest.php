<?php

namespace Tests\Unit\Models;

use App\Models\Service;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_has_fillable_attributes(): void
    {
        $service = new Service();

        $expected = [
            'sitter_id',
            'category_id',
            'title',
            'description',
            'price_per_hour',
            'price_per_day',
            'pet_types',
            'pet_sizes',
            'home_service',
            'sitter_home',
            'max_pets',
            'is_active',
        ];

        $this->assertEquals($expected, $service->getFillable());
    }

    public function test_service_casts_attributes_correctly(): void
    {
        $service = Service::factory()->create([
            'pet_types' => ['dogs', 'cats'],
            'pet_sizes' => ['small', 'medium'],
            'home_service' => true,
            'sitter_home' => false,
            'is_active' => true,
            'price_per_hour' => 25.50,
            'price_per_day' => 150.75,
        ]);

        $this->assertIsArray($service->pet_types);
        $this->assertEquals(['dogs', 'cats'], $service->pet_types);

        $this->assertIsArray($service->pet_sizes);
        $this->assertEquals(['small', 'medium'], $service->pet_sizes);

        $this->assertIsBool($service->home_service);
        $this->assertTrue($service->home_service);

        $this->assertIsBool($service->sitter_home);
        $this->assertFalse($service->sitter_home);

        $this->assertIsBool($service->is_active);
        $this->assertTrue($service->is_active);

        $this->assertEquals(25.50, $service->price_per_hour);
        $this->assertEquals(150.75, $service->price_per_day);
    }

    public function test_service_belongs_to_sitter(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->withSitter($user->id)->create();

        $this->assertInstanceOf(User::class, $service->sitter);
        $this->assertEquals($user->id, $service->sitter->id);
    }

    public function test_service_belongs_to_category(): void
    {
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->withCategory($category->id)->create();

        $this->assertInstanceOf(ServiceCategory::class, $service->category);
        $this->assertEquals($category->id, $service->category->id);
    }

    public function test_service_has_bookings_relationship(): void
    {
        $service = Service::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $service->bookings);
        $this->assertCount(0, $service->bookings);
    }

    public function test_service_active_scope(): void
    {
        Service::factory()->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);

        $activeServices = Service::active()->get();

        $this->assertCount(1, $activeServices);
        $this->assertTrue($activeServices->first()->is_active);
    }

    public function test_service_by_pet_type_scope(): void
    {
        Service::factory()->create(['pet_types' => ['dogs', 'cats']]);
        Service::factory()->create(['pet_types' => ['birds']]);

        $dogServices = Service::byPetType('dogs')->get();
        $birdServices = Service::byPetType('birds')->get();

        $this->assertCount(1, $dogServices);
        $this->assertContains('dogs', $dogServices->first()->pet_types);

        $this->assertCount(1, $birdServices);
        $this->assertContains('birds', $birdServices->first()->pet_types);
    }

    public function test_service_by_pet_size_scope(): void
    {
        Service::factory()->create(['pet_sizes' => ['small', 'medium']]);
        Service::factory()->create(['pet_sizes' => ['large']]);

        $smallPetServices = Service::byPetSize('small')->get();
        $largePetServices = Service::byPetSize('large')->get();

        $this->assertCount(1, $smallPetServices);
        $this->assertContains('small', $smallPetServices->first()->pet_sizes);

        $this->assertCount(1, $largePetServices);
        $this->assertContains('large', $largePetServices->first()->pet_sizes);
    }

    public function test_service_by_service_type_scope(): void
    {
        $homeService = Service::factory()->create([
            'home_service' => true,
            'sitter_home' => false
        ]);

        $sitterHomeService = Service::factory()->create([
            'home_service' => false,
            'sitter_home' => true
        ]);

        $homeServices = Service::byServiceType('home_service')->get();
        $sitterHomeServices = Service::byServiceType('sitter_home')->get();

        $this->assertCount(1, $homeServices);
        $this->assertTrue($homeServices->first()->home_service);

        $this->assertCount(1, $sitterHomeServices);
        $this->assertTrue($sitterHomeServices->first()->sitter_home);
    }

    public function test_service_by_price_range_scope(): void
    {
        Service::factory()->create(['price_per_hour' => 20.00]);
        Service::factory()->create(['price_per_hour' => 50.00]);
        Service::factory()->create(['price_per_hour' => 80.00]);

        $midRangeServices = Service::byPriceRange(25.00, 60.00, 'hour')->get();

        $this->assertCount(1, $midRangeServices);
        $this->assertEquals(50.00, $midRangeServices->first()->price_per_hour);
    }

    public function test_service_display_price_attribute(): void
    {
        // Service with both hourly and daily rates
        $service1 = Service::factory()->create([
            'price_per_hour' => 25.00,
            'price_per_day' => 150.00
        ]);

        $this->assertEquals('od 25zł/h (150zł/dzień)', $service1->display_price);

        // Service with only hourly rate
        $service2 = Service::factory()->create([
            'price_per_hour' => 30.00,
            'price_per_day' => null
        ]);

        $this->assertEquals('30zł/h', $service2->display_price);

        // Service with only daily rate
        $service3 = Service::factory()->create([
            'price_per_hour' => null,
            'price_per_day' => 200.00
        ]);

        $this->assertEquals('200zł/dzień', $service3->display_price);

        // Service with no prices
        $service4 = Service::factory()->create([
            'price_per_hour' => null,
            'price_per_day' => null
        ]);

        $this->assertEquals('Do uzgodnienia', $service4->display_price);
    }

    public function test_service_service_types_attribute(): void
    {
        $service1 = Service::factory()->create([
            'home_service' => true,
            'sitter_home' => false
        ]);

        $this->assertEquals(['U klienta'], $service1->service_types);

        $service2 = Service::factory()->create([
            'home_service' => false,
            'sitter_home' => true
        ]);

        $this->assertEquals(['U opiekuna'], $service2->service_types);

        $service3 = Service::factory()->create([
            'home_service' => true,
            'sitter_home' => true
        ]);

        $this->assertEquals(['U klienta', 'U opiekuna'], $service3->service_types);

        $service4 = Service::factory()->create([
            'home_service' => false,
            'sitter_home' => false
        ]);

        $this->assertEquals([], $service4->service_types);
    }

    public function test_service_can_be_created_with_factory(): void
    {
        $service = Service::factory()->create();

        $this->assertNotNull($service->id);
        $this->assertNotNull($service->sitter_id);
        $this->assertNotNull($service->category_id);
        $this->assertNotNull($service->title);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'title' => $service->title,
        ]);
    }

    public function test_service_inactive_factory_state(): void
    {
        $service = Service::factory()->inactive()->create();

        $this->assertFalse($service->is_active);
    }

    public function test_service_with_sitter_factory_state(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->withSitter($user->id)->create();

        $this->assertEquals($user->id, $service->sitter_id);
        $this->assertEquals($user->id, $service->sitter->id);
    }

    public function test_service_with_category_factory_state(): void
    {
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->withCategory($category->id)->create();

        $this->assertEquals($category->id, $service->category_id);
        $this->assertEquals($category->id, $service->category->id);
    }

    public function test_service_reviews_count_attribute(): void
    {
        $service = Service::factory()->create();

        // Initially should be 0
        $this->assertEquals(0, $service->reviews_count);
    }

    public function test_service_average_rating_attribute(): void
    {
        $service = Service::factory()->create();

        // Without reviews, should be 0
        $this->assertEquals(0, $service->average_rating);
    }
}
