<?php

use App\Models\ServiceCategory;
use App\Models\PetType;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use function Livewire\Volt\{computed};

new #[Layout('layouts.app')] class extends Component {

    /**
     * Breadcrumbs dla strony tworzenia usługi.
     *
     * @return array
     */
    public function getBreadcrumbsProperty(): array
    {
        return [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('profile.dashboard')
            ],
            [
                'title' => 'Pet Sitter',
                'icon' => '🐕',
                'url' => route('profile.dashboard')
            ],
            [
                'title' => 'Usługi',
                'icon' => '🐾',
                'url' => route('profile.services.index')
            ],
            [
                'title' => 'Dodaj usługę',
                'icon' => '➕'
            ]
        ];
    }
    #[Validate('required|string|min:5|max:255')]
    public string $title = '';

    #[Validate('required|string|min:20|max:1000')]
    public string $description = '';

    #[Validate('required|exists:service_categories,id')]
    public string $category_id = '';

    #[Validate('nullable|numeric|min:10|max:500')]
    public ?float $price_per_hour = null;

    #[Validate('nullable|numeric|min:50|max:2000')]
    public ?float $price_per_day = null;

    #[Validate('required|array|min:1')]
    public array $pet_types = [];

    #[Validate('required|array|min:1')]
    public array $pet_sizes = [];

    #[Validate('required|integer|min:1|max:10')]
    public int $max_pets = 1;

    #[Validate('boolean')]
    public bool $home_service = false;

    #[Validate('boolean')]
    public bool $sitter_home = false;

    // Address fields for creating MapItem
    #[Validate('required|string|max:255')]
    public string $address = '';

    #[Validate('required|string|max:100')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $voivodeship = '';

    #[Validate('required|numeric|between:-90,90')]
    public ?float $latitude = null;

    #[Validate('required|numeric|between:-180,180')]
    public ?float $longitude = null;

    public function mount()
    {
        $this->pet_types = [];
        $this->pet_sizes = [];
    }

    public function serviceCategories()
    {
        return ServiceCategory::active()->ordered()->get();
    }

    public function petTypes()
    {
        return PetType::active()->ordered()->get();
    }

    public function petSizeOptions()
    {
        return [
            'small' => 'Małe (do 10kg)',
            'medium' => 'Średnie (10-25kg)',
            'large' => 'Duże (powyżej 25kg)'
        ];
    }

    public function save()
    {
        $this->validate();

        // Check if user has at least one service type
        if (!$this->home_service && !$this->sitter_home) {
            $this->addError('service_type', 'Musisz wybrać przynajmniej jeden typ usługi.');
            return;
        }

        // Check if user has at least one price
        if (!$this->price_per_hour && !$this->price_per_day) {
            $this->addError('price', 'Musisz podać cenę za godzinę lub dzień.');
            return;
        }

        try {
            \DB::transaction(function () {
                // Create the service
                $service = \App\Models\Service::create([
                    'sitter_id' => auth()->id(),
                    'category_id' => $this->category_id,
                    'title' => $this->title,
                    'description' => $this->description,
                    'price_per_hour' => $this->price_per_hour,
                    'price_per_day' => $this->price_per_day,
                    'pet_types' => $this->pet_types,
                    'pet_sizes' => $this->pet_sizes,
                    'home_service' => $this->home_service,
                    'sitter_home' => $this->sitter_home,
                    'max_pets' => $this->max_pets,
                    'is_active' => true,
                ]);

                // Create corresponding MapItem for searchability
                $category = ServiceCategory::find($this->category_id);

                \App\Models\MapItem::create([
                    'user_id' => auth()->id(),
                    'mappable_type' => 'Service',
                    'mappable_id' => $service->id,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'city' => $this->city,
                    'voivodeship' => $this->voivodeship,
                    'full_address' => $this->address,
                    'title' => $this->title,
                    'description_short' => substr($this->description, 0, 200),
                    'content_type' => 'pet_sitter',
                    'category_name' => $category->name ?? 'Pet Sitting',
                    'category_icon' => $category->icon ?? '🐾',
                    'category_color' => $category->color ?? '#3B82F6',
                    'price_from' => $this->price_per_hour ?? $this->price_per_day,
                    'currency' => 'PLN',
                    'status' => 'published',
                    'is_featured' => false,
                    'is_urgent' => false,
                    'rating_avg' => 0.00,
                    'rating_count' => 0,
                ]);
            });

            session()->flash('success', 'Usługa została pomyślnie dodana!');
            $this->redirect(route('profile.services.index'));

        } catch (\Exception $e) {
            \Log::error('Error creating service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas tworzenia usługi. Spróbuj ponownie.');
        }
    }


    public function voivodeshipOptions()
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
}; ?>

