<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_has_fillable_attributes(): void
    {
        $location = new Location();

        $expected = [
            'user_id',
            'name',
            'street',
            'city',
            'postal_code',
            'country',
            'latitude',
            'longitude',
            'is_primary',
        ];

        $this->assertEquals($expected, $location->getFillable());
    }

    public function test_location_casts_attributes_correctly(): void
    {
        $location = Location::factory()->create([
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_primary' => true,
        ]);

        $this->assertIsFloat($location->latitude);
        $this->assertEquals(52.2297, $location->latitude);

        $this->assertIsFloat($location->longitude);
        $this->assertEquals(21.0122, $location->longitude);

        $this->assertIsBool($location->is_primary);
        $this->assertTrue($location->is_primary);
    }

    public function test_location_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $location->user);
        $this->assertEquals($user->id, $location->user->id);
    }

    public function test_location_full_address_attribute(): void
    {
        $location = Location::factory()->create([
            'street' => 'ul. Przykładowa 123',
            'postal_code' => '00-001',
            'city' => 'Warszawa',
        ]);

        $expected = 'ul. Przykładowa 123, 00-001 Warszawa';
        $this->assertEquals($expected, $location->full_address);
    }

    public function test_location_display_name_attribute_with_name(): void
    {
        $location = Location::factory()->create([
            'name' => 'Dom',
            'city' => 'Warszawa',
        ]);

        $this->assertEquals('Dom', $location->display_name);
    }

    public function test_location_display_name_attribute_without_name(): void
    {
        $location = Location::factory()->create([
            'name' => null,
            'city' => 'Warszawa',
        ]);

        $this->assertEquals('Warszawa', $location->display_name);
    }

    public function test_location_distance_to_method(): void
    {
        $location = Location::factory()->create([
            'latitude' => 52.2297,
            'longitude' => 21.0122,
        ]);

        // Distance from Warsaw to Krakow (approximately 250km)
        $krakowLat = 50.0647;
        $krakowLng = 19.9450;

        $distance = $location->distanceTo($krakowLat, $krakowLng);

        // Should be approximately 250km (allowing some margin for calculation differences)
        $this->assertGreaterThan(240, $distance);
        $this->assertLessThan(260, $distance);
    }

    public function test_location_distance_to_method_without_coordinates(): void
    {
        $location = Location::factory()->create([
            'latitude' => null,
            'longitude' => null,
        ]);

        $distance = $location->distanceTo(52.2297, 21.0122);

        $this->assertEquals(0, $distance);
    }

    public function test_location_distance_to_same_point(): void
    {
        $location = Location::factory()->create([
            'latitude' => 52.2297,
            'longitude' => 21.0122,
        ]);

        $distance = $location->distanceTo(52.2297, 21.0122);

        $this->assertEquals(0, $distance);
    }

    public function test_location_can_be_created_with_factory(): void
    {
        $location = Location::factory()->create();

        $this->assertNotNull($location->id);
        $this->assertNotNull($location->user_id);
        $this->assertNotNull($location->city);
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'city' => $location->city,
        ]);
    }

    public function test_location_can_be_primary(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create([
            'user_id' => $user->id,
            'is_primary' => true,
        ]);

        $this->assertTrue($location->is_primary);
    }

    public function test_location_can_be_non_primary(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create([
            'user_id' => $user->id,
            'is_primary' => false,
        ]);

        $this->assertFalse($location->is_primary);
    }

    public function test_user_can_have_multiple_locations(): void
    {
        $user = User::factory()->create();

        Location::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->locations);
    }

    public function test_location_geocode_address_static_method(): void
    {
        // This test would require network access to OpenStreetMap
        // In a real scenario, you would mock this service
        // For now, we'll just test that the method exists and returns expected type

        $result = Location::geocodeAddress('Warszawa, Poland');

        // The method should return either an array with coordinates or null
        $this->assertTrue(is_array($result) || is_null($result));

        if (is_array($result)) {
            $this->assertArrayHasKey('latitude', $result);
            $this->assertArrayHasKey('longitude', $result);
            $this->assertIsFloat($result['latitude']);
            $this->assertIsFloat($result['longitude']);
        }
    }

    public function test_location_geocode_method(): void
    {
        $location = Location::factory()->create([
            'street' => 'Plac Defilad 1',
            'city' => 'Warszawa',
            'postal_code' => '00-901',
            'country' => 'Poland',
            'latitude' => null,
            'longitude' => null,
        ]);

        // This would require network access in real testing
        // In production, you'd mock the geocoding service
        $result = $location->geocode();

        // Should return boolean indicating success/failure
        $this->assertIsBool($result);
    }

    public function test_location_with_polish_addresses(): void
    {
        $locations = [
            ['city' => 'Warszawa', 'country' => 'Polska'],
            ['city' => 'Kraków', 'country' => 'Polska'],
            ['city' => 'Gdańsk', 'country' => 'Polska'],
            ['city' => 'Wrocław', 'country' => 'Polska'],
            ['city' => 'Poznań', 'country' => 'Polska'],
        ];

        foreach ($locations as $locationData) {
            $location = Location::factory()->create($locationData);

            $this->assertEquals($locationData['city'], $location->city);
            $this->assertEquals($locationData['country'], $location->country);
            $this->assertDatabaseHas('locations', $locationData);
        }
    }

    public function test_location_with_coordinates_in_poland_range(): void
    {
        // Test that coordinates are within Poland's approximate boundaries
        $location = Location::factory()->create([
            'latitude' => 52.2297,  // Warsaw latitude
            'longitude' => 21.0122, // Warsaw longitude
        ]);

        // Poland's approximate coordinate ranges
        $this->assertGreaterThanOrEqual(49, $location->latitude);
        $this->assertLessThanOrEqual(55, $location->latitude);
        $this->assertGreaterThanOrEqual(14, $location->longitude);
        $this->assertLessThanOrEqual(24, $location->longitude);
    }
}