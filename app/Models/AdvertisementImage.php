<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'filename',
        'original_filename',
        'path',
        'disk',
        'file_size',
        'mime_type',
        'width',
        'height',
        'variants',
        'sort_order',
        'is_primary',
        'alt_text',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'is_primary' => 'boolean',
        ];
    }

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Helper methods
    public function getUrlAttribute(): string
    {
        return asset("storage/{$this->path}");
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (isset($this->variants['thumbnail'])) {
            return asset("storage/{$this->variants['thumbnail']}");
        }

        return $this->url;
    }

    public function getFileSizeHumanAttribute(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
