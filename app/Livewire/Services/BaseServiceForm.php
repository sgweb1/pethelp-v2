<?php

namespace App\Livewire\Services;

use App\Models\ServiceCategory;
use App\Models\PetType;
use App\Models\Service;
use App\Models\MapItem;
use Livewire\Component;

abstract class BaseServiceForm extends Component
{
    // Base properties that all services need
    public string $title = '';
    public string $description = '';
    public int $category_id;
    public array $pet_types = [];
    public array $pet_sizes = [];
    public int $max_pets = 1;
    public bool $home_service = false;
    public bool $sitter_home = false;

    // Advanced service properties
    public int $service_radius = 5;
    public bool $allow_mixed_pet_types = false;
    public ?int $minimum_duration = null;
    public ?int $maximum_duration = null;
    public string $price_structure = 'per_hour';
    public bool $requires_consultation = false;
    public bool $emergency_contact = false;
    public ?int $experience_years = null;
    public bool $insurance_coverage = false;
    public bool $vaccination_requirements = true;

    // Advanced pricing properties
    public ?float $price_per_visit = null;
    public ?float $price_per_week = null;
    public ?float $price_per_month = null;
    public ?float $weekend_surcharge_percent = null;
    public ?float $holiday_surcharge_percent = null;
    public ?float $early_morning_surcharge_percent = null;
    public ?float $late_evening_surcharge_percent = null;
    public ?float $bulk_discount_threshold = null;
    public ?float $bulk_discount_percent = null;
    public ?int $long_term_discount_days = null;
    public ?float $long_term_discount_percent = null;
    public array $additional_services_pricing = [];
    public bool $free_consultation = false;
    public bool $free_trial_visit = false;
    public string $payment_method = 'both';
    public int $cancellation_hours = 24;
    public float $cancellation_fee_percent = 0;


    // Address fields for creating MapItem
    public string $address = '';
    public string $city = '';
    public string $voivodeship = '';
    public ?float $latitude = null;
    public ?float $longitude = null;

    // For edit mode
    public ?Service $service = null;
    public bool $isEditMode = false;

    public function mount($categoryId = null, $service = null)
    {
        if ($service && $service instanceof Service) {
            // Edit mode
            $this->isEditMode = true;
            $this->service = $service;

            // Check if user owns this service
            if ($service->sitter_id !== auth()->id()) {
                abort(403, 'Nie masz uprawnień do edycji tej usługi.');
            }

            $this->category_id = $service->category_id;
            $this->loadServiceData($service);
        } else {
            // Create mode
            $this->category_id = (int) $categoryId;
            $this->pet_types = [];
            $this->pet_sizes = [];


            // Set default values based on category
            $this->setDefaultValues();
        }
    }

    protected function loadServiceData(Service $service)
    {
        $this->title = $service->title;
        $this->description = $service->description;
        $this->pet_types = $service->pet_types ?? [];
        $this->pet_sizes = $service->pet_sizes ?? [];
        $this->max_pets = $service->max_pets;
        $this->home_service = $service->home_service;
        $this->sitter_home = $service->sitter_home;

        // Load advanced properties
        $this->service_radius = $service->service_radius ?? 5;
        $this->allow_mixed_pet_types = $service->allow_mixed_pet_types ?? false;
        $this->minimum_duration = $service->minimum_duration;
        $this->maximum_duration = $service->maximum_duration;
        $this->price_structure = $service->price_structure ?? 'per_hour';
        $this->requires_consultation = $service->requires_consultation ?? false;
        $this->emergency_contact = $service->emergency_contact ?? false;
        $this->experience_years = $service->experience_years;
        $this->insurance_coverage = $service->insurance_coverage ?? false;
        $this->vaccination_requirements = $service->vaccination_requirements ?? true;

        // Load advanced pricing properties
        $this->price_per_visit = $service->price_per_visit;
        $this->price_per_week = $service->price_per_week;
        $this->price_per_month = $service->price_per_month;
        $this->weekend_surcharge_percent = $service->weekend_surcharge_percent;
        $this->holiday_surcharge_percent = $service->holiday_surcharge_percent;
        $this->early_morning_surcharge_percent = $service->early_morning_surcharge_percent;
        $this->late_evening_surcharge_percent = $service->late_evening_surcharge_percent;
        $this->bulk_discount_threshold = $service->bulk_discount_threshold;
        $this->bulk_discount_percent = $service->bulk_discount_percent;
        $this->long_term_discount_days = $service->long_term_discount_days;
        $this->long_term_discount_percent = $service->long_term_discount_percent;
        $this->additional_services_pricing = $service->additional_services_pricing ?? [];
        $this->free_consultation = $service->free_consultation ?? false;
        $this->free_trial_visit = $service->free_trial_visit ?? false;
        $this->payment_method = $service->payment_method ?? 'both';
        $this->cancellation_hours = $service->cancellation_hours ?? 24;
        $this->cancellation_fee_percent = $service->cancellation_fee_percent ?? 0;

        // Load address from MapItem if exists
        $mapItem = MapItem::where('mappable_type', Service::class)
            ->where('mappable_id', $service->id)
            ->first();

        if ($mapItem) {
            // Format address data for the address-search component
            $addressData = [
                'address' => $mapItem->full_address,
                'city' => $mapItem->city,
                'voivodeship' => $mapItem->voivodeship,
                'latitude' => $mapItem->latitude,
                'longitude' => $mapItem->longitude,
            ];
            $this->address = json_encode($addressData);
            $this->city = $mapItem->city;
            $this->voivodeship = $mapItem->voivodeship;
            $this->latitude = $mapItem->latitude;
            $this->longitude = $mapItem->longitude;
        }

        // Load category-specific data
        $this->loadCategorySpecificData($service);
    }

