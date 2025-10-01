@props([
    'wireModel',
    'label' => '',
    'placeholder' => 'Wpisz adres...',
    'required' => false,
    'id' => null,
    'showCurrentLocation' => true
])

@php
    $id = $id ?? 'address-search-' . uniqid();
@endphp

<div x-data="addressSearch"
     x-init="
        wireModelName = '{{ $wireModel }}';
        required = {{ $required ? 'true' : 'false' }};
        showCurrentLocation = {{ $showCurrentLocation ? 'true' : 'false' }};
        searchQuery = $wire.get('{{ $wireModel }}') || '';
     "
     class="relative">

    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        <!-- Address Input -->
        <input
            type="text"
            x-model="searchQuery"
            @focus="handleFocus"
            @click="handleClick"
            @input="handleInputManual"
            @keyup="handleKeyup"
            @keydown.escape="closeSuggestions"
            @keydown.arrow-down.prevent="navigateDown"
            @keydown.arrow-up.prevent="navigateUp"
            @keydown.enter.prevent="selectCurrentSuggestion"
            id="{{ $id }}"
            :placeholder="loading ? 'Szukam...' : '{{ $placeholder }}'"
            class="w-full px-4 py-3.5 pr-20 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white min-h-[48px] hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors cursor-text"
            :class="{ 'animate-pulse': loading }"
            style="caret-color: auto !important; user-select: text !important;"
            :class="{ 'border-red-500': required && !$el.value }"
            autocomplete="off"
            x-ref="addressInput"
        />

        <!-- Current Location Button -->
        @if($showCurrentLocation)
        <button
            type="button"
            @click="getCurrentLocation"
            title="Użyj mojej lokalizacji"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-purple-500 hover:text-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors"
            :disabled="gettingLocation"
        >
            <svg x-show="!gettingLocation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z"/>
            </svg>
            <svg x-show="gettingLocation" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
        @endif
    </div>

    <!-- Suggestions Dropdown -->
    <div x-show="showSuggestions && (suggestions.length > 0 || loading)"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closeSuggestions"
         @mousedown.prevent
         tabindex="-1"
         class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-64 overflow-y-auto">

        <!-- Loading State -->
        <div x-show="loading" class="p-3 text-center text-gray-500 dark:text-gray-400">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Szukam adresów...
            </div>
        </div>

        <!-- Suggestions -->
        <template x-for="(suggestion, index) in suggestions" :key="index">
            <button
                type="button"
                @click="selectSuggestion(suggestion)"
                @mouseenter="highlightedIndex = index"
                @mousedown.prevent
                tabindex="-1"
                class="w-full px-3 py-3 text-left hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                :class="{ 'bg-purple-50 dark:bg-purple-900/20': highlightedIndex === index }"
            >
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="suggestion.display"></div>
                        <div x-show="suggestion.description" class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <span x-text="suggestion.description"></span>
                            <span x-show="suggestion.coordinates" class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z"/>
                                </svg>
                                GPS
                            </span>
                        </div>
                    </div>
                </div>
            </button>
        </template>

        <!-- No Results -->
        <div x-show="!loading && suggestions.length === 0 && searchQuery.length >= 2" class="p-3 text-center text-gray-500 dark:text-gray-400">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-sm">Nie znaleziono adresów</span>
                <span class="text-xs">Spróbuj innej frazy wyszukiwania</span>
            </div>
        </div>
    </div>
</div>

