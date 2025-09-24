<?php

namespace App\Livewire\Services;

use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class WalkingServiceForm extends BaseServiceForm
{
    // Walking specific fields
    #[Validate('required|numeric|min:5|max:200')]
    public float $price_per_walk = 20;

    #[Validate('required|integer|min:15|max:180')]
    public int $walk_duration_minutes = 30;

    #[Validate('required|integer|min:1|max:5')]
    public int $max_dogs_per_walk = 1;

    #[Validate('boolean')]
    public bool $group_walks_available = false;

    #[Validate('boolean')]
    public bool $pickup_dropoff = true;

    #[Validate('array')]
    public array $available_times = [];

    #[Validate('string|nullable|max:300')]
    public ?string $walking_routes = null;

    protected function setDefaultValues(): void
    {
        $this->home_service = true; // Walking service includes pickup
        $this->price_per_walk = 20;
        $this->walk_duration_minutes = 30;
        $this->max_dogs_per_walk = 1;
        $this->pickup_dropoff = true;
        $this->available_times = ['morning', 'afternoon'];
    }

    public function fillWithFakeData()
    {
        parent::fillWithFakeData();

        // Walking specific fake data
        $this->price_per_walk = 25.00;
        $this->walk_duration_minutes = 45;
        $this->max_dogs_per_walk = 2;
        $this->group_walks_available = true;
        $this->pickup_dropoff = true;
        $this->available_times = ['morning', 'afternoon', 'evening'];
        $this->walking_routes = 'Park Łazienkowski, Pole Mokotowskie, bulwary wiślane. Dostosowuję trasę do potrzeb psa i jego kondycji.';
    }

    protected function getCategorySpecificValidationRules(): array
    {
        return [
            'price_per_walk' => 'required|numeric|min:5|max:200',
            'walk_duration_minutes' => 'required|integer|min:15|max:180',
            'max_dogs_per_walk' => 'required|integer|min:1|max:5',
            'group_walks_available' => 'boolean',
            'pickup_dropoff' => 'boolean',
            'available_times' => 'array',
            'walking_routes' => 'string|nullable|max:300',
        ];
    }

    protected function getCategorySpecificData(): array
    {
        return [
            'price_per_hour' => null, // Walking uses per-walk pricing
            'price_per_day' => null,
            'metadata' => [
                'price_per_walk' => $this->price_per_walk,
                'walk_duration_minutes' => $this->walk_duration_minutes,
                'max_dogs_per_walk' => $this->max_dogs_per_walk,
                'group_walks_available' => $this->group_walks_available,
                'pickup_dropoff' => $this->pickup_dropoff,
                'available_times' => $this->available_times,
                'walking_routes' => $this->walking_routes,
            ]
        ];
    }

    protected function loadCategorySpecificData(\App\Models\Service $service): void
    {
        $metadata = $service->metadata ?? [];
        $this->price_per_walk = $metadata['price_per_walk'] ?? 20;
        $this->walk_duration_minutes = $metadata['walk_duration_minutes'] ?? 30;
        $this->max_dogs_per_walk = $metadata['max_dogs_per_walk'] ?? 1;
        $this->group_walks_available = $metadata['group_walks_available'] ?? false;
        $this->pickup_dropoff = $metadata['pickup_dropoff'] ?? true;
        $this->available_times = $metadata['available_times'] ?? ['morning', 'afternoon'];
        $this->walking_routes = $metadata['walking_routes'] ?? null;
    }

    protected function getMinPrice(): ?float
    {
        return $this->price_per_walk;
    }

    public function timeSlotOptions()
    {
        return [
            'early_morning' => 'Wczesny ranek (6:00-8:00)',
            'morning' => 'Ranek (8:00-12:00)',
            'afternoon' => 'Popołudnie (12:00-17:00)',
            'evening' => 'Wieczór (17:00-20:00)',
        ];
    }

    public function render()
    {
        return view('livewire.services.walking-service-form')->layout('components.dashboard-layout');
    }
}