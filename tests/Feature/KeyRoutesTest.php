<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Pet;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeyRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $authenticatedUser;
    private User $sitterUser;
    private Service $service;
    private Pet $pet;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    private function setupTestData(): void
    {
        // Create authenticated user (owner)
        $this->authenticatedUser = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $this->authenticatedUser->id,
            'role' => 'owner',
            'first_name' => 'Test',
            'last_name' => 'Owner'
        ]);

        // Create pet for the owner
        $this->pet = Pet::create([
            'owner_id' => $this->authenticatedUser->id,
            'name' => 'Test Pet',
            'type' => 'dog',
            'breed' => 'Test Breed',
            'size' => 'medium',
            'age' => 3,
            'gender' => 'male',
            'description' => 'Test pet description',
            'is_active' => true
        ]);

        // Create sitter user
        $this->sitterUser = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $this->sitterUser->id,
            'role' => 'sitter',
            'first_name' => 'Test',
            'last_name' => 'Sitter'
        ]);

        // Create service category
        $category = ServiceCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
            'icon' => '🐾',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Create service
        $this->service = Service::create([
            'sitter_id' => $this->sitterUser->id,
            'category_id' => $category->id,
            'title' => 'Test Service',
            'description' => 'Test service description',
            'price_per_hour' => 25.00,
            'price_per_day' => 150.00,
            'pet_types' => ['dog'],
            'pet_sizes' => ['medium'],
            'home_service' => true,
            'sitter_home' => false,
            'max_pets' => 3,
            'is_active' => true
        ]);

        // Create booking
        $this->booking = Booking::create([
            'owner_id' => $this->authenticatedUser->id,
            'sitter_id' => $this->sitterUser->id,
            'service_id' => $this->service->id,
            'pet_id' => $this->pet->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'total_price' => 200.00,
            'status' => 'confirmed',
            'notes' => 'Test booking notes'
        ]);
    }

    public function test_public_routes(): void
    {
        $publicRoutes = [
            '/' => 200,
            '/search' => 200,
            '/reviews' => 200,
            '/login' => 200,
            '/register' => 200,
        ];

        foreach ($publicRoutes as $route => $expectedStatus) {
            $response = $this->get($route);
            $this->assertEquals($expectedStatus, $response->getStatusCode(), "Route {$route} failed");
        }
    }

    public function test_authenticated_routes(): void
    {
        $authenticatedRoutes = [
            '/dashboard' => 200,
            '/bookings' => 200,
            '/pets' => 200,
            '/pets/create' => 200,
            '/chat' => 200,
            '/notifications' => 200,
            '/availability' => 200,
        ];

        foreach ($authenticatedRoutes as $route => $expectedStatus) {
            $response = $this->actingAs($this->authenticatedUser)->get($route);
            $this->assertEquals($expectedStatus, $response->getStatusCode(), "Authenticated route {$route} failed");
        }
    }

    public function test_parameterized_routes(): void
    {
        $parameterizedRoutes = [
            "/booking/{$this->service->id}" => 200,
            "/sitter/{$this->sitterUser->id}" => 200,
            "/payment/{$this->booking->id}" => 200,
            "/review/{$this->booking->id}" => 200,
        ];

        foreach ($parameterizedRoutes as $route => $expectedStatus) {
            $response = $this->actingAs($this->authenticatedUser)->get($route);
            $this->assertEquals($expectedStatus, $response->getStatusCode(), "Parameterized route {$route} failed");
        }
    }

    public function test_chat_with_booking_url(): void
    {
        $chatUrl = "/chat?user={$this->sitterUser->id}&booking={$this->booking->id}";
        $response = $this->actingAs($this->authenticatedUser)->get($chatUrl);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSee('chat-app', $response->getContent());
    }

    public function test_booking_form_loads(): void
    {
        $response = $this->get("/booking/{$this->service->id}");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSee('Zarezerwuj tę usługę', $response->getContent());
    }
}