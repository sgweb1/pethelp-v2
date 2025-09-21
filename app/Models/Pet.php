<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'weight' => 'decimal:2',
            'medical_info' => 'array',
            'behavior_traits' => 'array',
            'emergency_contacts' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected $fillable = [
        'owner_id',
        'name',
        'type',
        'breed',
        'birth_date',
        'gender',
        'weight',
        'description',
        'medical_info',
        'behavior_traits',
        'photo_url',
        'emergency_contacts',
        'is_active',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function owner(): BelongsTo
    {
        return $this->user();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function careTypes(): BelongsToMany
    {
        return $this->belongsToMany(CareType::class, 'pet_care_types');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeBySize(Builder $query, string $size): Builder
    {
        return $query->where('size', $size);
    }

    public function scopeForOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    // Helper Methods
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'dog' => 'Pies',
            'cat' => 'Kot',
            'bird' => 'Ptak',
            'rabbit' => 'Królik',
            'other' => 'Inne',
            default => 'Nieznany'
        };
    }

    public function getSizeLabelAttribute(): string
    {
        return match($this->size) {
            'small' => 'Mały',
            'medium' => 'Średni',
            'large' => 'Duży',
            default => 'Nieznany'
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'male' => 'Samiec',
            'female' => 'Samica',
            default => 'Nieznana'
        };
    }

    public function getAgeGroupAttribute(): string
    {
        if (!$this->age) return 'Nieznany wiek';

        return match(true) {
            $this->age < 1 => 'Szczeniak/Kociak',
            $this->age < 3 => 'Młody',
            $this->age < 8 => 'Dorosły',
            default => 'Senior'
        };
    }

    public function hasSpecialNeeds(): bool
    {
        return !empty($this->special_needs);
    }

    public function getSpecialNeedsListAttribute(): array
    {
        $needs = $this->special_needs ?? [];
        $labels = [
            'medication' => 'Leki',
            'exercise' => 'Specjalne ćwiczenia',
            'diet' => 'Specjalna dieta',
            'elderly' => 'Opieka senioralna',
            'medical' => 'Opieka medyczna',
            'training' => 'Trening',
        ];

        return array_map(fn($need) => $labels[$need] ?? $need, $needs);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return asset('images/pet-placeholder.png');
    }
}
