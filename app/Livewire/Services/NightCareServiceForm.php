<?php

namespace App\Livewire\Services;

class NightCareServiceForm extends BaseServiceForm
{
    // Night care specific pricing fields
    public ?float $price_per_night = null;
    public ?float $weekend_price_per_night = null;

    // Stay duration limits
    public int $min_nights = 1;
    public int $max_nights = 14;

    // Pet preferences
    public bool $allows_multiple_owners = true;
    public bool $allows_mixing_pet_types = false;

    // Transport service
    public bool $transport_enabled = false;
    public ?int $transport_radius_km = null;

    // Basic services included in price
    public bool $feeding_included = true;
    public bool $walking_included = true;
    public bool $play_time = true;
    public bool $basic_grooming = false;
    public bool $medication_admin = false;
    public bool $daily_updates = true;

    // Additional notes
    public ?string $special_notes = null;

    protected function setDefaultValues(): void
    {
        $this->sitter_home = true; // Night care is always at sitter's home
        $this->home_service = false; // Override base default
        $this->min_nights = 1;
        $this->max_nights = 14;
        $this->allows_multiple_owners = true;
        $this->allows_mixing_pet_types = false;
        $this->feeding_included = true;
        $this->walking_included = true;
        $this->play_time = true;
        $this->daily_updates = true;
    }

    public function fillWithFakeData()
    {
        parent::fillWithFakeData();

        // Night care specific fake data
        $this->title = 'Hotel dla psów i kotów - Warszawa Mokotów';
        $this->description = 'Oferuję profesjonalną opiekę noclegową w komfortowych warunkach domowych. Posiadam własny dom z ogródkiem, gdzie zwierzęta mogą się swobodnie poruszać. Zapewniam pełną opiekę 24/7, regularne spacery, karmienie według harmonogramu oraz dużo miłości i uwagi. Mam wieloletnie doświadczenie w opiece nad różnymi gatunkami zwierząt.';

        $this->price_per_night = 80.00;
        $this->weekend_price_per_night = 100.00;
        $this->min_nights = 1;
        $this->max_nights = 14;
        $this->max_pets = 3;

        $this->allows_multiple_owners = true;
        $this->allows_mixing_pet_types = false;

        $this->transport_enabled = true;
        $this->transport_radius_km = 15;

        $this->feeding_included = true;
        $this->walking_included = true;
        $this->play_time = true;
        $this->basic_grooming = false;
        $this->medication_admin = true;
        $this->daily_updates = true;

        $this->special_notes = 'Dom z ogródkiem, doświadczenie z podawaniem leków. Specjalizuję się w opiece nad starszymi psami.';
    }

    protected function getCategorySpecificValidationRules(): array
    {
        return [
            'price_per_night' => 'required|numeric|min:30|max:500',
            'weekend_price_per_night' => 'nullable|numeric|min:30|max:500',
            'min_nights' => 'required|integer|min:1|max:30',
            'max_nights' => 'required|integer|min:1|max:365',
            'allows_multiple_owners' => 'boolean',
            'allows_mixing_pet_types' => 'boolean',
            'transport_enabled' => 'boolean',
            'transport_radius_km' => 'nullable|integer|min:1|max:50',
            'feeding_included' => 'boolean',
            'walking_included' => 'boolean',
            'play_time' => 'boolean',
            'basic_grooming' => 'boolean',
            'medication_admin' => 'boolean',
            'daily_updates' => 'boolean',
            'special_notes' => 'string|nullable|max:500',
        ];
    }

    protected function getCategorySpecificData(): array
    {
        return [
            'price_per_night' => $this->price_per_night,
            'weekend_price_per_night' => $this->weekend_price_per_night,
            'min_nights' => $this->min_nights,
            'max_nights' => $this->max_nights,
            'transport_enabled' => $this->transport_enabled,
            'transport_radius_km' => $this->transport_radius_km,
            'allows_multiple_owners' => $this->allows_multiple_owners,
            'allows_mixing_pet_types' => $this->allows_mixing_pet_types,
            'metadata' => [
                'feeding_included' => $this->feeding_included,
                'walking_included' => $this->walking_included,
                'play_time' => $this->play_time,
                'basic_grooming' => $this->basic_grooming,
                'medication_admin' => $this->medication_admin,
                'daily_updates' => $this->daily_updates,
                'special_notes' => $this->special_notes,
            ]
        ];
    }

    protected function loadCategorySpecificData(\App\Models\Service $service): void
    {
        $this->price_per_night = $service->price_per_night;
        $this->weekend_price_per_night = $service->weekend_price_per_night;
        $this->min_nights = $service->min_nights ?? 1;
        $this->max_nights = $service->max_nights ?? 14;
        $this->transport_enabled = $service->transport_enabled ?? false;
        $this->transport_radius_km = $service->transport_radius_km;
        $this->allows_multiple_owners = $service->allows_multiple_owners ?? true;
        $this->allows_mixing_pet_types = $service->allows_mixing_pet_types ?? false;

        $metadata = $service->metadata ?? [];
        $this->feeding_included = $metadata['feeding_included'] ?? true;
        $this->walking_included = $metadata['walking_included'] ?? true;
        $this->play_time = $metadata['play_time'] ?? true;
        $this->basic_grooming = $metadata['basic_grooming'] ?? false;
        $this->medication_admin = $metadata['medication_admin'] ?? false;
        $this->daily_updates = $metadata['daily_updates'] ?? true;
        $this->special_notes = $metadata['special_notes'] ?? null;
    }

    protected function getMinPrice(): ?float
    {
        return $this->price_per_night;
    }

    public function validateAndSave()
    {
        $baseValid = parent::validateAndSave();
        if (!$baseValid) return false;

        // Category specific validation
        if (!$this->price_per_night) {
            $this->addError('price_per_night', 'Cena za noc jest wymagana.');
            return false;
        }

        if ($this->min_nights > $this->max_nights) {
            $this->addError('max_nights', 'Maksymalna liczba nocy nie może być mniejsza od minimalnej.');
            return false;
        }

        if ($this->weekend_price_per_night && $this->weekend_price_per_night < $this->price_per_night) {
            $this->addError('weekend_price_per_night', 'Cena weekendowa nie może być niższa od podstawowej.');
            return false;
        }

        if ($this->transport_enabled && !$this->transport_radius_km) {
            $this->addError('transport_radius_km', 'Podaj zasięg transportu gdy usługa jest włączona.');
            return false;
        }

        return true;
    }

    public function render()
    {
        return view('livewire.services.night-care-service-form')->layout('components.dashboard-layout');
    }
}