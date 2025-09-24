<?php

namespace App\Livewire\Search;

use Livewire\Component;

class RoverSearch extends Component
{
    public string $serviceType = 'pet_sitter';
    public string $location = '';
    public string $petType = '';
    public int $petCount = 1;
    public string $petSize = '';
    public string $priceRange = '';
    public array $specialServices = [];
    public float $minRating = 0;
    public bool $showAdvanced = false;

    public function getServiceTypesProperty(): array
    {
        return [
            'pet_sitter' => [
                'name' => 'Pet Sitting',
                'icon' => '🏠',
                'services' => [
                    'home_visit' => 'Opieka w domu',
                    'overnight' => 'Opieka nocna',
                    'dog_walking' => 'Spacery z psem',
                    'pet_taxi' => 'Transport zwierząt',
                ]
            ],
            'grooming' => [
                'name' => 'Pielęgnacja',
                'icon' => '✂️',
                'services' => [
                    'basic_grooming' => 'Podstawowa pielęgnacja',
                    'full_grooming' => 'Pełna pielęgnacja',
                    'nail_cutting' => 'Obcinanie pazurów',
                ]
            ],
            'vet_services' => [
                'name' => 'Usługi weterynaryjne',
                'icon' => '🩺',
                'services' => [
                    'consultation' => 'Konsultacja',
                    'vaccination' => 'Szczepienia',
                    'checkup' => 'Kontrola zdrowia',
                ]
            ]
        ];
    }

    public function getPetTypesProperty(): array
    {
        return [
            'dog' => ['name' => 'Psy', 'icon' => '🐕'],
            'cat' => ['name' => 'Koty', 'icon' => '🐱'],
            'bird' => ['name' => 'Ptaki', 'icon' => '🐦'],
            'fish' => ['name' => 'Ryby', 'icon' => '🐠'],
            'rabbit' => ['name' => 'Króliki', 'icon' => '🐰'],
            'other' => ['name' => 'Inne', 'icon' => '🐾']
        ];
    }

    public function getPetSizesProperty(): array
    {
        return [
            'small' => ['name' => 'Małe', 'icon' => '🐕‍🦺'],
            'medium' => ['name' => 'Średnie', 'icon' => '🐕'],
            'large' => ['name' => 'Duże', 'icon' => '🐕‍🦮'],
            'giant' => ['name' => 'Bardzo duże', 'icon' => '🦮']
        ];
    }

    public function getPriceRangesProperty(): array
    {
        return [
            'budget' => ['name' => 'Budżetowe', 'range' => '20-40 zł/godz'],
            'standard' => ['name' => 'Standardowe', 'range' => '40-60 zł/godz'],
            'premium' => ['name' => 'Premium', 'range' => '60-80 zł/godz'],
            'luxury' => ['name' => 'Luksusowe', 'range' => '80+ zł/godz']
        ];
    }

    public function getAvailableSpecialServicesProperty(): array
    {
        return [
            'emergency' => 'Opieka awaryjna',
            'medication' => 'Podawanie leków',
            'special_needs' => 'Zwierzęta o specjalnych potrzebach',
            'multiple_pets' => 'Wiele zwierząt',
            'pickup_delivery' => 'Odbiór i dowóz'
        ];
    }

    public function toggle(string $property): void
    {
        $this->$property = !$this->$property;
    }

    public function setCurrentLocation(float $lat, float $lng, string $address): void
    {
        $this->location = $address;
    }

    public function search()
    {
        // Redirect to search page with filters
        $params = [
            'service_type' => $this->serviceType,
            'location' => $this->location,
            'pet_type' => $this->petType,
            'pet_count' => $this->petCount,
        ];

        if ($this->petSize) {
            $params['pet_size'] = $this->petSize;
        }

        if ($this->priceRange) {
            $params['price_range'] = $this->priceRange;
        }

        if (!empty($this->specialServices)) {
            $params['special_services'] = implode(',', $this->specialServices);
        }

        if ($this->minRating > 0) {
            $params['min_rating'] = $this->minRating;
        }

        return redirect()->route('search', $params);
    }

    public function render()
    {
        return view('livewire.search.rover-search');
    }
}