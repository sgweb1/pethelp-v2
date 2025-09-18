<?php

use App\Models\Pet;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.app')] class extends Component {
    #[Validate('required|string|min:2|max:50')]
    public string $name = '';

    #[Validate('required|in:dog,cat,bird,rabbit,other')]
    public string $type = '';

    #[Validate('nullable|string|max:100')]
    public string $breed = '';

    #[Validate('nullable|date|before_or_equal:today')]
    public string $birth_date = '';

    #[Validate('required|in:male,female')]
    public string $gender = '';

    #[Validate('required|in:small,medium,large')]
    public string $size = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    public array $medical_info = [];
    public array $behavior_traits = [];
    public array $emergency_contacts = [];

    // Medical checkboxes
    public bool $vaccinated = false;
    public bool $sterilized = false;
    public bool $microchipped = false;
    public string $allergies = '';
    public string $medications = '';
    public string $vet_contact = '';

    // Behavior checkboxes
    public bool $friendly_with_dogs = false;
    public bool $friendly_with_cats = false;
    public bool $friendly_with_children = false;
    public bool $house_trained = false;
    public bool $needs_exercise = false;
    public string $special_needs = '';

    public function save()
    {
        $this->validate();

        // Prepare medical info
        $this->medical_info = [
            'vaccinated' => $this->vaccinated,
            'sterilized' => $this->sterilized,
            'microchipped' => $this->microchipped,
            'allergies' => $this->allergies ?: null,
            'medications' => $this->medications ?: null,
            'vet_contact' => $this->vet_contact ?: null,
        ];

        // Prepare behavior traits
        $this->behavior_traits = [
            'friendly_with_dogs' => $this->friendly_with_dogs,
            'friendly_with_cats' => $this->friendly_with_cats,
            'friendly_with_children' => $this->friendly_with_children,
            'house_trained' => $this->house_trained,
            'needs_exercise' => $this->needs_exercise,
            'special_needs' => $this->special_needs ?: null,
        ];

        Pet::create([
            'owner_id' => auth()->id(),
            'name' => $this->name,
            'type' => $this->type,
            'breed' => $this->breed ?: null,
            'size' => $this->size,
            'age' => $this->birth_date ? now()->diffInYears($this->birth_date) : null,
            'gender' => $this->gender,
            'description' => $this->description ?: null,
            'medical_info' => $this->medical_info,
            'behavior_traits' => $this->behavior_traits,
            'emergency_contacts' => $this->emergency_contacts,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Profil zwierzęcia został utworzony pomyślnie!');
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function cancel()
    {
        return $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="desktop-window">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Dodaj zwierzę
                    </h1>
                    <p class="text-white/80 mt-1">
                        Utwórz profil dla swojego pupila
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('pets.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-white/10 border border-white/20 rounded-lg hover:bg-white/20 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Lista zwierząt
                    </a>
                    <x-ui.button variant="outline" size="sm" wire:click="cancel" class="bg-white/10 border-white/20 text-white hover:bg-white/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Anuluj
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit="save" class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-8 space-y-8">
            <!-- Basic Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Podstawowe informacje
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <x-ui.input
                        wire:model="name"
                        label="Imię zwierzęcia"
                        icon="user"
                        required
                        error="{{ $errors->first('name') }}"
                        hint="Wpisz imię swojego pupila"
                    />

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Typ zwierzęcia <span class="text-danger-500">*</span>
                        </label>
                        <select wire:model="type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Wybierz typ</option>
                            <option value="dog">🐕 Pies</option>
                            <option value="cat">🐱 Kot</option>
                            <option value="bird">🐦 Ptak</option>
                            <option value="rabbit">🐰 Królik</option>
                            <option value="other">🐾 Inne</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Breed -->
                    <x-ui.input
                        wire:model="breed"
                        label="Rasa"
                        error="{{ $errors->first('breed') }}"
                        hint="Opcjonalnie - wpisz rasę"
                    />

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Płeć <span class="text-danger-500">*</span>
                        </label>
                        <select wire:model="gender" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Wybierz płeć</option>
                            <option value="male">Samiec</option>
                            <option value="female">Samica</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Rozmiar <span class="text-danger-500">*</span>
                        </label>
                        <select wire:model="size" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Wybierz rozmiar</option>
                            <option value="small">Mały</option>
                            <option value="medium">Średni</option>
                            <option value="large">Duży</option>
                        </select>
                        @error('size')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <x-ui.input
                        wire:model="birth_date"
                        label="Data urodzenia"
                        type="date"
                        error="{{ $errors->first('birth_date') }}"
                        hint="Opcjonalnie"
                    />

                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                    <textarea
                        wire:model="description"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Opisz swojego pupila - charakter, zwyczaje, preferencje..."
                    ></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Medical Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Informacje medyczne
                </h2>

                <!-- Medical Checkboxes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="vaccinated" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Zaszczepione</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="sterilized" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Wysterylizowane</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="microchipped" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Zaczipowane</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Allergies -->
                    <x-ui.input
                        wire:model="allergies"
                        label="Alergie"
                        error="{{ $errors->first('allergies') }}"
                        hint="Wpisz znane alergie"
                    />

                    <!-- Medications -->
                    <x-ui.input
                        wire:model="medications"
                        label="Leki"
                        error="{{ $errors->first('medications') }}"
                        hint="Obecnie przyjmowane leki"
                    />

                    <!-- Vet Contact -->
                    <div class="md:col-span-2">
                        <x-ui.input
                            wire:model="vet_contact"
                            label="Kontakt do weterynarza"
                            error="{{ $errors->first('vet_contact') }}"
                            hint="Numer telefonu lub nazwa kliniki"
                        />
                    </div>
                </div>
            </div>

            <!-- Behavior Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-nature-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Cechy behawioralne
                </h2>

                <!-- Behavior Checkboxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="friendly_with_dogs" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Lubi inne psy</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="friendly_with_cats" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Lubi koty</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="friendly_with_children" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Lubi dzieci</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="house_trained" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Wychowane domowo</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="needs_exercise" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-3 text-sm text-gray-700">Potrzebuje ruchu</span>
                    </label>
                </div>

                <!-- Special Needs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specjalne potrzeby</label>
                    <textarea
                        wire:model="special_needs"
                        rows="2"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Opisz specjalne potrzeby lub wymagania..."
                    ></textarea>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="is_active" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-3 text-sm text-gray-700">Profil aktywny (gotowy do rezerwacji)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-end gap-4 pt-6 border-t border-gray-200">
                <x-ui.button variant="outline" type="button" wire:click="cancel" class="sm:w-auto">
                    Anuluj
                </x-ui.button>

                <x-ui.button variant="primary" type="submit" class="sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Utwórz profil
                </x-ui.button>
            </div>
        </form>
    </div>
</div>