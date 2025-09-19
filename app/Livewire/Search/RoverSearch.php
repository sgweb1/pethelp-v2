<?php

namespace App\Livewire\Search;

use App\Models\MapItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Debounce;
use Livewire\Component;

class RoverSearch extends Component
{
    // Search state - Rover.com style
    public string $location = '';
    public string $serviceType = 'pet_sitter'; // Default to pet sitters (main business)
    public string $petType = 'dog'; // dog, cat, other
    public int $petCount = 1;
    public string $frequency = 'onetime'; // onetime, recurring
    public string $startDate = '';
    public string $endDate = '';
    public int $radius = 25; // km - Rover uses larger default radius
    public string $sortBy = 'rating';

    // UI state
    public bool $showSuggestions = false;
    public bool $showAdvanced = false;
    public bool $isLoading = false;
    public bool $useCurrentLocation = false;
    public ?float $userLat = null;
    public ?float $userLng = null;

    // Performance optimizations
    private const CACHE_TTL = 300; // 5 minutes
    private const SUGGESTION_LIMIT = 8;
    private const MIN_SEARCH_LENGTH = 2;

    public function mount(): void
    {
        $this->loadPopularLocations();
    }

    #[Computed]
    public function serviceTypes(): array
    {
        return [
            'pet_sitter' => [
                'name' => 'Opieka nad pupilami',
                'icon' => '🏠',
                'services' => [
                    'boarding' => 'Opieka w domu pet sittera',
                    'house_sitting' => 'Opieka w domu właściciela',
                    'dog_walking' => 'Spacery z psem',
                    'drop_in_visits' => 'Wizyty w ciągu dnia',
                ]
            ],
            'service' => [
                'name' => 'Usługi profesjonalne',
                'icon' => '🏥',
                'services' => [
                    'veterinary' => 'Opieka weterynaryjna',
                    'grooming' => 'Grooming i fryzjer',
                    'training' => 'Szkolenia i treningi',
                    'daycare' => 'Żłobek dla psów',
                ]
            ],
            'event_public' => [
                'name' => 'Wydarzenia społeczne',
                'icon' => '🗓️',
                'services' => [
                    'meetups' => 'Spotkania właścicieli',
                    'training_groups' => 'Treningi grupowe',
                    'dog_shows' => 'Wystawy psów',
                ]
            ]
        ];
    }

    #[Computed]
    public function petTypes(): array
    {
        return [
            'dog' => ['name' => 'Psy', 'icon' => '🐕'],
            'cat' => ['name' => 'Koty', 'icon' => '🐱'],
            'other' => ['name' => 'Inne', 'icon' => '🐾']
        ];
    }

    #[Computed]
    public function locationSuggestions(): Collection
    {
        if (strlen($this->location) < self::MIN_SEARCH_LENGTH) {
            return collect();
        }

        $cacheKey = 'location_suggestions_' . md5($this->location);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return MapItem::select('city', 'full_address as address')
                ->where('status', 'published')
                ->where(function ($query) {
                    $query->where('city', 'like', "%{$this->location}%")
                          ->orWhere('full_address', 'like', "%{$this->location}%");
                })
                ->distinct()
                ->limit(self::SUGGESTION_LIMIT)
                ->get()
                ->map(function ($item) {
                    return [
                        'display' => $item->city . ($item->address ? ", {$item->address}" : ''),
                        'city' => $item->city,
                        'address' => $item->address,
                    ];
                });
        });
    }

    #[Debounce(300)]
    public function updatedLocation(): void
    {
        $this->showSuggestions = strlen($this->location) >= self::MIN_SEARCH_LENGTH;

        if ($this->showSuggestions) {
            $this->isLoading = true;
            $this->locationSuggestions;
            $this->isLoading = false;
        }
    }

    public function selectSuggestion(string $city, ?string $address = null): void
    {
        $this->location = $city . ($address ? ", {$address}" : '');
        $this->showSuggestions = false;
        $this->dispatch('location-selected', [
            'city' => $city,
            'address' => $address
        ]);
    }

    public function getCurrentLocation(): void
    {
        $this->useCurrentLocation = true;
        $this->dispatch('get-current-location');
    }

    public function setCurrentLocation(float $lat, float $lng, string $address): void
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->location = $address;
        $this->useCurrentLocation = false;
        $this->showSuggestions = false;
    }

    public function search()
    {
        $this->validate([
            'location' => 'required|string|min:2|max:255',
            'radius' => 'integer|min:1|max:100',
            'serviceType' => 'required|string',
            'petType' => 'required|string',
            'petCount' => 'integer|min:1|max:20',
        ]);

        // Build optimized search parameters - Rover style
        $searchParams = [
            'location' => $this->location,
            'service_type' => $this->serviceType,
            'pet_type' => $this->petType,
            'pet_count' => $this->petCount,
            'frequency' => $this->frequency,
            'radius' => $this->radius,
            'sort' => $this->sortBy,
        ];

        // Add dates if provided
        if ($this->startDate) {
            $searchParams['start_date'] = $this->startDate;
        }
        if ($this->endDate) {
            $searchParams['end_date'] = $this->endDate;
        }

        // Add coordinates if available
        if ($this->userLat && $this->userLng) {
            $searchParams['lat'] = $this->userLat;
            $searchParams['lng'] = $this->userLng;
        }

        // Redirect to search results with optimized query
        return redirect()->route('search', $searchParams);
    }

    public function resetSearch(): void
    {
        $this->reset(['location', 'serviceType', 'petType', 'petCount', 'frequency', 'startDate', 'endDate', 'radius', 'userLat', 'userLng']);
        $this->showSuggestions = false;
        $this->serviceType = 'pet_sitter'; // Reset to default core business
        $this->petType = 'dog';
        $this->petCount = 1;
    }

    public function toggle(string $property): void
    {
        if (property_exists($this, $property)) {
            $this->$property = !$this->$property;
        }
    }

    private function loadPopularLocations(): void
    {
        Cache::remember('popular_locations', 3600, function () {
            return MapItem::select('city')
                ->selectRaw('COUNT(*) as count')
                ->where('status', 'published')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('city');
        });
    }

    public function render()
    {
        return view('livewire.search.rover-search');
    }
}
