<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class UserRepository
{
    protected $cacheTime = 300; // 5 minutes

    public function findSitters(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $cacheKey = 'sitters_' . md5(json_encode($filters) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($filters, $perPage) {
            $query = User::with(['profile', 'reviews:id,user_id,rating,created_at', 'locations:id,user_id,city,latitude,longitude'])
                ->where('role', 'sitter')
                ->where('is_active', true)
                ->select(['id', 'name', 'email', 'avatar', 'bio', 'hourly_rate', 'daily_rate', 'is_verified', 'created_at']);

            $this->applyFilters($query, $filters);

            return $query->paginate($perPage);
        });
    }

    public function findSitterById(int $id): ?User
    {
        $cacheKey = "sitter_{$id}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return User::with([
                'profile',
                'reviews' => function ($query) {
                    $query->with('reviewer:id,name,avatar')->latest()->take(10);
                },
                'locations',
                'availability' => function ($query) {
                    $query->where('date', '>=', now())->take(30);
                }
            ])
            ->where('role', 'sitter')
            ->where('is_active', true)
            ->find($id);
        });
    }

    public function getTopRatedSitters(int $limit = 6): Collection
    {
        $cacheKey = "top_rated_sitters_{$limit}";

        return Cache::remember($cacheKey, $this->cacheTime * 2, function () use ($limit) {
            return User::with(['profile', 'reviews:id,user_id,rating'])
                ->where('role', 'sitter')
                ->where('is_active', true)
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->having('reviews_count', '>=', 3)
                ->orderBy('reviews_avg_rating', 'desc')
                ->orderBy('reviews_count', 'desc')
                ->take($limit)
                ->get();
        });
    }

    public function getNearestSitters(float $latitude, float $longitude, int $radius = 10, int $limit = 10): Collection
    {
        $cacheKey = "nearest_sitters_{$latitude}_{$longitude}_{$radius}_{$limit}";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($latitude, $longitude, $radius, $limit) {
            return User::with(['profile', 'reviews:id,user_id,rating', 'locations'])
                ->where('role', 'sitter')
                ->where('is_active', true)
                ->whereHas('locations', function ($query) use ($latitude, $longitude, $radius) {
                    $query->selectRaw("
                        *, (
                            6371 * acos(
                                cos(radians(?)) * cos(radians(latitude)) *
                                cos(radians(longitude) - radians(?)) +
                                sin(radians(?)) * sin(radians(latitude))
                            )
                        ) AS distance", [$latitude, $longitude, $latitude])
                        ->having('distance', '<=', $radius)
                        ->orderBy('distance');
                })
                ->take($limit)
                ->get();
        });
    }

    public function getSittersByAvailability(\DateTime $date, int $limit = 20): Collection
    {
        $cacheKey = "available_sitters_{$date->format('Y-m-d')}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($date, $limit) { // 30 minutes cache
            return User::with(['profile', 'reviews:id,user_id,rating'])
                ->where('role', 'sitter')
                ->where('is_active', true)
                ->whereHas('availability', function ($query) use ($date) {
                    $query->where('date', $date->format('Y-m-d'))
                          ->where('is_available', true);
                })
                ->withAvg('reviews', 'rating')
                ->orderBy('reviews_avg_rating', 'desc')
                ->take($limit)
                ->get();
        });
    }

    public function clearSitterCache(int $sitterId): void
    {
        Cache::forget("sitter_{$sitterId}");
        Cache::forget("top_rated_sitters_6");

        // Clear search caches (simplified - in production you'd want more sophisticated cache tagging)
        $this->clearSearchCaches();
    }

    public function clearSearchCaches(): void
    {
        // In production, use cache tags for better cache management
        Cache::flush();
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search_term'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_term'] . '%')
                  ->orWhere('bio', 'like', '%' . $filters['search_term'] . '%');
            });
        }

        if (!empty($filters['min_price'])) {
            $priceColumn = ($filters['price_type'] ?? 'hour') === 'hour' ? 'hourly_rate' : 'daily_rate';
            $query->where($priceColumn, '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $priceColumn = ($filters['price_type'] ?? 'hour') === 'hour' ? 'hourly_rate' : 'daily_rate';
            $query->where($priceColumn, '<=', $filters['max_price']);
        }

        if (!empty($filters['min_rating'])) {
            $query->whereHas('reviews', function ($q) use ($filters) {
                $q->havingRaw('AVG(rating) >= ?', [$filters['min_rating']]);
            });
        }

        if ($filters['verified_only'] ?? false) {
            $query->where('is_verified', true);
        }

        // Apply sorting
        $this->applySorting($query, $filters['sort_by'] ?? 'relevance');
    }

    private function applySorting($query, string $sortBy): void
    {
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('hourly_rate', 'asc');
                break;
            case 'price_high':
                $query->orderBy('hourly_rate', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default: // relevance
                $query->withCount('reviews')
                      ->orderBy('reviews_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }
    }
}
