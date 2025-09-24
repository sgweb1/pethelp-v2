<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $isEditMode ? 'Edytuj usługę opieki w domu' : 'Nowa usługa opieki w domu' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Wypełnij formularz aby {{ $isEditMode ? 'zaktualizować' : 'dodać' }} swoją ofertę opieki nad zwierzętami w domu właściciela.
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
                           placeholder="np. Profesjonalna opieka nad psami i kotami">
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
                              placeholder="Opisz swoją usługę, doświadczenie i to co wyróżnia Cię jako opiekuna..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Pet Types and Sizes -->
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
                        @foreach($this->getPetSizeOptionsProperty() as $size => $label)
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

            <!-- Basic Pricing -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Podstawowe ceny
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cena za godzinę (PLN) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="price_per_hour"
                               wire:model="price_per_hour"
                               step="0.01"
                               min="0"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="np. 25.00">
                        @error('price_per_hour') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="price_per_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cena za dzień (PLN)
                        </label>
                        <input type="number"
                               id="price_per_day"
                               wire:model="price_per_day"
                               step="0.01"
                               min="0"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="np. 150.00">
                        @error('price_per_day') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Advanced Pricing Structure -->
            <x-advanced-pricing-form />

            <!-- Service Configuration -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Konfiguracja usługi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Radius -->
                    <div>
                        <label for="service_radius" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Promień działania (km) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="service_radius"
                               wire:model="service_radius"
                               min="1"
                               max="50"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('service_radius') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">W jakim promieniu od Twojej lokalizacji świadczysz usługi</p>
                    </div>

                    <!-- Experience Years -->
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lata doświadczenia
                        </label>
                        <input type="number"
                               id="experience_years"
                               wire:model="experience_years"
                               min="0"
                               max="50"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('experience_years') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Advanced Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="allow_mixed_pet_types"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Obsługuję psy i koty jednocześnie</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="requires_consultation"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Wymagam wstępnej konsultacji</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="emergency_contact"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dostępny w nagłych przypadkach</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="insurance_coverage"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Posiadam ubezpieczenie OC</span>
                    </label>
                </div>
            </div>

            <!-- Services Included -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Usługi w cenie</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                               wire:model="overnight_care"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opieka nocna</span>
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
                          placeholder="Dodatkowe informacje, wymagania, ograniczenia..."></textarea>
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
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
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
                    <a href="{{ route('sitter-services.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Anuluj
                    </a>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove>{{ $isEditMode ? 'Zapisz' : 'Dodaj usługę' }}</span>
                        <span wire:loading>Zapisywanie...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>