    // Abstract method for loading category-specific data in edit mode
    abstract protected function loadCategorySpecificData(Service $service): void;

    public function fillWithFakeData()
    {
        $this->title = 'Profesjonalna opieka nad zwierzętami - Warszawa Mokotów';
        $this->description = 'Oferuję profesjonalną opiekę nad Waszymi pupilami w komfortowych warunkach domowych. Mam wieloletnie doświadczenie w opiece nad różnymi gatunkami zwierząt. Zapewniam pełną opiekę, regularne spacery, karmienie według harmonogramu oraz dużo miłości i uwagi. Posiadam własny dom z ogródkiem, gdzie zwierzęta mogą się swobodnie poruszać i bawić.';
        $this->pet_types = ['dog', 'cat'];
        $this->pet_sizes = ['small', 'medium'];
        $this->max_pets = 2;
        $this->home_service = true;

        // Format address data for the address-search component
        $addressData = [
            'address' => 'ul. Puławska 123, Warszawa',
            'city' => 'Warszawa',
            'voivodeship' => 'mazowieckie',
            'latitude' => 52.229676,
            'longitude' => 21.012229,
        ];
        $this->address = json_encode($addressData);
        $this->city = 'Warszawa';
        $this->voivodeship = 'mazowieckie';
        $this->latitude = 52.229676;
        $this->longitude = 21.012229;

        session()->flash('info', 'Formularz został wypełniony przykładowymi danymi.');
    }

    // Abstract methods for category-specific behavior
    abstract protected function setDefaultValues(): void;
    abstract protected function getCategorySpecificValidationRules(): array;
    abstract protected function getCategorySpecificData(): array;

    public function category()
    {
        return ServiceCategory::find($this->category_id);
    }

    public function getPetTypesProperty()
    {
        return PetType::active()->ordered()->get();
    }

    public function getPetSizeOptionsProperty()
    {
        return [
            'small' => 'Małe (do 10kg)',
            'medium' => 'Średnie (10-25kg)',
            'large' => 'Duże (powyżej 25kg)'
        ];
    }

    public function getVoivodeshipOptionsProperty()
    {
        return [
            'dolnośląskie' => 'Dolnośląskie',
            'kujawsko-pomorskie' => 'Kujawsko-pomorskie',
            'lubelskie' => 'Lubelskie',
            'lubuskie' => 'Lubuskie',
            'łódzkie' => 'Łódzkie',
            'małopolskie' => 'Małopolskie',
            'mazowieckie' => 'Mazowieckie',
            'opolskie' => 'Opolskie',
            'podkarpackie' => 'Podkarpackie',
            'podlaskie' => 'Podlaskie',
            'pomorskie' => 'Pomorskie',
            'śląskie' => 'Śląskie',
            'świętokrzyskie' => 'Świętokrzyskie',
            'warmińsko-mazurskie' => 'Warmińsko-mazurskie',
            'wielkopolskie' => 'Wielkopolskie',
            'zachodniopomorskie' => 'Zachodniopomorskie',
        ];
    }

    protected function validateAndSave()
    {
        // Check if user already has a service in this category (only for create mode)
        if (!$this->isEditMode) {
            $existingService = Service::where('sitter_id', auth()->id())
                ->where('category_id', $this->category_id)
                ->first();

            if ($existingService) {
                $category = ServiceCategory::find($this->category_id);
                $this->addError('category_id', 'Możesz mieć tylko jedną usługę w kategorii "' . $category->name . '". Usuń najpierw istniejącą usługę.');
                return false;
            }
        }

        // Parse address data from the address-search component
        $addressData = json_decode($this->address, true) ?? [];

        // Set city and voivodeship from parsed address data
        $this->city = $addressData['city'] ?? '';
        $this->voivodeship = $addressData['voivodeship'] ?? '';
        $this->latitude = $addressData['latitude'] ?? null;
        $this->longitude = $addressData['longitude'] ?? null;

        // Merge base validation with category-specific rules
        $rules = array_merge([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:1000',
            'category_id' => 'required|exists:service_categories,id',
            'pet_types' => 'required|array|min:1',
            'pet_sizes' => 'required|array|min:1',
            'max_pets' => 'required|integer|min:1|max:10',
            'home_service' => 'boolean',
            'sitter_home' => 'boolean',
            'address' => 'required|string|max:1000',
        ], $this->getCategorySpecificValidationRules());

        $this->validate($rules);

        // Check if user has at least one service type
        if (!$this->home_service && !$this->sitter_home) {
            $this->addError('service_type', 'Musisz wybrać przynajmniej jeden typ usługi.');
            return false;
        }

        return true;
    }