@php
    // Przekaż breadcrumbs do layoutu
    $breadcrumbs = $this->breadcrumbs;
@endphp

<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dodaj nową usługę pet sittingu</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Stwórz ofertę swojej usługi opieki nad zwierzętami</p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                📝 Podstawowe informacje
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tytuł usługi *
                    </label>
                    <input
                        wire:model="title"
                        type="text"
                        id="title"
                        placeholder="np. Spacery z psami w centrum miasta"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kategoria usługi *
                    </label>
                    <select
                        wire:model="category_id"
                        id="category_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Wybierz kategorię</option>
                        @foreach($this->serviceCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Max Pets -->
                <div>
                    <label for="max_pets" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Maksymalna liczba zwierząt *
                    </label>
                    <select
                        wire:model="max_pets"
                        id="max_pets"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'zwierzę' : ($i <= 4 ? 'zwierzęta' : 'zwierząt') }}</option>
                        @endfor
                    </select>
                    @error('max_pets') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opis usługi *
                    </label>
                    <textarea
                        wire:model="description"
                        id="description"
                        rows="4"
                        placeholder="Opisz swoją usługę, doświadczenie, dostępność..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Pet Types & Sizes Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                🐾 Rodzaje i rozmiary zwierząt
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pet Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Rodzaje zwierząt *
                    </label>
                    <div class="space-y-2">
                        @foreach($this->petTypes as $petType)
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="pet_types"
                                    value="{{ $petType->slug }}"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $petType->icon }} {{ $petType->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('pet_types') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pet Sizes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Rozmiary zwierząt *
                    </label>
                    <div class="space-y-2">
                        @foreach($this->petSizeOptions as $value => $label)
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="pet_sizes"
                                    value="{{ $value }}"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('pet_sizes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Service Types Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                🏠 Typ usługi
            </h2>

            <div class="space-y-4">
                <label class="flex items-start">
                    <input
                        type="checkbox"
                        wire:model="home_service"
                        class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🏡 U klienta</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Świadczysz usługę w domu właściciela zwierzęcia</p>
                    </div>
                </label>

                <label class="flex items-start">
                    <input
                        type="checkbox"
                        wire:model="sitter_home"
                        class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🏠 U opiekuna</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Zwierzę przebywa u Ciebie w domu</p>
                    </div>
                </label>
                @error('service_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                💰 Cennik
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price per hour -->
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za godzinę (PLN)
                    </label>
                    <input
                        wire:model="price_per_hour"
                        type="number"
                        id="price_per_hour"
                        min="10"
                        max="500"
                        step="5"
                        placeholder="np. 25"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('price_per_hour') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Price per day -->
                <div>
                    <label for="price_per_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za dzień (PLN)
                    </label>
                    <input
                        wire:model="price_per_day"
                        type="number"
                        id="price_per_day"
                        min="50"
                        max="2000"
                        step="10"
                        placeholder="np. 150"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('price_per_day') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @error('price') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Podaj przynajmniej jedną cenę (za godzinę lub dzień)</p>
        </div>

        <!-- Location Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                📍 Lokalizacja
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Adres *
                    </label>
                    <input
                        wire:model="address"
                        type="text"
                        id="address"
                        placeholder="ul. Przykładowa 123"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Miasto *
                    </label>
                    <input
                        wire:model="city"
                        type="text"
                        id="city"
                        placeholder="Warszawa"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Voivodeship -->
                <div>
                    <label for="voivodeship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Województwo *
                    </label>
                    <select
                        wire:model="voivodeship"
                        id="voivodeship"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Wybierz województwo</option>
                        @foreach($this->voivodeshipOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('voivodeship') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Coordinates -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Szerokość geograficzna *
                    </label>
                    <input
                        wire:model="latitude"
                        type="number"
                        id="latitude"
                        step="any"
                        placeholder="52.2297"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('latitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Długość geograficzna *
                    </label>
                    <input
                        wire:model="longitude"
                        type="number"
                        id="longitude"
                        step="any"
                        placeholder="21.0122"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('longitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                💡 Tip: Możesz znaleźć współrzędne swojego adresu na Google Maps
            </p>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-6">
            <a
                href="{{ route('profile.services.index') }}"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
            >
                Anuluj
            </a>

            <button
                type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Dodaj usługę</span>
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Dodawanie...
                </span>
            </button>
        </div>
    </form>
</div>