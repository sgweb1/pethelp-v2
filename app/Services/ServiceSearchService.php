<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\PetType;

class ServiceSearchService
{
    private const CACHE_PREFIX = 'service_search';
    private const DEFAULT_TTL = 300; // 5 minutes

    public function search(array $filters, int $limit = 12): array
    {
        // For pet_sitter content_type, search services instead of map_items
        if (($filters['content_type'] ?? '') === 'pet_sitter') {
            return $this->searchServices($filters, $limit);
        }

        // For other content types, fallback to existing search
        // TODO: Integrate with SearchCacheService or implement other types
        return ['items' => [], 'total' => 0];
    }

    private function searchServices(array $filters, int $limit): array
    {
        $cacheKey = $this->generateCacheKey($filters, $limit);

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($filters, $limit) {
            return $this->performServiceSearch($filters, $limit);
        });
    }

    private function performServiceSearch(array $filters, int $limit): array
    {
        Log::info('🔍 ServiceSearch: performServiceSearch called', [
            'filters' => $filters,
            'limit' => $limit
        ]);

        $query = Service::query()
            ->active()
            ->with(['sitter.profile', 'category']);

        // Join with users and map_items to get location data
        $query->join('users', 'services.sitter_id', '=', 'users.id')
              ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
              ->leftJoin('map_items', function($join) {
                  $join->on('map_items.mappable_id', '=', 'services.id')
                       ->where('map_items.mappable_type', '=', 'Service')
                       ->where('map_items.content_type', '=', 'pet_sitter')
                       ->where('map_items.status', '=', 'published');
              })
              ->select([
                  'services.*',
                  'users.name as sitter_name',
                  'user_profiles.avatar as sitter_avatar',
                  // Use map_items location data as primary source
                  'map_items.city',
                  'map_items.voivodeship',
                  'map_items.latitude',
                  'map_items.longitude',
                  'map_items.full_address'
              ]);

        $this->applyServiceFilters($query, $filters);
        $this->applyServiceSorting($query, $filters['sort_by'] ?? 'relevance');

        // Execute query with limit
        $services = $query->limit($limit)->get();
        $total = $query->count();

        Log::info('🔍 ServiceSearch: Query results', [
            'count' => $services->count(),
            'total' => $total
        ]);

        return [
            'items' => $this->formatServiceResults($services),
            'total' => $total
        ];
    }

    private function applyServiceFilters($query, array $filters): void
    {
        // Search term filter
        if (!empty($filters['search_term'])) {
            $searchTerm = $filters['search_term'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('services.title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('services.description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('users.name', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Location filter
        if (!empty($filters['location'])) {
            $location = strtolower($filters['location']);
            $query->where(function ($q) use ($location) {
                $q->whereRaw('LOWER(map_items.city) LIKE ?', ["%{$location}%"])
                  ->orWhereRaw('LOWER(map_items.voivodeship) LIKE ?', ["%{$location}%"])
                  ->orWhereRaw('LOWER(map_items.full_address) LIKE ?', ["%{$location}%"]);
            });
        }

        // Pet type filter
        if (!empty($filters['pet_type'])) {
            $petType = $filters['pet_type'];
            $query->whereJsonContains('services.pet_types', $petType);
        }

        // Pet size filter (NEW)
        if (!empty($filters['pet_size'])) {
            $petSize = $filters['pet_size'];
            $query->whereJsonContains('services.pet_sizes', $petSize);
        }

        // Service category filter
        if (!empty($filters['category_id'])) {
            $query->where('services.category_id', $filters['category_id']);
        }

        // Price filters (NEW - more precise)
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $priceType = $filters['price_type'] ?? 'hour';
            $priceColumn = $priceType === 'day' ? 'price_per_day' : 'price_per_hour';

            if (!empty($filters['min_price'])) {
                $query->where("services.{$priceColumn}", '>=', $filters['min_price']);
            }
            if (!empty($filters['max_price'])) {
                $query->where("services.{$priceColumn}", '<=', $filters['max_price']);
            }
        }

        // Service type filter (NEW)
        if (!empty($filters['service_type'])) {
            match ($filters['service_type']) {
                'home_service' => $query->where('services.home_service', true),
                'sitter_home' => $query->where('services.sitter_home', true),
                default => null
            };
        }

        // Max pets filter (NEW)
        if (!empty($filters['max_pets'])) {
            $query->where('services.max_pets', '>=', $filters['max_pets']);
        }

        // Geographic bounds filter
        if (!empty($filters['bounds']) && count($filters['bounds']) === 4) {
            [$south, $west, $north, $east] = $filters['bounds'];
            $query->whereBetween('map_items.latitude', [$south, $north])
                  ->whereBetween('map_items.longitude', [$west, $east]);
        }

        // Location radius filter
        if (!empty($filters['latitude']) && !empty($filters['longitude']) && !empty($filters['radius'])) {
            $lat = $filters['latitude'];
            $lng = $filters['longitude'];
            $radius = $filters['radius'];

            $query->whereRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(map_items.latitude)) * cos(radians(map_items.longitude) - radians(?)) + sin(radians(?)) * sin(radians(map_items.latitude)))) <= ?',
                [$lat, $lng, $lat, $radius]
            );
        }
    }

    private function applyServiceSorting($query, string $sortBy): void
    {
        match ($sortBy) {
            'price_low' => $query->orderByRaw('COALESCE(services.price_per_hour, services.price_per_day) ASC'),
            'price_high' => $query->orderByRaw('COALESCE(services.price_per_hour, services.price_per_day) DESC'),
            'rating' => $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc'),
            'newest' => $query->orderBy('services.created_at', 'desc'),
            'relevance' => $query->orderBy('services.created_at', 'desc'),
            default => $query->orderBy('services.created_at', 'desc')
        };
    }

    private function formatServiceResults($services): array
    {
        return $services->map(function ($service) {
            return [
                'id' => $service->id,
                'type' => 'service',
                'title' => $service->title,
                'description' => $service->description,
                'sitter' => [
                    'id' => $service->sitter_id,
                    'name' => $service->sitter_name,
                    'avatar' => $service->sitter_avatar,
                ],
                'location' => [
                    'city' => $service->city,
                    'voivodeship' => $service->voivodeship,
                    'coordinates' => [
                        'lat' => (float) $service->latitude,
                        'lng' => (float) $service->longitude,
                    ]
                ],
                'category' => [
                    'id' => $service->category_id,
                    'name' => $service->category->name ?? 'Pet Sitting',
                    'icon' => $service->category->icon ?? '🐾',
                ],
                'price' => [
                    'per_hour' => $service->price_per_hour ? (float) $service->price_per_hour : null,
                    'per_day' => $service->price_per_day ? (float) $service->price_per_day : null,
                    'currency' => 'PLN',
                    'display' => $service->display_price,
                ],
                'pet_types' => $service->pet_types ?? [],
                'pet_sizes' => $service->pet_sizes ?? [],
                'service_types' => $service->service_types,
                'max_pets' => $service->max_pets,
                'quality' => [
                    'rating' => $service->average_rating,
                    'reviews_count' => $service->reviews_count,
                ],
                'flags' => [
                    'home_service' => $service->home_service,
                    'sitter_home' => $service->sitter_home,
                ],
                'created_at' => $service->created_at->toISOString(),
            ];
        })->toArray();
    }

    private function generateCacheKey(array $filters, int $limit): string
    {
        $normalizedFilters = $this->normalizeFilters($filters);

        $keyData = [
            'filters' => $normalizedFilters,
            'limit' => $limit,
            'version' => 'v1_services',
        ];

        return self::CACHE_PREFIX . ':' . md5(serialize($keyData));
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

    public function clearCache(): void
    {
        Cache::flush(); // Clear all cache for now - can be optimized later
        Log::info('🗑️ ServiceSearch: Cache cleared');
    }
}