<?php

namespace App\Traits;

use App\Models\MapItem;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMapLocation
{
    protected static function bootHasMapLocation(): void
    {
        // Temporarily disable auto sync during seeding
        if (app()->environment('production') || ! app()->runningInConsole()) {
            // Automatically create/update MapItem when model is created/updated
            static::saved(function ($model) {
                $model->syncToMap();
            });

            // Delete MapItem when model is deleted
            static::deleted(function ($model) {
                $model->mapItem?->delete();
            });
        }
    }

    public function mapItem(): MorphOne
    {
        return $this->morphOne(MapItem::class, 'mappable');
    }

    public function syncToMap(): void
    {
        $mapData = $this->getMapData();

        if (! $mapData) {
            // If no map data, remove from map
            $this->mapItem?->delete();

            return;
        }

        $this->mapItem()->updateOrCreate(
            [
                'mappable_type' => get_class($this),
                'mappable_id' => $this->id,
            ],
            $mapData
        );
    }

    // Abstract method - each model must implement this
    abstract protected function getMapData(): ?array;

    // Helper method to validate required location data
    protected function validateLocationData(array $data): bool
    {
        $required = ['latitude', 'longitude', 'city', 'voivodeship', 'full_address'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    // Helper to truncate description for map display
    protected function truncateDescription(string $text, int $maxLength = 300): string
    {
        return strlen($text) > $maxLength ? substr($text, 0, $maxLength - 3).'...' : $text;
    }

    // Helper to extract keywords for search
    protected function extractSearchKeywords(string $text): array
    {
        // Remove common words, keep meaningful keywords
        $commonWords = ['i', 'a', 'the', 'is', 'at', 'to', 'for', 'of', 'with', 'on', 'in', 'jako', 'dla', 'na', 'w', 'z', 'do', 'przez', 'od'];

        $words = preg_split('/[\s\-_,.\/:;!?()]+/', strtolower($text));
        $keywords = array_filter($words, function ($word) use ($commonWords) {
            return strlen($word) > 2 && ! in_array($word, $commonWords);
        });

        return array_values(array_unique($keywords));
    }
}
