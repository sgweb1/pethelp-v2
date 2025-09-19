<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mappable_type',
        'mappable_id',
        'latitude',
        'longitude',
        'city',
        'voivodeship',
        'full_address',
        'title',
        'description_short',
        'primary_image_url',
        'content_type',
        'business_priority',
        'category_name',
        'category_icon',
        'category_color',
        'price_from',
        'price_to',
        'currency',
        'price_negotiable',
        'status',
        'is_featured',
        'is_urgent',
        'starts_at',
        'ends_at',
        'expires_at',
        'view_count',
        'interaction_count',
        'rating_avg',
        'rating_count',
        'zoom_level_min',
        'search_keywords',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'price_from' => 'decimal:2',
            'price_to' => 'decimal:2',
            'price_negotiable' => 'boolean',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'expires_at' => 'datetime',
            'rating_avg' => 'decimal:2',
            'search_keywords' => 'array',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mappable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes for map filtering
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeInBounds(Builder $query, float $south, float $west, float $north, float $east): Builder
    {
        return $query->whereBetween('latitude', [$south, $north])
            ->whereBetween('longitude', [$west, $east]);
    }

    public function scopeNearLocation(Builder $query, float $lat, float $lng, int $radiusKm = 25): Builder
    {
        // Use Haversine formula for distance calculation (compatible with all MySQL versions)
        return $query->whereRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
            [$lat, $lng, $lat, $radiusKm]
        )->orderByRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))',
            [$lat, $lng, $lat]
        );
    }

    public function scopeByContentType(Builder $query, string|array $types): Builder
    {
        return is_array($types)
            ? $query->whereIn('content_type', $types)
            : $query->where('content_type', $types);
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeInVoivodeship(Builder $query, string $voivodeship): Builder
    {
        return $query->where('voivodeship', 'like', "%{$voivodeship}%");
    }

    public function scopePriceRange(Builder $query, ?float $minPrice = null, ?float $maxPrice = null): Builder
    {
        if ($minPrice !== null) {
            $query->where(function ($q) use ($minPrice) {
                $q->whereNull('price_from')
                    ->orWhere('price_from', '>=', $minPrice);
            });
        }

        if ($maxPrice !== null) {
            $query->where(function ($q) use ($maxPrice) {
                $q->whereNull('price_to')
                    ->orWhere('price_to', '<=', $maxPrice);
            });
        }

        return $query;
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('is_urgent', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeVisibleAtZoom(Builder $query, int $zoom): Builder
    {
        return $query->where('zoom_level_min', '<=', $zoom);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description_short', 'like', "%{$search}%")
                ->orWhere('category_name', 'like', "%{$search}%")
                ->orWhere('full_address', 'like', "%{$search}%")
                ->orWhereJsonContains('search_keywords', $search);
        });
    }

    // Performance methods
    public function incrementViewCount(): void
    {
        $this->increment('view_count');

        // Also update the source model if it has view_count
        if (method_exists($this->mappable, 'incrementViewCount')) {
            $this->mappable->incrementViewCount();
        }
    }

    public function incrementInteractionCount(): void
    {
        $this->increment('interaction_count');
    }

    // Helper methods
    public function getDistanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371; // kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function getPriceDisplayAttribute(): ?string
    {
        if (! $this->price_from) {
            return null;
        }

        $price = number_format($this->price_from, 2).' '.$this->currency;

        if ($this->price_to && $this->price_to != $this->price_from) {
            $price = number_format($this->price_from, 2).' - '.number_format($this->price_to, 2).' '.$this->currency;
        }

        if ($this->price_negotiable) {
            $price .= ' (do negocjacji)';
        }

        return $price;
    }

    public function getContentTypeNameAttribute(): string
    {
        return match ($this->content_type) {
            'pet_sitter' => 'Pet Sitter',
            'service' => 'Usługa',
            'event_public' => 'Wydarzenie publiczne',
            'event_private' => 'Wydarzenie prywatne',
            'adoption' => 'Adopcja',
            'sale' => 'Sprzedaż',
            'lost_pet' => 'Zaginiony',
            'found_pet' => 'Znaleziony',
            'supplies' => 'Akcesoria',
            default => 'Inne'
        };
    }

    // New scopes for business logic
    public function scopePetSitters(Builder $query): Builder
    {
        return $query->where('content_type', 'pet_sitter')->published();
    }

    public function scopeProfessionalServices(Builder $query): Builder
    {
        return $query->where('content_type', 'service')->published();
    }

    public function scopePublicEvents(Builder $query): Builder
    {
        return $query->where('content_type', 'event_public')->published()->upcoming();
    }

    public function scopePrivateEvents(Builder $query): Builder
    {
        return $query->where('content_type', 'event_private');
    }

    public function scopeOrderByBusinessPriority(Builder $query): Builder
    {
        return $query->orderBy('business_priority')
                    ->orderBy('is_urgent', 'desc')
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('rating_avg', 'desc');
    }

    public function scopeUrgentFirst(Builder $query): Builder
    {
        return $query->orderByRaw('CASE
            WHEN content_type IN ("lost_pet", "found_pet") THEN 0
            ELSE business_priority
        END')
        ->orderBy('is_urgent', 'desc')
        ->orderBy('created_at', 'desc');
    }

    // Performance optimized scopes
    public function scopeWithinBounds(Builder $query, float $minLat, float $maxLat, float $minLon, float $maxLon): Builder
    {
        return $query->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLon, $maxLon]);
    }

    public function scopeOptimizedForMap(Builder $query, array $bounds = [], int $zoom = 10, int $limit = 100): Builder
    {
        $query = $query->published()
            ->visibleAtZoom($zoom)
            ->orderBy('is_featured', 'desc')
            ->orderBy('is_urgent', 'desc')
            ->limit($limit);

        if (! empty($bounds) && count($bounds) === 4) {
            [$south, $west, $north, $east] = $bounds;
            $query->withinBounds($south, $north, $west, $east);
        }

        return $query;
    }

    public function scopeGridClustered(Builder $query, float $gridSize = 0.1): Builder
    {
        return $query->selectRaw('
            ROUND(latitude / ?, 0) * ? as cluster_lat,
            ROUND(longitude / ?, 0) * ? as cluster_lng,
            COUNT(*) as cluster_size,
            GROUP_CONCAT(id) as item_ids,
            AVG(latitude) as avg_lat,
            AVG(longitude) as avg_lng,
            content_type,
            MAX(is_featured) as has_featured,
            MAX(is_urgent) as has_urgent,
            MIN(price_from) as min_price,
            MAX(price_from) as max_price
        ', [$gridSize, $gridSize, $gridSize, $gridSize])
            ->groupByRaw('
            ROUND(latitude / ?),
            ROUND(longitude / ?),
            content_type
        ', [$gridSize, $gridSize]);
    }

    // Cache management methods
    public static function clearMapCache(): void
    {
        Cache::forget('map_categories_v2');
        Cache::forget('map_initial_data_'.now()->format('Y-m-d-H'));

        // Clear cache keys with patterns (only if Redis is available)
        try {
            $store = Cache::getStore();

            // Check if Redis is available and working
            if ($store instanceof \Illuminate\Cache\RedisStore) {
                // Test Redis connection first
                $redis = $store->getRedis();
                $redis->ping(); // This will throw exception if Redis is not working

                $patterns = ['map_data:', 'map_stats_'];
                foreach ($patterns as $pattern) {
                    $keys = $redis->keys("*{$pattern}*");
                    if (! empty($keys)) {
                        $redis->del($keys);
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback to clearing specific cache keys
            \Log::warning('Redis cache clear failed, using fallback', [
                'error' => $e->getMessage(),
                'cache_driver' => config('cache.default'),
            ]);

            // Try to clear known cache patterns manually
            $fallbackKeys = [
                'map_statistics',
                'map_categories_v2',
                'map_initial_data_'.now()->format('Y-m-d-H'),
            ];

            foreach ($fallbackKeys as $key) {
                Cache::forget($key);
            }
        }
    }

    public static function getMapStatistics(): array
    {
        return Cache::remember('map_statistics', 3600, function () {
            return [
                'total_items' => self::count(),
                'published_items' => self::published()->count(),
                'by_content_type' => self::published()
                    ->selectRaw('content_type, COUNT(*) as count')
                    ->groupBy('content_type')
                    ->pluck('count', 'content_type')
                    ->toArray(),
                'by_city' => self::published()
                    ->selectRaw('city, COUNT(*) as count')
                    ->groupBy('city')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->pluck('count', 'city')
                    ->toArray(),
                'featured_count' => self::published()->where('is_featured', true)->count(),
                'urgent_count' => self::published()->where('is_urgent', true)->count(),
                'avg_rating' => self::published()->where('rating_avg', '>', 0)->avg('rating_avg'),
                'total_views' => self::published()->sum('view_count'),
            ];
        });
    }

    // Batch operations for performance
    public static function bulkUpdateStats(array $itemIds): void
    {
        $timestamp = now();

        DB::table('map_items')
            ->whereIn('id', $itemIds)
            ->increment('interaction_count', 1, ['updated_at' => $timestamp]);
    }

    public static function bulkIncrementViews(array $itemIds): void
    {
        DB::table('map_items')
            ->whereIn('id', $itemIds)
            ->increment('view_count', 1, ['updated_at' => now()]);
    }

    // Performance monitoring
    public static function getPerformanceMetrics(): array
    {
        return [
            'total_items' => self::count(),
            'items_with_coordinates' => self::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count(),
            'index_usage' => DB::select("
                SHOW INDEX FROM map_items
                WHERE Key_name LIKE 'idx_%'
            "),
            'table_size' => DB::select("
                SELECT
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.TABLES
                WHERE table_schema = DATABASE()
                AND table_name = 'map_items'
            ")[0]->size_mb ?? 0,
            'avg_query_time' => Cache::get('map_avg_query_time', 0),
        ];
    }

    // Model events for cache invalidation
    protected static function booted(): void
    {
        static::saved(function ($mapItem) {
            self::clearMapCache();
        });

        static::deleted(function ($mapItem) {
            self::clearMapCache();
        });
    }
}
