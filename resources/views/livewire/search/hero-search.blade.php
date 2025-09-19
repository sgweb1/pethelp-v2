<div
    class="w-full max-w-4xl mx-auto"
    x-data="heroSearch"
    x-init="init()"
>
    <form wire:submit="search" class="space-y-4">
        {{-- Main search row --}}
        <div class="flex flex-col sm:flex-row gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            {{-- Location input with suggestions --}}
            <div class="relative flex-1 min-w-0">
                <label for="location" class="sr-only">Lokalizacja</label>
                <div class="relative">
                    <input
                        wire:model.live.debounce.300ms="location"
                        wire:loading.attr="disabled"
                        x-ref="locationInput"
                        type="text"
                        id="location"
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-base placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500 focus:outline-none transition-colors"
                        placeholder="Wpisz miasto lub adres..."
                        autocomplete="off"
                        @click="$wire.showSuggestions = true"
                        @keydown.escape="$wire.showSuggestions = false"
                        @keydown.arrow-down.prevent="selectNextSuggestion()"
                        @keydown.arrow-up.prevent="selectPrevSuggestion()"
                        @keydown.enter.prevent="selectCurrentSuggestion()"
                    />

                    {{-- Location icon --}}
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>

                    {{-- GPS button --}}
                    <button
                        type="button"
                        wire:click="getCurrentLocation"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-purple-600 transition-colors"
                        title="Użyj mojej lokalizacji"
                    >
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </button>

                    {{-- Loading indicator --}}
                    <div wire:loading.flex wire:target="updatedLocation" class="absolute inset-y-0 right-8 flex items-center">
                        <svg class="animate-spin h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Location suggestions dropdown --}}
                @if($showSuggestions && $locationSuggestions->isNotEmpty())
                <div
                    class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg"
                    x-show="$wire.showSuggestions"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    @click.away="$wire.showSuggestions = false"
                >
                    <ul class="max-h-64 overflow-auto py-1">
                        @foreach($locationSuggestions as $index => $suggestion)
                        <li>
                            <button
                                type="button"
                                wire:click="selectSuggestion('{{ $suggestion['city'] }}', '{{ $suggestion['address'] }}')"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-purple-50 hover:text-purple-700 dark:hover:bg-purple-900 dark:hover:text-purple-300 transition-colors focus:bg-purple-50 focus:outline-none"
                                x-ref="suggestion{{ $index }}"
                            >
                                <div class="font-medium text-gray-900 dark:text-white">{{ $suggestion['city'] }}</div>
                                @if($suggestion['address'])
                                <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $suggestion['address'] }}</div>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            {{-- Content Type filter --}}
            <div class="w-full sm:w-56">
                <label for="contentType" class="sr-only">Typ treści</label>
                <select
                    wire:model.live="contentType"
                    id="contentType"
                    class="block w-full py-3 px-3 border border-gray-300 dark:border-gray-600 rounded-xl text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500 focus:outline-none transition-colors"
                >
                    @foreach($this->contentTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Specific Category filter (shows when content type is selected) --}}
            @if($contentType && $this->categories->isNotEmpty())
            <div class="w-full sm:w-48">
                <label for="category" class="sr-only">Kategoria</label>
                <select
                    wire:model.live="category"
                    id="category"
                    class="block w-full py-3 px-3 border border-gray-300 dark:border-gray-600 rounded-xl text-base bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500 focus:outline-none transition-colors"
                >
                    <option value="">Wszystkie podkategorie</option>
                    @foreach($this->categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Search button --}}
            <div class="w-full sm:w-auto">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-8 py-3 bg-yellow-400 text-gray-900 font-bold rounded-xl hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 hover:shadow-xl"
                >
                    <span wire:loading.remove wire:target="search">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Szukaj
                    </span>
                    <span wire:loading wire:target="search">
                        <svg class="inline-block animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Szukam...
                    </span>
                </button>
            </div>
        </div>

        {{-- Advanced filters (collapsible) --}}
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700" x-data="{ showAdvanced: false }">
            <button
                type="button"
                @click="showAdvanced = !showAdvanced"
                class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                <span>Zaawansowane filtry</span>
                <svg
                    class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': showAdvanced }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="showAdvanced"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="px-4 pb-4 border-t border-gray-200 dark:border-gray-600"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                    {{-- Radius filter --}}
                    <div>
                        <label for="radius" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Zasięg: {{ $radius }} km
                        </label>
                        <input
                            wire:model.live="radius"
                            type="range"
                            id="radius"
                            min="1"
                            max="50"
                            step="1"
                            class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-xl appearance-none cursor-pointer"
                        />
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>1 km</span>
                            <span>50 km</span>
                        </div>
                    </div>

                    {{-- Sort options --}}
                    <div>
                        <label for="sortBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Sortowanie
                        </label>
                        <select
                            wire:model.live="sortBy"
                            id="sortBy"
                            class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                        >
                            <option value="distance">Najbliższe</option>
                            <option value="rating">Najlepiej oceniane</option>
                            <option value="price_asc">Cena: rosnąco</option>
                            <option value="price_desc">Cena: malejąco</option>
                            <option value="newest">Najnowsze</option>
                        </select>
                    </div>

                    {{-- Reset button --}}
                    <div class="flex items-end">
                        <button
                            type="button"
                            wire:click="resetSearch"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            Wyczyść filtry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Error messages --}}
    @error('location')
    <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>

