<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchCacheService;
use App\Services\ServiceSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 🚀 Unified Search API Controller - Airbnb-style single endpoint
 *
 * Replaces:
 * - MapController@index
 * - MapDataController@getMapItems
 * - Part of SearchResults logic
 *
 * Benefits:
 * - Single API call instead of 3-4 requests
 * - Consistent data between map and list
 * - Better caching and performance
 * - Simpler frontend integration
 */
class UnifiedSearchController extends Controller
{
    public function __construct(
        private SearchCacheService $cacheService,
        private ServiceSearchService $serviceSearchService
    ) {}

    /**
     * 🎯 Main search endpoint - handles both list and map data
     */
    public function search(Request $request): JsonResponse
    {
        \Log::info('🚀 UnifiedSearchController::search called', [
            'url' => $request->fullUrl(),
            'params' => $request->all(),
            'method' => $request->method(),
            'headers' => [
                'accept' => $request->header('Accept'),
                'user-agent' => $request->header('User-Agent'),
            ]
        ]);

        $validator = Validator::make($request->all(), [
            // Search filters
            'content_type' => 'nullable|string|in:pet_sitter,service,event,adoption,supplies',
            'service_type' => 'nullable|string|in:pet_sitter,vet,supplies,event,adoption,home_service,sitter_home',
            'pet_type' => 'nullable|string|in:dog,cat,bird,rabbit,fish,pies,kot,ptak,królik,ryba',
            'pet_size' => 'nullable|string|in:small,medium,large',
            'category_id' => 'nullable|integer|exists:service_categories,id',
            'max_pets' => 'nullable|integer|min:1|max:10',
            'price_type' => 'nullable|string|in:hour,day',
            'search_term' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'voivodeship' => 'nullable|string|max:100',

            // Geographic filters
            'bounds' => 'nullable|array|size:4',
            'bounds.*' => 'numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'zoom_level' => 'nullable|integer|between:1,20',

            // Price filters
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',

            // Quality filters
            'min_rating' => 'nullable|numeric|between:0,5',
            'featured_only' => 'nullable|boolean',
            'urgent_only' => 'nullable|boolean',

            // Pagination & format
            'limit' => 'nullable|integer|between:1,1000',
            'page' => 'nullable|integer|min:1',
            'format' => 'nullable|string|in:list,map,geojson',
            'sort_by' => 'nullable|string|in:relevance,distance,price_low,price_high,rating,newest,featured,experience,most_booked',
        ]);

        if ($validator->fails()) {
            \Log::error('🚨 UnifiedSearchController validation failed', [
                'errors' => $validator->errors(),
                'params' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $filters = $validator->validated();
        $format = $filters['format'] ?? 'list';
        $limit = $filters['limit'] ?? ($format === 'map' ? 500 : 12);

        \Log::info('🎯 UnifiedSearchController after validation', [
            'validated_filters' => $filters,
            'format' => $format,
            'limit' => $limit
        ]);

        // Remove non-filter params
        unset($filters['format'], $filters['limit'], $filters['page']);

        try {
            $startTime = microtime(true);

            \Log::info('🚀 Calling SearchCacheService', [
                'filters' => $filters,
                'limit' => $limit,
                'no_cache' => $request->has('no_cache')
            ]);

            // Choose appropriate search service based on content type
            if (($filters['content_type'] ?? '') === 'pet_sitter') {
                \Log::info('🐾 Using ServiceSearchService for pet_sitter');
                $searchResults = $this->serviceSearchService->search($filters, $limit);
                $results = collect($searchResults['items'] ?? []);
                $totalCount = $searchResults['total'] ?? 0;
            } else {
                \Log::info('📋 Using SearchCacheService for other content types');
                // Use our optimized cache service or bypass cache
                if ($request->has('no_cache')) {
                    \Log::info('⚡ BYPASSING CACHE due to no_cache parameter');
                    $results = $this->cacheService->performSearch($filters, $limit);
                } else {
                    $results = $this->cacheService->getCachedSearchResults($filters, $limit);
                }
                $totalCount = $results->count();
            }

            \Log::info('📊 SearchCacheService returned', [
                'results_count' => $results->count(),
                'first_item_id' => $results->first()?->id ?? 'null'
            ]);

            $responseTime = (microtime(true) - $startTime) * 1000;

            // Format response based on request type
            $response = match ($format) {
                'map' => $this->formatForMap($results, ($filters['content_type'] ?? '') === 'pet_sitter'),
                'geojson' => $this->formatAsGeoJSON($results, ($filters['content_type'] ?? '') === 'pet_sitter'),
                default => $this->formatForList($results, $filters, ($filters['content_type'] ?? '') === 'pet_sitter')
            };

            return response()->json([
                'success' => true,
                'data' => $response,
                'meta' => [
                    'total' => $totalCount,
                    'limit' => $limit,
                    'response_time_ms' => round($responseTime, 2),
                    'format' => $format,
                    'cache_optimized' => true,
                    'search_type' => ($filters['content_type'] ?? '') === 'pet_sitter' ? 'services' : 'map_items',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * 🗺️ Format results for map display
     */
    private function formatForMap($results, bool $isServiceData = false): array
    {
        return [
            'markers' => $results->map(function ($item) use ($isServiceData) {
                if ($isServiceData) {
                    // Service data format (from ServiceSearchService)
                    return [
                        'id' => $item['id'],
                        'lat' => $item['location']['coordinates']['lat'],
                        'lng' => $item['location']['coordinates']['lng'],
                        'title' => $item['title'],
                        'category' => $item['category']['name'],
                        'icon' => $item['category']['icon'],
                        'color' => '#3B82F6', // Default blue for services
                        'price' => $item['price']['per_hour'] ?? $item['price']['per_day'],
                        'currency' => $item['price']['currency'],
                        'featured' => false, // Services don't have featured flag yet
                        'urgent' => false,
                        'rating' => $item['quality']['rating'],
                        'content_type' => 'pet_sitter',
                        'sitter_name' => $item['sitter']['name'],
                    ];
                } else {
                    // MapItem data format (from SearchCacheService)
                    return [
                        'id' => $item->id,
                        'lat' => (float) $item->latitude,
                        'lng' => (float) $item->longitude,
                        'title' => $item->title,
                        'category' => $item->category_name,
                        'icon' => $item->category_icon,
                        'color' => $item->category_color,
                        'price' => $item->price_from,
                        'currency' => $item->currency ?? 'zł',
                        'featured' => (bool) $item->is_featured,
                        'urgent' => (bool) $item->is_urgent,
                        'rating' => (float) $item->rating_avg,
                        'content_type' => $item->content_type,
                    ];
                }
            })->toArray(),
            'bounds' => $this->calculateBounds($results, $isServiceData),
            'clusters' => $this->generateClusters($results, $isServiceData),
        ];
    }

    /**
     * 📋 Format results for list display
     */
    private function formatForList($results, array $filters, bool $isServiceData = false): array
    {
        return [
            'items' => $results->map(function ($item) use ($isServiceData) {
                if ($isServiceData) {
                    // Service data format - already formatted by ServiceSearchService
                    return $item;
                } else {
                    // MapItem data format (from SearchCacheService)
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description_short,
                        'image' => $item->primary_image_url,
                        'category' => [
                            'name' => $item->category_name,
                            'icon' => $item->category_icon,
                            'color' => $item->category_color,
                        ],
                        'location' => [
                            'city' => $item->city,
                            'coordinates' => [
                                'lat' => (float) $item->latitude,
                                'lng' => (float) $item->longitude,
                            ],
                        ],
                        'price' => [
                            'from' => $item->price_from,
                            'currency' => $item->currency ?? 'zł',
                        ],
                        'quality' => [
                            'rating' => (float) $item->rating_avg,
                            'rating_count' => (int) $item->rating_count,
                            'view_count' => (int) $item->view_count,
                        ],
                        'flags' => [
                            'featured' => (bool) $item->is_featured,
                            'urgent' => (bool) $item->is_urgent,
                        ],
                        'content_type' => $item->content_type,
                        'user' => [
                            'name' => $item->user->name ?? null,
                            'avatar' => $item->user->avatar_url ?? null,
                        ],
                    ];
                }
            })->toArray(),
            'pagination' => [
                'current_page' => $filters['page'] ?? 1,
                'has_more' => $results->count() >= ($filters['limit'] ?? 12),
            ],
        ];
    }

    /**
     * 🗺️ Format as GeoJSON for advanced mapping
     */
    private function formatAsGeoJSON($results, bool $isServiceData = false): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $results->map(function ($item) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float) $item->longitude, (float) $item->latitude],
                    ],
                    'properties' => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'category' => $item->category_name,
                        'content_type' => $item->content_type,
                        'price' => $item->price_from,
                        'rating' => (float) $item->rating_avg,
                        'featured' => (bool) $item->is_featured,
                    ],
                ];
            })->toArray(),
        ];
    }

    /**
     * Calculate geographic bounds for map centering
     */
    private function calculateBounds($results, bool $isServiceData = false): ?array
    {
        if ($results->isEmpty()) {
            return null;
        }

        if ($isServiceData) {
            $lats = $results->pluck('location.coordinates.lat')->filter();
            $lngs = $results->pluck('location.coordinates.lng')->filter();
        } else {
            $lats = $results->pluck('latitude')->filter();
            $lngs = $results->pluck('longitude')->filter();
        }

        if ($lats->isEmpty() || $lngs->isEmpty()) {
            return null;
        }

        return [
            'north' => $lats->max(),
            'south' => $lats->min(),
            'east' => $lngs->max(),
            'west' => $lngs->min(),
        ];
    }

    /**
     * Generate simple clustering for map performance
     */
    private function generateClusters($results, bool $isServiceData = false): array
    {
        // Simple clustering by rounding coordinates
        $clusters = [];

        foreach ($results as $item) {
            if ($isServiceData) {
                $lat = $item['location']['coordinates']['lat'] ?? null;
                $lng = $item['location']['coordinates']['lng'] ?? null;
                $id = $item['id'];
            } else {
                $lat = $item->latitude;
                $lng = $item->longitude;
                $id = $item->id;
            }

            if (!$lat || !$lng) {
                continue;
            }

            $clusterLat = round($lat, 2);
            $clusterLng = round($lng, 2);
            $key = "{$clusterLat},{$clusterLng}";

            if (! isset($clusters[$key])) {
                $clusters[$key] = [
                    'lat' => $clusterLat,
                    'lng' => $clusterLng,
                    'count' => 0,
                    'items' => [],
                ];
            }

            $clusters[$key]['count']++;
            $clusters[$key]['items'][] = $id;
        }

        return array_values($clusters);
    }

    /**
     * 📊 Get search statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'timeframe' => 'nullable|string|in:hour,day,week,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $stats = $this->cacheService->getSearchAnalytics();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
