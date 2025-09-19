<?php

namespace App\Livewire\Search;

use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SearchResults extends Component
{
    use WithPagination;

    public array $filters = [];

    public bool $loading = false;

    protected $listeners = ['filters-updated' => 'updateFilters'];

    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->resetPage();
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function getServicesProperty()
    {
        if (empty($this->filters)) {
            return Service::active()
                ->with(['sitter', 'category', 'sitter.locations', 'sitter.profile'])
                ->paginate(12);
        }

        $query = Service::active()
            ->with(['sitter', 'category', 'sitter.locations', 'sitter.profile']);

        // Text search
        if (! empty($this->filters['search_term'])) {
            $searchTerm = '%'.$this->filters['search_term'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('sitter', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('sitter.profile', function ($sq) use ($searchTerm) {
                        $sq->where('first_name', 'like', $searchTerm)
                            ->orWhere('last_name', 'like', $searchTerm);
                    })
                    ->orWhereHas('category', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Location-based search
        if (! empty($this->filters['location'])) {
            $query->whereHas('sitter.locations', function ($q) {
                $q->where('city', 'like', "%{$this->filters['location']}%")
                    ->orWhere('street', 'like', "%{$this->filters['location']}%");
            });
        }

        // Category filter
        if (! empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        // Pet type filter
        if (! empty($this->filters['pet_type'])) {
            $query->byPetType($this->filters['pet_type']);
        }

        // Pet size filter
        if (! empty($this->filters['pet_size'])) {
            $query->byPetSize($this->filters['pet_size']);
        }

        // Service type filter
        if (! empty($this->filters['service_type'])) {
            $query->byServiceType($this->filters['service_type']);
        }

        // Price range filter
        if (! empty($this->filters['min_price']) || ! empty($this->filters['max_price'])) {
            $minPrice = ! empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = ! empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $priceType = $this->filters['price_type'] ?? 'hour';
            $query->byPriceRange($minPrice, $maxPrice, $priceType);
        }

        // Rating filter
        if (! empty($this->filters['min_rating'])) {
            $query->minRating($this->filters['min_rating']);
        }

        // Advanced filters
        if (! empty($this->filters['max_pets'])) {
            $query->where('max_pets', '>=', $this->filters['max_pets']);
        }

        // Availability filter
        if (! empty($this->filters['available_date'])) {
            $query->whereHas('sitter.availability', function ($q) {
                $q->where('date', $this->filters['available_date'])
                    ->where('is_available', true);

                if (! empty($this->filters['start_time'])) {
                    $q->where('start_time', '<=', $this->filters['start_time']);
                }
                if (! empty($this->filters['end_time'])) {
                    $q->where('end_time', '>=', $this->filters['end_time']);
                }
            });
        }

        // Verified sitters only
        if (! empty($this->filters['verified_only'])) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('is_verified', true);
            });
        }

        // Instant booking
        if (! empty($this->filters['instant_booking'])) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('instant_booking', true);
            });
        }

        // Experience years
        if (! empty($this->filters['experience_years'])) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('experience_years', '>=', $this->filters['experience_years']);
            });
        }

        // Insurance
        if (! empty($this->filters['has_insurance'])) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('has_insurance', true);
            });
        }

        // Sorting
        $sortBy = $this->filters['sort_by'] ?? 'relevance';
        switch ($sortBy) {
            case 'price_low':
                $column = ($this->filters['price_type'] ?? 'hour') === 'day' ? 'price_per_day' : 'price_per_hour';
                $query->orderBy($column, 'asc');
                break;
            case 'price_high':
                $column = ($this->filters['price_type'] ?? 'hour') === 'day' ? 'price_per_day' : 'price_per_hour';
                $query->orderBy($column, 'desc');
                break;
            case 'rating':
                $query->withAvgRating()->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'experience':
                $query->join('user_profiles', 'user_profiles.user_id', '=', 'services.sitter_id')
                    ->orderBy('user_profiles.experience_years', 'desc');
                break;
            case 'most_booked':
                $query->withCount('bookings')->orderBy('bookings_count', 'desc');
                break;
            default: // relevance
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(12);
    }

    #[Computed]
    public function resultsCount(): int
    {
        return $this->services->total();
    }

    public function render()
    {
        return view('livewire.search.search-results');
    }
}
