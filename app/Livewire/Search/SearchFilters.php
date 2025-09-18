<?php

namespace App\Livewire\Search;

use Livewire\Component;

class SearchFilters extends Component
{
    public $filters = [];
    public $showAdvanced = false;

    public $petTypes = [
        'dogs' => 'Psy',
        'cats' => 'Koty',
        'birds' => 'Ptaki',
        'fish' => 'Ryby',
        'reptiles' => 'Gady',
        'rabbits' => 'Króliki',
        'hamsters' => 'Chomiki',
        'other' => 'Inne'
    ];

    public $serviceTypes = [
        'pet_sitting' => 'Opieka nad zwierzętami',
        'dog_walking' => 'Wyprowadzanie psów',
        'pet_grooming' => 'Pielęgnacja',
        'veterinary_visits' => 'Wizyty weterynaryjne',
        'pet_boarding' => 'Pensjonat',
        'pet_training' => 'Szkolenie zwierząt'
    ];

    public $sortOptions = [
        'relevance' => 'Trafność',
        'rating' => 'Ocena',
        'price_low' => 'Cena: od najniższej',
        'price_high' => 'Cena: od najwyższej',
        'distance' => 'Odległość',
        'newest' => 'Najnowsze'
    ];

    public function mount(array $initialFilters = [])
    {
        $this->filters = array_merge([
            'search_term' => '',
            'location' => '',
            'min_price' => '',
            'max_price' => '',
            'price_type' => 'hour',
            'min_rating' => '',
            'pet_type' => '',
            'service_type' => '',
            'sort_by' => 'relevance',
            'radius' => 10,
            'verified_only' => false,
            'instant_booking' => false,
            'flexible_cancellation' => false,
            'experience_years' => '',
            'has_insurance' => false,
        ], $initialFilters);
    }

    public function updatedFilters()
    {
        $this->dispatch('filters-updated', $this->filters);
    }

    public function clearFilters()
    {
        $this->filters = [
            'search_term' => '',
            'location' => '',
            'min_price' => '',
            'max_price' => '',
            'price_type' => 'hour',
            'min_rating' => '',
            'pet_type' => '',
            'service_type' => '',
            'sort_by' => 'relevance',
            'radius' => 10,
            'verified_only' => false,
            'instant_booking' => false,
            'flexible_cancellation' => false,
            'experience_years' => '',
            'has_insurance' => false,
        ];

        $this->dispatch('filters-updated', $this->filters);
    }

    public function toggleAdvanced()
    {
        $this->showAdvanced = !$this->showAdvanced;
    }

    public function getActiveFiltersCount(): int
    {
        $count = 0;

        if (!empty($this->filters['search_term'])) $count++;
        if (!empty($this->filters['location'])) $count++;
        if (!empty($this->filters['min_price']) || !empty($this->filters['max_price'])) $count++;
        if (!empty($this->filters['min_rating'])) $count++;
        if (!empty($this->filters['pet_type'])) $count++;
        if (!empty($this->filters['service_type'])) $count++;
        if ($this->filters['verified_only']) $count++;
        if ($this->filters['instant_booking']) $count++;
        if (!empty($this->filters['experience_years'])) $count++;
        if ($this->filters['has_insurance']) $count++;

        return $count;
    }

    public function render()
    {
        return view('livewire.search.search-filters', [
            'activeFiltersCount' => $this->getActiveFiltersCount()
        ]);
    }
}