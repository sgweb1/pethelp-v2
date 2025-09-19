<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // Scopes for performance
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Cache frequently accessed data
    public static function getActiveTypesCache(): \Illuminate\Support\Collection
    {
        return cache()->remember(
            'event_types.active',
            3600, // 1 hour
            fn () => static::active()->ordered()->get()
        );
    }
}
