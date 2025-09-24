<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $editing ? 'Edytuj zwierzę' : 'Dodaj nowe zwierzę' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $editing ? 'Zaktualizuj informacje o swoim pupilu' : 'Dodaj swojego pupila do platformy' }}
                </p>
            </div>
            <button wire:click="cancel"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Powrót
            </button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Podstawowe informacje</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pet Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Imię zwierzęcia *
                    </label>
                    <input type="text"
                           wire:model="name"
                           id="name"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                           placeholder="np. Burek, Luna">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pet Type -->
                <div>
                    <label for="pet_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Typ zwierzęcia *
                    </label>
                    <select wire:model="pet_type_id"
                            id="pet_type_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Wybierz typ</option>
                        @foreach($this->petTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('pet_type_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Breed -->
                <div>
                    <label for="breed" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rasa
                    </label>
                    <input type="text"
                           wire:model="breed"
                           id="breed"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                           placeholder="np. Golden Retriever, Perz domowy">
                    @error('breed')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data urodzenia
                    </label>
                    <input type="date"
                           wire:model="birth_date"
                           id="birth_date"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Płeć
                    </label>
                    <select wire:model="gender"
                            id="gender"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Wybierz płeć</option>
                        <option value="male">Samiec</option>
                        <option value="female">Samica</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Weight -->
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Waga (kg)
                    </label>
                    <input type="number"
                           wire:model="weight"
                           id="weight"
                           step="0.1"
                           min="0"
                           max="200"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                           placeholder="np. 25.5">
                    @error('weight')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox"
                               wire:model="is_active"
                               id="is_active"
                               class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary-600">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Zwierzę jest aktywne
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Upload -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Zdjęcie</h2>

            <div class="flex items-center space-x-6">
                <!-- Current Photo -->
                <div class="shrink-0">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}"
                             alt="Podgląd"
                             class="h-20 w-20 object-cover rounded-full">
                    @elseif($editing && $pet->photo_url && $pet->photo_url !== asset('images/pet-placeholder.png'))
                        <img src="{{ $pet->photo_url }}"
                             alt="{{ $pet->name }}"
                             class="h-20 w-20 object-cover rounded-full">
                    @else
                        <div class="h-20 w-20 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">
                                {{ $name ? substr($name, 0, 1) : '🐾' }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Upload Input -->
                <div class="flex-1">
                    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Wybierz zdjęcie
                    </label>
                    <input type="file"
                           wire:model="photo"
                           id="photo"
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/20 dark:file:text-primary-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG do 2MB</p>
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Loading indicator for photo upload -->
            <div wire:loading wire:target="photo" class="mt-4">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Wczytywanie zdjęcia...
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Opis</h2>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Opis zwierzęcia
                </label>
                <textarea wire:model="description"
                          id="description"
                          rows="4"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                          placeholder="Opisz swojego pupila - charakter, ulubione zajęcia, specjalne potrzeby..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informacje medyczne</h2>
                <button type="button"
                        wire:click="addMedicalInfo"
                        class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj
                </button>
            </div>

            <div class="space-y-4">
                @forelse($medical_info as $index => $info)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <input type="text"
                                   wire:model="medical_info.{{ $index }}.type"
                                   placeholder="Typ (np. szczepienie)"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <input type="text"
                                   wire:model="medical_info.{{ $index }}.description"
                                   placeholder="Opis"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="date"
                                   wire:model="medical_info.{{ $index }}.date"
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <button type="button"
                                    wire:click="removeMedicalInfo({{ $index }})"
                                    class="text-red-600 hover:text-red-700 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-sm italic">Brak informacji medycznych. Kliknij "Dodaj" aby dodać pierwszą.</p>
                @endforelse
            </div>
        </div>

        <!-- Emergency Contacts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kontakty awaryjne</h2>
                <button type="button"
                        wire:click="addEmergencyContact"
                        class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj
                </button>
            </div>

            <div class="space-y-4">
                @forelse($emergency_contacts as $index => $contact)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <input type="text"
                                   wire:model="emergency_contacts.{{ $index }}.name"
                                   placeholder="Imię i nazwisko"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <input type="tel"
                                   wire:model="emergency_contacts.{{ $index }}.phone"
                                   placeholder="Telefon"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <input type="text"
                                   wire:model="emergency_contacts.{{ $index }}.relationship"
                                   placeholder="Relacja (np. weterynarz)"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="flex justify-end">
                            <button type="button"
                                    wire:click="removeEmergencyContact({{ $index }})"
                                    class="text-red-600 hover:text-red-700 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-sm italic">Brak kontaktów awaryjnych. Kliknij "Dodaj" aby dodać pierwszy kontakt.</p>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="button"
                    wire:click="cancel"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Anuluj
            </button>

            <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="save">
                <span wire:loading.remove wire:target="save">
                    {{ $editing ? 'Zaktualizuj' : 'Dodaj zwierzę' }}
                </span>
                <span wire:loading wire:target="save" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $editing ? 'Aktualizuję...' : 'Dodaję...' }}
                </span>
            </button>
        </div>
    </form>
</div>
