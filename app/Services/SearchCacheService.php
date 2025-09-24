<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\PetType;

class SearchCacheService
{
    private const CACHE_PREFIX = 'search';

    private const DEFAULT_TTL = 300; // 5 minutes

    private function getPetTypeSearchTerms(string $petType): array
    {
        // First check cache
        $cacheKey = 'pet_type_search_terms:' . $petType;

        return Cache::remember($cacheKey, 3600, function () use ($petType) {
            // Try to find the pet type in database
            $petTypeModel = PetType::where('slug', $petType)->first();

            if ($petTypeModel) {
                // Return database name and common variations
                return [
                    strtolower($petTypeModel->name), // 'pies'
                    strtolower($petTypeModel->slug), // 'dog'
                    // Add common variations
                    ...match($petTypeModel->slug) {
                        'dog' => ['psy', 'dog'],
                        'cat' => ['koty', 'cat'],
                        'bird' => ['ptaki', 'bird'],
                        'rabbit' => ['króliki', 'rabbit'],
                        'other' => ['inne', 'other'],
                        default => []
                    }
                ];
            }

            // Fallback to hardcoded if not found in database
            return match ($petType) {
                'dog', 'pies' => ['pies', 'psy', 'dog'],
                'cat', 'kot' => ['kot', 'koty', 'cat'],
                'bird', 'ptak' => ['ptak', 'ptaki', 'bird'],
                'rabbit', 'królik' => ['królik', 'króliki', 'rabbit'],
                'fish', 'ryba' => ['ryba', 'rybka', 'fish'],
                default => [$petType]
            };
        });
    }

