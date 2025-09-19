{{-- Rover.com Style Search Component --}}
<section class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-16 relative overflow-hidden" id="services-overview">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <svg class="w-full h-full" viewBox="0 0 60 60">
            <defs>
                <pattern id="paw-pattern" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
                    <g transform="translate(30,30)">
                        <circle cx="0" cy="-8" r="3" fill="currentColor"/>
                        <circle cx="6" cy="-4" r="3" fill="currentColor"/>
                        <circle cx="6" cy="4" r="3" fill="currentColor"/>
                        <circle cx="-6" cy="-4" r="3" fill="currentColor"/>
                        <circle cx="-6" cy="4" r="3" fill="currentColor"/>
                        <ellipse cx="0" cy="2" rx="4" ry="6" fill="currentColor"/>
                    </g>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#paw-pattern)" class="text-purple-600"/>
        </svg>
    </div>

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Search Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Znajdź idealnego <span class="text-purple-600 dark:text-purple-400">opiekuna</span> dla swojego pupila
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Tysiące zweryfikowanych pet sitterów gotowych zapewnić najlepszą opiekę Twojemu pupilowi
            </p>
        </div>

        <!-- Search Form Card - Rover Style -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 mb-8">
            <form wire:submit="search" class="space-y-6">
                <!-- Service Type Selection (Rover Style Tabs) -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                    <nav class="flex space-x-8 overflow-x-auto">
                        @foreach($this->serviceTypes as $type => $config)
                        <button
                            type="button"
                            wire:click="$set('serviceType', '{{ $type }}')"
                            class="group py-4 px-2 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-300 {{ $serviceType === $type ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:border-gray-300' }}"
                        >
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ $config['icon'] }}</span>
                                <span>{{ $config['name'] }}</span>
                            </div>
                            @if($serviceType === $type)
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ count($config['services']) }} usług dostępnych
                            </div>
                            @endif
                        </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Main Search Row -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
                    <!-- Location Input -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Lokalizacja
                            </span>
                        </label>
                        <div class="relative">
                            <input
                                wire:model.live.debounce.300ms="location"
                                x-ref="locationInput"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-base focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-colors"
                                placeholder="Wpisz miasto..."
                                autocomplete="off"
                                @click="$wire.showSuggestions = true"
                                @keydown.escape="$wire.showSuggestions = false"
                            />

                            <!-- GPS Button -->
                            <button
                                type="button"
                                wire:click="getCurrentLocation"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-purple-600 transition-colors"
                                title="Użyj mojej lokalizacji"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </button>

                            <!-- Location Suggestions Dropdown -->
                            @if($showSuggestions && $locationSuggestions->isNotEmpty())
                            <div
                                class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-64 overflow-auto"
                                @click.away="$wire.showSuggestions = false"
                            >
                                @foreach($locationSuggestions as $suggestion)
                                <button
                                    type="button"
                                    wire:click="selectSuggestion('{{ $suggestion['city'] }}', '{{ $suggestion['address'] }}')"
                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $suggestion['city'] }}</div>
                                    @if($suggestion['address'])
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $suggestion['address'] }}</div>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pet Type & Count -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                Twój pupil
                            </span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Pet Type -->
                            <select
                                wire:model.live="petType"
                                class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                            >
                                @foreach($this->petTypes as $type => $config)
                                <option value="{{ $type }}">{{ $config['icon'] }} {{ $config['name'] }}</option>
                                @endforeach
                            </select>

                            <!-- Pet Count -->
                            <select
                                wire:model.live="petCount"
                                class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                            >
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'pupil' : 'pupili' }}</option>
                                @endfor
                                <option value="6">6+ pupili</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dates (for pet sitters) -->
                    @if($serviceType === 'pet_sitter')
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Kiedy potrzebujesz?
                            </span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input
                                wire:model.live="startDate"
                                type="date"
                                class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                min="{{ date('Y-m-d') }}"
                            />
                            <input
                                wire:model.live="endDate"
                                type="date"
                                class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                min="{{ date('Y-m-d') }}"
                            />
                        </div>
                    </div>
                    @endif

                    <!-- Search Button -->
                    <div class="lg:col-span-1 flex items-end">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <span wire:loading.remove wire:target="search" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Wyszukaj
                            </span>
                            <span wire:loading wire:target="search" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-900" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Szukam...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Service-specific filters -->
                @if($serviceType === 'pet_sitter')
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-xl p-4 mt-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Typ opieki</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($this->serviceTypes['pet_sitter']['services'] as $key => $service)
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="specific_service" value="{{ $key }}" class="sr-only">
                            <div class="flex-1 text-center py-2 px-3 text-sm font-medium rounded-lg border-2 border-transparent group-hover:border-purple-200 group-hover:bg-white dark:group-hover:bg-gray-700 transition-colors">
                                {{ $service }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Advanced Filters Toggle -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-200 transition-colors"
                        @click="$wire.toggle('showAdvanced')"
                    >
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                            </svg>
                            Więcej filtrów
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="resetSearch"
                        class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                    >
                        Wyczyść filtry
                    </button>
                </div>
            </form>
        </div>

        <!-- Popular Searches -->
        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Popularne wyszukiwania:</p>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach(['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań'] as $city)
                <button
                    wire:click="$set('location', '{{ $city }}')"
                    class="px-4 py-2 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600 hover:border-purple-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors"
                >
                    {{ $city }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Alpine.js Component --}}
@script
<script>
Alpine.data('roverSearch', () => ({
    init() {
        // Focus management and keyboard navigation
        this.$wire.on('location-selected', (data) => {
            if (this.$refs.locationInput) {
                this.$refs.locationInput.focus();
            }
        });

        // GPS geolocation
        this.$wire.on('get-current-location', () => {
            this.getCurrentLocation();
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
                this.reverseGeocode(lat, lng);
            },
            (error) => {
                console.error('Błąd pobierania lokalizacji:', error);
                alert('Nie udało się pobrać lokalizacji. Sprawdź uprawnienia przeglądarki.');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
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
            console.error('Błąd geokodowania:', error);
            this.$wire.setCurrentLocation(lat, lng, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
        }
    },

    formatAddress(address) {
        const parts = [];
        if (address.city) parts.push(address.city);
        else if (address.town) parts.push(address.town);
        else if (address.village) parts.push(address.village);

        return parts.join(', ') || 'Nieznana lokalizacja';
    }
}));
</script>
@endscript