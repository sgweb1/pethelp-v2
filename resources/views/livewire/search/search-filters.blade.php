<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4" role="search" aria-label="Filtry wyszukiwania opiekunów">
    <!-- Quick Search Bar -->
    <div class="mb-4">
        <label for="search-term" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Wyszukaj
        </label>
        <input
            id="search-term"
            type="text"
            wire:model.live.debounce.500ms="search_term"
            placeholder="Opiekun, usługa..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
            autocomplete="off"
            spellcheck="false"
        >
    </div>

    <div class="space-y-4 mb-4">
        <!-- Location Input -->
        <div class="sm:col-span-2 lg:col-span-2">
            <label for="location-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lokalizacja</label>
            <div class="relative">
                <input
                    id="location-input"
                    type="text"
                    wire:model.live.debounce.500ms="location"
                    placeholder="Wpisz miasto lub kod pocztowy..."
                    class="w-full px-3 sm:px-4 py-2 sm:py-3 pr-10 sm:pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                    autocomplete="address-level2"
                >
                <button
                    type="button"
                    wire:click="$dispatch('detect-location')"
                    class="absolute right-2 sm:right-3 top-2 sm:top-3 text-gray-400 dark:text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200 btn-accessible"
                    aria-label="Wykryj moją lokalizację automatycznie"
                    title="Wykryj moją lokalizację"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="sm:col-span-1 lg:col-span-1">
            <label for="category-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategoria</label>
            <select
                id="category-select"
                wire:model.live="category_id"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                aria-describedby="category-help"
            >
                <option value="">Wszystkie kategorie</option>
                @foreach($this->categories as $category)
                    <option wire:key="category-option-{{ $category->id }}" value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                @endforeach
            </select>
            <div id="category-help" class="help-text mt-1 text-sm text-gray-600 dark:text-gray-400">
                Wybierz kategorię aby zawęzić wyniki
            </div>
        </div>

        <!-- Pet Type -->
        <div class="sm:col-span-1 lg:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Typ</label>
            <select
                wire:model.live="pet_type"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-base"
            >
                <option value="">Wszystkie</option>
                <option value="dog">🐕 Pies</option>
                <option value="cat">🐱 Kot</option>
                <option value="bird">🐦 Ptak</option>
                <option value="rabbit">🐰 Królik</option>
                <option value="rodent">🐹 Gryzoń</option>
                <option value="fish">🐠 Ryba</option>
                <option value="reptile">🦎 Gad</option>
                <option value="other">🐾 Inne</option>
            </select>
        </div>
    </div>

    <!-- Mobile Filters Toggle & Search Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-4">
        <div class="flex flex-wrap items-center gap-2 sm:space-x-4 sm:gap-0">
            <x-ui.button
                wire:click="$toggle('show_filters')"
                variant="outline"
                size="sm"
                class="relative"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                {{ $show_filters ? 'Ukryj filtry' : 'Więcej filtrów' }}
                @if($this->active_filters_count > 0)
                    <span class="absolute -top-2 -right-2 bg-danger-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $this->active_filters_count }}
                    </span>
                @endif
            </x-ui.button>

            <x-ui.button
                wire:click="clearFilters"
                variant="ghost"
                size="sm"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Wyczyść
            </x-ui.button>
        </div>
    </div>

    <!-- Advanced Filters -->
    @if($show_filters)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Zaawansowane filtry</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Pet Size -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rozmiar zwierzęcia</label>
                    <select
                        wire:model.live="pet_size"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Wszystkie</option>
                        <option value="small">Małe (do 10kg)</option>
                        <option value="medium">Średnie (10-25kg)</option>
                        <option value="large">Duże (25kg+)</option>
                    </select>
                </div>

                <!-- Service Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rodzaj opieki</label>
                    <select
                        wire:model.live="service_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Wszystkie</option>
                        <option value="home_service">U klienta</option>
                        <option value="sitter_home">U opiekuna</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena (zł)</label>
                    <div class="flex space-x-2">
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="min_price"
                            placeholder="Od"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        >
                        <input
                            type="number"
                            wire:model.live.debounce.500ms="max_price"
                            placeholder="Do"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.live="price_type" value="hour" class="text-indigo-600">
                            <span class="ml-2 text-sm">za godzinę</span>
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input type="radio" wire:model.live="price_type" value="day" class="text-indigo-600">
                            <span class="ml-2 text-sm">za dzień</span>
                        </label>
                    </div>
                </div>

                <!-- Min Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min. ocena</label>
                    <select
                        wire:model.live="min_rating"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Wszystkie</option>
                        <option value="3">⭐⭐⭐ 3+ gwiazdki</option>
                        <option value="4">⭐⭐⭐⭐ 4+ gwiazdki</option>
                        <option value="4.5">⭐⭐⭐⭐⭐ 4.5+ gwiazdki</option>
                    </select>
                </div>
            </div>

            <!-- Second Row of Advanced Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Available Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data dostępności</label>
                    <input
                        type="date"
                        wire:model.live="available_date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Od godziny</label>
                    <input
                        type="time"
                        wire:model.live="start_time"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Do godziny</label>
                    <input
                        type="time"
                        wire:model.live="end_time"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Max Pets -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maksymalnie zwierząt</label>
                    <select
                        wire:model.live="max_pets"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Wszystkie</option>
                        <option value="1">1 zwierzę</option>
                        <option value="2">2 zwierzęta</option>
                        <option value="3">3 zwierzęta</option>
                        <option value="5">5+ zwierząt</option>
                    </select>
                </div>
            </div>

            <!-- Third Row - Checkboxes and Additional Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Experience Years -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min. lat doświadczenia</label>
                    <select
                        wire:model.live="experience_years"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Wszystkie</option>
                        <option value="1">1+ rok</option>
                        <option value="3">3+ lata</option>
                        <option value="5">5+ lat</option>
                        <option value="10">10+ lat</option>
                    </select>
                </div>

                <!-- Checkboxes Column 1 -->
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="verified_only"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">✓ Tylko zweryfikowani</span>
                    </label>

                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="instant_booking"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">⚡ Natychmiastowa rezerwacja</span>
                    </label>
                </div>

                <!-- Checkboxes Column 2 -->
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="has_insurance"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">🛡️ Ma ubezpieczenie</span>
                    </label>

                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="flexible_cancellation"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">🔄 Elastyczne anulowanie</span>
                    </label>
                </div>

                <!-- Empty space for visual balance -->
                <div></div>
            </div>

            <!-- Fourth Row - Radius and Sort -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Radius -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Promień wyszukiwania: {{ $radius }} km
                    </label>
                    <input
                        type="range"
                        wire:model.live="radius"
                        min="1"
                        max="50"
                        class="w-full"
                    >
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sortuj według</label>
                    <select
                        wire:model.live="sort_by"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="relevance">Trafność</option>
                        <option value="distance">Odległość</option>
                        <option value="price_low">Cena: od najniższej</option>
                        <option value="price_high">Cena: od najwyższej</option>
                        <option value="rating">Najwyżej oceniane</option>
                        <option value="experience">Doświadczenie</option>
                        <option value="most_booked">Najczęściej rezerwowane</option>
                        <option value="newest">Najnowsze</option>
                    </select>
                </div>
            </div>
        </div>
    @endif
</div>