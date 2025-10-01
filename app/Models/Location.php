<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_primary' => 'boolean',
        ];
    }

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'city',
        'postal_code',
        'country',
        'facilities',
        'max_capacity',
        'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->street}, {$this->postal_code} {$this->city}";
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->city;
    }

    // Calculate distance to another location
    public function distanceTo(float $lat, float $lng): float
    {
        if (! $this->latitude || ! $this->longitude) {
            return 0;
        }

        $earthRadius = 6371; // kilometers

        $latDelta = deg2rad($lat - $this->latitude);
        $lngDelta = deg2rad($lng - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    // Geocoding helpers
    public static function geocodeAddress(string $address): ?array
    {
        // Simple implementation - in production use a proper geocoding service
        $address = urlencode($address);
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q={$address}";

        $response = @file_get_contents($url);
        if (! $response) {
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data)) {
            return null;
        }

        return [
            'latitude' => (float) $data[0]['lat'],
            'longitude' => (float) $data[0]['lon'],
        ];
    }

    public function geocode(): bool
    {
        $coordinates = self::geocodeAddress($this->full_address);

        if (! $coordinates) {
            return false;
        }

        $this->latitude = $coordinates['latitude'];
        $this->longitude = $coordinates['longitude'];

        return $this->save();
    }
}
