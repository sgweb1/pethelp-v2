<?php

namespace App\Livewire\Search;

use App\Models\MapItem;
use App\Services\SearchCacheService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchResults extends Component
{
    public array $filters = [];

    public string $viewMode = 'grid';

    public int $perPage = 12;

    public int $currentPage = 1;

    public bool $loading = false;

    public bool $hasMore = true;

    private static ?int $lastCacheClear = null;

    public function boot(): void
    {
        // Boot method for Livewire lifecycle
    }

    protected $listeners = [
        'filters-updated' => 'updateFilters',
        'update-results-filters' => 'updateFilters',
        'update-view-mode' => 'updateViewMode',
    ];

    public function listenForFiltersUpdate($filters)
    {
        \Log::info('📡 SearchResults received EVENT filters-updated', [
            'received_filters' => $filters,
            'current_filters' => $this->filters,
        ]);
        $this->updateFilters($filters);
    }

    public function mount($viewMode = 'grid', $filters = []): void
    {
        \Log::info('🚀 SearchResults mount called', [
            'viewMode' => $viewMode,
            'passed_filters' => $filters,
            'request_params' => request()->all(),
            'listeners' => $this->listeners,
        ]);

        // Initialize view mode
        $this->viewMode = $viewMode;

        // Use passed filters if available, otherwise fallback to URL parameters
        if (! empty($filters)) {
            $this->filters = $filters;
            \Log::info('✅ Using passed filters from parent component', [
                'filters' => $this->filters,
            ]);
        } else {
            // Initialize filters from URL parameters - default to pet_sitter
            $this->filters = [
                'content_type' => request('service_type', request('content_type', 'pet_sitter')),
                'search_term' => request('search', ''),
                'location' => request('location', ''),
                'category_name' => request('category', ''),
                'pet_type' => request('pet_type', ''),
                'sort_by' => request('sort', 'relevance'),
            ];
            \Log::info('✅ Using URL-based filters', [
                'filters' => $this->filters,
            ]);
        }

        \Log::info('✅ SearchResults mount completed', [
            'final_filters' => $this->filters,
            'has_location' => ! empty($this->filters['location']),
            'location_value' => $this->filters['location'] ?? 'EMPTY',
            'listeners_count' => count($this->listeners),
        ]);
    }

    public function updateFilters(array $filters): void
    {
        \Log::info('🎯 SearchResults updateFilters called', [
            'new_filters' => $filters,
            'old_filters' => $this->filters,
            'has_location_in_new' => ! empty($filters['location']),
            'location_value_new' => $filters['location'] ?? 'EMPTY',
            'has_location_in_old' => ! empty($this->filters['location']),
            'location_value_old' => $this->filters['location'] ?? 'EMPTY',
        ]);

        $this->filters = $filters;
        $this->currentPage = 1;
        $this->hasMore = true;

        // Clear cache when filters change
        $this->clearCache();
    }

    public function updatingFilters(): void
    {
        $this->currentPage = 1;
        $this->hasMore = true;

        // Clear cache when filters change
        $this->clearCache();
    }

    public function loadMore(): void
    {
        $this->loading = true;
        $this->currentPage++;
        $this->loading = false;
    }

    public function updateViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function getItemsProperty()
    {
        \Log::info('🔍 getItemsProperty called', [
            'filters' => $this->filters,
            'perPage' => $this->perPage,
            'currentPage' => $this->currentPage,
        ]);

        // 🚀 Use SearchCacheService via DI for better performance
        $cacheService = app(SearchCacheService::class);
        $limit = $this->perPage * $this->currentPage;

        \Log::info('📞 About to call getCachedSearchResults', [
            'filters' => $this->filters,
            'limit' => $limit,
        ]);

        $results = $cacheService->getCachedSearchResults($this->filters, $limit);

        \Log::info('✅ getCachedSearchResults completed', [
            'count' => $results->count(),
            'first_city' => $results->first()->city ?? 'NO_ITEMS',
        ]);

        // Load user relationship with availability for vacation check
        $results->load(['user.availability' => function ($query) {
            $query->where('is_available', false)
                  ->whereNotNull('vacation_end_date')
                  ->where('date', '<=', now()->toDateString())
                  ->where('vacation_end_date', '>=', now()->toDateString());
        }]);

        // Update hasMore flag based on results count
        $this->hasMore = $results->count() >= $limit;

        return $results;
    }

    #[Computed]
    public function resultsCount(): int
    {
        return $this->getOptimizedCount();
    }

    private function getOptimizedCount(): int
    {
        $cacheKey = $this->generateCacheKey('count');

        return Cache::remember($cacheKey, 300, function () {
            return $this->buildBaseQuery()->count();
        });
    }

    private function buildBaseQuery()
    {
        $query = MapItem::published();

        // Text search
        if (! empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
        }

        // Location-based search
        if (! empty($this->filters['location'])) {
            $query->where(function ($q) {
                $location = $this->filters['location'];

                // Clean location string - remove common words that interfere with search
                $cleanedLocation = str_replace(['województwo ', ', województwo'], '', $location);

                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('full_address', 'like', "%{$location}%")
                    ->orWhere('voivodeship', 'like', "%{$location}%");

                // If location was cleaned, also search with cleaned version
                if ($cleanedLocation !== $location) {
                    $q->orWhere('city', 'like', "%{$cleanedLocation}%")
                        ->orWhere('full_address', 'like', "%{$cleanedLocation}%")
                        ->orWhere('voivodeship', 'like', "%{$cleanedLocation}%");
                }
            });
        }

        // Content type filter (pet_sitter, service, etc.) - handle both content_type and service_type
        if (! empty($this->filters['content_type'])) {
            $query->byContentType($this->filters['content_type']);
        } elseif (! empty($this->filters['service_type'])) {
            // Map service_type to content_type for consistency with MapController
            $contentType = $this->mapServiceTypeToContentType($this->filters['service_type']);
            if ($contentType) {
                $query->byContentType($contentType);
            }
        }

        // Category filter by name
        if (! empty($this->filters['category_name'])) {
            $query->where('category_name', 'like', "%{$this->filters['category_name']}%");
        }

        // Pet type filter (stored in category_name) - map English to Polish
        if (! empty($this->filters['pet_type'])) {
            $searchTerm = $this->mapPetType($this->filters['pet_type']);
            $query->where('category_name', 'like', "%{$searchTerm}%");
        }

        // Price range filter
        if (! empty($this->filters['min_price']) || ! empty($this->filters['max_price'])) {
            $minPrice = ! empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = ! empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $query->priceRange($minPrice, $maxPrice);
        }

        // Rating filter
        if (! empty($this->filters['min_rating'])) {
            $query->where('rating_avg', '>=', $this->filters['min_rating']);
        }

        // Featured items
        if (! empty($this->filters['featured_only'])) {
            $query->featured();
        }

        // City filter
        if (! empty($this->filters['city'])) {
            $query->inCity($this->filters['city']);
        }

        // Voivodeship filter
        if (! empty($this->filters['voivodeship'])) {
            $query->inVoivodeship($this->filters['voivodeship']);
        }

        // Map bounds filtering (prioritize over radius-based filtering)
        if (! empty($this->filters['map_bounds']) && is_array($this->filters['map_bounds']) && count($this->filters['map_bounds']) === 4) {
            [$south, $west, $north, $east] = $this->filters['map_bounds'];
            $query->withinBounds($south, $north, $west, $east);
        } elseif (! empty($this->filters['latitude']) && ! empty($this->filters['longitude'])) {
            // Fallback to radius-based search
            $radius = $this->filters['radius'] ?? 10;
            $query->nearLocation($this->filters['latitude'], $this->filters['longitude'], $radius);
        }

        return $query;
    }

    private function mapPetType(string $petType): string
    {
        $petTypeMapping = [
            'cat' => 'kot',
            'dog' => 'psy', // Używamy liczby mnogiej jak w bazie
            'bird' => 'ptak',
            'rabbit' => 'królik',
            'other' => 'inne',
        ];

        return $petTypeMapping[$petType] ?? $petType;
    }

    /**
     * 🔄 Map service type to content type - SYNCHRONIZED with MapController and SearchCacheService
     */
    private function mapServiceTypeToContentType(string $serviceType): ?string
    {
        return match ($serviceType) {
            'pet_sitter' => 'pet_sitter', // 🔧 FIX: Don't map pet_sitter to service!
            'vet' => 'service',
            'supplies' => 'supplies',
            'event' => 'event',
            'adoption' => 'adoption',
            default => null,
        };
    }

    private function generateCacheKey(string $type): string
    {
        $keyData = [
            'type' => $type,
            'filters' => $this->filters,
            'view_mode' => $this->viewMode,
            'page' => $this->currentPage,
            'per_page' => $this->perPage,
        ];

        return 'search_results_'.$type.'_'.md5(serialize($keyData));
    }

    private function clearCache(): void
    {
        $now = time();
        $throttleSeconds = 5; // Czyść cache maksymalnie raz na 5 sekund

        // Sprawdź czy nie za wcześnie na kolejne czyszczenie
        if (self::$lastCacheClear !== null && ($now - self::$lastCacheClear) < $throttleSeconds) {
            return; // Pomiń czyszczenie cache
        }

        self::$lastCacheClear = $now;

        app(SearchCacheService::class)->invalidateSearchCache([
            'search_results_items_*',
            'search_results_count_*',
        ]);
    }

    /**
     * 🚀 Build API parameters for UnifiedSearchController
     */
    private function buildApiParams(): array
    {
        $params = [
            'format' => 'list',
            'limit' => $this->perPage,
            'page' => $this->currentPage,
        ];

        // Apply all filters from the filters array
        if (! empty($this->filters['content_type'])) {
            $params['content_type'] = $this->filters['content_type'];
        }

        if (! empty($this->filters['search_term'])) {
            $params['search_term'] = $this->filters['search_term'];
        }

        if (! empty($this->filters['location'])) {
            $params['location'] = $this->filters['location'];
        }

        if (! empty($this->filters['category_name'])) {
            $params['category'] = $this->filters['category_name'];
        }

        if (! empty($this->filters['pet_type'])) {
            // Map pet type for API consistency
            $petTypeMapping = [
                'cat' => 'kot',
                'dog' => 'psy',
                'bird' => 'ptak',
                'rabbit' => 'królik',
                'other' => 'inne',
            ];
            $params['pet_type'] = $petTypeMapping[$this->filters['pet_type']] ?? $this->filters['pet_type'];
        }

        if (! empty($this->filters['city'])) {
            $params['city'] = $this->filters['city'];
        }

        if (! empty($this->filters['voivodeship'])) {
            $params['voivodeship'] = $this->filters['voivodeship'];
        }

        // Geographic filters
        if (! empty($this->filters['map_bounds']) && is_array($this->filters['map_bounds']) && count($this->filters['map_bounds']) === 4) {
            $params['bounds'] = $this->filters['map_bounds'];
        } elseif (! empty($this->filters['latitude']) && ! empty($this->filters['longitude'])) {
            $params['latitude'] = $this->filters['latitude'];
            $params['longitude'] = $this->filters['longitude'];
            $params['radius'] = $this->filters['radius'] ?? 10;
        }

        // Price filters
        if (! empty($this->filters['min_price'])) {
            $params['min_price'] = $this->filters['min_price'];
        }

        if (! empty($this->filters['max_price'])) {
            $params['max_price'] = $this->filters['max_price'];
        }

        // Quality filters
        if (! empty($this->filters['min_rating'])) {
            $params['min_rating'] = $this->filters['min_rating'];
        }

        if (! empty($this->filters['featured_only'])) {
            $params['featured_only'] = true;
        }

        // Sorting
        if (! empty($this->filters['sort_by'])) {
            $params['sort_by'] = $this->filters['sort_by'];
        }

        return array_filter($params, function ($value) {
            return $value !== null && $value !== '' && $value !== [];
        });
    }

    public function render()
    {
        \Log::info('🎨 SearchResults render called', [
            'filters' => $this->filters,
            'items_count' => $this->items->count() ?? 'ERROR',
        ]);

        return view('livewire.search.search-results');
    }
}
