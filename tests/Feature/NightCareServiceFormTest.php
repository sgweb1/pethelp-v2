<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\PetType;
use App\Livewire\Services\NightCareServiceForm;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NightCareServiceFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ServiceCategory $nightCareCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();

        // Create night care service category
        $this->nightCareCategory = ServiceCategory::factory()->create([
            'name' => 'Opieka nocna',
            'slug' => 'opieka-nocna',
            'icon' => 'moon'
        ]);

        // Create some pet types for testing
        PetType::factory()->create(['name' => 'Psy', 'slug' => 'dog']);
        PetType::factory()->create(['name' => 'Koty', 'slug' => 'cat']);
    }

    /** @test */
    public function it_renders_night_care_service_form_successfully()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->assertSuccessful()
            ->assertSee('Nowa usługa noclegowa')
            ->assertSee('Cennik')
            ->assertSee('Cena za noc')
            ->assertSee('Długość pobytu')
            ->assertSee('Transport')
            ->assertSee('Usługi w cenie');
    }

    /** @test */
    public function it_can_fill_form_with_fake_data()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->call('fillWithFakeData')
            ->assertSet('price_per_night', 80.00)
            ->assertSet('weekend_price_per_night', 100.00)
            ->assertSet('min_nights', 1)
            ->assertSet('max_nights', 14)
            ->assertSet('allows_multiple_owners', true)
            ->assertSet('transport_enabled', true)
            ->assertSet('transport_radius_km', 15)
            ->assertSee('Hotel dla psów i kotów');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->set('title', '')
            ->set('description', '')
            ->set('price_per_night', '')
            ->call('save')
            ->assertHasErrors(['title', 'description', 'price_per_night'])
            ->assertSee('Tytuł usługi')
            ->assertSee('Opis usługi');
    }

    /** @test */
    public function it_validates_price_constraints()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->set('price_per_night', 25.00) // Below minimum
            ->set('weekend_price_per_night', 20.00) // Lower than base price
            ->call('validateAndSave')
            ->assertHasErrors(['price_per_night', 'weekend_price_per_night']);
    }

    /** @test */
    public function it_validates_night_range()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->set('min_nights', 10)
            ->set('max_nights', 5) // Less than min
            ->call('validateAndSave')
            ->assertHasErrors(['max_nights']);
    }

    /** @test */
    public function it_validates_transport_radius_when_enabled()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->set('transport_enabled', true)
            ->set('transport_radius_km', null)
            ->call('validateAndSave')
            ->assertHasErrors(['transport_radius_km']);
    }

    /** @test */
    public function it_sets_correct_default_values()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->assertSet('sitter_home', true)
            ->assertSet('home_service', false)
            ->assertSet('min_nights', 1)
            ->assertSet('max_nights', 14)
            ->assertSet('allows_multiple_owners', true)
            ->assertSet('allows_mixing_pet_types', false)
            ->assertSet('feeding_included', true)
            ->assertSet('walking_included', true)
            ->assertSet('play_time', true)
            ->assertSet('daily_updates', true);
    }

    /** @test */
    public function transport_radius_field_is_shown_when_transport_enabled()
    {
        Livewire::actingAs($this->user)
            ->test(NightCareServiceForm::class, ['categoryId' => $this->nightCareCategory->id])
            ->set('transport_enabled', true)
            ->assertSee('Zasięg transportu (km)')
            ->set('transport_enabled', false)
            ->assertDontSee('Zasięg transportu (km)');
    }
}