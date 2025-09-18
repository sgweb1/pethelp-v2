<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Pet;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteTest extends TestCase
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

    public function test_all_routes_return_valid_responses(): void
    {
        $routes = Route::getRoutes();
        $skippedRoutes = [];
        $failedRoutes = [];
        $successfulRoutes = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $name = $route->getName();
            $middleware = $route->middleware();

            // Skip routes that shouldn't be tested
            if ($this->shouldSkipRoute($uri, $methods, $middleware)) {
                $skippedRoutes[] = "{$methods[0]} /{$uri}";
                continue;
            }

            // Test each HTTP method for this route
            foreach ($methods as $method) {
                if (in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
                    try {
                        $response = $this->makeRequestToRoute($route, $method);

                        if ($response->getStatusCode() >= 500) {
                            $failedRoutes[] = "{$method} /{$uri} - Status: {$response->getStatusCode()}";
                        } else {
                            $successfulRoutes[] = "{$method} /{$uri} - Status: {$response->getStatusCode()}";
                        }
                    } catch (\Exception $e) {
                        $failedRoutes[] = "{$method} /{$uri} - Exception: {$e->getMessage()}";
                    }
                }
            }
        }

        // Output results
        $this->outputTestResults($successfulRoutes, $failedRoutes, $skippedRoutes);

        // Assert that no routes failed with 500 errors
        $this->assertEmpty($failedRoutes,
            "The following routes failed with 500 errors:\n" . implode("\n", $failedRoutes)
        );
    }

    private function shouldSkipRoute(string $uri, array $methods, array $middleware): bool
    {
        // Skip internal Laravel/Livewire routes
        if (str_starts_with($uri, 'livewire/') ||
            str_starts_with($uri, 'sanctum/') ||
            str_starts_with($uri, 'storage/') ||
            $uri === 'up' ||
            str_contains($uri, 'telescope') ||
            str_contains($uri, 'horizon')) {
            return true;
        }

        // Skip routes with parameters we can't easily mock
        if (str_contains($uri, '{') && str_contains($uri, '}')) {
            // Only test a few key parameterized routes
            if (!in_array($uri, [
                'sitter/{sitter}',
                'booking/{service}',
                'review/{booking}',
                'payment/{booking}'
            ])) {
                return true;
            }
        }

        // Skip POST/PUT/DELETE routes that require specific data
        if (in_array('POST', $methods) || in_array('PUT', $methods) || in_array('DELETE', $methods)) {
            // Allow some specific POST routes
            if (!in_array($uri, ['test-csrf', 'logout'])) {
                return true;
            }
        }

        return false;
    }

    private function makeRequestToRoute($route, string $method): \Illuminate\Testing\TestResponse
    {
        $uri = $route->uri();
        $middleware = $route->middleware();

        // Check if route requires authentication
        $requiresAuth = in_array('auth', $middleware) ||
                       in_array('Illuminate\\Auth\\Middleware\\Authenticate', $middleware);

        // Check if route requires guest (unauthenticated)
        $requiresGuest = in_array('Illuminate\\Auth\\Middleware\\RedirectIfAuthenticated', $middleware);

        // Prepare URI with parameters if needed
        $uri = $this->prepareUriWithParameters($uri);

        // Make request based on authentication requirements
        if ($requiresAuth) {
            return $this->actingAs($this->authenticatedUser)->call($method, "/{$uri}");
        } elseif ($requiresGuest) {
            // For guest routes, make sure we're not authenticated
            return $this->call($method, "/{$uri}");
        } else {
            // Public routes
            return $this->call($method, "/{$uri}");
        }
    }

    private function prepareUriWithParameters(string $uri): string
    {
        // Replace route parameters with actual IDs
        $replacements = [
            '{service}' => $this->service->id,
            '{sitter}' => $this->sitterUser->id,
            '{user}' => $this->authenticatedUser->id,
            '{booking}' => $this->booking->id,
            '{pet}' => $this->pet->id,
            '{token}' => 'test-token',
            '{id}' => $this->authenticatedUser->id,
            '{hash}' => 'test-hash',
            '{filename}' => 'test-file.jpg',
            '{path}' => 'test/path'
        ];

        foreach ($replacements as $parameter => $value) {
            $uri = str_replace($parameter, $value, $uri);
        }

        return $uri;
    }

    private function outputTestResults(array $successful, array $failed, array $skipped): void
    {
        echo "\n\n=== ROUTE TEST RESULTS ===\n";

        echo "\n✅ SUCCESSFUL ROUTES (" . count($successful) . "):\n";
        foreach ($successful as $route) {
            echo "  ✓ {$route}\n";
        }

        if (!empty($failed)) {
            echo "\n❌ FAILED ROUTES (" . count($failed) . "):\n";
            foreach ($failed as $route) {
                echo "  ✗ {$route}\n";
            }
        }

        echo "\n⏭️ SKIPPED ROUTES (" . count($skipped) . "):\n";
        foreach (array_slice($skipped, 0, 10) as $route) {
            echo "  - {$route}\n";
        }
        if (count($skipped) > 10) {
            echo "  ... and " . (count($skipped) - 10) . " more\n";
        }

        echo "\n=== SUMMARY ===\n";
        echo "Total Successful: " . count($successful) . "\n";
        echo "Total Failed: " . count($failed) . "\n";
        echo "Total Skipped: " . count($skipped) . "\n";
        echo "=================\n\n";
    }
}