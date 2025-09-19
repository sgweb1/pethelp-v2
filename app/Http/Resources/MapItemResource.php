<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coordinates' => [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ],
            'location' => [
                'city' => $this->city,
                'voivodeship' => $this->voivodeship,
                'full_address' => $this->when(
                    $this->shouldShowFullAddress(),
                    $this->full_address
                ),
            ],
            'content' => [
                'title' => $this->title,
                'description_short' => $this->description_short,
                'content_type' => $this->content_type,
                'primary_image_url' => $this->primary_image_url,
            ],
            'category' => [
                'name' => $this->category_name,
                'icon' => $this->category_icon,
                'color' => $this->category_color,
            ],
            'pricing' => $this->when($this->price_from, [
                'price_from' => (float) $this->price_from,
                'price_to' => $this->when($this->price_to, (float) $this->price_to),
                'currency' => $this->currency,
                'negotiable' => (bool) $this->price_negotiable,
            ]),
            'status' => [
                'status' => $this->status,
                'is_featured' => (bool) $this->is_featured,
                'is_urgent' => (bool) $this->is_urgent,
                'expires_at' => $this->expires_at?->toISOString(),
            ],
            'timing' => $this->when($this->starts_at || $this->ends_at, [
                'starts_at' => $this->starts_at?->toISOString(),
                'ends_at' => $this->ends_at?->toISOString(),
            ]),
            'engagement' => [
                'view_count' => (int) $this->view_count,
                'interaction_count' => (int) $this->interaction_count,
                'rating' => $this->when($this->rating_avg > 0, [
                    'average' => (float) $this->rating_avg,
                    'count' => (int) $this->rating_count,
                ]),
            ],
            'display' => [
                'zoom_level_min' => (int) $this->zoom_level_min,
            ],
            'owner' => [
                'id' => $this->user_id,
            ],
            'links' => [
                'view' => $this->getViewLink(),
                'contact' => $this->getContactLink(),
            ],
            'metadata' => [
                'mappable_type' => $this->mappable_type,
                'mappable_id' => $this->mappable_id,
                'last_synced' => $this->updated_at?->toISOString(),
            ],
        ];
    }

    private function shouldShowFullAddress(): bool
    {
        // Don't show full address for certain content types (privacy)
        if (in_array($this->content_type, ['lost_pet', 'found_pet'])) {
            return false;
        }

        // Don't show for private events
        if ($this->content_type === 'event' && $this->mappable) {
            return ! ($this->mappable->is_private ?? false);
        }

        return true;
    }

    private function getViewLink(): string
    {
        try {
            return match ($this->content_type) {
                'event' => route('events.show', $this->mappable_id),
                'adoption', 'sale', 'lost_pet', 'found_pet', 'supplies' => '#', // TODO: Implement advertisements.show route
                'service' => '#', // TODO: Implement services.show route
                default => '#'
            };
        } catch (\Exception $e) {
            return '#';
        }
    }

    private function getContactLink(): ?string
    {
        if (in_array($this->content_type, ['event', 'service'])) {
            try {
                return route('contact.show', [
                    'type' => $this->content_type,
                    'id' => $this->mappable_id,
                ]);
            } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                return null; // TODO: Implement contact.show route
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
