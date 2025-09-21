<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\MapItem;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Cache;

class Search extends Component
{
    // UI state
    public bool $show_map = false;
    public bool $show_mobile_map = false;
    public bool $show_desktop_map = true;
    public string $sort_by = 'relevance';
    public string $contentType = '';
    public string $petType = '';
    public string $view_mode = 'grid'; // grid or list

    public array $filters = [];

    // Advanced filter properties
    public string $pet_size = '';
    public string $min_price = '';
    public string $max_price = '';
    public string $price_type = 'hour';
    public string $min_rating = '';
    public string $available_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $max_pets = '';
    public bool $verified_only = false;
    public bool $instant_booking = false;
    public bool $flexible_cancellation = false;
    public string $experience_years = '';
    public bool $has_insurance = false;
    public int $radius = 10;

    // Search state management
    protected $listeners = [
        'filters-updated' => 'handleFiltersUpdate',
        'location-detected' => 'handleLocationDetected',
        'search-saved' => 'handleSearchSaved',
        'map-bounds-changed' => 'handleMapBoundsChanged',
        'result-hovered' => 'handleResultHovered',
        'result-clicked' => 'handleResultClicked',
        'header-search-updated' => 'handleHeaderSearchUpdate',
        'update-browser-url' => 'handleBrowserUrlUpdate',
    ];

    public function mount(): void
    {
        // Initialize with URL parameters - service_type takes precedence over content_type
        $this->contentType = request('service_type', request('content_type', ''));
        $this->petType = request('pet_type', '');

        // Initialize UI state from URL parameters
        $this->view_mode = request('view', 'grid');
        $this->show_desktop_map = request('map', 'true') === 'true';

        $this->filters = [
            'search_term' => request('search', ''),
            'location' => request('location', ''),
            'category_id' => request('category', ''),
            'pet_type' => $this->petType,
            'content_type' => $this->contentType,
        ];

        // Initialize advanced filters from URL params
        $this->pet_size = request('pet_size', '');
        $this->min_price = request('min_price', '');
        $this->max_price = request('max_price', '');
        $this->price_type = request('price_type', 'hour');
        $this->min_rating = request('min_rating', '');
        $this->available_date = request('available_date', '');
        $this->start_time = request('start_time', '');
        $this->end_time = request('end_time', '');
        $this->max_pets = request('max_pets', '');
        $this->verified_only = (bool) request('verified_only', false);
        $this->instant_booking = (bool) request('instant_booking', false);
        $this->flexible_cancellation = (bool) request('flexible_cancellation', false);
        $this->experience_years = request('experience_years', '');
        $this->has_insurance = (bool) request('has_insurance', false);
        $this->radius = (int) request('radius', 10);
    }

    public function updatedContentType($value): void
    {
        $this->filters['content_type'] = $value;
        $this->dispatch('filters-updated', $this->filters);
    }

