<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LocationManager;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LocationManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        UserProfile::create([
            'user_id' => $this->user->id,
            'role' => 'sitter',
            'first_name' => 'Test',
            'last_name' => 'User',
        ]);

        $this->actingAs($this->user);
    }

    public function test_component_loads_user_locations(): void
    {
        // Create test locations
        Location::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Dom',
            'city' => 'Warszawa',
        ]);

        Location::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Praca',
            'city' => 'Warszawa',
        ]);

        Livewire::test(LocationManager::class)
            ->assertCount('locations', 2)
            ->assertSee('Dom')
            ->assertSee('Praca');
    }

    public function test_can_add_new_location(): void
    {
        Livewire::test(LocationManager::class)
            ->call('addLocation')
            ->assertSet('showModal', true)
            ->assertSet('editingLocation', null)
            ->set('name', 'Nowy adres')
            ->set('street', 'Testowa 123')
            ->set('city', 'Warszawa')
            ->set('postal_code', '00-001')
            ->set('country', 'Polska')
            ->call('saveLocation')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('locations', [
            'user_id' => $this->user->id,
            'name' => 'Nowy adres',
            'street' => 'Testowa 123',
            'city' => 'Warszawa',
        ]);
    }

    public function test_can_edit_existing_location(): void
    {
        $location = Location::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Stary adres',
            'street' => 'Stara 1',
        ]);

        Livewire::test(LocationManager::class)
            ->call('editLocation', $location->id)
            ->assertSet('editingLocation', $location->id)
            ->assertSet('name', 'Stary adres')
            ->assertSet('street', 'Stara 1')
            ->set('name', 'Zaktualizowany adres')
            ->call('saveLocation');

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Zaktualizowany adres',
        ]);
    }

    public function test_can_delete_location(): void
    {
        $location = Location::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Do usunięcia',
        ]);

        Livewire::test(LocationManager::class)
            ->call('deleteLocation', $location->id);

        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_can_set_primary_location(): void
    {
        $location1 = Location::factory()->create([
            'user_id' => $this->user->id,
            'is_primary' => true,
        ]);

        $location2 = Location::factory()->create([
            'user_id' => $this->user->id,
            'is_primary' => false,
        ]);

        Livewire::test(LocationManager::class)
            ->call('setPrimary', $location2->id);

        $this->assertDatabaseHas('locations', [
            'id' => $location1->id,
            'is_primary' => false,
        ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location2->id,
            'is_primary' => true,
        ]);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test(LocationManager::class)
            ->call('addLocation')
            ->set('name', '')
            ->set('street', '')
            ->set('city', '')
            ->call('saveLocation')
            ->assertHasErrors(['name', 'street', 'city']);
    }

    public function test_can_close_modal(): void
    {
        Livewire::test(LocationManager::class)
            ->call('addLocation')
            ->assertSet('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertSet('editingLocation', null)
            ->assertSet('name', '');
    }

    public function test_user_can_only_edit_own_locations(): void
    {
        $otherUser = User::factory()->create();
        $otherLocation = Location::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Livewire::test(LocationManager::class)
            ->call('editLocation', $otherLocation->id)
            ->assertSet('editingLocation', null); // Should not edit other user's location
    }

    public function test_detects_current_location(): void
    {
        Livewire::test(LocationManager::class)
            ->call('detectCurrentLocation')
            ->assertDispatched('detect-current-location');
    }

    public function test_sets_detected_location(): void
    {
        Livewire::test(LocationManager::class)
            ->call('setDetectedLocation', 52.2297, 21.0122, 'Warszawa, Poland')
            ->assertSet('latitude', 52.2297)
            ->assertSet('longitude', 21.0122)
            ->assertSet('city', 'Warszawa'); // Correctly parsed from address
    }
}