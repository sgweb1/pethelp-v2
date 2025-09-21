<?php

namespace App\Livewire\Search;

use App\Models\MapItem;
use App\Models\Service;
use App\Services\MapCacheService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchMap extends Component
{
    public bool $show_map = false;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public bool $location_detected = false;

    public int $radius = 10;

    public array $filters = [];

    public array $map_bounds = [];

    public int $zoom_level = 12;

    public array $selected_content_types = ['pet_sitter'];

    public bool $cluster_mode = false;

    protected $listeners = [
        'filters-updated' => 'updateFilters',
        'update-map-filters' => 'updateFilters',
        'location-updated' => 'handleLocationUpdate',
        'map-toggled' => 'toggleMap',
        'map-bounds-changed' => 'updateMapBounds',
        'map-zoom-changed' => 'updateZoomLevel',
        'highlight-map-marker' => 'highlightMarker',
        'focus-map-marker' => 'focusMarker',
    ];

    public function mount(): void
    {
        // Initialize filters from URL parameters
        $this->filters = [
            'content_type' => request('service_type', request('content_type', '')),
            'search_term' => request('search', ''),
            'location' => request('location', ''),
            'category_name' => request('category', ''),
            'pet_type' => request('pet_type', ''),
        ];

        // Initialize map state from URL parameters
        $this->latitude = request('lat') ? (float) request('lat') : null;
        $this->longitude = request('lng') ? (float) request('lng') : null;
        $this->zoom_level = request('zoom') ? (int) request('zoom') : 12;
        $this->radius = request('radius') ? (int) request('radius') : 10;

        // Set location detected if coordinates are provided
        if ($this->latitude && $this->longitude) {
            $this->location_detected = true;
        }

        // Set content types based on URL
        if (!empty($this->filters['content_type'])) {
            $this->selected_content_types = [$this->filters['content_type']];
        }
    }

    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->radius = $filters['radius'] ?? 10;

        // Update location if provided in filters
        if (isset($filters['latitude']) && isset($filters['longitude'])) {
            $this->latitude = $filters['latitude'];
            $this->longitude = $filters['longitude'];
            $this->location_detected = true;
        }

        // Auto-detect content types based on filters
        if (!empty($filters['content_type'])) {
            $this->selected_content_types = [$filters['content_type']];
        }

        // Auto-show map if we have location data
        if ($this->latitude && $this->longitude && !$this->show_map) {
            $this->show_map = true;
        }

        // Clear cache when filters change
        $this->clearMapCache();
    }

    public function handleLocationUpdate(array $locationData): void
    {
        $this->latitude = $locationData['latitude'];
        $this->longitude = $locationData['longitude'];
        $this->location_detected = true;

        // Auto-show map when location is detected
        if (!$this->show_map) {
            $this->show_map = true;
        }

        // Dispatch to frontend to update map center
        $this->dispatch('map-center-updated', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }

    public function highlightMarker($markerId = null): void
    {
        $this->dispatch('highlight-marker', ['markerId' => $markerId]);
    }

    public function focusMarker($markerId = null): void
    {
        $this->dispatch('focus-marker', ['markerId' => $markerId]);
    }

    public function toggleMap(): void
    {
        $this->show_map = ! $this->show_map;

        if ($this->show_map) {
            $this->dispatch('initialize-map');
        }
    }

    public function detectLocation(): void
    {
        $this->dispatch('detect-location');
    }

    public function setLocation(float $lat, float $lng, string $address = ''): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->location_detected = true;

        // Update filters with location
        $this->dispatch('location-detected', [
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
        ]);
    }

    public function updateMapBounds(array $bounds): void
    {
        $this->map_bounds = $bounds;
        $this->updateUrlState();
    }

    public function updateZoomLevel(int $zoom): void
    {
        $this->zoom_level = $zoom;

        // Enable clustering for lower zoom levels
        $this->cluster_mode = $zoom < 10;

        $this->updateUrlState();
    }

    public function updateMapCenter(float $lat, float $lng): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->location_detected = true;

        $this->updateUrlState();
    }

    private function updateUrlState(): void
    {
        $currentParams = request()->query();

        // Update map parameters
        if ($this->latitude && $this->longitude) {
            $currentParams['lat'] = round($this->latitude, 6);
            $currentParams['lng'] = round($this->longitude, 6);
        }

        $currentParams['zoom'] = $this->zoom_level;
        $currentParams['radius'] = $this->radius;

        // Remove empty parameters
        $currentParams = array_filter($currentParams, function($value) {
            return $value !== '' && $value !== null;
        });

        // Build new URL
        $newUrl = request()->url() . '?' . http_build_query($currentParams);

        // Update browser URL without page reload
        $this->dispatch('update-browser-url', $newUrl);
    }

    public function toggleContentType(string $contentType): void
    {
        if (in_array($contentType, $this->selected_content_types)) {
            $this->selected_content_types = array_diff($this->selected_content_types, [$contentType]);
        } else {
            $this->selected_content_types[] = $contentType;
        }

        // Clear cache when content types change
        $this->clearMapCache();
    }

    public function clearMapCache(): void
    {
        $cacheService = app(MapCacheService::class);
        $cacheService->invalidateMapCache();
    }

    #[Computed]
    public function clusterData()
    {
        if (!$this->cluster_mode || empty($this->map_bounds) || count($this->map_bounds) !== 4) {
            return ['clusters' => [], 'markers' => []];
        }

        $cacheService = app(MapCacheService::class);
        return $cacheService->getCachedClusterData($this->map_bounds, $this->zoom_level);
    }

    private function mapSearchTypeToContentTypes(string $searchType): array
    {
        return match($searchType) {
            'event_public' => ['event_public'],
            'advertisement' => ['advertisement'],
            'service' => ['service'],
            'pet_sitter' => ['pet_sitter'],
            default => ['pet_sitter']
        };
    }

    #[Computed]
    public function mapItems()
    {
        $cacheService = app(MapCacheService::class);

        $filters = [
            'content_types' => $this->selected_content_types,
            'search_term' => $this->filters['search_term'] ?? null,
            'bounds' => !empty($this->map_bounds) && count($this->map_bounds) === 4 ? $this->map_bounds : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'price_min' => $this->filters['price_min'] ?? null,
            'price_max' => $this->filters['price_max'] ?? null,
            'zoom_level' => $this->zoom_level,
            'city' => $this->filters['city'] ?? null,
        ];

        $limit = $this->cluster_mode ? 1000 : 100;

        return $cacheService->getCachedMapItems($filters, $limit);
    }

    #[Computed]
    public function directMapItems()
    {
        // Direct query method for simple filtering without cache
        $query = MapItem::published()
            ->with(['user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Apply content type filter
        if (!empty($this->selected_content_types)) {
            $query->whereIn('content_type', $this->selected_content_types);
        }

        // Apply search filters
        if (!empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
        }

        if (!empty($this->filters['content_type'])) {
            $query->where('content_type', $this->filters['content_type']);
        }

        if (!empty($this->filters['pet_type'])) {
            $query->where('category_name', 'like', "%{$this->filters['pet_type']}%");
        }

        // Location-based filtering with radius
        if ($this->latitude && $this->longitude) {
            $query->nearLocation($this->latitude, $this->longitude, $this->radius);
        }

        // Map bounds filtering for performance
        if (!empty($this->map_bounds) && count($this->map_bounds) === 4) {
            [$south, $west, $north, $east] = $this->map_bounds;
            $query->withinBounds($south, $north, $west, $east);
        }

        // Price range filter
        if (!empty($this->filters['min_price']) || !empty($this->filters['max_price'])) {
            $minPrice = !empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = !empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $query->priceRange($minPrice, $maxPrice);
        }

        return $query->limit(100)->get();
    }

    #[Computed]
    public function mapStatistics()
    {
        $cacheService = app(MapCacheService::class);

        $filters = [
            'content_types' => $this->selected_content_types,
            'search_term' => $this->filters['search_term'] ?? null,
            'bounds' => !empty($this->map_bounds) && count($this->map_bounds) === 4 ? $this->map_bounds : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'price_min' => $this->filters['price_min'] ?? null,
            'price_max' => $this->filters['price_max'] ?? null,
            'zoom_level' => $this->zoom_level,
            'city' => $this->filters['city'] ?? null,
        ];

        return $cacheService->getCachedStatistics($filters);
    }

    #[Computed]
    public function availableContentTypes()
    {
        return [
            'pet_sitter' => ['name' => 'Pet Sitters', 'icon' => 'dog', 'color' => 'purple'],
            'service' => ['name' => 'Usługi', 'icon' => 'briefcase', 'color' => 'blue'],
            'event_public' => ['name' => 'Wydarzenia', 'icon' => 'calendar', 'color' => 'green'],
            'advertisement' => ['name' => 'Ogłoszenia', 'icon' => 'megaphone', 'color' => 'orange'],
        ];
    }

    public function render()
    {
        return view('livewire.search.search-map');
    }
}
