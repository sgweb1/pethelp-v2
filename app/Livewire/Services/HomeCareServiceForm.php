<?php

namespace App\Livewire\Services;

class HomeCareServiceForm extends BaseServiceForm
{
    // Home care specific fields
    public ?float $price_per_hour = null;

    public ?float $price_per_day = null;

    public bool $overnight_care = false;

    public bool $feeding_included = true;

    public bool $walking_included = false;

    public bool $play_time = true;

    public bool $basic_grooming = false;

    public ?string $special_notes = null;

    protected function setDefaultValues(): void
    {
        $this->home_service = true; // Home care is always at client's home
        $this->feeding_included = true;
        $this->play_time = true;
    }

    public function fillWithFakeData()
    {
        parent::fillWithFakeData();

        // Home care specific fake data
        $this->price_per_hour = 35.00;
        $this->price_per_day = 200.00;
        $this->overnight_care = true;
        $this->feeding_included = true;
        $this->walking_included = true;
        $this->play_time = true;
        $this->basic_grooming = false;
        $this->special_notes = 'Specjalizuję się w opiece nad starszymi psami. Mam doświadczenie z podawaniem leków.';
    }

    protected function getCategorySpecificValidationRules(): array
    {
        return [
            'price_per_hour' => 'nullable|numeric|min:10|max:500',
            'price_per_day' => 'nullable|numeric|min:50|max:2000',
            'overnight_care' => 'boolean',
            'feeding_included' => 'boolean',
            'walking_included' => 'boolean',
            'play_time' => 'boolean',
            'basic_grooming' => 'boolean',
            'special_notes' => 'string|nullable|max:500',
        ];
    }

    protected function getCategorySpecificData(): array
    {
        return [
            'price_per_hour' => $this->price_per_hour,
            'price_per_day' => $this->price_per_day,
            'metadata' => [
                'overnight_care' => $this->overnight_care,
                'feeding_included' => $this->feeding_included,
                'walking_included' => $this->walking_included,
                'play_time' => $this->play_time,
                'basic_grooming' => $this->basic_grooming,
                'special_notes' => $this->special_notes,
            ],
        ];
    }

    protected function loadCategorySpecificData(\App\Models\Service $service): void
    {
        $this->price_per_hour = $service->price_per_hour;
        $this->price_per_day = $service->price_per_day;

        $metadata = $service->metadata ?? [];
        $this->overnight_care = $metadata['overnight_care'] ?? false;
        $this->feeding_included = $metadata['feeding_included'] ?? true;
        $this->walking_included = $metadata['walking_included'] ?? false;
        $this->play_time = $metadata['play_time'] ?? true;
        $this->basic_grooming = $metadata['basic_grooming'] ?? false;
        $this->special_notes = $metadata['special_notes'] ?? null;
    }

    protected function getMinPrice(): ?float
    {
        return $this->price_per_hour ?? $this->price_per_day;
    }

    public function validateAndSave()
    {
        $baseValid = parent::validateAndSave();
        if (! $baseValid) {
            return false;
        }

        // Category specific validation
        if (! $this->price_per_hour && ! $this->price_per_day) {
            $this->addError('price', 'Musisz podać cenę za godzinę lub dzień.');

            return false;
        }

        return true;
    }

    public function render()
    {
        return view('domains.services.forms.home-care')->layout('components.dashboard-layout');
    }
}
