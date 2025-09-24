<?php

namespace App\Http\Controllers;

use App\Http\Resources\MapItemResource;
use App\Models\MapItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class MapController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'bounds' => 'array|size:4',
            'bounds.0' => 'numeric|between:-90,90', // south latitude
            'bounds.1' => 'numeric|between:-180,180', // west longitude
            'bounds.2' => 'numeric|between:-90,90', // north latitude
            'bounds.3' => 'numeric|between:-180,180', // east longitude
            'content_types' => 'array',
            'content_types.*' => Rule::in(['event', 'adoption', 'sale', 'lost_pet', 'found_pet', 'supplies', 'service']),
            'content_type' => 'string',
            'service_type' => 'string',
            'location' => 'string|max:255',
            'city' => 'string|max:255',
            'voivodeship' => 'string|max:255',
            'zoom_level' => 'integer|between:1,20',
            'featured_only' => 'boolean',
            'urgent_only' => 'boolean',
            'max_price' => 'numeric|min:0',
            'min_price' => 'numeric|min:0',
            'search' => 'string|max:255',
            'limit' => 'integer|between:1,1000',
        ]);

        $query = MapItem::query()
            ->select([
                'id', 'latitude', 'longitude', 'title', 'description_short',
                'primary_image_url', 'content_type', 'category_name', 'category_icon',
                'category_color', 'price_from', 'currency', 'status', 'is_featured',
                'is_urgent', 'rating_avg', 'rating_count', 'view_count',
            ])
            ->active()
            ->visibleAtZoom($validated['zoom_level'] ?? 12);

        // Geographic bounds filtering
        if (isset($validated['bounds'])) {
            [$south, $west, $north, $east] = $validated['bounds'];
            $query->inBounds($south, $west, $north, $east);
        }

        // Content type filtering
        if (isset($validated['content_types'])) {
            $query->whereIn('content_type', $validated['content_types']);
        }

        // Feature flags
        if ($validated['featured_only'] ?? false) {
            $query->featured();
        }

        if ($validated['urgent_only'] ?? false) {
            $query->urgent();
        }

        // Price range filtering
        if (isset($validated['min_price'])) {
            $query->where('price_from', '>=', $validated['min_price']);
        }

        if (isset($validated['max_price'])) {
            $query->where('price_from', '<=', $validated['max_price']);
        }

        // Text search
        if (isset($validated['search'])) {
            $query->search($validated['search']);
        }

        // Content type filtering (single value)
        if (isset($validated['content_type'])) {
            $query->byContentType($validated['content_type']);
        }

        // Service type mapping (for backward compatibility)
        if (isset($validated['service_type'])) {
            $contentType = $this->mapServiceTypeToContentType($validated['service_type']);
            if ($contentType) {
                $query->byContentType($contentType);
            }
        }

        // Location filtering (city-based search)
        if (isset($validated['location'])) {
            $location = $validated['location'];
            // Clean location string - remove common words that interfere with search
            $cleanedLocation = str_replace(['województwo ', ', województwo'], '', $location);

            $query->where(function ($q) use ($location, $cleanedLocation) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('full_address', 'like', "%{$location}%")
                    ->orWhere('voivodeship', 'like', "%{$location}%");

                // If location was cleaned, also search with cleaned version
                if ($cleanedLocation !== $location) {
                    $q->orWhere('city', 'like', "%{$cleanedLocation}%")
                        ->orWhere('full_address', 'like', "%{$cleanedLocation}%")
                        ->orWhere('voivodeship', 'like', "%{$cleanedLocation}%");
                }
            });
        }

        // City filtering
        if (isset($validated['city'])) {
            $query->where('city', 'like', "%{$validated['city']}%");
        }

        // Voivodeship filtering
        if (isset($validated['voivodeship'])) {
            $query->where('voivodeship', 'like', "%{$validated['voivodeship']}%");
        }

        // Apply limit and get results
        $limit = $validated['limit'] ?? 500;
        $items = $query->limit($limit)->get();

        return MapItemResource::collection($items);
    }

    public function show(MapItem $mapItem): MapItemResource
    {
        // Increment view count for analytics
        $mapItem->increment('view_count');

        // Load the actual content model for detailed view
        $mapItem->load('mappable');

        return new MapItemResource($mapItem);
    }

    public function nearLocation(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'numeric|between:0.1,100',
            'content_types' => 'array',
            'content_types.*' => Rule::in(['event', 'adoption', 'sale', 'lost_pet', 'found_pet', 'supplies', 'service']),
            'limit' => 'integer|between:1,100',
        ]);

        $radiusKm = $validated['radius_km'] ?? 10;
        $limit = $validated['limit'] ?? 20;

        $query = MapItem::query()
            ->select([
                'id', 'latitude', 'longitude', 'title', 'description_short',
                'primary_image_url', 'content_type', 'category_name', 'category_icon',
                'category_color', 'price_from', 'currency', 'is_featured', 'rating_avg',
            ])
            ->active()
            ->nearLocation($validated['latitude'], $validated['longitude'], $radiusKm);

        if (isset($validated['content_types'])) {
            $query->whereIn('content_type', $validated['content_types']);
        }

        $items = $query->limit($limit)->get();

        return MapItemResource::collection($items);
    }

    public function stats(): array
    {
        $cacheKey = 'map_stats_'.now()->format('Y-m-d-H');

        return cache()->remember($cacheKey, 3600, function () {
            return [
                'total_locations' => MapItem::active()->count(),
                'content_types' => MapItem::active()
                    ->selectRaw('content_type, COUNT(*) as count')
                    ->groupBy('content_type')
                    ->pluck('count', 'content_type')
                    ->toArray(),
                'featured_count' => MapItem::active()->featured()->count(),
                'urgent_count' => MapItem::active()->urgent()->count(),
                'cities' => MapItem::active()
                    ->selectRaw('city, COUNT(*) as count')
                    ->whereNotNull('city')
                    ->groupBy('city')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->pluck('count', 'city')
                    ->toArray(),
            ];
        });
    }

    public function categories(): array
    {
        return cache()->remember('map_categories', 7200, function () {
            return MapItem::active()
                ->selectRaw('content_type, category_name, category_icon, category_color, COUNT(*) as count')
                ->groupBy('content_type', 'category_name', 'category_icon', 'category_color')
                ->orderBy('content_type')
                ->orderByDesc('count')
                ->get()
                ->groupBy('content_type')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'name' => $item->category_name,
                            'icon' => $item->category_icon,
                            'color' => $item->category_color,
                            'count' => $item->count,
                        ];
                    });
                })
                ->toArray();
        });
    }

    private function mapServiceTypeToContentType(string $serviceType): ?string
    {
        return match ($serviceType) {
            'pet_sitter' => 'pet_sitter', // 🔧 FIX: Don't map pet_sitter to service!
            'vet' => 'service',
            'supplies' => 'supplies',
            'event' => 'event',
            'adoption' => 'adoption',
            default => null,
        };
    }
}