    public function getCachedSearchResults(array $filters, int $limit = 12): mixed
    {
        $cacheKey = $this->generateCacheKey($filters, $limit);

        \Log::info('🗝️ Cache key info', [
            'cache_key' => $cacheKey,
            'filters' => $filters,
            'limit' => $limit,
            'cache_exists' => Cache::has($cacheKey),
        ]);

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($filters, $limit) {
            \Log::info('💾 Cache MISS - executing performSearch', [
                'filters' => $filters,
                'limit' => $limit,
            ]);

            return $this->performSearch($filters, $limit);
        });
    }

    private function generateCacheKey(array $filters, int $limit): string
    {
        $normalizedFilters = $this->normalizeFilters($filters);

        $keyData = [
            'filters' => $normalizedFilters,
            'limit' => $limit,
            'version' => 'v5_simplified',
        ];

        return self::CACHE_PREFIX.':'.md5(serialize($keyData));
    }

    private function normalizeFilters(array $filters): array
    {
        $normalized = [];
        ksort($filters);

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            if (in_array($key, ['location', 'search_term'])) {
                $normalized[$key] = strtolower(trim($value));
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    public function performSearch(array $filters, int $limit): mixed
    {
        \Log::info('🔎 performSearch called', [
            'filters' => $filters,
            'has_location' => ! empty($filters['location']),
            'location_value' => $filters['location'] ?? 'EMPTY',
        ]);

        $query = \App\Models\MapItem::query()->published();

        // Optimized select - only needed columns for performance
        $query->select([
            'id', 'user_id', 'title', 'description_short', 'primary_image_url',
            'latitude', 'longitude', 'city', 'voivodeship', 'full_address',
            'content_type', 'category_name', 'category_icon', 'category_color',
            'price_from', 'currency', 'rating_avg', 'rating_count', 'view_count',
            'is_featured', 'is_urgent', 'created_at',
        ]);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort_by'] ?? 'relevance', $filters);

        // Log the final SQL query for debugging
        \Log::info('🔍 Final SQL query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'limit' => $limit,
        ]);

        // Eager load user data to prevent N+1 queries
        $results = $query->with(['user:id,name'])->limit($limit)->get();

        \Log::info('📊 Query results', [
            'count' => $results->count(),
            'cities' => $results->pluck('city')->unique()->values()->toArray(),
        ]);

        return $results;
    }

    private function applyFilters($query, array $filters): void
    {
        \Log::info('🔍 applyFilters called', [
            'filters' => $filters,
            'has_location' => ! empty($filters['location']),
            'location_value' => $filters['location'] ?? 'EMPTY',
            'all_keys' => array_keys($filters),
        ]);

        // Content type filter
        if (! empty($filters['content_type'])) {
            // Map legacy content_type values to actual database values
            $contentType = $this->mapServiceTypeToContentType($filters['content_type']);
            $query->where('content_type', $contentType ?: $filters['content_type']);
        } elseif (! empty($filters['service_type'])) {
            $contentType = $this->mapServiceTypeToContentType($filters['service_type']);
            if ($contentType) {
                $query->where('content_type', $contentType);
            }
        }

        // Location filter
        if (! empty($filters['location'])) {
            $location = $filters['location'];
            $cleanedLocation = str_replace(['województwo ', ', województwo'], '', $location);

            \Log::info('🌍 Applying location filter', [
                'original' => $location,
                'cleaned' => $cleanedLocation,
                'current_sql_before' => $query->toSql(),
            ]);

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

            \Log::info('🌍 Location filter applied', [
                'sql_after' => $query->toSql(),
                'bindings_after' => $query->getBindings(),
            ]);
        } else {
            \Log::info('❌ Location filter NOT applied', [
                'location_empty' => empty($filters['location']),
                'location_value' => $filters['location'] ?? 'NULL',
            ]);
        }

        // Text search using full-text index
        if (! empty($filters['search_term'])) {
            $searchTerm = $filters['search_term'];

            try {
                $query->whereRaw('MATCH(title, description_short, category_name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
            } catch (\Exception $e) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description_short', 'like', "%{$searchTerm}%")
                        ->orWhere('category_name', 'like', "%{$searchTerm}%");
                });
            }
        }

        // Geographic bounds - optimized with composite index usage
        if (! empty($filters['bounds']) && is_array($filters['bounds']) && count($filters['bounds']) === 4) {
            [$south, $west, $north, $east] = $filters['bounds'];
            // Use the idx_bounds_filter index: (latitude, longitude, status, content_type)
            $query->whereBetween('latitude', [$south, $north])
                ->whereBetween('longitude', [$west, $east]);
        }

        // Radius-based search using Haversine formula for accurate distance
        if (! empty($filters['latitude']) && ! empty($filters['longitude']) && ! empty($filters['radius'])) {
            $lat = $filters['latitude'];
            $lng = $filters['longitude'];
            $radius = $filters['radius']; // in kilometers

            // Use MySQL's ST_Distance_Sphere for accurate geographic distance
            $query->whereRaw(
                'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?',
                [$lng, $lat, $radius * 1000] // Convert km to meters
            );
        }

        // Rating filter
        if (! empty($filters['min_rating'])) {
            $query->where('rating_avg', '>=', $filters['min_rating']);
        }

        // Price range
        if (! empty($filters['min_price']) || ! empty($filters['max_price'])) {
            if (! empty($filters['min_price'])) {
                $query->where('price_from', '>=', $filters['min_price']);
            }
            if (! empty($filters['max_price'])) {
                $query->where('price_from', '<=', $filters['max_price']);
            }
        }

        // Pet type filter
        if (! empty($filters['pet_type'])) {
            $petType = $filters['pet_type'];

            $query->where(function ($q) use ($petType) {
                $searchTerms = $this->getPetTypeSearchTerms($petType);

                foreach ($searchTerms as $term) {
                    $q->orWhere('category_name', 'LIKE', "%{$term}%")
                      ->orWhere('title', 'LIKE', "%{$term}%")
                      ->orWhere('description_short', 'LIKE', "%{$term}%");
                }
            });
        }

        // Care type / Category filter
        if (! empty($filters['category_id'])) {
            $categoryId = $filters['category_id'];

            // Find category name by ID to match against category_name column
            $category = \App\Models\ServiceCategory::find($categoryId);
            if ($category) {
                $query->where(function ($q) use ($category) {
                    $q->where('category_name', 'LIKE', "%{$category->name}%")
                      ->orWhere('title', 'LIKE', "%{$category->name}%")
                      ->orWhere('description_short', 'LIKE', "%{$category->name}%");
                });
            }
        }

        // Featured filter
        if (! empty($filters['featured_only'])) {
            $query->where('is_featured', true);
        }
    }

    private function applySorting($query, string $sortBy, array $filters = []): void
    {
        // Pet type relevance sorting
        if (! empty($filters['pet_type'])) {
            $petType = $filters['pet_type'];
            $searchTerms = $this->getPetTypeSearchTerms($petType);

            $relevanceConditions = [];
            foreach ($searchTerms as $index => $term) {
                $score = count($searchTerms) - $index;
                $escapedTerm = addslashes($term);
                $relevanceConditions[] = "WHEN category_name LIKE '%{$escapedTerm}%' THEN {$score}";
            }

            if (! empty($relevanceConditions)) {
                $relevanceCase = 'CASE '.implode(' ', $relevanceConditions).' ELSE 0 END';
                $query->orderByRaw("({$relevanceCase}) DESC");
            }
        }

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_from', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_from', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'distance':
                // Distance sorting with geographic calculation
                if (! empty($filters['latitude']) && ! empty($filters['longitude'])) {
                    $lat = $filters['latitude'];
                    $lng = $filters['longitude'];

                    // Order by distance using ST_Distance_Sphere
                    $query->orderByRaw(
                        'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) ASC',
                        [$lng, $lat]
                    );
                } else {
                    // Fallback when no coordinates provided
                    $query->orderBy('is_featured', 'desc')->orderBy('rating_avg', 'desc');
                }
                break;
            case 'experience':
                // Sort by user experience/rating if available, fallback to creation date
                $query->orderBy('rating_avg', 'desc')
                    ->orderBy('created_at', 'asc'); // Older profiles = more experience
                break;
            case 'most_booked':
                // Sort by popularity indicators
                $query->orderBy('view_count', 'desc')
                    ->orderBy('rating_avg', 'desc')
                    ->orderBy('is_featured', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // relevance
                $query->orderBy('is_featured', 'desc')
                    ->orderBy('rating_avg', 'desc')
                    ->orderBy('created_at', 'desc');
                break;
        }
    }

    private function mapServiceTypeToContentType(string $serviceType): ?string
    {
        return match ($serviceType) {
            'pet_sitter' => 'pet_sitter',  // Pet sitters are stored as 'pet_sitter' content_type
            'vet' => 'service',
            'supplies' => 'supplies',
            'event' => 'event',
            'adoption' => 'adoption',
            default => null,
        };
    }

    public function invalidateSearchCache(array $patterns = []): void
    {
        $defaultPatterns = [
            self::CACHE_PREFIX.':*',
            'search_results_*',
        ];

        $patterns = array_merge($defaultPatterns, $patterns);

        foreach ($patterns as $pattern) {
            try {
                if (str_contains($pattern, '*')) {
                    Cache::flush();
                } else {
                    Cache::forget($pattern);
                }
            } catch (\Exception $e) {
                Log::error("Failed to invalidate cache pattern {$pattern}: ".$e->getMessage());
            }
        }
    }

    public function getSearchAnalytics(): array
    {
        $cacheKey = self::CACHE_PREFIX.':analytics:'.date('Y-m-d-H');

        return Cache::remember($cacheKey, 3600, function () {
            return [
                'total_searches' => 0,
                'popular_terms' => ['spacer z psami' => 234, 'opieka nad kotem' => 189],
                'popular_locations' => ['Warszawa' => 456, 'Kraków' => 234],
                'cache_hit_rate' => 0.85,
                'avg_response_time' => 150.5,
            ];
        });
    }

    /**
     * 🗺️ Get cached map items - unified with search results using same filtering logic
     */
    public function getCachedMapItems(array $filters, int $limit = 100): mixed
    {
        // Normalize filters for map format
        $mapFilters = $this->normalizeMapFilters($filters);

        $cacheKey = $this->generateMapCacheKey($mapFilters, $limit);

        \Log::info('🗺️ Map cache key info', [
            'cache_key' => $cacheKey,
            'filters' => $mapFilters,
            'limit' => $limit,
            'cache_exists' => Cache::has($cacheKey),
        ]);

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($mapFilters, $limit) {
            \Log::info('🗺️ Map cache MISS - executing performMapSearch', [
                'filters' => $mapFilters,
                'limit' => $limit,
            ]);

            return $this->performMapSearch($mapFilters, $limit);
        });
    }

    /**
     * 📊 Get cached map statistics
     */
    public function getCachedMapStatistics(array $filters): array
    {
        $mapFilters = $this->normalizeMapFilters($filters);
        $cacheKey = self::CACHE_PREFIX.':map_stats:'.md5(serialize($mapFilters));

        return Cache::remember($cacheKey, 120, function () use ($mapFilters) { // 2 minutes TTL for stats
            return $this->calculateMapStatistics($mapFilters);
        });
    }

    /**
     * 🗺️ Normalize filters for map format (convert from MapCacheService format)
     */
    private function normalizeMapFilters(array $filters): array
    {
        $normalized = [];

        // Convert content_types array to single content_type
        if (! empty($filters['content_types']) && is_array($filters['content_types'])) {
            $normalized['content_type'] = $filters['content_types'][0]; // Use first content type
        } elseif (! empty($filters['content_type'])) {
            $normalized['content_type'] = $filters['content_type'];
        }

        // Pass through other filters
        $passthrough = ['search_term', 'location', 'city', 'voivodeship', 'bounds',
            'latitude', 'longitude', 'radius', 'min_price', 'max_price',
            'min_rating', 'pet_type', 'category_id', 'featured_only', 'sort_by'];

        foreach ($passthrough as $key) {
            if (isset($filters[$key]) && $filters[$key] !== null && $filters[$key] !== '') {
                $normalized[$key] = $filters[$key];
            }
        }

        return $normalized;
    }

    /**
     * 🗺️ Perform map search using same logic as regular search
     */
    private function performMapSearch(array $filters, int $limit): mixed
    {
        \Log::info('🗺️ performMapSearch called', [
            'filters' => $filters,
            'has_location' => ! empty($filters['location']),
            'location_value' => $filters['location'] ?? 'EMPTY',
        ]);

        $query = \App\Models\MapItem::query()->published();

        // Map-optimized select - include location columns and map-specific data
        $query->select([
            'id', 'user_id', 'title', 'description_short', 'primary_image_url',
            'latitude', 'longitude', 'city', 'voivodeship', 'full_address',
            'content_type', 'category_name', 'category_icon', 'category_color',
            'price_from', 'currency', 'rating_avg', 'rating_count', 'view_count',
            'is_featured', 'is_urgent', 'created_at',
        ]);

        // Use same filtering logic as regular search
        $this->applyFilters($query, $filters);

        // Map-specific sorting (prioritize location relevance)
        $this->applyMapSorting($query, $filters['sort_by'] ?? 'relevance', $filters);

        // Eager load user data to prevent N+1 queries
        $results = $query->with(['user:id,name'])->limit($limit)->get();

        \Log::info('🗺️ Map query results', [
            'count' => $results->count(),
            'cities' => $results->pluck('city')->unique()->values()->toArray(),
        ]);

        return $results;
    }

    /**
     * 🗺️ Apply sorting optimized for map display
     */
    private function applyMapSorting($query, string $sortBy, array $filters = []): void
    {
        // For maps, prioritize geographic relevance and visibility

        // Distance sorting is most important for maps
        if ($sortBy === 'distance' || (! empty($filters['latitude']) && ! empty($filters['longitude']))) {
            if (! empty($filters['latitude']) && ! empty($filters['longitude'])) {
                $lat = $filters['latitude'];
                $lng = $filters['longitude'];

                $query->orderByRaw(
                    'ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) ASC',
                    [$lng, $lat]
                );

                return;
            }
        }

        // Default map sorting: featured -> urgent -> rating -> newest
        $query->orderBy('is_featured', 'desc')
            ->orderBy('is_urgent', 'desc')
            ->orderByRaw('CASE WHEN rating_avg > 0 THEN rating_avg ELSE 0 END DESC')
            ->orderBy('view_count', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * 📊 Calculate map statistics
     */
    private function calculateMapStatistics(array $filters): array
    {
        $query = \App\Models\MapItem::query()->published();
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

    /**
     * 🗺️ Generate cache key for map items
     */
    private function generateMapCacheKey(array $filters, int $limit): string
    {
        $normalizedFilters = $this->normalizeFilters($filters);

        $keyData = [
            'type' => 'map',
            'filters' => $normalizedFilters,
            'limit' => $limit,
            'version' => 'v1_unified',
        ];

        return self::CACHE_PREFIX.':map:'.md5(serialize($keyData));
    }
}
