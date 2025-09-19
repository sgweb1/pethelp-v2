<?php

namespace App\Models;

use App\Traits\HasMapLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory, HasMapLocation;

    protected $fillable = [
        'user_id',
        'event_type_id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'max_participants',
        'entry_fee',
        'currency',
        'is_invitation_only',
        'status',
        'is_featured',
        'registration_deadline',
        'allow_waiting_list',
        'current_participants',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'is_invitation_only' => 'boolean',
            'is_featured' => 'boolean',
            'allow_waiting_list' => 'boolean',
            'entry_fee' => 'decimal:2',
        ];
    }

    // Relationships with eager loading optimization
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(EventLocation::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', 'confirmed');
    }

    // Performance scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->whereHas('location', function ($q) use ($city) {
            $q->where('city', 'like', "%{$city}%");
        });
    }

    public function scopeNearLocation(Builder $query, float $lat, float $lng, int $radiusKm = 10): Builder
    {
        return $query->whereHas('location', function ($q) use ($lat, $lng, $radiusKm) {
            $q->whereRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                [$lat, $lng, $lat, $radiusKm]
            );
        });
    }

    public function scopeWithType(Builder $query, int $typeId): Builder
    {
        return $query->where('event_type_id', $typeId);
    }

    // Performance methods
    public function updateParticipantCount(): void
    {
        $this->update([
            'current_participants' => $this->confirmedRegistrations()->count(),
        ]);
    }

    public function canUserRegister(User $user): bool
    {
        // Check if user is already registered
        if ($this->registrations()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check if event has capacity
        if ($this->max_participants && $this->current_participants >= $this->max_participants) {
            return $this->allow_waiting_list;
        }

        // Check registration deadline
        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        return $this->status === 'published';
    }

    public function getAvailableSpotsAttribute(): ?int
    {
        if (! $this->max_participants) {
            return null;
        }

        return max(0, $this->max_participants - $this->current_participants);
    }

    // Implementation of HasMapLocation trait
    protected function getMapData(): ?array
    {
        // Only sync published events with location data
        if ($this->status !== 'published' || ! $this->location) {
            return null;
        }

        $location = $this->location;

        return [
            'user_id' => $this->user_id,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'city' => $location->city,
            'voivodeship' => $this->determineVoivodeship($location->city),
            'full_address' => $location->full_address,
            'title' => $this->title,
            'description_short' => $this->truncateDescription($this->description),
            'primary_image_url' => null, // Events don't have images yet
            'content_type' => 'event',
            'category_name' => $this->eventType->name,
            'category_icon' => $this->eventType->icon ?? 'calendar',
            'category_color' => $this->eventType->color,
            'price_from' => $this->entry_fee > 0 ? $this->entry_fee : null,
            'price_to' => null,
            'currency' => $this->currency,
            'price_negotiable' => false,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_urgent' => false,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'expires_at' => $this->registration_deadline,
            'view_count' => $this->view_count,
            'interaction_count' => $this->current_participants,
            'rating_avg' => 0.00,
            'rating_count' => 0,
            'zoom_level_min' => 10,
            'search_keywords' => $this->extractSearchKeywords(
                $this->title.' '.$this->description.' '.$this->eventType->name
            ),
        ];
    }

    // Helper to determine voivodeship from city (simplified)
    private function determineVoivodeship(string $city): string
    {
        $voivodeships = [
            'Warszawa' => 'mazowieckie',
            'Kraków' => 'małopolskie',
            'Gdańsk' => 'pomorskie',
            'Wrocław' => 'dolnośląskie',
            'Poznań' => 'wielkopolskie',
            'Łódź' => 'łódzkie',
            'Szczecin' => 'zachodniopomorskie',
            'Katowice' => 'śląskie',
            // Add more mappings as needed
        ];

        foreach ($voivodeships as $majorCity => $voivodeship) {
            if (stripos($city, $majorCity) !== false) {
                return $voivodeship;
            }
        }

        return 'mazowieckie'; // Default fallback
    }
}