    public function save()
    {
        if (!$this->validateAndSave()) {
            return;
        }

        try {
            \DB::transaction(function () {
                // Merge base data with category-specific data
                $serviceData = array_merge([
                    'title' => $this->title,
                    'description' => $this->description,
                    'pet_types' => $this->pet_types,
                    'pet_sizes' => $this->pet_sizes,
                    'home_service' => $this->home_service,
                    'sitter_home' => $this->sitter_home,
                    'max_pets' => $this->max_pets,
                    'service_radius' => $this->service_radius,
                    'allow_mixed_pet_types' => $this->allow_mixed_pet_types,
                    'minimum_duration' => $this->minimum_duration,
                    'maximum_duration' => $this->maximum_duration,
                    'price_structure' => $this->price_structure,
                    'requires_consultation' => $this->requires_consultation,
                    'emergency_contact' => $this->emergency_contact,
                    'experience_years' => $this->experience_years,
                    'insurance_coverage' => $this->insurance_coverage,
                    'vaccination_requirements' => $this->vaccination_requirements,
                    'price_per_visit' => $this->price_per_visit,
                    'price_per_week' => $this->price_per_week,
                    'price_per_month' => $this->price_per_month,
                    'weekend_surcharge_percent' => $this->weekend_surcharge_percent,
                    'holiday_surcharge_percent' => $this->holiday_surcharge_percent,
                    'early_morning_surcharge_percent' => $this->early_morning_surcharge_percent,
                    'late_evening_surcharge_percent' => $this->late_evening_surcharge_percent,
                    'bulk_discount_threshold' => $this->bulk_discount_threshold,
                    'bulk_discount_percent' => $this->bulk_discount_percent,
                    'long_term_discount_days' => $this->long_term_discount_days,
                    'long_term_discount_percent' => $this->long_term_discount_percent,
                    'additional_services_pricing' => $this->additional_services_pricing,
                    'free_consultation' => $this->free_consultation,
                    'free_trial_visit' => $this->free_trial_visit,
                    'payment_method' => $this->payment_method,
                    'cancellation_hours' => $this->cancellation_hours,
                    'cancellation_fee_percent' => $this->cancellation_fee_percent,
                ], $this->getCategorySpecificData());

                if ($this->isEditMode) {
                    // Update existing service
                    $this->service->update($serviceData);
                    $service = $this->service;
                } else {
                    // Create new service
                    $serviceData = array_merge($serviceData, [
                        'sitter_id' => auth()->id(),
                        'category_id' => $this->category_id,
                        'is_active' => true,
                    ]);
                    $service = Service::create($serviceData);
                }

                // Update or create corresponding MapItem
                $category = ServiceCategory::find($this->category_id);

                // Get address string from JSON data or use as is
                $addressData = json_decode($this->address, true);
                $fullAddress = is_array($addressData) ? $addressData['address'] : $this->address;

                $mapItemData = [
                    'user_id' => auth()->id(),
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'city' => $this->city,
                    'voivodeship' => $this->voivodeship,
                    'full_address' => $fullAddress,
                    'title' => $this->title,
                    'description_short' => substr($this->description, 0, 200),
                    'content_type' => 'pet_sitter',
                    'category_name' => $category->name,
                    'category_icon' => $category->icon,
                    'price_from' => $this->getMinPrice(),
                    'status' => 'published',
                    'is_featured' => false,
                    'rating_avg' => 0,
                    'rating_count' => 0,
                ];

                if ($this->isEditMode) {
                    // Update existing MapItem
                    MapItem::where('mappable_type', Service::class)
                        ->where('mappable_id', $service->id)
                        ->update($mapItemData);
                } else {
                    // Create new MapItem
                    MapItem::create(array_merge($mapItemData, [
                        'mappable_type' => Service::class,
                        'mappable_id' => $service->id,
                        'published_at' => now(),
                    ]));
                }
            });

            $message = $this->isEditMode ? 'Usługa została pomyślnie zaktualizowana!' : 'Usługa została pomyślnie dodana!';
            session()->flash('success', $message);
            $this->redirect(route('services.index'));

        } catch (\Exception $e) {
            $action = $this->isEditMode ? 'aktualizacji' : 'tworzenia';
            \Log::error("Error {$action} service: " . $e->getMessage());
            session()->flash('error', "Wystąpił błąd podczas {$action} usługi. Spróbuj ponownie.");
        }
    }

    protected function getMinPrice(): ?float
    {
        // Override in child classes if needed
        return null;
    }
}