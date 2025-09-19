<?php

namespace App\Livewire\Search;

use Livewire\Component;

class LocationDetector extends Component
{
    public ?float $latitude = null;

    public ?float $longitude = null;

    public bool $location_detected = false;

    public string $location_address = '';

    public bool $detecting = false;

    protected $listeners = ['detect-location' => 'startDetection'];

    public function startDetection(): void
    {
        $this->detecting = true;
        $this->dispatch('start-geolocation');
    }

    public function setLocation(float $lat, float $lng, string $address = ''): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->location_address = $address;
        $this->location_detected = true;
        $this->detecting = false;

        // Notify parent components
        $this->dispatch('location-set', [
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
        ]);
    }

    public function clearLocation(): void
    {
        $this->reset(['latitude', 'longitude', 'location_address', 'location_detected']);
        $this->dispatch('location-cleared');
    }

    public function failedDetection(string $error = ''): void
    {
        $this->detecting = false;
        $this->dispatch('location-error', $error);
    }

    public function render()
    {
        return view('livewire.search.location-detector');
    }
}
