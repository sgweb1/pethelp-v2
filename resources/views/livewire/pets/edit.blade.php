<?php

use App\Models\Pet;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    /**
     * Breadcrumbs dla edycji pupila.
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
                'title' => 'Moje pupile',
                'icon' => '🐾',
                'url' => route('profile.pets.index')
            ],
            [
                'title' => $this->pet->name ?? 'Edytuj pupila',
                'icon' => '✏️'
            ]
        ];
    }

    public Pet $pet;
    public $name;
    public $type;
    public $breed;
    public $gender;
    public $birth_date;
    public $weight;
    public $description;
    public $is_active;

    // Medical info
    public $vaccinations = '';
    public $allergies = '';
    public $medications = '';
    public $vet_contact = '';
    public $health_conditions = '';

    // Behavior traits
    public $energy_level = 'medium';
    public $socialization = 'friendly';
    public $training_level = 'basic';
    public $special_needs = [];
    public $behavioral_notes = '';

    // Emergency contacts
    public $emergency_name = '';
    public $emergency_phone = '';
    public $emergency_relationship = '';

    public function mount(Pet $pet)
    {
        // Check if the pet belongs to the authenticated user
        if ($pet->user_id !== auth()->id()) {
            session()->flash('error', 'Nie masz uprawnień do edycji tego zwierzęcia.');
            return redirect()->route('profile.pets.index');
        }

        $this->pet = $pet;
        $this->name = $pet->name;
        $this->type = $pet->type;
        $this->breed = $pet->breed;
        $this->gender = $pet->gender;
        $this->birth_date = $pet->birth_date?->format('Y-m-d');
        $this->weight = $pet->weight;
        $this->description = $pet->description;
        $this->is_active = $pet->is_active;

        // Load medical info
        $medicalInfo = $pet->medical_info ?? [];
        $this->vaccinations = $medicalInfo['vaccinations'] ?? '';
        $this->allergies = $medicalInfo['allergies'] ?? '';
        $this->medications = $medicalInfo['medications'] ?? '';
        $this->vet_contact = $medicalInfo['vet_contact'] ?? '';
        $this->health_conditions = $medicalInfo['health_conditions'] ?? '';

        // Load behavior traits
        $behaviorTraits = $pet->behavior_traits ?? [];
        $this->energy_level = $behaviorTraits['energy_level'] ?? 'medium';
        $this->socialization = $behaviorTraits['socialization'] ?? 'friendly';
        $this->training_level = $behaviorTraits['training_level'] ?? 'basic';
        $this->special_needs = $behaviorTraits['special_needs'] ?? [];
        $this->behavioral_notes = $behaviorTraits['behavioral_notes'] ?? '';

        // Load emergency contacts
        $emergencyContacts = $pet->emergency_contacts ?? [];
        if (!empty($emergencyContacts)) {
            $this->emergency_name = $emergencyContacts[0]['name'] ?? '';
            $this->emergency_phone = $emergencyContacts[0]['phone'] ?? '';
            $this->emergency_relationship = $emergencyContacts[0]['relationship'] ?? '';
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:dog,cat,bird,rabbit,other',
            'gender' => 'required|in:male,female',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0.1|max:200',
            'description' => 'nullable|string|max:1000',
            'breed' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Imię zwierzęcia jest wymagane.',
            'name.max' => 'Imię zwierzęcia nie może przekraczać 255 znaków.',
            'type.required' => 'Typ zwierzęcia jest wymagany.',
            'type.in' => 'Nieprawidłowy typ zwierzęcia.',
            'gender.required' => 'Płeć jest wymagana.',
            'gender.in' => 'Nieprawidłowa płeć.',
            'birth_date.date' => 'Nieprawidłowa data urodzenia.',
            'birth_date.before_or_equal' => 'Data urodzenia nie może być w przyszłości.',
            'weight.numeric' => 'Waga musi być liczbą.',
            'weight.min' => 'Waga musi być większa niż 0.1 kg.',
            'weight.max' => 'Waga nie może przekraczać 200 kg.',
            'description.max' => 'Opis nie może przekraczać 1000 znaków.',
            'breed.max' => 'Rasa nie może przekraczać 255 znaków.',
            'emergency_phone.max' => 'Numer telefonu nie może przekraczać 20 znaków.',
        ]);

        // Prepare medical info
        $medical_info = [
            'vaccinations' => $this->vaccinations,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'vet_contact' => $this->vet_contact,
            'health_conditions' => $this->health_conditions,
        ];

        // Prepare behavior traits
        $behavior_traits = [
            'energy_level' => $this->energy_level,
            'socialization' => $this->socialization,
            'training_level' => $this->training_level,
            'special_needs' => $this->special_needs,
            'behavioral_notes' => $this->behavioral_notes,
        ];

        // Prepare emergency contacts
        $emergency_contacts = [];
        if ($this->emergency_name || $this->emergency_phone) {
            $emergency_contacts[] = [
                'name' => $this->emergency_name,
                'phone' => $this->emergency_phone,
                'relationship' => $this->emergency_relationship,
            ];
        }

        $this->pet->update([
            'name' => $this->name,
            'type' => $this->type,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'weight' => $this->weight,
            'description' => $this->description,
            'is_active' => $this->is_active ?? true,
            'medical_info' => $medical_info,
            'behavior_traits' => $behavior_traits,
            'emergency_contacts' => $emergency_contacts,
        ]);

        session()->flash('success', 'Profil zwierzęcia został zaktualizowany pomyślnie.');
        return redirect()->route('profile.pets.index');
    }
}; ?>

@php
    // Przekaż breadcrumbs do layoutu
    $breadcrumbs = $this->breadcrumbs;
@endphp

<div class="desktop-window">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Edytuj profil zwierzęcia
                    </h1>
                    <p class="text-white/80 mt-1">
                        Zaktualizuj informacje o {{ $pet->name }}
                    </p>
                </div>

                <a href="{{ route('profile.pets.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium text-white/70 hover:text-white transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Powrót do listy
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-large p-8">
            <form wire:submit="update" class="space-y-8">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Podstawowe informacje</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-900">
                                Imię zwierzęcia *
                            </label>
                            <input type="text" id="name" wire:model="name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. Burek, Mruczek"
                                required>
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="space-y-2">
                            <label for="type" class="block text-sm font-medium text-gray-900">
                                Typ zwierzęcia *
                            </label>
                            <select id="type" wire:model="type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                                <option value="">Wybierz typ zwierzęcia</option>
                                <option value="dog">Pies</option>
                                <option value="cat">Kot</option>
                                <option value="bird">Ptak</option>
                                <option value="rabbit">Królik</option>
                                <option value="other">Inne</option>
                            </select>
                            @error('type')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Breed -->
                        <div class="space-y-2">
                            <label for="breed" class="block text-sm font-medium text-gray-900">
                                Rasa
                            </label>
                            <input type="text" id="breed" wire:model="breed"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. Labrador, Perski, Kanaryczek">
                            @error('breed')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="space-y-2">
                            <label for="gender" class="block text-sm font-medium text-gray-900">
                                Płeć *
                            </label>
                            <select id="gender" wire:model="gender" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                                <option value="">Wybierz płeć</option>
                                <option value="male">Samiec</option>
                                <option value="female">Samica</option>
                            </select>
                            @error('gender')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="space-y-2">
                            <label for="birth_date" class="block text-sm font-medium text-gray-900">
                                Data urodzenia
                            </label>
                            <input type="date" id="birth_date" wire:model="birth_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                            @error('birth_date')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weight -->
                        <div class="space-y-2">
                            <label for="weight" class="block text-sm font-medium text-gray-900">
                                Waga (kg)
                            </label>
                            <input type="number" step="0.1" min="0.1" max="200" id="weight" wire:model="weight"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. 25.5">
                            @error('weight')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" wire:model="is_active"
                                class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Zwierzę jest aktywne (widoczne dla opiekunów)
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-900">
                            Opis zwierzęcia
                        </label>
                        <textarea id="description" wire:model="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                            placeholder="Opisz charakterystyczne cechy, ulubione zabawy, przyzwyczajenia..."></textarea>
                        @error('description')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Informacje medyczne</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vaccinations -->
                        <div class="space-y-2">
                            <label for="vaccinations" class="block text-sm font-medium text-gray-900">
                                Szczepienia
                            </label>
                            <textarea id="vaccinations" wire:model="vaccinations" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                                placeholder="Lista aktualnych szczepień, daty następnych..."></textarea>
                        </div>

                        <!-- Allergies -->
                        <div class="space-y-2">
                            <label for="allergies" class="block text-sm font-medium text-gray-900">
                                Alergie
                            </label>
                            <textarea id="allergies" wire:model="allergies" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                                placeholder="Znane alergie pokarmowe, środowiskowe..."></textarea>
                        </div>

                        <!-- Medications -->
                        <div class="space-y-2">
                            <label for="medications" class="block text-sm font-medium text-gray-900">
                                Leki
                            </label>
                            <textarea id="medications" wire:model="medications" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                                placeholder="Obecnie przyjmowane leki, dawkowanie..."></textarea>
                        </div>

                        <!-- Health Conditions -->
                        <div class="space-y-2">
                            <label for="health_conditions" class="block text-sm font-medium text-gray-900">
                                Schorzenia
                            </label>
                            <textarea id="health_conditions" wire:model="health_conditions" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                                placeholder="Przewlekłe choroby, ograniczenia zdrowotne..."></textarea>
                        </div>
                    </div>

                    <!-- Vet Contact -->
                    <div class="space-y-2">
                        <label for="vet_contact" class="block text-sm font-medium text-gray-900">
                            Kontakt do weterynarza
                        </label>
                        <input type="text" id="vet_contact" wire:model="vet_contact"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                            placeholder="Nazwa kliniki, adres, telefon">
                    </div>
                </div>

                <!-- Behavior & Training -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Zachowanie i potrzeby</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Energy Level -->
                        <div class="space-y-2">
                            <label for="energy_level" class="block text-sm font-medium text-gray-900">
                                Poziom energii
                            </label>
                            <select id="energy_level" wire:model="energy_level"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                                <option value="low">Niski</option>
                                <option value="medium">Średni</option>
                                <option value="high">Wysoki</option>
                            </select>
                        </div>

                        <!-- Socialization -->
                        <div class="space-y-2">
                            <label for="socialization" class="block text-sm font-medium text-gray-900">
                                Socjalizacja
                            </label>
                            <select id="socialization" wire:model="socialization"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                                <option value="shy">Nieśmiały</option>
                                <option value="friendly">Przyjazny</option>
                                <option value="very_social">Bardzo towarzyski</option>
                                <option value="aggressive">Agresywny</option>
                            </select>
                        </div>

                        <!-- Training Level -->
                        <div class="space-y-2">
                            <label for="training_level" class="block text-sm font-medium text-gray-900">
                                Poziom wyszkolenia
                            </label>
                            <select id="training_level" wire:model="training_level"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white">
                                <option value="none">Brak</option>
                                <option value="basic">Podstawowy</option>
                                <option value="intermediate">Średniozaawansowany</option>
                                <option value="advanced">Zaawansowany</option>
                            </select>
                        </div>
                    </div>

                    <!-- Special Needs -->
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-900">
                            Specjalne potrzeby (zaznacz wszystkie)
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach(['medication' => 'Podawanie leków', 'exercise' => 'Specjalne ćwiczenia', 'diet' => 'Specjalna dieta', 'elderly' => 'Opieka senioralna', 'medical' => 'Opieka medyczna', 'training' => 'Kontynuacja treningu'] as $key => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" id="need_{{ $key }}" value="{{ $key }}" wire:model="special_needs"
                                        class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2">
                                    <label for="need_{{ $key }}" class="ml-2 text-sm text-gray-900">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Behavioral Notes -->
                    <div class="space-y-2">
                        <label for="behavioral_notes" class="block text-sm font-medium text-gray-900">
                            Dodatkowe uwagi o zachowaniu
                        </label>
                        <textarea id="behavioral_notes" wire:model="behavioral_notes" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white resize-none"
                            placeholder="Opisz szczególne zachowania, lęki, preferencje, rutynę dnia..."></textarea>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.764 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Kontakt awaryjny</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Emergency Name -->
                        <div class="space-y-2">
                            <label for="emergency_name" class="block text-sm font-medium text-gray-900">
                                Imię i nazwisko
                            </label>
                            <input type="text" id="emergency_name" wire:model="emergency_name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. Jan Kowalski">
                        </div>

                        <!-- Emergency Phone -->
                        <div class="space-y-2">
                            <label for="emergency_phone" class="block text-sm font-medium text-gray-900">
                                Numer telefonu
                            </label>
                            <input type="tel" id="emergency_phone" wire:model="emergency_phone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. +48 123 456 789">
                            @error('emergency_phone')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Relationship -->
                        <div class="space-y-2">
                            <label for="emergency_relationship" class="block text-sm font-medium text-gray-900">
                                Stopień pokrewieństwa
                            </label>
                            <input type="text" id="emergency_relationship" wire:model="emergency_relationship"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 bg-white"
                                placeholder="Np. Rodzina, Przyjaciel">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('profile.pets.index') }}" wire:navigate
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        Anuluj
                    </a>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-8 py-3 text-sm font-medium text-white bg-primary-600 rounded-xl hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Zaktualizuj profil</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Aktualizowanie...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>