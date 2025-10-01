<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $isEditMode ? 'Edytuj usługę noclegową' : 'Nowa usługa noclegowa' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Wypełnij formularz aby {{ $isEditMode ? 'zaktualizować' : 'dodać' }} swoją ofertę opieki noclegowej w Twoim domu.
            </p>
        </div>

        <form wire:submit.prevent="save">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tytuł usługi <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           wire:model="title"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                           placeholder="np. Hotel dla psów i kotów - Warszawa Mokotów">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opis usługi <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description"
                              wire:model="description"
                              rows="4"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Opisz swoją usługę noclegową, doświadczenie, warunki zakwaterowania..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pet Types and Preferences -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Pet Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rodzaje zwierząt <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach($this->getPetTypesProperty() as $petType)
                            <label class="flex items-center">
                                <input type="checkbox"
                                       wire:model="pet_types"
                                       value="{{ $petType->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $petType->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('pet_types') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Pet Sizes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rozmiary zwierząt <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach($this->petSizeOptions as $size => $label)
                            <label class="flex items-center">
                                <input type="checkbox"
                                       wire:model="pet_sizes"
                                       value="{{ $size }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('pet_sizes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cennik</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price_per_night" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cena za noc (PLN) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="price_per_night"
                               wire:model="price_per_night"
                               step="0.01"
                               min="30"
                               max="500"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('price_per_night') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="weekend_price_per_night" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cena weekendowa (PLN)
                        </label>
                        <input type="number"
                               id="weekend_price_per_night"
                               wire:model="weekend_price_per_night"
                               step="0.01"
                               min="30"
                               max="500"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('weekend_price_per_night') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Opcjonalne - jeśli puste, używana jest cena podstawowa</p>
                    </div>

                    <div>
                        <label for="max_pets" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maksymalna liczba zwierząt <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="max_pets"
                               wire:model="max_pets"
                               min="1"
                               max="10"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('max_pets') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Stay Duration -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Długość pobytu</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_nights" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Minimum nocy <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="min_nights"
                               wire:model="min_nights"
                               min="1"
                               max="30"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('min_nights') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="max_nights" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maksimum nocy <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="max_nights"
                               wire:model="max_nights"
                               min="1"
                               max="365"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('max_nights') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Pet Preferences -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Preferencje zwierząt</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="checkbox"
                               wire:model="allows_multiple_owners"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Zwierzęta od różnych właścicieli</span>
                            <p class="text-xs text-gray-500">Czy przyjmujesz zwierzęta od różnych osób jednocześnie?</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="checkbox"
                               wire:model="allows_mixing_pet_types"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mieszanie typów zwierząt</span>
                            <p class="text-xs text-gray-500">Czy psy i koty mogą przebywać u Ciebie jednocześnie?</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Transport Service -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Transport</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="transport_enabled"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Oferuję transport zwierząt</span>
                    </label>

                    @if($transport_enabled)
                        <div class="ml-6">
                            <label for="transport_radius_km" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Zasięg transportu (km) <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="transport_radius_km"
                                   wire:model="transport_radius_km"
                                   min="1"
                                   max="50"
                                   class="w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @error('transport_radius_km') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Services Included -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Usługi w cenie</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="feeding_included"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Karmienie</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="walking_included"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Spacery</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="play_time"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Czas na zabawę</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="basic_grooming"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Podstawowa pielęgnacja</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="medication_admin"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Podawanie leków</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="daily_updates"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Codzienne zdjęcia/raporty</span>
                    </label>
                </div>
            </div>

            <!-- Special Notes -->
            <div class="mb-8">
                <label for="special_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dodatkowe informacje
                </label>
                <textarea id="special_notes"
                          wire:model="special_notes"
                          rows="3"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Dodatkowe informacje o warunkach, ograniczeniach, specjalizacji..."></textarea>
                @error('special_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>


            <!-- Location -->
            <div class="mb-8">
                <x-address-search
                    wire-model="address"
                    label="Adres lub obszar działalności"
                    placeholder="np. Warszawa, Mokotów lub konkretny adres"
                    :required="true"
                />
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                        wire:click="fillWithFakeData"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    Wypełnij testowymi danymi
                </button>

                <!-- Quick Login Links -->
                <div class="flex space-x-2 text-xs">
                    <a href="/quick-login" class="text-blue-600 hover:text-blue-800">Maria (Owner)</a>
                    <span class="text-gray-400">|</span>
                    <a href="/quick-login-sitter" class="text-blue-600 hover:text-blue-800">Anna (Sitter)</a>
                    <span class="text-gray-400">|</span>
                    <a href="/quick-login-owner" class="text-blue-600 hover:text-blue-800">Jan (Owner)</a>
                </div>

                <div class="flex space-x-4">
                    <a href="{{ route('profile.services.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Anuluj
                    </a>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove>{{ $isEditMode ? 'Zapisz zmiany' : 'Dodaj usługę' }}</span>
                        <span wire:loading>Zapisywanie...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>