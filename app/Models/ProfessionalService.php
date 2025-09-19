<?php

namespace App\Models;

use App\Traits\HasMapLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalService extends Model
{
    use HasFactory, HasMapLocation;

    protected $fillable = [
        'user_id',
        'advertisement_category_id',
        'business_name',
        'contact_person',
        'description',
        'services_offered',
        'base_price',
        'hourly_rate',
        'currency',
        'pricing_details',
        'availability',
        'city',
        'voivodeship',
        'full_address',
        'latitude',
        'longitude',
        'service_radius_km',
        'phone',
        'email',
        'website',
        'social_media',
        'certifications',
        'specializations',
        'experience_years',
        'is_insured',
        'is_licensed',
        'status',
        'is_featured',
        'accepts_online_booking',
        'offers_emergency_services',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'view_count',
        'contact_count',
        'average_rating',
        'review_count',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'pricing_details' => 'array',
            'availability' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'social_media' => 'array',
            'certifications' => 'array',
            'specializations' => 'array',
            'is_insured' => 'boolean',
            'is_licensed' => 'boolean',
            'is_featured' => 'boolean',
            'accepts_online_booking' => 'boolean',
            'offers_emergency_services' => 'boolean',
            'approved_at' => 'datetime',
            'average_rating' => 'decimal:2',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advertisementCategory(): BelongsTo
    {
        return $this->belongsTo(AdvertisementCategory::class);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeWithinServiceRadius(Builder $query, float $lat, float $lng): Builder
    {
        return $query->selectRaw('
            *,
            ST_Distance_Sphere(
                POINT(longitude, latitude),
                POINT(?, ?)
            ) / 1000 as distance_km
        ', [$lng, $lat])
            ->havingRaw('distance_km <= service_radius_km')
            ->orderBy('distance_km');
    }

    public function scopeWithRating(Builder $query, float $minRating): Builder
    {
        return $query->where('average_rating', '>=', $minRating);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithEmergencyServices(Builder $query): Builder
    {
        return $query->where('offers_emergency_services', true);
    }

    public function scopeWithOnlineBooking(Builder $query): Builder
    {
        return $query->where('accepts_online_booking', true);
    }

    // Performance methods
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementContactCount(): void
    {
        $this->increment('contact_count');
    }

    // Helper methods
    public function getServiceAreaAttribute(): string
    {
        return "{$this->city} + {$this->service_radius_km} km";
    }

    public function getPriceRangeAttribute(): ?string
    {
        if (! $this->base_price && ! $this->hourly_rate) {
            return null;
        }

        $prices = [];

        if ($this->base_price) {
            $prices[] = "od {$this->base_price} {$this->currency}";
        }

        if ($this->hourly_rate) {
            $prices[] = "{$this->hourly_rate} {$this->currency}/h";
        }

        return implode(', ', $prices);
    }

    public function getServicesListAttribute(): array
    {
        if (is_string($this->services_offered)) {
            return array_filter(explode(',', $this->services_offered));
        }

        return $this->services_offered ?? [];
    }

    public function getSpecializationListAttribute(): array
    {
        if (is_string($this->specializations)) {
            return json_decode($this->specializations, true) ?? [];
        }

        return $this->specializations ?? [];
    }

    public function getRatingDisplayAttribute(): string
    {
        if (! $this->average_rating || ! $this->review_count) {
            return 'Brak ocen';
        }

        return number_format($this->average_rating, 1)."/5.0 ({$this->review_count} ".
               ($this->review_count === 1 ? 'ocena' : 'ocen').')';
    }

    // Implementation of HasMapLocation trait
    protected function getMapData(): ?array
    {
        // Only sync published services with location
        if ($this->status !== 'published' || ! $this->latitude || ! $this->longitude) {
            return null;
        }

        return [
            'user_id' => $this->user_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'voivodeship' => $this->voivodeship,
            'full_address' => $this->full_address,
            'title' => $this->business_name,
            'description_short' => $this->truncateDescription($this->description),
            'primary_image_url' => null, // Services don't have images yet
            'content_type' => 'service',
            'category_name' => $this->advertisementCategory->name,
            'category_icon' => $this->advertisementCategory->icon ?? 'briefcase',
            'category_color' => $this->advertisementCategory->color,
            'price_from' => $this->base_price ?: $this->hourly_rate,
            'price_to' => null,
            'currency' => $this->currency,
            'price_negotiable' => true, // Services are usually negotiable
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->offers_emergency_services,
            'starts_at' => null,
            'ends_at' => null,
            'expires_at' => null,
            'view_count' => $this->view_count,
            'interaction_count' => $this->contact_count,
            'rating_avg' => $this->average_rating,
            'rating_count' => $this->review_count,
            'zoom_level_min' => 11, // Show services at city level
            'search_keywords' => $this->extractSearchKeywords(
                $this->business_name.' '.$this->description.' '.
                implode(' ', $this->getServicesListAttribute()).' '.
                implode(' ', $this->getSpecializationListAttribute())
            ),
        ];
    }
}
