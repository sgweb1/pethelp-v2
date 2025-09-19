<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvertisementCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'type',
        'is_active',
        'sort_order',
        'requires_approval',
        'allows_images',
        'max_images',
        'advertisement_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
            'allows_images' => 'boolean',
        ];
    }

    // Relationships
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Cache frequently accessed categories
    public static function getActiveCategoriesCache(): \Illuminate\Support\Collection
    {
        return cache()->remember(
            'advertisement_categories.active',
            3600, // 1 hour
            fn () => static::active()->ordered()->get()
        );
    }

    public static function getCategoriesByType(string $type): \Illuminate\Support\Collection
    {
        return cache()->remember(
            "advertisement_categories.type.{$type}",
            3600,
            fn () => static::active()->byType($type)->ordered()->get()
        );
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->name.' > '.$this->name;
        }

        return $this->name;
    }

    public function updateAdvertisementCount(): void
    {
        $this->update([
            'advertisement_count' => $this->advertisements()->where('status', 'published')->count(),
        ]);
    }
}