{{-- Alpine.js component --}}
@script
<script>
Alpine.data('heroSearch', () => ({
    currentSuggestionIndex: -1,

    init() {
        // Listen for geolocation events
        this.$wire.on('get-current-location', () => {
            this.getCurrentLocation();
        });

        // Listen for location selection events
        this.$wire.on('location-selected', (data) => {
            this.$refs.locationInput.focus();
        });
    },

    getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Reverse geocoding to get address
                this.reverseGeocode(lat, lng);
            },
            (error) => {
                console.error('Error getting location:', error);
                alert('Nie udało się pobrać lokalizacji. Sprawdź uprawnienia przeglądarki.');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            }
        );
    },

    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
            );
            const data = await response.json();

            const address = this.formatAddress(data.address);
            this.$wire.setCurrentLocation(lat, lng, address);
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            this.$wire.setCurrentLocation(lat, lng, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
        }
    },

    formatAddress(address) {
        const parts = [];
        if (address.city) parts.push(address.city);
        else if (address.town) parts.push(address.town);
        else if (address.village) parts.push(address.village);

        if (address.state) parts.push(address.state);

        return parts.join(', ') || 'Nieznana lokalizacja';
    },

    selectNextSuggestion() {
        const suggestions = this.$el.querySelectorAll('[x-ref^="suggestion"]');
        if (suggestions.length === 0) return;

        this.currentSuggestionIndex = Math.min(
            this.currentSuggestionIndex + 1,
            suggestions.length - 1
        );
        this.highlightSuggestion();
    },

    selectPrevSuggestion() {
        if (this.currentSuggestionIndex <= 0) {
            this.currentSuggestionIndex = -1;
            return;
        }

        this.currentSuggestionIndex--;
        this.highlightSuggestion();
    },

    highlightSuggestion() {
        const suggestions = this.$el.querySelectorAll('[x-ref^="suggestion"]');
        suggestions.forEach((el, index) => {
            el.classList.toggle('bg-purple-50', index === this.currentSuggestionIndex);
        });
    },

    selectCurrentSuggestion() {
        if (this.currentSuggestionIndex >= 0) {
            const suggestions = this.$el.querySelectorAll('[x-ref^="suggestion"]');
            const currentSuggestion = suggestions[this.currentSuggestionIndex];
            if (currentSuggestion) {
                currentSuggestion.click();
            }
        }
    }
}));
</script>
@endscript