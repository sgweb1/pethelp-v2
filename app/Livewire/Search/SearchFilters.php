<?php

namespace App\Livewire\Search;

use App\Models\ServiceCategory;
use Livewire\Component;

class SearchFilters extends Component
{
    // Search filters
    public string $search_term = '';

    public string $location = '';

    public string $category_id = '';

    public string $pet_type = '';

    public string $pet_size = '';

    public string $service_type = '';

    public string $min_price = '';

    public string $max_price = '';

    public string $price_type = 'hour';

    public string $min_rating = '';

    public string $sort_by = 'relevance';

    public int $radius = 10;

    // Advanced filters
    public string $available_date = '';

    public string $start_time = '';

    public string $end_time = '';

    public string $max_pets = '';

    public bool $verified_only = false;

    public bool $instant_booking = false;

    public bool $flexible_cancellation = false;

    public string $experience_years = '';

    public bool $has_insurance = false;

    // UI state
    public bool $show_filters = false;

    protected $listeners = ['setFilters', 'resetFilters'];

    public function mount()
    {
        // Set default values from URL parameters
        $this->search_term = request('search', '');
        $this->location = request('location', '');
        $this->category_id = request('category_id', request('category', ''));
        $this->pet_type = request('pet_type', '');
    }

    public function updatedSearchTerm()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedLocation()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedCategoryId()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedPetType()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedPetSize()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedServiceType()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedMinPrice()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedMaxPrice()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedPriceType()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedMinRating()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedSortBy()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedRadius()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedAvailableDate()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedStartTime()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedEndTime()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedMaxPets()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedVerifiedOnly()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedInstantBooking()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedFlexibleCancellation()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedExperienceYears()
    {
        $this->dispatchFiltersUpdate();
    }

    public function updatedHasInsurance()
    {
        $this->dispatchFiltersUpdate();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search_term', 'location', 'category_id', 'pet_type', 'pet_size',
            'service_type', 'min_price', 'max_price', 'min_rating',
            'available_date', 'start_time', 'end_time', 'max_pets',
            'verified_only', 'instant_booking', 'flexible_cancellation',
            'experience_years', 'has_insurance',
        ]);
        $this->dispatchFiltersUpdate();
    }

    public function setFilters(array $filters): void
    {
        foreach ($filters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function resetFilters(): void
    {
        $this->clearFilters();
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;

        if ($this->search_term) {
            $count++;
        }
        if ($this->location) {
            $count++;
        }
        if ($this->category_id) {
            $count++;
        }
        if ($this->pet_type) {
            $count++;
        }
        if ($this->pet_size) {
            $count++;
        }
        if ($this->service_type) {
            $count++;
        }
        if ($this->min_price || $this->max_price) {
            $count++;
        }
        if ($this->min_rating) {
            $count++;
        }
        if ($this->available_date) {
            $count++;
        }
        if ($this->max_pets) {
            $count++;
        }
        if ($this->verified_only) {
            $count++;
        }
        if ($this->instant_booking) {
            $count++;
        }
        if ($this->experience_years) {
            $count++;
        }
        if ($this->has_insurance) {
            $count++;
        }

        return $count;
    }

    public function getCategoriesProperty()
    {
        return ServiceCategory::active()->ordered()->get();
    }

    public function getFiltersArrayProperty(): array
    {
        return [
            'search_term' => $this->search_term,
            'location' => $this->location,
            'category_id' => $this->category_id,
            'pet_type' => $this->pet_type,
            'pet_size' => $this->pet_size,
            'service_type' => $this->service_type,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'price_type' => $this->price_type,
            'min_rating' => $this->min_rating,
            'sort_by' => $this->sort_by,
            'radius' => $this->radius,
            'available_date' => $this->available_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_pets' => $this->max_pets,
            'verified_only' => $this->verified_only,
            'instant_booking' => $this->instant_booking,
            'flexible_cancellation' => $this->flexible_cancellation,
            'experience_years' => $this->experience_years,
            'has_insurance' => $this->has_insurance,
        ];
    }

    private function dispatchFiltersUpdate(): void
    {
        $this->dispatch('filters-updated', $this->filtersArray);
    }

    public function render()
    {
        return view('livewire.search.search-filters');
    }
}