    public function updatedFilters($value, $key): void
    {
        // Handle nested filter updates like filters.location
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function selectCategory(string $categoryType): void
    {
        $this->contentType = $categoryType;
        $this->filters['content_type'] = $categoryType;

        // Update URL with new parameters
        $params = array_filter([
            'service_type' => $categoryType,
            'search' => $this->filters['search_term'] ?? '',
            'location' => $this->filters['location'] ?? '',
            'category' => $this->filters['category_id'] ?? '',
            'pet_type' => $this->filters['pet_type'] ?? '',
        ]);

        $this->redirect(route('search', $params), navigate: true);
    }

    public function selectPetType(string $petType): void
    {
        $this->petType = $petType;
        $this->filters['pet_type'] = $petType;

        // Propagate to child components
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function setCareType(string $categoryId): void
    {
        $this->filters['category_id'] = $categoryId;

        // Propagate to child components
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleFiltersUpdate(array $filters): void
    {
        $this->filters = $filters;

        // Propagate filters to all child components
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleLocationDetected(array $locationData): void
    {
        $this->filters = array_merge($this->filters, [
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
            'location' => $locationData['address'],
        ]);

        // Update all child components with new location
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('location-updated', $locationData);
    }

    public function handleMapBoundsChanged(array $bounds): void
    {
        // Update results when map bounds change (for viewport-based filtering)
        $this->dispatch('map-viewport-changed', $bounds);
    }

    public function handleResultHovered(?int $resultId): void
    {
        // Highlight corresponding marker on map
        $this->dispatch('highlight-map-marker', $resultId);
    }

    public function handleResultClicked(int $resultId): void
    {
        // Focus on specific marker on map
        $this->dispatch('focus-map-marker', $resultId);
    }

    public function handleHeaderSearchUpdate(array $headerData): void
    {
        // Update filters with data from header
        $this->filters = array_merge($this->filters, [
            'location' => $headerData['location'] ?? '',
            'check_in' => $headerData['check_in'] ?? '',
            'check_out' => $headerData['check_out'] ?? '',
            'guests' => $headerData['guests'] ?? 1,
            'pet_type' => $headerData['pet_type'] ?? '',
        ]);

        // Propagate to all child components
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleSearchSaved(): void
    {
        if (! auth()->check()) {
            return;
        }

        // Save search to session for now (can be extended to database)
        session()->put('last_search', $this->filters);
    }

    public function toggleMap(): void
    {
        $this->show_map = ! $this->show_map;
        $this->dispatch('map-toggled');
    }

    public function toggleDesktopMap(): void
    {
        $this->show_desktop_map = ! $this->show_desktop_map;
        $this->dispatch('desktop-map-toggled', $this->show_desktop_map);
        $this->updateUrlWithCurrentState();
    }

    public function setViewMode(string $mode): void
    {
        $this->view_mode = $mode;
        $this->dispatch('view-mode-changed', $mode);
        $this->dispatch('update-view-mode', $mode);
        $this->updateUrlWithCurrentState();
    }

    public function saveSearch(): void
    {
        $this->dispatch('search-saved');
    }

    public function loadSavedSearch(): void
    {
        if (! auth()->check()) {
            return;
        }

        $searchData = session()->get('last_search');
        if ($searchData) {
            $this->filters = array_merge($this->filters, $searchData);
            $this->dispatch('filters-updated', $this->filters);
        }
    }

    #[Computed]
    public function totalResults(): int
    {
        $query = MapItem::published();

        // If no filters applied, return all published items
        $hasActiveFilters = false;

        // Apply same filters as SearchResults component
        if (! empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
            $hasActiveFilters = true;
        }

        if (! empty($this->filters['location'])) {
            $query->where(function ($q) {
                $q->where('city', 'like', "%{$this->filters['location']}%")
                    ->orWhere('full_address', 'like', "%{$this->filters['location']}%");
            });
            $hasActiveFilters = true;
        }

        if (! empty($this->filters['content_type'])) {
            $query->byContentType($this->filters['content_type']);
            $hasActiveFilters = true;
        }

        if (! empty($this->filters['category_id'])) {
            $query->where('category_name', 'like', "%{$this->filters['category_id']}%");
            $hasActiveFilters = true;
        }

        if (! empty($this->filters['pet_type'])) {
            $query->where('category_name', 'like', "%{$this->filters['pet_type']}%");
            $hasActiveFilters = true;
        }

        if (! empty($this->min_price) || ! empty($this->max_price)) {
            $minPrice = ! empty($this->min_price) ? (float) $this->min_price : null;
            $maxPrice = ! empty($this->max_price) ? (float) $this->max_price : null;
            $query->priceRange($minPrice, $maxPrice);
            $hasActiveFilters = true;
        }

        if (! empty($this->min_rating)) {
            $query->where('rating_avg', '>=', $this->min_rating);
            $hasActiveFilters = true;
        }

        return $query->count();
    }

    public function handleBrowserUrlUpdate(string $url): void
    {
        // This method is called when child components want to update the URL
        $this->dispatch('update-browser-url', $url);
    }

    private function updateUrlWithCurrentState(): void
    {
        $currentParams = request()->query();

        // Update UI state parameters
        $currentParams['view'] = $this->view_mode;
        $currentParams['map'] = $this->show_desktop_map ? 'true' : 'false';

        // Remove empty parameters to keep URL clean
        $currentParams = array_filter($currentParams, function($value) {
            return $value !== '' && $value !== null;
        });

        // Build new URL
        $newUrl = request()->url() . '?' . http_build_query($currentParams);

        // Update browser URL without page reload
        $this->dispatch('update-browser-url', $newUrl);
    }

    public function getCategoryCount(string $contentType): int
    {
        $cacheKey = "category_count_{$contentType}";

        return Cache::remember($cacheKey, 300, function () use ($contentType) {
            return MapItem::where('content_type', $contentType)
                ->where('status', 'published')
                ->count();
        });
    }

    #[Computed]
    public function careTypes()
    {
        $careTypes = Cache::remember('service_categories_active', 300, function() {
            return ServiceCategory::active()->ordered()->get();
        });

        // Move selected care type to first position
        $selectedId = $this->filters['category_id'] ?? null;
        if ($selectedId) {
            $selected = $careTypes->where('id', $selectedId)->first();
            if ($selected) {
                $others = $careTypes->where('id', '!=', $selectedId);
                return collect([$selected])->merge($others);
            }
        }

        return $careTypes;
    }

    public function isMobile(): bool
    {
        $userAgent = request()->header('User-Agent');

        // Basic mobile detection
        return preg_match('/Mobile|Android|iPhone|iPad|Windows Phone/', $userAgent) === 1;
    }

    public function render()
    {
        return view('livewire.search');
    }
}
