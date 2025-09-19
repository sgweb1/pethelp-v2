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

    public array $selected_content_types = ['service'];

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
        if (!empty($filters['search_for']) && $filters['search_for'] !== 'services') {
            $this->selected_content_types = $this->mapSearchTypeToContentTypes($filters['search_for']);
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

    public function highlightMarker(?int $markerId): void
    {
        $this->dispatch('highlight-marker', $markerId);
    }

    public function focusMarker(int $markerId): void
    {
        $this->dispatch('focus-marker', $markerId);
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
    }

    public function updateZoomLevel(int $zoom): void
    {
        $this->zoom_level = $zoom;

        // Enable clustering for lower zoom levels
        $this->cluster_mode = $zoom < 10;
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
            'events' => ['event'],
            'adoptions' => ['adoption'],
            'lost_pets' => ['lost_pet'],
            'found_pets' => ['found_pet'],
            'supplies' => ['supplies'],
            'services' => ['service'],
            default => ['service']
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
    public function mapServices()
    {
        // Legacy method for backward compatibility
        if (empty($this->filters)) {
            return collect([]);
        }

        $query = Service::active()
            ->with(['sitter', 'category', 'sitter.locations'])
            ->whereHas('sitter.locations', function ($q) {
                $q->whereNotNull('latitude')
                    ->whereNotNull('longitude');
            });

        // Apply same filters as SearchResults but limit to 50 for map performance
        if (! empty($this->filters['search_term'])) {
            $searchTerm = '%'.$this->filters['search_term'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('sitter', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', $searchTerm);
                    });
            });
        }

        if (! empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (! empty($this->filters['pet_type'])) {
            $query->byPetType($this->filters['pet_type']);
        }

        // Location-based filtering with radius
        if ($this->latitude && $this->longitude) {
            $query->byLocation($this->latitude, $this->longitude, $this->radius);
        }

        return $query->limit(50)->get();
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
            'service' => ['name' => 'Usługi', 'icon' => 'briefcase', 'color' => 'blue'],
            'event' => ['name' => 'Wydarzenia', 'icon' => 'calendar', 'color' => 'green'],
            'adoption' => ['name' => 'Adopcje', 'icon' => 'heart', 'color' => 'red'],
            'lost_pet' => ['name' => 'Zaginione', 'icon' => 'search', 'color' => 'orange'],
            'found_pet' => ['name' => 'Znalezione', 'icon' => 'check', 'color' => 'emerald'],
            'supplies' => ['name' => 'Akcesoria', 'icon' => 'shopping-bag', 'color' => 'purple'],
        ];
    }

    public function render()
    {
        return view('livewire.search.search-map');
    }
}