<script>
function addressSearch() {
    return {
        searchQuery: '',
        lastSearchQuery: '',
        suggestions: [],
        showSuggestions: false,
        loading: false,
        gettingLocation: false,
        highlightedIndex: -1,
        debounceTimer: null,
        wireModelName: '',
        required: false,
        showCurrentLocation: true,
        searchCache: new Map(), // Cache dla wyników wyszukiwania (TTL 5 minut)

        init() {
            // Initialize searchQuery from Livewire
            this.searchQuery = this.$wire.get('{{ $wireModel }}') || '';
            this.lastSearchQuery = this.searchQuery;

            // Aggressive cursor management - force to end after ANY Livewire event
            const forceCursorToEnd = () => {
                const input = this.$refs.addressInput;
                if (input && document.activeElement === input) {
                    // Multiple attempts for reliability using requestAnimationFrame
                    requestAnimationFrame(() => {
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });

                    // Backup attempt
                    requestAnimationFrame(() => {
                        setTimeout(() => {
                            const length = input.value.length;
                            input.setSelectionRange(length, length);
                        }, 10);
                    });

                    this.$nextTick(() => {
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });
                }
            };

            // Listen to all Livewire events
            document.addEventListener('livewire:update', forceCursorToEnd);
            document.addEventListener('livewire:morph', forceCursorToEnd);
            document.addEventListener('livewire:morphed', forceCursorToEnd);
            document.addEventListener('livewire:commit', forceCursorToEnd);

            // Watch for changes in the wire model
            this.$watch('$wire.{{ $wireModel }}', (value) => {
                const input = this.$refs.addressInput;
                if (input && document.activeElement === input && value !== undefined) {
                    this.$nextTick(() => {
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });
                }
            });
        },

        // ===== NOWE METODY OBSŁUGI EVENTÓW BEZ SETTIMEOUT =====

        /**
         * Obsługuje fokus na polu input - zastępuje setTimeout w @focus.
         */
        handleFocus(event) {
            const input = event.target;
            // Użyj requestAnimationFrame zamiast setTimeout
            requestAnimationFrame(() => {
                const length = input.value.length;
                input.setSelectionRange(length, length);
                if (this.suggestions.length > 0 && input.value.length >= 2) {
                    this.showSuggestions = true;
                }
            });
        },

        /**
         * Obsługuje klik na polu input - zastępuje setTimeout w @click.
         */
        handleClick(event) {
            const input = event.target;
            requestAnimationFrame(() => {
                const length = input.value.length;
                input.setSelectionRange(length, length);
            });
        },

        /**
         * Obsługuje keyup na polu input - zastępuje setTimeout w @keyup.
         */
        handleKeyup(event) {
            const input = event.target;
            if (document.activeElement === input) {
                requestAnimationFrame(() => {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                });
            }
        },

        handleInputManual(event) {
            const currentValue = event.target.value;
            const input = event.target;

            // Update Alpine state
            this.searchQuery = currentValue;

            // Manually update Livewire without triggering reactivity that affects cursor
            if (this.wireModelName) {
                this.$wire.set(this.wireModelName, currentValue, false); // false = don't trigger updates
            }

            // Force cursor to end IMMEDIATELY using requestAnimationFrame
            requestAnimationFrame(() => {
                if (document.activeElement === input) {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                }
            });

            // Only trigger search if the value actually changed
            if (currentValue !== this.lastSearchQuery) {
                this.lastSearchQuery = currentValue;
                this.triggerSearch(currentValue);
            }
        },

        handleInput(event) {
            const currentValue = event.target.value;
            const input = event.target;

            // Only trigger search if the value actually changed
            if (currentValue !== this.lastSearchQuery) {
                this.lastSearchQuery = currentValue;
                this.searchQuery = currentValue; // Sync with Alpine

                // Set cursor to end after typing - use requestAnimationFrame for reliability
                requestAnimationFrame(() => {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                });

                this.$nextTick(() => {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                });

                this.triggerSearch(currentValue);
            }
        },

        triggerSearch(value) {
            // Clear previous timer
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            const searchValue = value || this.searchQuery;

            // Don't search if query is too short
            if (searchValue.length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }

            // Debounce the search
            this.debounceTimer = setTimeout(() => {
                this.searchAddresses(searchValue);
            }, 300);
        },


        async searchAddresses(searchValue) {
            this.loading = true;
            this.highlightedIndex = -1;

            const query = searchValue || this.searchQuery;

            // 🚀 Sprawdź cache najpierw - jak Airbnb!
            const cacheKey = query.toLowerCase().trim();
            const cached = this.searchCache.get(cacheKey);
            if (cached && (Date.now() - cached.timestamp < 300000)) { // 5 minut TTL
                console.log('🎯 Cache HIT dla:', cacheKey);
                this.suggestions = cached.data;
                this.showSuggestions = true;
                this.loading = false;
                return;
            }

            // Store current input reference and focus state
            const input = this.$el?.querySelector('input');
            const wasFocused = input && document.activeElement === input;
            const cursorPos = input && typeof input.selectionStart === 'number' ? input.selectionStart : 0;

            try {
                // Use hierarchical location search API first
                const hierarchicalUrl = `/api/locations/search?q=${encodeURIComponent(query)}&limit=8`;
                console.log('Searching for:', query, 'URL:', hierarchicalUrl);

                const response = await fetch(hierarchicalUrl);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Hierarchical API Response:', data);

                    if (data.success && data.data) {
                        // Transform hierarchical API response to match component format
                        this.suggestions = data.data.map(item => ({
                            display: item.label,
                            description: this.getLocationTypeDescription(item.type, item.parent_city),
                            type: item.type,
                            value: item.label,
                            coordinates: item.coordinates ? {
                                lat: item.coordinates[0],
                                lng: item.coordinates[1]
                            } : null,
                            raw_data: item.data
                        }));
                        console.log('Hierarchical suggestions set:', this.suggestions);

                        // 💾 Zapisz do cache - jak Airbnb!
                        this.searchCache.set(cacheKey, {
                            data: this.suggestions,
                            timestamp: Date.now()
                        });
                        console.log('💾 Cache SAVED dla:', cacheKey);
                    } else {
                        throw new Error('Invalid hierarchical API response');
                    }
                } else {
                    console.log('Hierarchical API failed, trying fallback');
                    // Fallback to original API
                    const fallbackUrl = `/api/search-addresses?query=${encodeURIComponent(query)}`;
                    const fallbackResponse = await fetch(fallbackUrl);

                    if (fallbackResponse.ok) {
                        const fallbackData = await fallbackResponse.json();
                        this.suggestions = fallbackData.suggestions || [];
                    } else {
                        // Final fallback to MapItem search
                        await this.searchMapItems();
                    }
                }

                this.showSuggestions = true;
                console.log('Show suggestions:', this.showSuggestions, 'Suggestions count:', this.suggestions.length);

                // Restore focus and cursor position after showing suggestions
                if (wasFocused && input) {
                    this.$nextTick(() => {
                        input.focus();
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });
                }
            } catch (error) {
                console.error('Address search error:', error);
                // Fallback to MapItem search
                await this.searchMapItems();
            } finally {
                this.loading = false;

                // Final attempt to restore focus
                const input = this.$refs.addressInput;
                if (input && document.activeElement !== input && this.showSuggestions) {
                    this.$nextTick(() => {
                        input.focus();
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });
                }
            }
        },

        async searchMapItems() {
            try {
                // Search in existing map items as fallback
                const suggestions = [];

                // Add some common Polish cities as suggestions
                const commonCities = [
                    'Warszawa', 'Kraków', 'Wrocław', 'Poznań', 'Gdańsk',
                    'Szczecin', 'Bydgoszcz', 'Lublin', 'Katowice', 'Białystok',
                    'Gdynia', 'Częstochowa', 'Radom', 'Sosnowiec', 'Toruń'
                ];

                const query = this.searchQuery.toLowerCase();
                commonCities.forEach(city => {
                    if (city.toLowerCase().includes(query)) {
                        suggestions.push({
                            display: city,
                            description: 'Miasto',
                            type: 'city',
                            value: city
                        });
                    }
                });

                // Add street suggestions if query looks like street name
                if (query.includes('ul.') || query.includes('aleja') || query.includes('plac')) {
                    suggestions.push({
                        display: this.searchQuery,
                        description: 'Adres',
                        type: 'address',
                        value: this.searchQuery
                    });
                }

                this.suggestions = suggestions.slice(0, 8); // Limit to 8 suggestions
            } catch (error) {
                console.error('MapItem search error:', error);
                this.suggestions = [];
            }
        },

        selectSuggestion(suggestion) {
            this.searchQuery = suggestion.display;
            this.lastSearchQuery = suggestion.display; // Update last search to prevent re-triggering

            // Update input value and set cursor to end
            const input = this.$refs.addressInput;
            if (input) {
                input.value = suggestion.display;
                input.focus();

                this.$nextTick(() => {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                });
            }

            // IMPORTANT: Manually sync with Livewire
            console.log('🔧 wireModelName:', this.wireModelName, 'suggestion.display:', suggestion.display);
            if (this.wireModelName) {
                this.$wire.set(this.wireModelName, suggestion.display);
                console.log('✅ Updated Livewire model:', this.wireModelName, 'to:', suggestion.display);

                // Force trigger update
                this.$wire.$commit();
                console.log('🔄 Forced Livewire commit');
            } else {
                console.log('❌ ERROR: wireModelName is not set!');
            }

            this.closeSuggestions();

            // Clear suggestions after selection to prevent showing them on focus
            setTimeout(() => {
                this.suggestions = [];
            }, 100);

            // Try both approaches for setting the filter

            // Method 1: Direct Livewire call
            try {
                console.log('🚀 Trying direct Livewire call...');
                this.$wire.call('handleAddressSelected', {
                    address: suggestion.display,
                    value: suggestion.value,
                    type: suggestion.type,
                    coordinates: suggestion.coordinates || null,
                    description: suggestion.description
                });
                console.log('✅ Direct call succeeded');
            } catch (error) {
                console.error('❌ Direct call failed:', error);
            }

            // Method 2: Dispatch event with additional data
            const eventData = {
                address: suggestion.display,
                value: suggestion.value,
                type: suggestion.type,
                coordinates: suggestion.coordinates || null,
                description: suggestion.description
            };

            console.log('📍 Dispatching address-selected event:', eventData);
            this.$dispatch('address-selected', eventData);

            console.log('Address selected:', suggestion);
        },

        closeSuggestions() {
            this.showSuggestions = false;
            this.highlightedIndex = -1;
        },

        navigateDown() {
            if (this.highlightedIndex < this.suggestions.length - 1) {
                this.highlightedIndex++;
            }
        },

        navigateUp() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },

        selectCurrentSuggestion() {
            if (this.highlightedIndex >= 0 && this.suggestions[this.highlightedIndex]) {
                this.selectSuggestion(this.suggestions[this.highlightedIndex]);
            }
        },

        async getCurrentLocation() {
            if (!showCurrentLocation || !navigator.geolocation) {
                alert('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę.');
                return;
            }

            this.gettingLocation = true;

            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000 // 5 minutes
                    });
                });

                const { latitude, longitude } = position.coords;

                // Reverse geocoding to get address
                try {
                    const address = await this.reverseGeocode(latitude, longitude);
                    this.searchQuery = address;
                    this.lastSearchQuery = address;

                    // Update input value and set cursor to end
                    const input = this.$refs.addressInput;
                    input.value = address;
                    input.focus();
                    this.$nextTick(() => {
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });

                    // Dispatch location event
                    this.$dispatch('location-detected', {
                        address: address,
                        latitude: latitude,
                        longitude: longitude
                    });
                } catch (geocodeError) {
                    console.error('Reverse geocoding failed:', geocodeError);
                    const coords = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                    this.searchQuery = coords;
                    this.lastSearchQuery = coords;

                    // Update input value and set cursor to end
                    const input = this.$refs.addressInput;
                    input.value = coords;
                    input.focus();
                    this.$nextTick(() => {
                        const length = input.value.length;
                        input.setSelectionRange(length, length);
                    });
                }

            } catch (error) {
                console.error('Geolocation error:', error);
                let message = 'Nie udało się wykryć lokalizacji.';

                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Dostęp do lokalizacji został odrzucony. Sprawdź ustawienia przeglądarki.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Informacje o lokalizacji są niedostępne.';
                        break;
                    case error.TIMEOUT:
                        message = 'Przekroczono czas oczekiwania na lokalizację.';
                        break;
                }

                alert(message);
            } finally {
                this.gettingLocation = false;
            }
        },

        getLocationTypeDescription(type, parentCity) {
            const typeDescriptions = {
                'city': 'Miasto',
                'district': parentCity ? `Dzielnica w ${parentCity}` : 'Dzielnica',
                'village': 'Wieś',
                'other': 'Lokalizacja'
            };

            return typeDescriptions[type] || 'Lokalizacja';
        },

        async reverseGeocode(lat, lng) {
            try {
                // First try using our own API
                const response = await fetch(`/api/locations/reverse?lat=${lat}&lon=${lng}`);

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data) {
                        const location = data.data;
                        // Build a readable address from the structured data
                        const parts = [];

                        if (location.district) parts.push(location.district);
                        if (location.city) parts.push(location.city);
                        if (location.state) parts.push(location.state);

                        return parts.join(', ') || location.display_name;
                    }
                }

                // Fallback to external geocoding service
                const fallbackResponse = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);

                if (fallbackResponse.ok) {
                    const data = await fallbackResponse.json();

                    if (data.display_name) {
                        // Extract meaningful address parts
                        const address = data.address || {};
                        const parts = [];

                        if (address.road) parts.push(address.road);
                        if (address.house_number) parts.push(address.house_number);
                        if (address.city || address.town || address.village) {
                            parts.push(address.city || address.town || address.village);
                        }

                        return parts.join(', ') || data.display_name;
                    }
                }

                throw new Error('Geocoding failed');
            } catch (error) {
                console.error('Reverse geocoding error:', error);
                throw error;
            }
        }
    }
}
</script>