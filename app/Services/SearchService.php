<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function searchSitters(array $filters): LengthAwarePaginator
    {
        $query = User::with(['profile', 'reviews', 'locations'])
            ->where('role', 'sitter')
            ->where('is_active', true);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort_by'] ?? 'relevance');

        return $query->paginate(12);
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search_term'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_term'] . '%')
                  ->orWhere('bio', 'like', '%' . $filters['search_term'] . '%');
            });
        }

        if (!empty($filters['location']) && isset($filters['latitude'], $filters['longitude'])) {
            $this->applyLocationFilter($query, $filters);
        }

        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $this->applyPriceFilter($query, $filters);
        }

        if (!empty($filters['min_rating'])) {
            $query->whereHas('reviews', function ($q) use ($filters) {
                $q->havingRaw('AVG(rating) >= ?', [$filters['min_rating']]);
            });
        }

        if (!empty($filters['pet_type'])) {
            $query->whereJsonContains('pet_types', $filters['pet_type']);
        }

        if (!empty($filters['service_type'])) {
            $query->whereJsonContains('services', $filters['service_type']);
        }

        if ($filters['verified_only'] ?? false) {
            $query->where('is_verified', true);
        }

        if ($filters['instant_booking'] ?? false) {
            $query->where('instant_booking', true);
        }

        if (!empty($filters['experience_years'])) {
            $query->where('experience_years', '>=', $filters['experience_years']);
        }

        if ($filters['has_insurance'] ?? false) {
            $query->where('has_insurance', true);
        }
    }

    private function applyLocationFilter(Builder $query, array $filters): void
    {
        $latitude = $filters['latitude'];
        $longitude = $filters['longitude'];
        $radius = $filters['radius'] ?? 10;

        $query->whereHas('locations', function ($q) use ($latitude, $longitude, $radius) {
            $q->selectRaw("
                *, (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS distance", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
        });
    }

    private function applyPriceFilter(Builder $query, array $filters): void
    {
        $priceType = $filters['price_type'] ?? 'hour';
        $priceColumn = $priceType === 'hour' ? 'hourly_rate' : 'daily_rate';

        if (!empty($filters['min_price'])) {
            $query->where($priceColumn, '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where($priceColumn, '<=', $filters['max_price']);
        }
    }

    private function applySorting(Builder $query, string $sortBy): void
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
            case 'distance':
                // Distance sorting is handled in location filter
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

    public function getActiveFiltersCount(array $filters): int
    {
        $count = 0;

        if (!empty($filters['search_term'])) $count++;
        if (!empty($filters['location'])) $count++;
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) $count++;
        if (!empty($filters['min_rating'])) $count++;
        if (!empty($filters['pet_type'])) $count++;
        if (!empty($filters['service_type'])) $count++;
        if ($filters['verified_only'] ?? false) $count++;
        if ($filters['instant_booking'] ?? false) $count++;
        if (!empty($filters['experience_years'])) $count++;
        if ($filters['has_insurance'] ?? false) $count++;

        return $count;
    }
}
