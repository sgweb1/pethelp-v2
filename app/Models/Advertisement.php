<?php

namespace App\Models;

use App\Traits\HasMapLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Advertisement extends Model
{
    use HasFactory, HasMapLocation;

    protected $fillable = [
        'user_id',
        'advertisement_category_id',
        'title',
        'description',
        'price',
        'currency',
        'price_negotiable',
        'city',
        'voivodeship',
        'full_address',
        'latitude',
        'longitude',
        'pet_name',
        'pet_type',
        'pet_breed',
        'pet_gender',
        'pet_birth_date',
        'pet_weight',
        'pet_vaccinated',
        'pet_sterilized',
        'pet_health_info',
        'status',
        'is_featured',
        'is_urgent',
        'expires_at',
        'contact_phone',
        'contact_email',
        'show_phone',
        'show_email',
        'preferred_contact',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'view_count',
        'contact_count',
        'favorite_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_negotiable' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'pet_birth_date' => 'date',
            'pet_weight' => 'decimal:2',
            'pet_vaccinated' => 'boolean',
            'pet_sterilized' => 'boolean',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'expires_at' => 'datetime',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'approved_at' => 'datetime',
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

    public function images(): HasMany
    {
        return $this->hasMany(AdvertisementImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(AdvertisementImage::class)->where('is_primary', true);
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('pet_type', $type);
    }

    public function scopePriceRange(Builder $query, ?float $minPrice = null, ?float $maxPrice = null): Builder
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
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
    public function getPetAgeAttribute(): ?string
    {
        if (! $this->pet_birth_date) {
            return null;
        }

        $age = $this->pet_birth_date->diffForHumans();

        return $age;
    }

    public function getContactInfoAttribute(): array
    {
        $contact = [];

        if ($this->show_phone && $this->contact_phone) {
            $contact['phone'] = $this->contact_phone;
        }

        if ($this->show_email && $this->contact_email) {
            $contact['email'] = $this->contact_email;
        }

        return $contact;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    // Implementation of HasMapLocation trait
    protected function getMapData(): ?array
    {
        // Only sync published, non-expired ads with location
        if ($this->status !== 'published' || $this->isExpired() || ! $this->latitude || ! $this->longitude) {
            return null;
        }

        // Determine content type based on category
        $contentType = $this->determineContentType();

        return [
            'user_id' => $this->user_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'voivodeship' => $this->voivodeship,
            'full_address' => $this->full_address,
            'title' => $this->title,
            'description_short' => $this->truncateDescription($this->description),
            'primary_image_url' => $this->primaryImage?->path,
            'content_type' => $contentType,
            'category_name' => $this->advertisementCategory->name,
            'category_icon' => $this->advertisementCategory->icon ?? $this->getDefaultIcon($contentType),
            'category_color' => $this->advertisementCategory->color,
            'price_from' => $this->price,
            'price_to' => null,
            'currency' => $this->currency,
            'price_negotiable' => $this->price_negotiable,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
            'starts_at' => null,
            'ends_at' => null,
            'expires_at' => $this->expires_at,
            'view_count' => $this->view_count,
            'interaction_count' => $this->contact_count,
            'rating_avg' => 0.00,
            'rating_count' => 0,
            'zoom_level_min' => 12,
            'search_keywords' => $this->extractSearchKeywords(
                $this->title.' '.$this->description.' '.$this->pet_name.' '.$this->pet_breed
            ),
        ];
    }

    private function determineContentType(): string
    {
        $categoryType = $this->advertisementCategory->type;

        return match ($categoryType) {
            'adoption' => 'adoption',
            'sales' => 'sale',
            'lost_found' => $this->isLostPet() ? 'lost_pet' : 'found_pet',
            'supplies' => 'supplies',
            default => 'sale'
        };
    }

    private function isLostPet(): bool
    {
        return str_contains(strtolower($this->advertisementCategory->name), 'zaginion') ||
               str_contains(strtolower($this->title), 'zaginion');
    }

    private function getDefaultIcon(string $contentType): string
    {
        return match ($contentType) {
            'adoption' => 'heart',
            'sale' => 'currency-dollar',
            'lost_pet' => 'exclamation-triangle',
            'found_pet' => 'check-circle',
            'supplies' => 'shopping-bag',
            default => 'tag'
        };
    }
}
