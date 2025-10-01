<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'full_address',
        'street',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'public_location',
        'location_notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Get appropriate location based on privacy settings
    public function getDisplayLocationAttribute(): string
    {
        if ($this->event->is_invitation_only && ! $this->userCanSeeFullAddress()) {
            return $this->public_location ?? $this->city;
        }

        return $this->full_address;
    }

    private function userCanSeeFullAddress(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Organizer can always see full address
        if ($this->event->organizer_id === $user->id) {
            return true;
        }

        // Check if user has confirmed registration
        return $this->event->registrations()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->exists();
    }

    // Calculate distance to given coordinates
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371; // kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
