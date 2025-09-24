<?php

namespace App\Services;

use App\Models\MapItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MapCacheService
{
    const CACHE_PREFIX = 'map_items';

    const DEFAULT_TTL = 300; // 5 minutes

    const CLUSTER_TTL = 600; // 10 minutes for cluster data

    const STATS_TTL = 120; // 2 minutes for statistics

    public function getCachedMapItems(array $filters, int $limit = 100): \Illuminate\Support\Collection
    {
        $cacheKey = $this->generateCacheKey($filters, $limit);

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($filters, $limit) {
            return $this->queryMapItems($filters, $limit);
        });
    }

    public function getCachedClusterData(array $bounds, int $zoomLevel): array
    {
        $cacheKey = $this->generateClusterCacheKey($bounds, $zoomLevel);

        return Cache::remember($cacheKey, self::CLUSTER_TTL, function () use ($bounds, $zoomLevel) {
            return $this->generateClusterData($bounds, $zoomLevel);
        });
    }

    public function getCachedStatistics(array $filters): array
    {
        $cacheKey = self::CACHE_PREFIX.':stats:'.md5(serialize($filters));

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($filters) {
            return $this->calculateStatistics($filters);
        });
    }

    public function invalidateMapCache(?string $pattern = null): void
    {
        if ($pattern) {
            $this->invalidateCachePattern($pattern);
        } else {
            // Clear all map-related cache
            $this->invalidateCachePattern(self::CACHE_PREFIX.':*');
        }
    }

    private function queryMapItems(array $filters, int $limit): \Illuminate\Support\Collection
    {
        $query = MapItem::query()
            ->select([
                'id', 'latitude', 'longitude', 'title', 'description_short',
                'primary_image_url', 'content_type', 'category_name', 'category_icon',
                'category_color', 'price_from', 'currency', 'is_featured',
                'is_urgent', 'rating_avg', 'rating_count', 'view_count',
                // ✅ FIX: Add location columns needed for filtering
                'city', 'voivodeship', 'full_address',
            ])
            ->active();

        // Apply filters efficiently
        $this->applyFilters($query, $filters);

        // Optimize ordering for performance
        $query->orderBy('is_featured', 'desc')
            ->orderBy('is_urgent', 'desc')
            ->orderByRaw('CASE WHEN rating_avg > 0 THEN rating_avg ELSE 0 END DESC')
            ->orderBy('view_count', 'desc');

        return $query->limit($limit)->get();
    }

    private function generateClusterData(array $bounds, int $zoomLevel): array
    {
        if (count($bounds) !== 4) {
            return ['clusters' => [], 'markers' => []];
        }

        [$south, $west, $north, $east] = $bounds;

        // For high zoom levels, return individual markers
        if ($zoomLevel >= 12) {
            $markers = MapItem::query()
                ->select('id', 'latitude', 'longitude', 'title', 'content_type', 'is_featured', 'is_urgent')
                ->active()
                ->inBounds($south, $west, $north, $east)
                ->limit(200)
                ->get();

            return [
                'clusters' => [],
                'markers' => $markers->toArray(),
            ];
        }

        // For lower zoom levels, create clusters
        $gridSize = $this->getGridSize($zoomLevel);
        $clusters = $this->generateClusters($south, $west, $north, $east, $gridSize);

        return [
            'clusters' => $clusters,
            'markers' => [],
        ];
    }

    private function generateClusters(float $south, float $west, float $north, float $east, float $gridSize): array
    {
        $clusters = [];

        $latStep = ($north - $south) / $gridSize;
        $lngStep = ($east - $west) / $gridSize;

        for ($lat = $south; $lat < $north; $lat += $latStep) {
            for ($lng = $west; $lng < $east; $lng += $lngStep) {
                $clusterBounds = [
                    $lat,
                    $lng,
                    $lat + $latStep,
                    $lng + $lngStep,
                ];

                $itemsInCluster = MapItem::query()
                    ->select('content_type', 'is_featured', 'is_urgent')
                    ->active()
                    ->inBounds(...$clusterBounds)
                    ->get();

                if ($itemsInCluster->count() > 0) {
                    $clusters[] = [
                        'lat' => $lat + ($latStep / 2),
                        'lng' => $lng + ($lngStep / 2),
                        'count' => $itemsInCluster->count(),
                        'featured_count' => $itemsInCluster->where('is_featured', true)->count(),
                        'urgent_count' => $itemsInCluster->where('is_urgent', true)->count(),
                        'content_types' => $itemsInCluster->pluck('content_type')->unique()->values(),
                    ];
                }
            }
        }

        return $clusters;
    }

    private function calculateStatistics(array $filters): array
    {
        $query = MapItem::query()->active();
        $this->applyFilters($query, $filters);

        $baseStats = $query->selectRaw('
            COUNT(*) as total_items,
            COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured_count,
            COUNT(CASE WHEN is_urgent = 1 THEN 1 END) as urgent_count,
            AVG(CASE WHEN rating_avg > 0 THEN rating_avg END) as avg_rating
        ')->first();

        $contentTypeStats = $query->select('content_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('content_type')
            ->pluck('count', 'content_type')
            ->toArray();

        return [
            'total_items' => $baseStats->total_items ?? 0,
            'featured_count' => $baseStats->featured_count ?? 0,
            'urgent_count' => $baseStats->urgent_count ?? 0,
            'avg_rating' => round($baseStats->avg_rating ?? 0, 2),
            'by_content_type' => $contentTypeStats,
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        Log::info('🗺️ MapCacheService applyFilters called', [
            'filters' => $filters,
            'has_location' => ! empty($filters['location']),
            'location_value' => $filters['location'] ?? 'EMPTY',
        ]);

        // Content type filter
        if (! empty($filters['content_types'])) {
            $query->whereIn('content_type', $filters['content_types']);
        }

        // Geographic bounds filter (most important for performance)
        if (! empty($filters['bounds']) && count($filters['bounds']) === 4) {
            $query->inBounds(...$filters['bounds']);
        } elseif (! empty($filters['latitude']) && ! empty($filters['longitude'])) {
            $radius = $filters['radius'] ?? 10;
            $query->nearLocation($filters['latitude'], $filters['longitude'], $radius);
        }

        // Zoom level visibility
        if (! empty($filters['zoom_level'])) {
            $query->visibleAtZoom($filters['zoom_level']);
        }

        // Text search
        if (! empty($filters['search_term'])) {
            $query->search($filters['search_term']);
        }

        // Price filters
        if (! empty($filters['price_min'])) {
            $query->where('price_from', '>=', $filters['price_min']);
        }
        if (! empty($filters['price_max'])) {
            $query->where('price_from', '<=', $filters['price_max']);
        }

        // Location filter (general location search - cities, voivodeships, addresses)
        if (! empty($filters['location'])) {
            $location = $filters['location'];
            $cleanedLocation = str_replace(['województwo ', ', województwo'], '', $location);

            $query->where(function ($q) use ($location, $cleanedLocation) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('full_address', 'like', "%{$location}%")
                    ->orWhere('voivodeship', 'like', "%{$location}%");

                if ($cleanedLocation !== $location) {
                    $q->orWhere('city', 'like', "%{$cleanedLocation}%")
                        ->orWhere('full_address', 'like', "%{$cleanedLocation}%")
                        ->orWhere('voivodeship', 'like', "%{$cleanedLocation}%");
                }
            });
        }

        // City filter (specific city filter)
        if (! empty($filters['city'])) {
            $query->where('city', 'like', "%{$filters['city']}%");
        }
    }

    private function generateCacheKey(array $filters, int $limit): string
    {
        $keyData = [
            'filters' => $filters,
            'limit' => $limit,
            'version' => 'v2', // Increment when cache structure changes
        ];

        return self::CACHE_PREFIX.':items:'.md5(serialize($keyData));
    }

    private function generateClusterCacheKey(array $bounds, int $zoomLevel): string
    {
        $keyData = [
            'bounds' => $bounds,
            'zoom' => $zoomLevel,
            'version' => 'v2',
        ];

        return self::CACHE_PREFIX.':clusters:'.md5(serialize($keyData));
    }

    private function getGridSize(int $zoomLevel): float
    {
        return match (true) {
            $zoomLevel <= 6 => 4,   // Very coarse clustering
            $zoomLevel <= 8 => 8,   // Coarse clustering
            $zoomLevel <= 10 => 16, // Medium clustering
            default => 32           // Fine clustering
        };
    }

    private function invalidateCachePattern(string $pattern): void
    {
        try {
            // This is a simple implementation - in production you might want to use Redis SCAN
            $keys = Cache::getStore()->getPrefix().$pattern;

            // For array/file cache, we need a different approach
            if (method_exists(Cache::getStore(), 'flush')) {
                Log::info("Clearing all cache due to pattern invalidation: {$pattern}");
                Cache::flush();
            }
        } catch (\Exception $e) {
            Log::error("Failed to invalidate cache pattern {$pattern}: ".$e->getMessage());
        }
    }
}
