<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class LocationManager extends Component
{
    public $locations;
    public $showModal = false;
    public $editingLocation = null;

    // Form fields
    public $name = '';
    public $street = '';
    public $city = '';
    public $postal_code = '';
    public $country = 'Polska';
    public $latitude = null;
    public $longitude = null;
    public $is_primary = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'street' => 'required|string|max:255',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:10',
        'country' => 'required|string|max:100',
        'is_primary' => 'boolean',
    ];

    public function mount()
    {
        $this->loadLocations();
    }

    public function loadLocations()
    {
        $this->locations = Auth::user()->locations()->get();
    }

    public function addLocation()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editLocation($locationId)
    {
        $location = Location::where('user_id', Auth::id())->find($locationId);

        if ($location) {
            $this->editingLocation = $location->id;
            $this->name = $location->name ?? '';
            $this->street = $location->street;
            $this->city = $location->city;
            $this->postal_code = $location->postal_code;
            $this->country = $location->country;
            $this->latitude = $location->latitude;
            $this->longitude = $location->longitude;
            $this->is_primary = $location->is_primary;
            $this->showModal = true;
        }
    }

    public function saveLocation()
    {
        $this->validate();

        // If setting as primary, unset other primary locations
        if ($this->is_primary) {
            Location::where('user_id', Auth::id())
                   ->where('is_primary', true)
                   ->update(['is_primary' => false]);
        }

        $data = [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'street' => $this->street,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_primary' => $this->is_primary,
        ];

        if ($this->editingLocation) {
            $location = Location::find($this->editingLocation);
            $location->update($data);
        } else {
            $location = Location::create($data);
        }

        // Try to geocode if no coordinates
        if (!$this->latitude || !$this->longitude) {
            $this->geocodeLocation($location);
        }

        $this->closeModal();
        $this->loadLocations();
        $this->dispatch('location-saved');
    }

    public function deleteLocation($locationId)
    {
        $location = Location::where('user_id', Auth::id())->find($locationId);

        if ($location) {
            $location->delete();
            $this->loadLocations();
            $this->dispatch('location-deleted');
        }
    }

    public function setPrimary($locationId)
    {
        // Unset all primary locations first
        Location::where('user_id', Auth::id())
               ->update(['is_primary' => false]);

        // Set the selected location as primary
        Location::where('user_id', Auth::id())
               ->where('id', $locationId)
               ->update(['is_primary' => true]);

        $this->loadLocations();
        $this->dispatch('primary-location-updated');
    }

    public function geocodeLocation($location)
    {
        $address = $location->full_address;
        $coordinates = Location::geocodeAddress($address);

        if ($coordinates) {
            $location->update($coordinates);
            $this->dispatch('location-geocoded');
        }
    }

    public function detectCurrentLocation()
    {
        $this->dispatch('detect-current-location');
    }

    public function setDetectedLocation($lat, $lng, $address = '')
    {
        $this->latitude = $lat;
        $this->longitude = $lng;

        if ($address) {
            // Try to parse address components
            $parts = explode(',', $address);
            if (count($parts) >= 2) {
                $this->city = trim($parts[count($parts) - 2]);
                if (!$this->street && count($parts) >= 3) {
                    $this->street = trim($parts[0]);
                }
            }
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingLocation = null;
        $this->name = '';
        $this->street = '';
        $this->city = '';
        $this->postal_code = '';
        $this->country = 'Polska';
        $this->latitude = null;
        $this->longitude = null;
        $this->is_primary = false;
    }

    public function render()
    {
        return view('livewire.location-manager');
    }
}
