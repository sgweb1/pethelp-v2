<?php

namespace App\Livewire;

use App\Models\MapItem;
use App\Models\ServiceCategory;
use App\Models\PetType;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

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

    // Search state management
    protected $listeners = [
        'filters-updated' => 'handleFiltersUpdate',
        'location-detected' => 'handleLocationDetected',
        'address-selected' => 'handleAddressSelected',
        'search-saved' => 'handleSearchSaved',
        'map-bounds-changed' => 'handleMapBoundsChanged',
        'map-area-changed' => 'handleMapAreaChanged',
        'map-zoom-changed' => 'handleMapZoomChanged',
        'map-center-changed' => 'handleMapCenterChanged',
        'result-hovered' => 'handleResultHovered',
        'result-clicked' => 'handleResultClicked',
        'header-search-updated' => 'handleHeaderSearchUpdate',
        'update-browser-url' => 'handleBrowserUrlUpdate',
    ];

    public function mount(): void
    {
        // Initialize with URL parameters - service_type takes precedence over content_type
        // Default to 'pet_sitter' if no service type is specified
        $this->contentType = request('service_type', request('content_type', 'pet_sitter'));
        $this->petType = request('pet_type', '');

        \Log::info('🔧 Search::mount() - URL params', [
            'pet_type_url' => request('pet_type'),
            'petType_prop' => $this->petType,
            'contentType' => $this->contentType,
            'all_params' => request()->all()
        ]);

        // Initialize UI state from URL parameters
        $this->view_mode = request('view', 'grid');
        $this->show_desktop_map = request('map', 'true') === 'true';
        $this->sort_by = request('sort', 'relevance');

        // Always read pet_type from URL to handle direct URL access
        $urlPetType = request('pet_type', '');
        if ($urlPetType && $urlPetType !== $this->petType) {
            $this->petType = $urlPetType;
        }

        $this->filters = [
            'search_term' => request('search', ''),
            'location' => request('location', ''),
            'category_id' => request('category', ''),
            'pet_type' => $this->petType,
            'content_type' => $this->contentType,
            'sort_by' => $this->sort_by,
        ];

        \Log::info('🔧 Search::mount() - Filters set', ['filters' => $this->filters]);

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
    }

    public function updatedContentType($value): void
    {
        $this->filters['content_type'] = $value;
        $this->dispatch('filters-updated', $this->filters);
        $this->updateUrlWithFilters();
    }

    // Advanced filter property updates - instant URL updates for better UX
    public function updatedPetSize($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedPriceType($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedMinRating($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedMaxPets($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedExperienceYears($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedVerifiedOnly($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedInstantBooking($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedHasInsurance($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedAvailableDate($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedStartTime($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedEndTime($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedSortBy($value): void
    {
        $this->filters['sort_by'] = $value;

        // Clear search cache when sorting changes
        app(\App\Services\SearchCacheService::class)->invalidateSearchCache();

        $this->updateUrlWithFilters();
    }

    // Debounced updates for numeric inputs (will auto-trigger after debounce)
    public function updatedMinPrice($value): void
    {
        if (! empty($value)) {
            $this->updateUrlWithFilters();
        }
    }

    public function updatedMaxPrice($value): void
    {
        if (! empty($value)) {
            $this->updateUrlWithFilters();
        }
    }

    public function updatedRadius($value): void
    {
        $this->updateUrlWithFilters();
    }

    public function updatedFilters($value, $key): void
    {
        // Handle nested filter updates like filters.location
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);

        // Update URL with new filter parameters
        $this->updateUrlWithFilters();
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

        \Log::info('🎯 selectPetType called', [
            'petType_param' => $petType,
            'this_petType' => $this->petType,
            'filters_pet_type' => $this->filters['pet_type'],
            'currentPetType' => $this->currentPetType
        ]);

        $this->updateUrlWithFilters();
    }

    public function setCareType(string $categoryId): void
    {
        $this->filters['category_id'] = $categoryId;
        $this->updateUrlWithFilters();
    }

    public function clearLocation(): void
    {
        $this->filters['location'] = '';
        $this->updateUrlWithFilters();
    }

    public function handleFiltersUpdate(array $filters): void
    {
        $this->filters = $filters;

        // Propagate filters to all child components
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);

        // Update URL with new filter parameters
        $this->updateUrlWithFilters();
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

    public function handleAddressSelected(...$params): void
    {
        \Log::info('🎯 handleAddressSelected called', ['params' => $params, 'param_count' => count($params)]);

        $locationData = [];

        // Handle different parameter formats from Alpine.js event dispatching
        if (count($params) === 1 && is_array($params[0])) {
            // Format 1: Single object parameter (most common)
            $addressData = $params[0];
            $locationData = [
                'address' => $addressData['address'] ?? $addressData['value'] ?? '',
                'latitude' => $addressData['coordinates']['lat'] ?? null,
                'longitude' => $addressData['coordinates']['lng'] ?? null,
            ];
        } elseif (count($params) >= 4) {
            // Format 2: Multiple parameters (address, value, type, coordinates, description)
            $locationData = [
                'address' => $params[0] ?? '',
                'latitude' => $params[3]['lat'] ?? null,
                'longitude' => $params[3]['lng'] ?? null,
            ];
        } elseif (count($params) === 1 && is_string($params[0])) {
            // Format 3: Simple string parameter
            $locationData = [
                'address' => $params[0],
                'latitude' => null,
                'longitude' => null,
            ];
        } else {
            \Log::warning('🚨 Unexpected handleAddressSelected parameters', ['params' => $params]);
            return;
        }

        \Log::info('📍 Parsed location data:', ['data' => $locationData]);

        // Only update if we have a valid address
        if (empty($locationData['address'])) {
            \Log::warning('🚨 Empty address in handleAddressSelected', ['locationData' => $locationData]);
            return;
        }

        // Update filters with location data directly
        $this->filters['location'] = $locationData['address'];

        // Add coordinates if available
        if ($locationData['latitude'] && $locationData['longitude']) {
            $this->filters['latitude'] = $locationData['latitude'];
            $this->filters['longitude'] = $locationData['longitude'];
        }

        \Log::info('📍 Updated filters:', ['filters' => $this->filters]);

        // Propagate filters to all child components (like handleFiltersUpdate does)
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
        $this->dispatch('filters-updated', $this->filters);
        $this->dispatch('location-updated', $locationData);

        // Update URL with new location
        $this->updateUrlWithFilters();
    }

    public function handleMapBoundsChanged(array $bounds): void
    {
        // Update results when map bounds change (for viewport-based filtering)
        $this->dispatch('map-viewport-changed', $bounds);
    }

    public function handleMapAreaChanged(array $data): void
    {
        // Mapa zmieniła obszar widoku - odśwież wyniki
        $this->filters['map_bounds'] = $data['bounds'];
        $this->filters['zoom_level'] = $data['zoom'];

        // Propagate to search results
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleMapZoomChanged(array $data): void
    {
        // Mapa zmieniła poziom zoomu - odśwież wyniki jeśli potrzeba
        $this->filters['zoom_level'] = $data['zoom'];
        $this->filters['cluster_mode'] = $data['cluster_mode'];

        // Propagate to search results for potential re-clustering
        $this->dispatch('update-results-filters', $this->filters);
    }

    public function handleMapCenterChanged(array $data): void
    {
        // Mapa zmieniła centrum - aktualizuj lokalizację w filtrach
        $this->filters['latitude'] = $data['latitude'];
        $this->filters['longitude'] = $data['longitude'];

        // Propagate to search results
        $this->dispatch('update-results-filters', $this->filters);
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
            $petTypeMapping = [
                'cat' => 'kot',
                'dog' => 'psy', // Używamy liczby mnogiej jak w bazie
                'bird' => 'ptak',
                'rabbit' => 'królik',
                'other' => 'inne',
            ];

            $searchTerm = $petTypeMapping[$this->filters['pet_type']] ?? $this->filters['pet_type'];
            $query->where('category_name', 'like', "%{$searchTerm}%");
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
        $currentParams = array_filter($currentParams, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Build new URL
        $newUrl = request()->url().'?'.http_build_query($currentParams);

        // Update browser URL without page reload
        $this->dispatch('update-browser-url', $newUrl);
    }

    private function buildSearchParams(): array
    {
        return array_filter([
            'service_type' => $this->filters['content_type'] ?? $this->contentType,
            'pet_type' => $this->filters['pet_type'] ?? $this->petType,
            'search' => $this->filters['search_term'] ?? '',
            'location' => $this->filters['location'] ?? '',
            'category' => $this->filters['category_id'] ?? '',
            'pet_size' => $this->pet_size,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'price_type' => $this->price_type,
            'min_rating' => $this->min_rating,
            'available_date' => $this->available_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_pets' => $this->max_pets,
            'experience_years' => $this->experience_years,
            'view' => $this->view_mode,
            'map' => $this->show_desktop_map ? 'true' : 'false',
            'sort' => $this->sort_by,
            'verified_only' => $this->verified_only ? '1' : null,
            'instant_booking' => $this->instant_booking ? '1' : null,
            'has_insurance' => $this->has_insurance ? '1' : null,
            'flexible_cancellation' => $this->flexible_cancellation ? '1' : null,
        ], function ($value) {
            return $value !== '' && $value !== null && $value !== '0';
        });
    }

    private function updateUrlWithFilters(): void
    {
        $params = $this->buildSearchParams();
        \Log::info('🚀 updateUrlWithFilters', ['params' => $params, 'filters' => $this->filters]);

        // Instead of redirect, update URL without component remount
        $url = route('search', $params);
        $this->js("window.history.replaceState({}, '', '$url')");

        // Dispatch events to update child components
        $this->dispatch('search-filters-changed', $this->filters);
        $this->dispatch('update-map-filters', $this->filters);
        $this->dispatch('update-results-filters', $this->filters);
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
        $careTypes = Cache::remember('service_categories_active', 300, function () {
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

    #[Computed]
    public function petTypes()
    {
        return Cache::remember('pet_types_active', 300, function () {
            return PetType::active()->ordered()->get();
        });
    }

    #[Computed]
    public function currentPetType()
    {
        // Always return current pet_type from URL or property
        return request('pet_type', $this->petType ?: '');
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
