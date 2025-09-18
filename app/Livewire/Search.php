<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\ServiceCategory;

class Search extends Component
{
    use WithPagination;

    // Search filters
    public $search_term = '';
    public $location = '';
    public $category_id = '';
    public $pet_type = '';
    public $pet_size = '';
    public $service_type = '';
    public $min_price = '';
    public $max_price = '';
    public $price_type = 'hour';
    public $min_rating = '';
    public $sort_by = 'relevance';
    public $radius = 10;

    // Advanced filters
    public $available_date = '';
    public $start_time = '';
    public $end_time = '';
    public $max_pets = '';
    public $verified_only = false;
    public $instant_booking = false;
    public $flexible_cancellation = false;
    public $experience_years = '';
    public $has_insurance = false;

    // Location detection
    public $latitude = null;
    public $longitude = null;
    public $location_detected = false;

    // UI state
    public $show_filters = false;
    public $show_map = false;

    public function mount()
    {
        // Set default values from URL parameters
        $this->search_term = request('search', '');
        $this->location = request('location', '');
        $this->category_id = request('category', '');
        $this->pet_type = request('pet_type', '');
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search_term', 'location', 'category_id', 'pet_type', 'pet_size',
            'service_type', 'min_price', 'max_price', 'min_rating',
            'available_date', 'start_time', 'end_time', 'max_pets',
            'verified_only', 'instant_booking', 'flexible_cancellation',
            'experience_years', 'has_insurance'
        ]);
        $this->resetPage();
    }

    public function updatingAvailableDate()
    {
        $this->resetPage();
    }

    public function updatingMaxPets()
    {
        $this->resetPage();
    }

    public function updatingVerifiedOnly()
    {
        $this->resetPage();
    }

    public function detectLocation()
    {
        // This will be handled by JavaScript
        $this->dispatch('detect-location');
    }

    public function setLocation($lat, $lng, $address = '')
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->location = $address;
        $this->location_detected = true;
        $this->resetPage();
    }

    public function updatedShowMap()
    {
        if ($this->show_map) {
            $this->dispatch('map-toggled');
        }
    }

    public function getCategoriesProperty()
    {
        return ServiceCategory::active()->ordered()->get();
    }

    public function getServicesProperty()
    {
        $query = Service::active()
            ->with(['sitter', 'category', 'sitter.locations', 'sitter.profile']);

        // Text search
        if ($this->search_term) {
            $searchTerm = '%' . $this->search_term . '%';
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
        if ($this->latitude && $this->longitude) {
            $query->byLocation($this->latitude, $this->longitude, $this->radius);
        } elseif ($this->location) {
            // Simple city/area search
            $query->whereHas('sitter.locations', function ($q) {
                $q->where('city', 'like', "%{$this->location}%")
                  ->orWhere('street', 'like', "%{$this->location}%");
            });
        }

        // Category filter
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        // Pet type filter
        if ($this->pet_type) {
            $query->byPetType($this->pet_type);
        }

        // Pet size filter
        if ($this->pet_size) {
            $query->byPetSize($this->pet_size);
        }

        // Service type filter
        if ($this->service_type) {
            $query->byServiceType($this->service_type);
        }

        // Price range filter
        if ($this->min_price || $this->max_price) {
            $minPrice = $this->min_price ? (float) $this->min_price : null;
            $maxPrice = $this->max_price ? (float) $this->max_price : null;
            $query->byPriceRange($minPrice, $maxPrice, $this->price_type);
        }

        // Rating filter
        if ($this->min_rating) {
            $query->minRating($this->min_rating);
        }

        // Advanced filters

        // Max pets filter
        if ($this->max_pets) {
            $query->where('max_pets', '>=', $this->max_pets);
        }

        // Availability filter
        if ($this->available_date) {
            $query->whereHas('sitter.availability', function ($q) {
                $q->where('date', $this->available_date)
                  ->where('is_available', true);

                if ($this->start_time) {
                    $q->where('start_time', '<=', $this->start_time);
                }
                if ($this->end_time) {
                    $q->where('end_time', '>=', $this->end_time);
                }
            });
        }

        // Verified sitters only
        if ($this->verified_only) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('is_verified', true);
            });
        }

        // Instant booking
        if ($this->instant_booking) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('instant_booking', true);
            });
        }

        // Experience years
        if ($this->experience_years) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('experience_years', '>=', $this->experience_years);
            });
        }

        // Insurance
        if ($this->has_insurance) {
            $query->whereHas('sitter.profile', function ($q) {
                $q->where('has_insurance', true);
            });
        }

        // Sorting
        switch ($this->sort_by) {
            case 'price_low':
                $column = $this->price_type === 'day' ? 'price_per_day' : 'price_per_hour';
                $query->orderBy($column, 'asc');
                break;
            case 'price_high':
                $column = $this->price_type === 'day' ? 'price_per_day' : 'price_per_hour';
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
            case 'distance':
                if ($this->latitude && $this->longitude) {
                    $query->select('services.*')
                          ->join('users', 'users.id', '=', 'services.sitter_id')
                          ->join('locations', 'locations.user_id', '=', 'users.id')
                          ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude)))) AS distance',
                                     [$this->latitude, $this->longitude, $this->latitude])
                          ->orderBy('distance');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
            case 'most_booked':
                $query->withCount('bookings')->orderBy('bookings_count', 'desc');
                break;
            default: // relevance
                if ($this->latitude && $this->longitude) {
                    // Relevance with location priority
                    $query->select('services.*')
                          ->join('users', 'users.id', '=', 'services.sitter_id')
                          ->join('locations', 'locations.user_id', '=', 'users.id')
                          ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude)))) AS distance',
                                     [$this->latitude, $this->longitude, $this->latitude])
                          ->orderBy('distance')
                          ->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
        }

        return $query->paginate(12);
    }

    public function getResultsCountProperty()
    {
        return $this->services->total();
    }

    public function saveSearch()
    {
        if (!auth()->check()) {
            return;
        }

        $searchData = [
            'location' => $this->location,
            'category_id' => $this->category_id,
            'pet_type' => $this->pet_type,
            'pet_size' => $this->pet_size,
            'service_type' => $this->service_type,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'price_type' => $this->price_type,
            'min_rating' => $this->min_rating,
            'radius' => $this->radius,
        ];

        // Save to user's saved searches (we can implement this later)
        session()->put('last_search', $searchData);

        $this->dispatch('search-saved');
    }

    public function loadSavedSearch()
    {
        if (!auth()->check()) {
            return;
        }

        $searchData = session()->get('last_search');
        if ($searchData) {
            foreach ($searchData as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            $this->resetPage();
        }
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;

        if ($this->search_term) $count++;
        if ($this->location) $count++;
        if ($this->category_id) $count++;
        if ($this->pet_type) $count++;
        if ($this->pet_size) $count++;
        if ($this->service_type) $count++;
        if ($this->min_price || $this->max_price) $count++;
        if ($this->min_rating) $count++;
        if ($this->available_date) $count++;
        if ($this->max_pets) $count++;
        if ($this->verified_only) $count++;
        if ($this->instant_booking) $count++;
        if ($this->experience_years) $count++;
        if ($this->has_insurance) $count++;

        return $count;
    }

    public function render()
    {
        return view('livewire.search');
    }
}
