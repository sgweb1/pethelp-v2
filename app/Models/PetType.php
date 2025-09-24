<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model reprezentujący typ/gatunek zwierzęcia w aplikacji PetHelp.
 *
 * Zawiera informacje o różnych typach zwierząt domowych takich jak psy, koty, ptaki itp.
 * Używany do kategoryzacji zwierząt i filtrowania usług według akceptowanych typów.
 *
 * @package App\Models
 * @author Claude AI Assistant
 * @since 1.0.0
 *
 * @property int $id Unikalny identyfikator typu zwierzęcia
 * @property string $name Nazwa typu zwierzęcia (np. "Pies", "Kot")
 * @property string $slug Slug dla URL (np. "pies", "kot")
 * @property string|null $description Opis typu zwierzęcia
 * @property string|null $icon Ikona reprezentująca typ zwierzęcia
 * @property bool $is_active Czy typ jest aktywny w aplikacji
 * @property int|null $sort_order Kolejność sortowania
 * @property \Carbon\Carbon $created_at Data utworzenia
 * @property \Carbon\Carbon $updated_at Data ostatniej aktualizacji
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\Pet> $pets Zwierzęta tego typu
 * @property-read string $display_name Nazwa typu do wyświetlenia
 *
 * @method static \App\Models\PetType create(array $attributes = []) Tworzy nowy typ zwierzęcia
 * @method static \Illuminate\Database\Eloquent\Builder active() Filtruje aktywne typy
 * @method static \Illuminate\Database\Eloquent\Builder ordered() Sortuje według kolejności
 */
class PetType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    // Relationships
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'pet_type_id');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper Methods
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
}
