<?php

namespace App\Observers;

use App\Models\MapItem;
use App\Services\MapCacheService;
use Illuminate\Support\Facades\Log;

class MapItemObserver
{
    public function __construct(
        private MapCacheService $cacheService
    ) {}

    public function created(MapItem $mapItem): void
    {
        $this->invalidateRelatedCache($mapItem);
        Log::info("MapItem created, cache invalidated", ['id' => $mapItem->id]);
    }

    public function updated(MapItem $mapItem): void
    {
        $this->invalidateRelatedCache($mapItem);

        // If location changed, invalidate broader cache
        if ($mapItem->wasChanged(['latitude', 'longitude'])) {
            $this->cacheService->invalidateMapCache();
            Log::info("MapItem location changed, full cache invalidated", ['id' => $mapItem->id]);
        } else {
            Log::info("MapItem updated, cache invalidated", ['id' => $mapItem->id]);
        }
    }

    public function deleted(MapItem $mapItem): void
    {
        $this->invalidateRelatedCache($mapItem);
        Log::info("MapItem deleted, cache invalidated", ['id' => $mapItem->id]);
    }

    private function invalidateRelatedCache(MapItem $mapItem): void
    {
        // Invalidate cache patterns related to this item
        $patterns = [
            'map_items:items:*', // General item cache
            'map_items:stats:*', // Statistics cache
        ];

        // If item has specific location/content type, be more targeted
        if ($mapItem->content_type) {
            $patterns[] = "map_items:*content_type*{$mapItem->content_type}*";
        }

        if ($mapItem->city) {
            $patterns[] = "map_items:*city*{$mapItem->city}*";
        }

        foreach ($patterns as $pattern) {
            try {
                $this->cacheService->invalidateMapCache($pattern);
            } catch (\Exception $e) {
                Log::warning("Failed to invalidate cache pattern: {$pattern}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}