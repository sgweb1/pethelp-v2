<?php

namespace App\Livewire\Search;

use App\Models\MapItem;
use App\Services\SearchCacheService;
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

    private static ?int $lastMapCacheClear = null;

    protected $listeners = [
        'filters-updated' => 'updateFilters',
        'update-map-filters' => 'updateFilters',
        'location-updated' => 'handleLocationUpdate',
        'map-toggled' => 'toggleMap',
        'map-bounds-changed' => 'updateMapBounds',
        // Removed 'map-zoom-changed' to prevent infinite loop
        'highlight-map-marker' => 'highlightMarker',
        'focus-map-marker' => 'focusMarker',
    ];

    public function mount(): void
    {
        // Initialize filters from URL parameters - default to pet_sitter
        $this->filters = [
            'content_type' => request('service_type', request('content_type', 'pet_sitter')),
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

        // Initialize map bounds from URL if provided
        if (request('bounds')) {
            $boundsString = request('bounds');
            $boundsArray = explode(',', $boundsString);
            if (count($boundsArray) === 4) {
                $this->map_bounds = array_map('floatval', $boundsArray);
            }
        }

        // Set location detected if coordinates are provided
        if ($this->latitude && $this->longitude) {
            $this->location_detected = true;
        } elseif (! empty($this->filters['location'])) {
            // Try to get coordinates from location name if no lat/lng provided
            $this->resolveLocationCoordinates($this->filters['location']);
        }

        // Set content types based on URL
        if (! empty($this->filters['content_type'])) {
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
        if (! empty($filters['content_type'])) {
            $this->selected_content_types = [$filters['content_type']];
        }

        // Auto-show map if we have location data
        if ($this->latitude && $this->longitude && ! $this->show_map) {
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
        if (! $this->show_map) {
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

        // Don't clear cache on bounds changes - causes infinite loop
        // Cache will be cleared when filters actually change

        // Dispatch event to update search results based on new map area
        $this->dispatch('map-area-changed', [
            'bounds' => $bounds,
            'zoom' => $this->zoom_level,
        ]);
    }

    public function updateZoomLevel($zoom): void
    {
        // Handle both direct int and array from event
        if (is_array($zoom)) {
            $zoom = $zoom['zoom'] ?? $zoom[0] ?? 12;
        }

        $this->zoom_level = (int) $zoom;

        // Enable clustering for lower zoom levels
        $this->cluster_mode = $zoom < 10;

        // Don't clear cache on zoom changes - causes infinite loop
        // Cache will be cleared when filters actually change

        // Dispatch event to update search results based on new zoom level
        $this->dispatch('map-zoom-changed', [
            'zoom' => $zoom,
            'cluster_mode' => $this->cluster_mode,
        ]);
    }

    public function updateMapCenter(float $lat, float $lng): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->location_detected = true;

        // Don't clear cache on center changes - causes infinite loop
        // Cache will be cleared when filters actually change

        // Dispatch event to update search results based on new center
        $this->dispatch('map-center-changed', [
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }

    public function updateUrlFromMapState(): void
    {
        // This method can be called explicitly when we want to update URL
        // For example, when user finishes dragging the map
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

        // Include map bounds for area-based searching
        if (! empty($this->map_bounds) && count($this->map_bounds) === 4) {
            $currentParams['bounds'] = implode(',', array_map(function ($bound) {
                return round($bound, 6);
            }, $this->map_bounds));
        }

        // Remove empty parameters
        $currentParams = array_filter($currentParams, function ($value) {
            return $value !== '' && $value !== null;
        });

        // Use proper redirect to search route instead of dispatch
        $this->redirect(route('search', $currentParams), navigate: true);
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
        $now = time();
        $throttleSeconds = 3; // Czyść cache mapy maksymalnie raz na 3 sekundy

        // Sprawdź czy nie za wcześnie na kolejne czyszczenie
        if (self::$lastMapCacheClear !== null && ($now - self::$lastMapCacheClear) < $throttleSeconds) {
            return; // Pomiń czyszczenie cache
        }

        self::$lastMapCacheClear = $now;

        // 🚀 Use unified SearchCacheService instead of MapCacheService
        $cacheService = app(SearchCacheService::class);
        $cacheService->invalidateSearchCache(['search:map:*']);
    }

    #[Computed]
    public function clusterData()
    {
        if (! $this->cluster_mode || empty($this->map_bounds) || count($this->map_bounds) !== 4) {
            return ['clusters' => [], 'markers' => []];
        }

        // For now, use simplified clustering via mapItems
        // TODO: Implement clustering in SearchCacheService if needed
        $items = $this->mapItems();

        return [
            'clusters' => [],
            'markers' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'lat' => (float) $item->latitude,
                    'lng' => (float) $item->longitude,
                    'title' => $item->title,
                    'content_type' => $item->content_type,
                    'is_featured' => (bool) $item->is_featured,
                    'is_urgent' => (bool) $item->is_urgent,
                ];
            })->toArray(),
        ];
    }

    private function mapSearchTypeToContentTypes(string $searchType): array
    {
        return match ($searchType) {
            'event_public' => ['event_public'],
            'advertisement' => ['advertisement'],
            'service' => ['service'],
            'pet_sitter' => ['pet_sitter'],
            default => ['pet_sitter']
        };
    }

    /**
     * 🚀 Build API parameters for UnifiedSearchController
     */
    private function buildApiParams(): array
    {
        $params = [
            'format' => 'map',
            'limit' => $this->cluster_mode ? 500 : 100,
        ];

        // Content type filter - use first selected type
        if (! empty($this->selected_content_types)) {
            $params['content_type'] = $this->selected_content_types[0];
        }

        // Search filters
        if (! empty($this->filters['search_term'])) {
            $params['search_term'] = $this->filters['search_term'];
        }

        if (! empty($this->filters['location'])) {
            $params['location'] = $this->filters['location'];
        }

        if (! empty($this->filters['city'])) {
            $params['city'] = $this->filters['city'];
        }

        if (! empty($this->filters['voivodeship'])) {
            $params['voivodeship'] = $this->filters['voivodeship'];
        }

        // Geographic filters - prioritize bounds over radius
        if (! empty($this->map_bounds) && count($this->map_bounds) === 4) {
            $params['bounds'] = $this->map_bounds;
        } elseif ($this->latitude && $this->longitude) {
            $params['latitude'] = $this->latitude;
            $params['longitude'] = $this->longitude;
            $params['radius'] = $this->radius;
        }

        // Price filters
        if (! empty($this->filters['min_price'])) {
            $params['min_price'] = $this->filters['min_price'];
        }

        if (! empty($this->filters['max_price'])) {
            $params['max_price'] = $this->filters['max_price'];
        }

        // Zoom level for clustering
        if ($this->zoom_level) {
            $params['zoom_level'] = $this->zoom_level;
        }

        return array_filter($params, function ($value) {
            return $value !== null && $value !== '' && $value !== [];
        });
    }

    #[Computed]
    public function mapItems()
    {
        // 🚀 Use unified SearchCacheService for consistent results across map and list
        $cacheService = app(SearchCacheService::class);

        $filters = [
            'content_types' => $this->selected_content_types,
            'search_term' => $this->filters['search_term'] ?? null,
            'location' => $this->filters['location'] ?? null,
            'bounds' => ! empty($this->map_bounds) && count($this->map_bounds) === 4 ? $this->map_bounds : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'min_price' => $this->filters['min_price'] ?? null,
            'max_price' => $this->filters['max_price'] ?? null,
            'zoom_level' => $this->zoom_level,
            'city' => $this->filters['city'] ?? null,
            'pet_type' => $this->filters['pet_type'] ?? null,
            'sort_by' => 'distance', // For maps, prioritize distance-based sorting
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
        if (! empty($this->selected_content_types)) {
            $query->whereIn('content_type', $this->selected_content_types);
        }

        // Apply search filters
        if (! empty($this->filters['search_term'])) {
            $query->search($this->filters['search_term']);
        }

        if (! empty($this->filters['content_type'])) {
            $query->where('content_type', $this->filters['content_type']);
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
        }

        // Location-based filtering - prioritize bounds over radius
        if (! empty($this->map_bounds) && count($this->map_bounds) === 4) {
            // Use map bounds for area-based search (more efficient for viewport)
            [$south, $west, $north, $east] = $this->map_bounds;
            $query->withinBounds($south, $north, $west, $east);
        } elseif ($this->latitude && $this->longitude) {
            // Fallback to radius-based search if no bounds
            $query->nearLocation($this->latitude, $this->longitude, $this->radius);
        }

        // Price range filter
        if (! empty($this->filters['min_price']) || ! empty($this->filters['max_price'])) {
            $minPrice = ! empty($this->filters['min_price']) ? (float) $this->filters['min_price'] : null;
            $maxPrice = ! empty($this->filters['max_price']) ? (float) $this->filters['max_price'] : null;
            $query->priceRange($minPrice, $maxPrice);
        }

        return $query->limit(100)->get();
    }

    #[Computed]
    public function mapStatistics()
    {
        // 🚀 Use unified SearchCacheService for consistent statistics
        $cacheService = app(SearchCacheService::class);

        $filters = [
            'content_types' => $this->selected_content_types,
            'search_term' => $this->filters['search_term'] ?? null,
            'location' => $this->filters['location'] ?? null,
            'bounds' => ! empty($this->map_bounds) && count($this->map_bounds) === 4 ? $this->map_bounds : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'min_price' => $this->filters['min_price'] ?? null,
            'max_price' => $this->filters['max_price'] ?? null,
            'zoom_level' => $this->zoom_level,
            'city' => $this->filters['city'] ?? null,
            'pet_type' => $this->filters['pet_type'] ?? null,
        ];

        return $cacheService->getCachedMapStatistics($filters);
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

    /**
     * 🗺️ Resolve coordinates from location name for map centering
     */
    private function resolveLocationCoordinates(string $locationName): void
    {
        // Simple mapping for common Polish cities
        $cityCoordinates = [
            'Olsztyn' => ['lat' => 53.7784, 'lng' => 20.4800],
            'Warszawa' => ['lat' => 52.2297, 'lng' => 21.0122],
            'Kraków' => ['lat' => 50.0647, 'lng' => 19.9450],
            'Gdańsk' => ['lat' => 54.3520, 'lng' => 18.6466],
            'Wrocław' => ['lat' => 51.1079, 'lng' => 17.0385],
            'Poznań' => ['lat' => 52.4064, 'lng' => 16.9252],
            'Łódź' => ['lat' => 51.7592, 'lng' => 19.4560],
            'Katowice' => ['lat' => 50.2649, 'lng' => 19.0238],
            'Lublin' => ['lat' => 51.2465, 'lng' => 22.5684],
            'Białystok' => ['lat' => 53.1325, 'lng' => 23.1688],
        ];

        // Clean location name (remove common prefixes/suffixes)
        $cleanLocation = trim(str_replace(['województwo ', ', województwo'], '', $locationName));

        // Find coordinates for the location
        foreach ($cityCoordinates as $city => $coords) {
            if (stripos($cleanLocation, $city) !== false || stripos($city, $cleanLocation) !== false) {
                $this->latitude = $coords['lat'];
                $this->longitude = $coords['lng'];
                $this->location_detected = true;

                // Dispatch event to center map on this location
                $this->dispatch('map-center-updated', [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ]);

                break;
            }
        }
    }

    public function render()
    {
        return view('livewire.search.search-map');
    }
}
