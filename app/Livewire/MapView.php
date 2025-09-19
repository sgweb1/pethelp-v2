<?php

namespace App\Livewire;

use App\Models\MapItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class MapView extends Component
{
    public array $bounds = [];

    public array $contentTypes = [];

    public int $zoomLevel = 12;

    public bool $featuredOnly = false;

    public bool $urgentOnly = false;

    public ?float $maxPrice = null;

    public ?float $minPrice = null;

    public string $search = '';

    public array $mapData = [];

    public array $mapStats = [];

    public array $categories = [];

    // Performance constants
    private const CACHE_TTL = 300; // 5 minutes

    private const MAX_ITEMS_PER_REQUEST = 100;

    private const MIN_ZOOM_FOR_DETAILS = 8;

    public function mount(): void
    {
        $this->loadCategories();
        $this->loadStats();
        $this->loadInitialMapData();
    }

    public function loadInitialMapData(): void
    {
        // Cache initial map data for performance
        $cacheKey = 'map_initial_data_'.now()->format('Y-m-d-H');

        $mapItems = Cache::remember($cacheKey, 3600, function () {
            return MapItem::query()
                ->select([
                    'id', 'latitude', 'longitude', 'title', 'description_short',
                    'primary_image_url', 'content_type', 'category_name', 'category_icon',
                    'category_color', 'price_from', 'currency', 'status', 'is_featured',
                    'is_urgent', 'rating_avg', 'rating_count', 'view_count', 'mappable_type', 'mappable_id',
                ])
                ->where('status', 'published')
                ->limit(500) // Increased limit for better clustering
                ->get();
        });

        // Transform data to match API format with nested coordinates
        $this->mapData = $mapItems->map(function ($item) {
            $data = $item->toArray();
            $data['coordinates'] = [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ];
            unset($data['latitude'], $data['longitude']);

            return $data;
        })->toArray();

        // Debug log
        \Log::info('Sending initial map data to frontend', [
            'count' => count($this->mapData),
            'first_item' => $this->mapData[0] ?? null,
        ]);

        // Automatically send initial data to frontend
        $this->dispatch('map-data-updated', $this->mapData);
    }

    public function loadCategories(): void
    {
        $this->categories = Cache::remember('map_categories_v2', 7200, function () {
            return MapItem::where('status', 'published')
                ->selectRaw('content_type, category_name, category_icon, category_color, COUNT(*) as count')
                ->groupBy('content_type', 'category_name', 'category_icon', 'category_color')
                ->orderBy('count', 'desc')
                ->get()
                ->groupBy('content_type')
                ->toArray();
        });
    }

    public function loadStats(): void
    {
        $cacheKey = 'map_stats_'.md5(serialize([
            'bounds' => $this->bounds,
            'filters' => [
                'content_types' => $this->contentTypes,
                'featured' => $this->featuredOnly,
                'urgent' => $this->urgentOnly,
                'search' => $this->search,
            ],
        ]));

        $this->mapStats = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $query = MapItem::where('status', 'published');

            // Apply same filters as main query
            if (! empty($this->bounds) && count($this->bounds) === 4) {
                [$south, $west, $north, $east] = $this->bounds;
                $query->whereBetween('latitude', [$south, $north])
                    ->whereBetween('longitude', [$west, $east]);
            }

            if (! empty($this->contentTypes)) {
                $query->whereIn('content_type', $this->contentTypes);
            }

            if (! empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description_short', 'like', '%'.$this->search.'%')
                        ->orWhere('category_name', 'like', '%'.$this->search.'%');
                });
            }

            return [
                'total_locations' => $query->count(),
                'featured_count' => (clone $query)->where('is_featured', true)->count(),
                'urgent_count' => (clone $query)->where('is_urgent', true)->count(),
                'content_types' => $query->selectRaw('content_type, COUNT(*) as count')
                    ->groupBy('content_type')
                    ->pluck('count', 'content_type')
                    ->toArray(),
            ];
        });
    }

    public function loadMapData(): void
    {
        if (empty($this->bounds)) {
            return;
        }

        // Generate cache key based on all filters
        $cacheKey = $this->generateCacheKey();

        // Try to get from cache first
        $mapItems = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->buildOptimizedQuery()->get();
        });

        // Transform data to match API format with nested coordinates
        $this->mapData = $mapItems->map(function ($item) {
            $data = $item->toArray();
            $data['coordinates'] = [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ];
            unset($data['latitude'], $data['longitude']);

            return $data;
        })->toArray();

        // Update stats
        $this->loadStats();

        // Log for monitoring
        \Log::info('Map data loaded', [
            'count' => count($this->mapData),
            'cache_key' => $cacheKey,
            'bounds' => $this->bounds,
            'filters' => [
                'content_types' => $this->contentTypes,
                'search' => $this->search,
                'featured' => $this->featuredOnly,
                'urgent' => $this->urgentOnly,
                'price_range' => [$this->minPrice, $this->maxPrice],
            ],
        ]);

        // Automatically send updated data to frontend
        $this->dispatch('map-data-updated', $this->mapData);
    }

    private function buildOptimizedQuery()
    {
        $query = MapItem::query()
            ->select([
                'id', 'latitude', 'longitude', 'title', 'description_short',
                'primary_image_url', 'content_type', 'category_name', 'category_icon',
                'category_color', 'price_from', 'currency', 'status', 'is_featured',
                'is_urgent', 'rating_avg', 'rating_count', 'view_count', 'mappable_type', 'mappable_id',
            ])
            ->where('status', 'published');

        // Apply geographic bounds with optimized index usage
        if (count($this->bounds) === 4) {
            [$south, $west, $north, $east] = $this->bounds;
            $query->whereBetween('latitude', [$south, $north])
                ->whereBetween('longitude', [$west, $east]);
        }

        // Apply content type filters
        if (! empty($this->contentTypes)) {
            $query->whereIn('content_type', $this->contentTypes);
        }

        // Apply feature flags
        if ($this->featuredOnly) {
            $query->where('is_featured', true);
        }

        if ($this->urgentOnly) {
            $query->where('is_urgent', true);
        }

        // Apply price filters
        if ($this->minPrice || $this->maxPrice) {
            $query->where(function ($q) {
                if ($this->minPrice) {
                    $q->where('price_from', '>=', $this->minPrice);
                }
                if ($this->maxPrice) {
                    $q->where('price_from', '<=', $this->maxPrice);
                }
            });
        }

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description_short', 'like', '%'.$this->search.'%')
                    ->orWhere('category_name', 'like', '%'.$this->search.'%');
            });
        }

        // Optimize based on zoom level
        if ($this->zoomLevel < self::MIN_ZOOM_FOR_DETAILS) {
            // For low zoom, use geographical clustering
            $query = $this->applyLowZoomOptimization($query);
        }

        return $query
            ->orderBy('is_featured', 'desc')
            ->orderBy('is_urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(min(self::MAX_ITEMS_PER_REQUEST, 500));
    }

    private function applyLowZoomOptimization($query)
    {
        // For low zoom levels, use grid-based sampling to reduce data load
        $gridSize = $this->zoomLevel < 6 ? 0.5 : 0.1; // Larger grid for very low zoom

        return $query->selectRaw('
            id,
            ROUND(latitude / ?, 0) * ? as latitude,
            ROUND(longitude / ?, 0) * ? as longitude,
            title,
            description_short,
            primary_image_url,
            content_type,
            category_name,
            category_icon,
            category_color,
            price_from,
            currency,
            status,
            is_featured,
            is_urgent,
            rating_avg,
            rating_count,
            view_count,
            mappable_type,
            mappable_id,
            COUNT(*) as cluster_size
        ', [$gridSize, $gridSize, $gridSize, $gridSize])
            ->groupByRaw('
            ROUND(latitude / ?),
            ROUND(longitude / ?),
            content_type
        ', [$gridSize, $gridSize])
            ->havingRaw('COUNT(*) >= 1');
    }

    private function generateCacheKey(): string
    {
        $keyData = [
            'bounds' => $this->bounds,
            'zoom' => $this->zoomLevel,
            'content_types' => $this->contentTypes,
            'search' => $this->search,
            'featured' => $this->featuredOnly,
            'urgent' => $this->urgentOnly,
            'price_range' => [$this->minPrice, $this->maxPrice],
            'version' => 'v3', // Update when changing query logic
        ];

        return 'map_data:'.md5(serialize($keyData));
    }

    #[On('map-bounds-changed')]
    public function updateBounds(array $bounds): void
    {
        $this->bounds = $bounds;
        $this->loadMapData();
    }

    public function updatedContentTypes(): void
    {
        $this->loadMapData();
    }

    public function updatedFeaturedOnly(): void
    {
        $this->loadMapData();
    }

    public function updatedUrgentOnly(): void
    {
        $this->loadMapData();
    }

    public function updatedSearch(): void
    {
        $this->loadMapData();
    }

    public function updatedMinPrice(): void
    {
        $this->loadMapData();
    }

    public function updatedMaxPrice(): void
    {
        $this->loadMapData();
    }

    public function toggleContentType(string $type): void
    {
        if (in_array($type, $this->contentTypes)) {
            $this->contentTypes = array_values(array_filter($this->contentTypes, fn ($t) => $t !== $type));
        } else {
            $this->contentTypes[] = $type;
        }
        $this->loadMapData();
    }

    public function clearFilters(): void
    {
        $this->contentTypes = [];
        $this->featuredOnly = false;
        $this->urgentOnly = false;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->search = '';
        $this->loadMapData();
    }

    public function getContentTypeLabel(string $type): string
    {
        return match ($type) {
            'event' => 'Wydarzenia',
            'adoption' => 'Adopcje',
            'sale' => 'Sprzedaż',
            'lost_pet' => 'Zagubione',
            'found_pet' => 'Znalezione',
            'supplies' => 'Artykuły',
            'service' => 'Usługi',
            default => ucfirst($type)
        };
    }

    // Performance monitoring method
    public function getPerformanceStats(): array
    {
        return [
            'current_items_count' => count($this->mapData),
            'memory_usage' => memory_get_peak_usage(true),
            'cache_keys_active' => [
                'categories' => Cache::has('map_categories_v2'),
                'initial_data' => Cache::has('map_initial_data_'.now()->format('Y-m-d-H')),
                'current_query' => Cache::has($this->generateCacheKey()),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.map-view');
    }
}
