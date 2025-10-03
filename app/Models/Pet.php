<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model reprezentujący zwierzę domowe w aplikacji PetHelp.
 *
 * Zawiera wszystkie informacje o zwierzętach użytkowników, w tym dane podstawowe,
 * medyczne, behawioralne i preferencje dotyczące opieki. Obsługuje różne gatunki
 * zwierząt z dedykowanymi polami dla każdego typu.
 *
 * @package App\Models
 * @author Claude AI Assistant
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator zwierzęcia
 * @property int $owner_id ID właściciela zwierzęcia
 * @property int|null $pet_type_id ID typu/gatunku zwierzęcia
 * @property string $name Imię zwierzęcia
 * @property string|null $breed Rasa zwierzęcia
 * @property \Carbon\Carbon|null $birth_date Data urodzenia
 * @property string|null $gender Płeć (male, female, unknown)
 * @property decimal|null $weight Waga w kilogramach
 * @property string|null $description Opis zwierzęcia
 * @property array|null $medical_info Informacje medyczne
 * @property array|null $behavior_traits Cechy behawioralne
 * @property string|null $photo_url URL do zdjęcia zwierzęcia
 * @property array|null $emergency_contacts Kontakty awaryjne
 * @property bool $is_active Czy profil jest aktywny
 * @property \Carbon\Carbon $created_at Data utworzenia profilu
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 *
 * @property-read \App\Models\User $user Właściciel zwierzęcia
 * @property-read \App\Models\User $owner Alias dla właściciela zwierzęcia
 * @property-read \App\Models\PetType|null $petType Typ/gatunek zwierzęcia
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Booking> $bookings Rezerwacje dla tego zwierzęcia
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\CareType> $careTypes Typy opieki dla zwierzęcia
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Photo> $photos Zdjęcia zwierzęcia
 * @property-read string $type_label Nazwa typu zwierzęcia
 * @property-read string $size_label Nazwa rozmiaru zwierzęcia
 * @property-read string $gender_label Nazwa płci zwierzęcia
 * @property-read int|null $age Wiek w latach
 * @property-read string $age_group Grupa wiekowa
 * @property-read array $special_needs_list Lista specjalnych potrzeb
 * @property-read string $photo_url Pełny URL do zdjęcia
 *
 * @method static \App\Models\Pet create(array $attributes = []) Tworzy nowe zwierzę
 * @method static \Illuminate\Database\Eloquent\Builder active() Filtruje aktywne profile
 * @method static \Illuminate\Database\Eloquent\Builder byType(string $type) Filtruje po typie zwierzęcia
 * @method static \Illuminate\Database\Eloquent\Builder byPetTypeId(int $petTypeId) Filtruje po ID typu
 * @method static \Illuminate\Database\Eloquent\Builder bySize(string $size) Filtruje po rozmiarze
 * @method static \Illuminate\Database\Eloquent\Builder forOwner(int $ownerId) Filtruje dla właściciela
 */
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
            'special_needs' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected $fillable = [
        'owner_id',
        'name',
        'pet_type_id',
        'breed',
        'birth_date',
        'gender',
        'weight',
        'description',
        'medical_info',
        'behavior_traits',
        'photo_url',
        'emergency_contacts',
        'special_needs',
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

    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function careTypes(): BelongsToMany
    {
        return $this->belongsToMany(CareType::class, 'pet_care_types');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->whereHas('petType', function ($q) use ($type) {
            $q->where('slug', $type);
        });
    }

    public function scopeByPetTypeId(Builder $query, int $petTypeId): Builder
    {
        return $query->where('pet_type_id', $petTypeId);
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
        return $this->petType?->name ?? 'Nieznany';
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

    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
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

        // Zabezpieczenie przed tym że special_needs może być string
        if (is_string($needs)) {
            $needs = json_decode($needs, true) ?? [];
        }

        // Upewnij się że to jest array
        if (!is_array($needs)) {
            return [];
        }

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
        if (isset($this->attributes['photo_url']) && $this->attributes['photo_url']) {
            // If it's already a full URL, return as is
            if (str_starts_with($this->attributes['photo_url'], 'http')) {
                return $this->attributes['photo_url'];
            }
            // Otherwise treat as storage path
            return asset('storage/' . $this->attributes['photo_url']);
        }

        return asset('images/pet-placeholder.png');
    }
}
