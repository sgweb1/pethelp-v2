<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pet_types' => 'array',
            'pet_sizes' => 'array',
            'home_service' => 'boolean',
            'sitter_home' => 'boolean',
            'is_active' => 'boolean',
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
        ];
    }

    protected $fillable = [
        'sitter_id',
        'category_id',
        'title',
        'description',
        'price_per_hour',
        'price_per_day',
        'pet_types',
        'pet_sizes',
        'home_service',
        'sitter_home',
        'max_pets',
        'is_active',
    ];

    public function sitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sitter_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }

    // Search Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation(Builder $query, float $lat, float $lng, int $radius = 10): Builder
    {
        return $query->whereHas('sitter.locations', function ($q) use ($lat, $lng, $radius) {
            $q->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
              ->having('distance', '<=', $radius)
              ->orderBy('distance');
        });
    }

    public function scopeByPetType(Builder $query, string $petType): Builder
    {
        return $query->whereJsonContains('pet_types', $petType);
    }

    public function scopeByPetSize(Builder $query, string $petSize): Builder
    {
        return $query->whereJsonContains('pet_sizes', $petSize);
    }

    public function scopeByServiceType(Builder $query, string $serviceType): Builder
    {
        if ($serviceType === 'home_service') {
            return $query->where('home_service', true);
        }
        if ($serviceType === 'sitter_home') {
            return $query->where('sitter_home', true);
        }
        return $query;
    }

    public function scopeByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice, string $priceType = 'hour'): Builder
    {
        $column = $priceType === 'day' ? 'price_per_day' : 'price_per_hour';

        if ($minPrice !== null) {
            $query->where($column, '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where($column, '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeWithAvgRating(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating');
    }

    public function scopeMinRating(Builder $query, float $minRating): Builder
    {
        return $query->withAvgRating()
                     ->having('reviews_avg_rating', '>=', $minRating);
    }

    // Helper Methods
    public function getDisplayPriceAttribute(): string
    {
        if ($this->price_per_hour && $this->price_per_day) {
            return "od {$this->price_per_hour}zł/h ({$this->price_per_day}zł/dzień)";
        }
        if ($this->price_per_hour) {
            return "{$this->price_per_hour}zł/h";
        }
        if ($this->price_per_day) {
            return "{$this->price_per_day}zł/dzień";
        }
        return 'Do uzgodnienia';
    }

    public function getServiceTypesAttribute(): array
    {
        $types = [];
        if ($this->home_service) $types[] = 'U klienta';
        if ($this->sitter_home) $types[] = 'U opiekuna';
        return $types;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews_avg_rating ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
