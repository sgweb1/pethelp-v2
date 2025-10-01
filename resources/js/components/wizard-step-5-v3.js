/**
 * Pet Sitter Wizard - Step 5 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 5 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 5 - Address & Service Radius
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep5() {
    return {
        // === BRAK LOKALNYCH ZMIENNYCH - WSZYSTKO Z GLOBAL STATE ===

        // Map related properties
        map: null,
        mapInitialized: false,
        userMarker: null,
        radiusCircle: null,

        // Timeouts dla debounce
        searchTimeout: null,
        populationTimeout: null,

        // Local reactive properties for Alpine.js (zwykłe properties, nie gettery)
        _localServiceRadius: 10,
        _localRadiusLabel: '10 km',
        _estimatedPopulation: null,
        _businessMetrics: null,

        // Alpine.js reactive properties (muszą być zwykłymi properties)
        estimatedPopulation: null,
        businessMetrics: null,
        populationText: 'Obliczanie...',
        businessText: '',
        radiusLabel: '10 km',
        _localAddress: '', // Local reactive property for address display

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('🗺️ Step 5 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();

            console.log('✅ Step 5 state initialized:', {
                address: this.currentAddress,
                serviceRadius: this.serviceRadius,
                suggestions: this.currentSuggestions?.length || 0
            });
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewireAddress = this.$wire?.address || '';
            const livewireRadius = this.$wire?.serviceRadius || 10;

            if (!this.currentAddress && livewireAddress) {
                window.WizardState.update('location.address', livewireAddress);
            }

            if (!this.serviceRadius && livewireRadius) {
                window.WizardState.update('location.serviceRadius', livewireRadius);
            }

            // Initialize empty arrays/objects
            if (!Array.isArray(this.currentSuggestions)) {
                window.WizardState.update('location.addressSuggestions', []);
            }

            if (this.selectedIndex === undefined) {
                window.WizardState.update('location.selectedSuggestionIndex', -1);
            }

            // Initialize local Alpine.js reactive properties
            const currentRadius = window.WizardState.get('location.serviceRadius') || livewireRadius || 10;
            this._localServiceRadius = currentRadius;
            this._localRadiusLabel = currentRadius + ' km';

            // Initialize Alpine.js reactive properties
            this.estimatedPopulation = null;
            this.businessMetrics = null;
            this.populationText = 'Obliczanie...';
            this.businessText = '';
            this.radiusLabel = currentRadius + ' km';

            // Initialize local address from WizardState or Livewire
            const stateAddress = window.WizardState.get('location.address');
            this._localAddress = stateAddress || livewireAddress || '';

            console.log('🎯 Initialized local properties:', {
                _localServiceRadius: this._localServiceRadius,
                _localRadiusLabel: this._localRadiusLabel,
                _localAddress: this._localAddress,
                estimatedPopulation: this.estimatedPopulation,
                populationText: this.populationText
            });

            // Update derived state
            this.updateDerivedState();
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE ===

        /**
         * Aktualny adres - używa lokalnej reactive property dla Alpine.js
         */
        get currentAddress() {
            return this._localAddress || '';
        },

        /**
         * Promień obsługi - hybrid approach (local + global)
         */
        get serviceRadius() {
            const globalRadius = window.WizardState?.get('location.serviceRadius') || 10;
            // Sync local with global if different
            if (this._localServiceRadius !== globalRadius) {
                this._localServiceRadius = globalRadius;
            }
            return this._localServiceRadius;
        },


        /**
         * Aktualizuje teksty populacji i biznesowe (nie gettery, zwykłe metody)
         */
        updatePopulationTexts() {
            console.log('🔄 updatePopulationTexts called');
            console.log('📊 _estimatedPopulation:', this._estimatedPopulation);
            console.log('📈 _businessMetrics:', this._businessMetrics);
            console.log('🌐 Current step:', window.WizardState?.get('meta.currentStep'));

            // Update populationText
            this.populationText = this.getFormattedPopulationText();
            this.businessText = this.getFormattedBusinessText();

            console.log('📝 Formatted texts:', {
                populationText: this.populationText,
                businessText: this.businessText
            });

            // Update Alpine properties dla reactive binding
            this.estimatedPopulation = this._estimatedPopulation;
            this.businessMetrics = this._businessMetrics;

            // Synchronizuj z global state dla sidebara
            if (window.WizardState) {
                window.WizardState.update('location.populationText', this.populationText);
                window.WizardState.update('location.businessText', this.businessText);
                window.WizardState.update('location.estimatedPopulation', this._estimatedPopulation);
                window.WizardState.update('location.businessMetrics', this._businessMetrics);

                // Dodaj sformatowane wersje liczb dla Alpine.js
                if (this._businessMetrics) {
                    const formatted = {
                        households: this._businessMetrics.households ? this._businessMetrics.households.toLocaleString('pl-PL') : '-',
                        petOwningHouseholds: this._businessMetrics.petOwningHouseholds ? this._businessMetrics.petOwningHouseholds.toLocaleString('pl-PL') : '-',
                        potentialClients: this._businessMetrics.potentialClients ? this._businessMetrics.potentialClients.toLocaleString('pl-PL') : '-'
                    };
                    window.WizardState.update('location.businessMetricsFormatted', formatted);
                    console.log('📊 Formatted numbers for Alpine.js:', formatted);
                }

                // Dodaj notatki estymacji do WizardState
                if (this._businessMetrics && this._businessMetrics.notes) {
                    window.WizardState.update('location.estimationNotes', this._businessMetrics.notes);
                }

                console.log('✅ WizardState updated with population data');
                console.log('🔍 WizardState check:', {
                    populationText: window.WizardState.get('location.populationText'),
                    businessMetricsFormatted: window.WizardState.get('location.businessMetricsFormatted'),
                    estimationNotes: window.WizardState.get('location.estimationNotes')
                });

                // Force update sidebara "📍 Analiza lokalizacji" bezpośrednio
                this.$nextTick(() => {
                    this.updateSidebarStats();
                });
            } else {
                console.error('❌ WizardState nie jest dostępny!');
            }
        },

        /**
         * Force update sidebara z danymi estymacji populacji
         */
        updateSidebarStats() {
            console.log('✅ Sidebar stats będą zaktualizowane automatycznie przez Alpine.js z WizardState');
            console.log('📊 Dane w WizardState:', {
                populationText: window.WizardState?.get('location.populationText'),
                businessMetrics: window.WizardState?.get('location.businessMetrics')
            });

            // Dispatch global event dla AI Panel aby wymusić refresh
            window.dispatchEvent(new CustomEvent('wizard-data-updated', {
                detail: {
                    step: 5,
                    type: 'population-estimation',
                    data: {
                        populationText: window.WizardState?.get('location.populationText'),
                        businessMetrics: window.WizardState?.get('location.businessMetrics'),
                        businessMetricsFormatted: window.WizardState?.get('location.businessMetricsFormatted')
                    }
                }
            }));
            console.log('🔔 Event wizard-data-updated dispatched for AI Panel refresh');
        },

        /**
         * Formatuje tekst populacji
         */
        getFormattedPopulationText() {
            if (!this._estimatedPopulation) {
                return 'Obliczanie...';
            } else if (this._estimatedPopulation >= 1000000) {
                return `${(this._estimatedPopulation / 1000000).toFixed(1)}M osób`;
            } else if (this._estimatedPopulation >= 1000) {
                return `${(this._estimatedPopulation / 1000).toFixed(1)}k osób`;
            } else {
                return `${this._estimatedPopulation} osób`;
            }
        },

        /**
         * Formatuje tekst biznesowy
         */
        getFormattedBusinessText() {
            if (!this._businessMetrics) {
                return '';
            }
            const m = this._businessMetrics;
            return `${m.potentialClients} potencjalnych klientów | ${m.areaType}`;
        },

        /**
         * Sugestie adresów z globalnego state
         */
        get currentSuggestions() {
            return window.WizardState?.get('location.addressSuggestions') || [];
        },

        /**
         * Index wybranej sugestii
         */
        get selectedIndex() {
            return window.WizardState?.get('location.selectedSuggestionIndex') || -1;
        },

        /**
         * Czy sugestie są widoczne
         */
        get suggestionsVisible() {
            return this.currentSuggestions.length > 0;
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Aktualizuje promień obsługi
         */
        updateRadius(newRadius) {
            // Update global state
            window.WizardState.update('location.serviceRadius', newRadius);

            // Update local reactive properties FIRST (for immediate Alpine.js reactivity)
            this._localServiceRadius = newRadius;
            this._localRadiusLabel = newRadius + ' km';

            // Update Alpine.js reactive properties
            this.radiusLabel = newRadius + ' km';

            this.updateDerivedState();
            this.syncWithLivewire('serviceRadius', newRadius);

            // Aktualizuj mapę jeśli jest zainicjalizowana
            this.updateMapRadius();

            // Rozpocznij debounced AI population estimation (3 sekundy opóźnienia)
            this.debouncePopulationEstimate(newRadius);

            console.log('📏 Service radius updated:', newRadius + ' km');
        },

        /**
         * Wyszukuje adresy (async)
         */
        async searchAddress(query) {
            if (query.length < 3) {
                window.WizardState.update('location.addressSuggestions', []);
                window.WizardState.update('location.selectedSuggestionIndex', -1);
                return;
            }

            try {
                console.log('🔍 Searching address:', query);

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // Zwiększony timeout do 15 sekund

                // Użyj Laravel API endpoint który automatycznie wybierze właściwe źródło (lokalny/zewnętrzny Nominatim)
                const response = await fetch(`/api/location/search`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        query: query + ', Poland',
                        limit: 5
                    }),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    if (response.status === 429) {
                        console.warn('⚠️ Rate limit exceeded - zbyt dużo zapytań do Nominatim API');
                        window.WizardState.update('location.addressSuggestions', []);
                        window.WizardState.update('location.selectedSuggestionIndex', -1);
                        return;
                    }
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                // Laravel API zwraca format: { success: true, data: [...] }
                if (!result.success) {
                    throw new Error(result.message || 'API request failed');
                }

                const data = result.data || [];

                const suggestions = data.map(item => ({
                    display_name: this.formatPolishAddress(item.display_name, item.address),
                    original_display_name: item.display_name, // zachowaj oryginalny dla debugowania
                    lat: parseFloat(item.lat),
                    lon: parseFloat(item.lon),
                    address: item.address || null
                }));

                window.WizardState.update('location.addressSuggestions', suggestions);
                window.WizardState.update('location.selectedSuggestionIndex', -1);

                console.log('📍 Address suggestions found:', suggestions.length);

            } catch (error) {
                if (error.name === 'AbortError') {
                    console.warn('⚠️ Address search timeout - API Nominatim nie odpowiada');
                } else if (error.message.includes('CONNECTION_TIMED_OUT')) {
                    console.warn('⚠️ Problemy z połączeniem do API Nominatim - sprawdź internet');
                } else {
                    console.error('❌ Address search error:', error);
                }
                window.WizardState.update('location.addressSuggestions', []);
                window.WizardState.update('location.selectedSuggestionIndex', -1);
            }
        },

        /**
         * Wybiera adres z sugestii
         */
        selectAddress(suggestion) {
            const address = suggestion.display_name;

            // Update WizardState
            window.WizardState.update('location.address', address);
            window.WizardState.update('location.latitude', suggestion.lat);
            window.WizardState.update('location.longitude', suggestion.lon);
            window.WizardState.update('location.addressSuggestions', []);
            window.WizardState.update('location.selectedSuggestionIndex', -1);

            // Update local reactive property for Alpine.js
            this._localAddress = address;

            this.updateDerivedState();
            this.syncWithLivewire('address', address);

            console.log('📍 Address selected and local property updated:', address);
        },

        /**
         * Aktualizuje adres ręcznie
         */
        updateAddress(value) {
            // Update WizardState
            window.WizardState.update('location.address', value);

            // Update local reactive property for Alpine.js
            this._localAddress = value;

            this.updateDerivedState();
            this.syncWithLivewire('address', value);

            // Trigger search po debounce
            this.debounceSearch(value);

            console.log('📝 Address manually updated:', value);
        },

        /**
         * Debounced search
         */
        debounceSearch(query) {
            // Clear previous timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.searchTimeout = setTimeout(() => {
                this.searchAddress(query);
            }, 300);
        },

        /**
         * Debounced AI population estimate (3 sekundy) - używa nowego systemu AI
         */
        debouncePopulationEstimate(newRadius) {
            // Clear previous timeout
            if (this.populationTimeout) {
                clearTimeout(this.populationTimeout);
            }

            console.log('⏱️ AI Population estimate delayed for 3 seconds...');

            this.populationTimeout = setTimeout(() => {
                const lat = window.WizardState.get('location.latitude') || window.WizardState.get('location.coordinates.lat') || 52.2297;
                const lng = window.WizardState.get('location.longitude') || window.WizardState.get('location.coordinates.lng') || 21.0122;
                console.log('🚀 Starting AI population estimate after delay');
                this.estimatePopulation(lat, lng, newRadius);
            }, 3000);
        },

        /**
         * Ręczne odświeżenie estymacji populacji - wywoływane przez przycisk w sidebarze
         */
        manualRefreshEstimation() {
            const lat = window.WizardState.get('location.latitude') || window.WizardState.get('location.coordinates.lat') || 52.2297;
            const lng = window.WizardState.get('location.longitude') || window.WizardState.get('location.coordinates.lng') || 21.0122;
            const radius = window.WizardState.get('location.serviceRadius') || 10;

            console.log('🔄 Manual refresh estimation triggered:', { lat, lng, radius });

            // Wylicz estymację od razu, bez opóźnienia
            this.estimatePopulation(lat, lng, radius);
        },

        /**
         * Nawigacja po sugestiach klawiszami
         */
        handleKeydown(event) {
            const suggestions = this.currentSuggestions;
            if (suggestions.length === 0) return;

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    const nextIndex = this.selectedIndex < suggestions.length - 1
                        ? this.selectedIndex + 1
                        : 0;
                    window.WizardState.update('location.selectedSuggestionIndex', nextIndex);
                    break;

                case 'ArrowUp':
                    event.preventDefault();
                    const prevIndex = this.selectedIndex > 0
                        ? this.selectedIndex - 1
                        : suggestions.length - 1;
                    window.WizardState.update('location.selectedSuggestionIndex', prevIndex);
                    break;

                case 'Enter':
                    event.preventDefault();
                    if (this.selectedIndex >= 0 && suggestions[this.selectedIndex]) {
                        this.selectAddress(suggestions[this.selectedIndex]);
                    }
                    break;

                case 'Escape':
                    event.preventDefault();
                    window.WizardState.update('location.addressSuggestions', []);
                    window.WizardState.update('location.selectedSuggestionIndex', -1);
                    break;
            }
        },

        /**
         * Ukrywa sugestie
         */
        hideSuggestions() {
            // Delay to allow click events on suggestions
            setTimeout(() => {
                window.WizardState.update('location.addressSuggestions', []);
                window.WizardState.update('location.selectedSuggestionIndex', -1);
            }, 200);
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const address = this.currentAddress;
            const radius = this.serviceRadius;
            const isValid = address.length > 10 && radius > 0;

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 5) {
                window.WizardState.state.meta.canProceed = isValid;
                window.WizardState.state.meta.isValid = isValid;
            }
        },

        /**
         * Synchronizacja z Livewire
         */
        syncWithLivewire(property, value) {
            if (window.Livewire && this.$wire) {
                try {
                    this.$wire.set(property, value, false);
                } catch (error) {
                    console.error('🔄 Livewire sync error:', error);
                }
            }
        },

        // === UI HELPER METHODS ===

        /**
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.currentAddress.length > 10 && this.serviceRadius > 0;
        },

        /**
         * Zwraca klasy CSS dla sugestii
         */
        getSuggestionClasses(index) {
            return {
                'bg-emerald-50 border-emerald-200': index === this.selectedIndex,
                'bg-white border-gray-200': index !== this.selectedIndex,
                'hover:bg-gray-50': index !== this.selectedIndex
            };
        },

        /**
         * Zwraca klasy CSS dla input adresu
         */
        getAddressInputClasses() {
            return {
                'border-emerald-500 bg-emerald-50': this.isStepValid(),
                'border-gray-300': !this.isStepValid()
            };
        },

        /**
         * Określa właściwy przedrostek dla ulicy (ul./al./pl. itp.)
         */
        getStreetPrefix(streetName) {
            if (!streetName) return 'ul.';

            const street = streetName.toLowerCase();

            // Aleje
            if (street.includes('aleja') || street.includes('aleje') ||
                street.includes('aleji') || street.includes('al.') ||
                street.match(/^al\s/) || street.match(/\sal\s/) ||
                street.includes('avenue') || street.includes('boulevard')) {
                return 'al.';
            }

            // Place
            if (street.includes('plac') || street.includes('place') ||
                street.includes('pl.') || street.match(/^pl\s/)) {
                return 'pl.';
            }

            // Ronda
            if (street.includes('rondo') || street.includes('rond')) {
                return 'rondo';
            }

            // Osiedla
            if (street.includes('osiedle') || street.includes('os.')) {
                return 'os.';
            }

            // Domyślnie ulica
            return 'ul.';
        },

        /**
         * Formatuje adres w polskim standardzie wieloliniowym z pełnymi danymi
         */
        formatPolishAddress(displayName, address = null) {
            if (!displayName) return '';

            console.log('🏷️ Formatuje adres:', { displayName, address });

            // Jeśli mamy szczegółowe dane z addressdetails=1
            if (address && typeof address === 'object') {
                const addressLines = [];

                // 1. Ulica i numer domu w formacie "ul./al. Nazwa numer"
                console.log('🛣️ Street data:', {
                    road: address.road,
                    house_number: address.house_number
                });

                if (address.road && address.house_number) {
                    const streetPrefix = this.getStreetPrefix(address.road);
                    // Sprawdź czy house_number nie wygląda na błędną wartość
                    const houseNumber = this.cleanHouseNumber(address.house_number);
                    if (houseNumber) {
                        addressLines.push(`${streetPrefix} ${address.road} ${houseNumber}`);
                    } else {
                        // Jeśli numer domu jest problematyczny, dodaj tylko ulicę
                        addressLines.push(`${streetPrefix} ${address.road}`);
                    }
                } else if (address.road) {
                    const streetPrefix = this.getStreetPrefix(address.road);
                    addressLines.push(`${streetPrefix} ${address.road}`);
                }

                // 2. Kod pocztowy + miejscowość w formacie "kod Miejscowość"
                const postcode = address.postcode;

                // Debug informacje o dostępnych polach miejscowości
                console.log('📍 Debug address fields:', {
                    city: address.city,
                    town: address.town,
                    village: address.village,
                    suburb: address.suburb,
                    neighbourhood: address.neighbourhood,
                    state: address.state
                });

                // Próbuj znaleźć właściwą miejscowość - z detekcją dzielnic Warszawy
                let locality = address.city || address.town || address.village;
                let district = null; // Opcjonalna nazwa dzielnicy

                // Sprawdź czy to dzielnica Warszawy
                const possibleDistrict = address.suburb || address.neighbourhood || address.city_district || locality;
                if (possibleDistrict && this.isWarsawDistrict(possibleDistrict)) {
                    console.log('🏙️ Wykryto dzielnicę Warszawy:', possibleDistrict);
                    locality = 'Warszawa';
                    district = possibleDistrict;
                }

                // Jeśli nie ma głównej miejscowości, spróbuj wyekstraktować z innych pól
                if (!locality) {
                    // Dla Warszawy, często miasto jest w suburb lub state
                    if (address.state && address.state.toLowerCase().includes('mazowieckie')) {
                        locality = 'Warszawa'; // Fallback dla województwa mazowieckiego
                    } else if (address.suburb && this.looksLikeCityName(address.suburb)) {
                        locality = address.suburb;
                    } else if (address.neighbourhood && this.looksLikeCityName(address.neighbourhood)) {
                        locality = address.neighbourhood;
                    }
                }

                console.log('🎯 Selected locality:', locality, 'district:', district);

                if (postcode && locality) {
                    addressLines.push(`${postcode} ${locality}`);
                } else if (locality) {
                    addressLines.push(locality);
                } else if (postcode) {
                    // Jeśli mamy tylko kod pocztowy, dodaj domyślną miejscowość
                    const fallbackCity = this.guessCityFromPostcode(postcode);
                    addressLines.push(`${postcode} ${fallbackCity}`);
                }

                // 3. Powiat (jeśli dostępny)
                if (address.county) {
                    const county = address.county.replace('powiat ', '').trim();
                    addressLines.push(`powiat ${county.toLowerCase()}`);
                }

                // 4. Województwo w formacie "woj. nazwa"
                if (address.state) {
                    const state = address.state.replace('województwo ', '').trim();
                    addressLines.push(`woj. ${state.toLowerCase()}`);
                }

                // Jeśli nie ma żadnych części, użyj fallback
                if (addressLines.length === 0) {
                    console.log('🔄 Brak structured address data, fallback to display_name');
                    return this.formatDisplayName(displayName);
                }

                const formatted = addressLines.join('\n');
                console.log('✨ Sformatowany adres z address details (wieloliniowy):', formatted);
                return formatted;
            }

            // Fallback - parsuj display_name
            return this.formatDisplayName(displayName);
        },

        /**
         * Parsuje i formatuje display_name z Nominatim w polskim standardzie
         */
        formatDisplayName(displayName) {
            if (!displayName) return '';

            const parts = displayName.split(',').map(part => part.trim());
            console.log('🔍 Parsing display_name parts:', parts);

            // Usuń "Poland/Polska" z końca
            if (parts.length > 0 &&
                (parts[parts.length - 1].toLowerCase() === 'poland' ||
                 parts[parts.length - 1].toLowerCase() === 'polska')) {
                parts.pop();
            }

            // Znajdź poszczególne komponenty adresu
            let street = '';
            let houseNumber = '';
            let postcode = '';
            let city = '';
            let state = '';

            // Regex patterns
            const postcodePattern = /^\d{2}-\d{3}$/;
            const houseNumberPattern = /^\d+[a-zA-Z]?$/;
            const statePattern = /województwo\s+(.+)/i;

            for (const part of parts) {
                // Kod pocztowy
                if (postcodePattern.test(part)) {
                    postcode = part;
                }
                // Województwo
                else if (statePattern.test(part)) {
                    const match = part.match(statePattern);
                    if (match) {
                        state = match[1].trim();
                    }
                }
                // Numer domu (jeśli jest na początku)
                else if (houseNumberPattern.test(part) && !houseNumber) {
                    houseNumber = part;
                }
                // Pomiń gminy, powiaty, osiedla
                else if (part.toLowerCase().includes('gmina') ||
                         part.toLowerCase().includes('powiat') ||
                         part.toLowerCase().includes('osiedle')) {
                    continue;
                }
                // Ulica (część która nie jest numerem ani kodem)
                else if (!street && !postcodePattern.test(part) && !statePattern.test(part)) {
                    // Sprawdź czy to może być nazwa ulicy
                    if (part.length > 1 && !houseNumberPattern.test(part)) {
                        street = part;
                    }
                }
                // Miasto (pierwsza sensowna część która nie jest ulicą)
                else if (!city && part.length > 1 &&
                         !postcodePattern.test(part) &&
                         !statePattern.test(part) &&
                         !houseNumberPattern.test(part) &&
                         part !== street) {
                    city = part;
                }
            }

            // Sprawdź czy miasto to dzielnica Warszawy
            if (city && this.isWarsawDistrict(city)) {
                console.log('🏙️ Wykryto dzielnicę Warszawy w fallback parsing:', city);
                city = 'Warszawa';
            }

            // Składaj adres w polskim formacie wieloliniowym
            const addressLines = [];
            let county = '';

            // Znajdź powiat w parts
            for (const part of parts) {
                if (part.toLowerCase().includes('powiat')) {
                    county = part.replace('powiat ', '').trim();
                    break;
                }
            }

            // 1. Ulica + numer z odpowiednim prefiksem (ul./al.)
            if (street && houseNumber) {
                const streetPrefix = this.getStreetPrefix(street);
                addressLines.push(`${streetPrefix} ${street} ${houseNumber}`);
            } else if (street) {
                const streetPrefix = this.getStreetPrefix(street);
                addressLines.push(`${streetPrefix} ${street}`);
            }

            // 2. Kod pocztowy + miasto
            if (postcode && city) {
                addressLines.push(`${postcode} ${city}`);
            } else if (city) {
                addressLines.push(city);
            } else if (postcode) {
                addressLines.push(postcode);
            }

            // 3. Powiat
            if (county) {
                addressLines.push(`powiat ${county.toLowerCase()}`);
            }

            // 4. Województwo
            if (state) {
                addressLines.push(`woj. ${state.toLowerCase()}`);
            }

            // Fallback - jeśli nie udało się sparsować, weź pierwsze sensowne części
            if (addressLines.length === 0) {
                const fallbackParts = parts.slice(0, 3).filter(part =>
                    part.length > 0 &&
                    !part.toLowerCase().includes('gmina') &&
                    !part.toLowerCase().includes('powiat') &&
                    !part.toLowerCase().includes('osiedle')
                );
                const formatted = fallbackParts.join('\n');
                console.log('🔄 Fallback parsed display_name (wieloliniowy):', formatted);
                return formatted;
            }

            const formatted = addressLines.join('\n');
            console.log('✨ Smart parsed display_name (wieloliniowy):', formatted);
            return formatted;
        },

        /**
         * Formatuje suggestion dla wyświetlenia
         */
        formatSuggestion(suggestion) {
            const formatted = this.formatPolishAddress(suggestion.display_name);
            // Skróć długie adresy
            if (formatted.length > 60) {
                return formatted.substring(0, 57) + '...';
            }
            return formatted;
        },

        // === HELPER METHODS ===

        /**
         * Zwraca podsumowanie lokalizacji
         */
        getLocationSummary() {
            return {
                address: this.currentAddress,
                serviceRadius: this.serviceRadius,
                radiusLabel: this.radiusLabel,
                hasCoordinates: !!(window.WizardState.get('location.latitude') && window.WizardState.get('location.longitude')),
                isComplete: this.isStepValid()
            };
        },


        // === MAP FUNCTIONALITY ===

        /**
         * Inicjalizuje mapę z lokalizacją i promieniem obsługi
         */
        async initializeMap() {
            console.log('🗺️ Attempting map initialization:', {
                mapInitialized: this.mapInitialized,
                currentAddress: this.currentAddress,
                addressLength: this.currentAddress?.length || 0
            });

            if (this.mapInitialized) {
                console.log('🗺️ Map already initialized, skipping');
                return;
            }

            try {
                // Załaduj bibliotekę Leaflet
                await this.loadLeafletLibrary();

                // Pobierz koordynaty z global state oraz Livewire jako fallback
                const stateLat = window.WizardState.get('location.latitude') || window.WizardState.get('location.coordinates.lat');
                const stateLng = window.WizardState.get('location.longitude') || window.WizardState.get('location.coordinates.lng');

                // Fallback do Livewire
                const livewireLat = this.$wire?.latitude;
                const livewireLng = this.$wire?.longitude;

                // Użyj pierwszych dostępnych koordynatów
                const lat = stateLat || livewireLat;
                const lng = stateLng || livewireLng;

                console.log('🗺️ Koordynaty z różnych źródeł:', {
                    stateLat, stateLng,
                    livewireLat, livewireLng,
                    finalLat: lat, finalLng: lng
                });

                // Jeśli znaleziono koordynaty z Livewire ale nie ma ich w WizardState, zsynchronizuj
                if (lat && lng && (!stateLat || !stateLng)) {
                    console.log('🔄 Synchronizing coordinates from Livewire to WizardState:', { lat, lng });
                    window.WizardState.update('location.latitude', lat);
                    window.WizardState.update('location.longitude', lng);
                }

                // Synchronizuj również adres jeśli istnieje w Livewire
                const livewireAddress = this.$wire?.address;
                const stateAddress = window.WizardState.get('location.address');
                if (livewireAddress && !stateAddress) {
                    console.log('🔄 Synchronizing address from Livewire to WizardState:', livewireAddress);
                    window.WizardState.update('location.address', livewireAddress);
                }

                // Fallback do domyślnych koordynatów Warszawy jeśli brak
                const finalLat = lat || 52.2297;
                const finalLng = lng || 21.0122;
                const hasRealCoords = !!(lat && lng);

                console.log('🗺️ Final coordinates:', {
                    finalLat,
                    finalLng,
                    hasRealCoords,
                    radius: this.serviceRadius
                });

                // Inicjalizuj mapę z bezpieczną konfiguracją
                this.map = L.map('wizard-step5-map', {
                    doubleClickZoom: false, // Wyłącz problematyczny double-click zoom
                    touchZoom: true,
                    scrollWheelZoom: true,
                    dragging: true
                }).setView([finalLat, finalLng], 13);

                // Dodaj tiles OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                // Dodaj marker dla lokalizacji użytkownika - biało-zielone kółeczko
                const userIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: '<div style="background-color: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                    popupAnchor: [0, -10]
                });

                const popupText = hasRealCoords
                    ? '<strong>Twoja lokalizacja</strong><br>' + this.currentAddress
                    : '<strong>Domyślna lokalizacja</strong><br>Wprowadź adres aby zobaczyć mapę';

                this.userMarker = L.marker([finalLat, finalLng], {
                    icon: userIcon,
                    draggable: true
                })
                    .addTo(this.map)
                    .bindPopup(popupText);

                // Dodaj event listenery dla przeciągania markera
                this.userMarker.on('dragstart', (e) => {
                    console.log('🎯 Rozpoczęto przeciąganie markera');
                    e.target.closePopup(); // Zamknij popup podczas przeciągania
                });

                this.userMarker.on('drag', (e) => {
                    const position = e.target.getLatLng();
                    console.log('🔄 Przeciąganie markera:', position.lat, position.lng);

                    // Aktualizuj okrąg w czasie rzeczywistym podczas przeciągania
                    if (this.radiusCircle) {
                        this.map.removeLayer(this.radiusCircle);
                    }
                    this.radiusCircle = L.circle([position.lat, position.lng], {
                        radius: this.serviceRadius * 1000,
                        fillColor: '#10b981',
                        color: '#10b981',
                        weight: 2,
                        opacity: 0.6,
                        fillOpacity: 0.1
                    }).addTo(this.map);
                });

                this.userMarker.on('dragend', (e) => {
                    const newPosition = e.target.getLatLng();
                    console.log('✅ Marker przesunięty na:', newPosition.lat, newPosition.lng);

                    // Aktualizuj coordinates w global state
                    window.WizardState.update('location.latitude', newPosition.lat);
                    window.WizardState.update('location.longitude', newPosition.lng);
                    console.log('📍 Koordynaty zaktualizowane w WizardState');

                    // Aktualizuj okrąg promienia obsługi (finalna pozycja)
                    this.addRadiusCircle(newPosition.lat, newPosition.lng);
                    console.log('⭕ Okrąg promienia zaktualizowany');

                    // Pobierz adres dla nowych koordynatów (reverse geocoding)
                    console.log('🔍 Rozpoczynam reverse geocoding...');
                    this.reverseGeocode(newPosition.lat, newPosition.lng);

                    // Sync z Livewire
                    this.syncWithLivewire('latitude', newPosition.lat);
                    this.syncWithLivewire('longitude', newPosition.lng);
                    console.log('🔄 Dane zsynchronizowane z Livewire');

                    // Rozpocznij debounced AI population estimation po przesunięciu markera (3 sekundy)
                    const currentRadius = window.WizardState.get('location.serviceRadius') || this.serviceRadius;
                    console.log('🤖 Rozpoczynam debounced AI estimation (3s delay) dla promienia:', currentRadius, 'km');
                    this.debouncePopulationEstimate(currentRadius);

                    console.log('🎯 Marker drag completed - wszystkie operacje zainicjowane');
                });

                // Dodaj okrąg promienia obsługi używając finalnych koordynatów
                this.addRadiusCircle(finalLat, finalLng);

                this.mapInitialized = true;
                console.log('✅ Mapa step 5 zainicjalizowana');

                // Estymuj populację dla początkowej lokalizacji
                this.estimatePopulation(finalLat, finalLng, this.serviceRadius);

            } catch (error) {
                console.error('❌ Błąd inicjalizacji mapy step 5:', error);
            }
        },

        /**
         * Dodaje okrąg promienia obsługi dla konkretnych koordynatów
         */
        addRadiusCircle(lat, lng) {
            if (!this.map) return;

            // Usuń poprzedni okrąg jeśli istnieje
            if (this.radiusCircle) {
                this.map.removeLayer(this.radiusCircle);
            }

            // Dodaj nowy okrąg
            this.radiusCircle = L.circle([lat, lng], {
                radius: this.serviceRadius * 1000, // Konwersja km na metry
                fillColor: '#10b981',
                color: '#10b981',
                weight: 2,
                opacity: 0.6,
                fillOpacity: 0.1
            }).addTo(this.map);

            // Dostosuj widok mapy do okręgu
            const bounds = this.radiusCircle.getBounds();
            this.map.fitBounds(bounds, { padding: [20, 20] });

            console.log('🗺️ Radius circle added:', { lat, lng, radius: this.serviceRadius });
        },

        /**
         * Aktualizuje okrąg promienia obsługi na mapie
         */
        updateRadiusCircle() {
            if (!this.map || !this.mapInitialized) return;

            const lat = window.WizardState.get('location.latitude') || window.WizardState.get('location.coordinates.lat') || 52.2297;
            const lng = window.WizardState.get('location.longitude') || window.WizardState.get('location.coordinates.lng') || 21.0122;

            this.addRadiusCircle(lat, lng);
        },

        /**
         * Załadowanie biblioteki Leaflet
         */
        async loadLeafletLibrary() {
            return new Promise((resolve) => {
                if (window.L) {
                    resolve();
                    return;
                }

                // Dodaj CSS Leaflet
                if (!document.querySelector('link[href*="leaflet"]')) {
                    const leafletCSS = document.createElement('link');
                    leafletCSS.rel = 'stylesheet';
                    leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(leafletCSS);
                }

                // Dodaj JavaScript Leaflet
                const leafletJS = document.createElement('script');
                leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                leafletJS.onload = () => {
                    console.log('✅ Biblioteka Leaflet załadowana');
                    resolve();
                };
                leafletJS.onerror = () => {
                    console.error('❌ Błąd ładowania biblioteki Leaflet');
                    resolve(); // Resolve anyway to prevent hanging
                };
                document.head.appendChild(leafletJS);
            });
        },

        /**
         * Aktualizuje mapę gdy zmieni się promień obsługi
         */
        updateMapRadius() {
            this.updateRadiusCircle();
        },

        /**
         * Aktualizuje mapę gdy zmieni się lokalizacja
         */
        updateMapLocation() {
            if (!this.map || !this.mapInitialized) {
                // Reinicjalizuj mapę z nową lokalizacją
                this.mapInitialized = false;
                this.initializeMap();
                return;
            }

            const lat = window.WizardState.get('location.latitude') || window.WizardState.get('location.coordinates.lat');
            const lng = window.WizardState.get('location.longitude') || window.WizardState.get('location.coordinates.lng');

            if (lat && lng) {
                // Przenieś mapę do nowej lokalizacji
                this.map.setView([lat, lng], 13);
                this.updateRadiusCircle();
            }
        },

        /**
         * Reverse geocoding - pobierz adres z koordynatów
         */
        async reverseGeocode(lat, lng) {
            try {
                console.log('🔍 Reverse geocoding dla:', lat, lng);

                // Dodaj opóźnienie i timeout żeby uniknąć rate-limitów
                await new Promise(resolve => setTimeout(resolve, 1000)); // 1 sekunda opóźnienia

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // Zwiększony timeout do 15 sekund

                // Użyj Laravel API endpoint dla reverse geocoding
                const response = await fetch(`/api/location/reverse`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        lat: lat,
                        lon: lng
                    }),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    console.error('❌ Response not OK:', {
                        status: response.status,
                        statusText: response.statusText,
                        url: response.url,
                        headers: Object.fromEntries(response.headers.entries())
                    });

                    if (response.status === 429) {
                        console.warn('⚠️ Rate limit exceeded - zbyt dużo zapytań do Nominatim API');
                        return;
                    }

                    if (response.status === 404) {
                        console.error('❌ Endpoint not found - sprawdź routing Laravel');
                        console.error('❌ Expected route: POST /api/location/reverse');
                        throw new Error('Endpoint /api/location/reverse nie istnieje (404)');
                    }

                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                // Laravel API zwraca format: { success: true, data: {...} }
                if (!result.success) {
                    throw new Error(result.message || 'Reverse geocoding API request failed');
                }

                const data = result.data;

                if (data && data.display_name) {
                    const rawAddress = data.display_name;
                    const newAddress = this.formatPolishAddress(rawAddress, data.address);

                    console.log('📍 Surowy adres z reverse geocoding:', rawAddress);
                    console.log('✨ Sformatowany adres:', newAddress);
                    console.log('🏙️ Structured address data from API:', data);

                    // Przechowaj strukturowane dane z reverse geocoding dla użycia w estymacji populacji
                    window.WizardState.update('location.lastReverseGeocodeData', data);
                    console.log('💾 Dane reverse geocoding zapisane w WizardState');

                    // Aktualizuj adres w global state
                    window.WizardState.update('location.address', newAddress);
                    console.log('✅ Adres zaktualizowany w WizardState:', newAddress);

                    // Update local reactive property for Alpine.js - KLUCZOWE dla reaktywności!
                    this._localAddress = newAddress;
                    console.log('✅ Adres zaktualizowany w _localAddress (reactive):', newAddress);

                    // Sync z Livewire
                    this.syncWithLivewire('address', newAddress);

                    // Aktualizuj popup markera
                    if (this.userMarker) {
                        this.userMarker.setPopupContent('<strong>Twoja lokalizacja</strong><br>' + newAddress);
                    }

                    // Dispatch event dla komponentów które nasłuchują
                    this.$nextTick(() => {
                        this.$dispatch('address-updated', { address: newAddress });
                        console.log('🔄 Address updated event dispatched');
                    });
                }

            } catch (error) {
                if (error.name === 'AbortError') {
                    console.warn('⚠️ Reverse geocoding timeout - API Nominatim nie odpowiada');
                } else if (error.message.includes('CONNECTION_TIMED_OUT')) {
                    console.warn('⚠️ Problemy z połączeniem do API Nominatim - sprawdź internet');
                } else {
                    console.error('❌ Błąd reverse geocoding:', error);
                    console.error('❌ Request details:', {
                        url: '/api/location/reverse',
                        method: 'POST',
                        lat: lat,
                        lon: lng,
                        baseURL: window.location.origin,
                        fullURL: window.location.origin + '/api/location/reverse',
                        error: error.message,
                        stack: error.stack
                    });
                }
            }
        },

        /**
         * Estymuje populację w obszarze obsługi używając lokalnego AI
         */
        async estimatePopulation(lat, lng, radiusKm) {
            try {
                console.log('🤖 AI Population Estimation - start:', { lat, lng, radiusKm });

                // ZAWSZE parsuj cityName - używaj strukturowanych danych z API, potem fallback
                let cityName = 'Warszawa'; // domyślny fallback

                // PIERWSZA PRÓBA: Użyj strukturowanych danych z reverse geocoding API
                const lastGeocodeData = window.WizardState?.get('location.lastReverseGeocodeData');
                if (lastGeocodeData) {
                    // Sprawdź różne pola które mogą zawierać nazwę miasta/dzielnicy
                    const possibleCity = lastGeocodeData.city || lastGeocodeData.town ||
                                        lastGeocodeData.suburb || lastGeocodeData.city_district;

                    if (possibleCity) {
                        // Jeśli to dzielnica Warszawy, użyj "Warszawa"
                        if (this.isWarsawDistrict(possibleCity)) {
                            cityName = 'Warszawa';
                            console.log('🏙️ Wykryto dzielnicę Warszawy w API data:', possibleCity, '→ Warszawa');
                        } else {
                            cityName = possibleCity;
                        }
                        console.log('🎯 City extracted from API structured data:', cityName);
                        console.log('📊 Available API fields:', {
                            city: lastGeocodeData.city,
                            town: lastGeocodeData.town,
                            suburb: lastGeocodeData.suburb,
                            district: lastGeocodeData.city_district,
                            state: lastGeocodeData.state,
                            country: lastGeocodeData.country
                        });
                    }
                }

                if (!cityName || cityName === 'Warszawa') {
                    console.log('🔄 No structured API data available, fallback to address parsing');

                    // DRUGA PRÓBA: Parsuj miasto z nowego formatu wieloliniowego adresu
                    const currentAddress = this.currentAddress;
                    if (currentAddress) {
                        console.log('🏠 Parsing city from multiline address:', currentAddress);

                        // Podziel na linie (format wieloliniowy)
                        const addressLines = currentAddress.split('\n').map(line => line.trim());
                        console.log('📍 Address lines:', addressLines);

                        // Znajdź linię z kodem pocztowym i miastem (druga linia zwykle)
                        const cityLine = addressLines.find(line =>
                            line.match(/^\d{2}-\d{3}\s+/) // linia zaczynająca się od kodu pocztowego
                        );

                        if (cityLine) {
                            // Wyciągnij miasto z linii "00-000 NazwaMiasta"
                            const cityMatch = cityLine.match(/^\d{2}-\d{3}\s+(.+)$/);
                            if (cityMatch) {
                                let extractedCity = cityMatch[1].trim();
                                // Sprawdź czy to dzielnica Warszawy
                                if (this.isWarsawDistrict(extractedCity)) {
                                    console.log('🏙️ Wykryto dzielnicę Warszawy:', extractedCity, '→ Warszawa');
                                    cityName = 'Warszawa';
                                } else {
                                    cityName = extractedCity;
                                }
                                console.log('✅ City extracted from postal code line:', cityName);
                            }
                        } else {
                            // TRZECIA PRÓBA: Fallback - szukaj miasta w starym stylu (kompatybilność wsteczna)
                            console.log('🔄 Using fallback city parsing method');
                            const addressParts = currentAddress.split(/[,\n]/).map(part => part.trim());
                            const filteredParts = addressParts.filter(part =>
                                part.length > 2 &&
                                !part.match(/^ul\./) && // nie ulica
                                !part.match(/^\d/) && // nie zaczyna się cyfrą
                                !part.match(/^\d{2}-\d{3}$/) && // nie sam kod pocztowy
                                !part.toLowerCase().includes('poland') &&
                                !part.toLowerCase().includes('polska') &&
                                !part.toLowerCase().includes('gmina') &&
                                !part.toLowerCase().includes('powiat') &&
                                !part.toLowerCase().includes('województwo') &&
                                !part.toLowerCase().includes('osiedle')
                            );

                            // Strategia wyboru miasta - preferuj charakterystyczne końcówki
                            let potentialCity = filteredParts.find(part =>
                                part.toLowerCase().match(/ów$|ice$|owo$|awa$|burg$|grad$|usk$/) ||
                                part.toLowerCase().includes('nowy ') ||
                                part.toLowerCase().includes('stary ')
                            );

                            if (!potentialCity && filteredParts.length > 0) {
                                potentialCity = filteredParts.reduce((longest, current) =>
                                    current.length > longest.length ? current : longest
                                );
                            }

                            if (potentialCity) {
                                // Sprawdź czy to dzielnica Warszawy
                                if (this.isWarsawDistrict(potentialCity)) {
                                    console.log('🏙️ Wykryto dzielnicę Warszawy w fallback:', potentialCity, '→ Warszawa');
                                    cityName = 'Warszawa';
                                } else {
                                    cityName = potentialCity;
                                }
                                console.log('✅ Fallback city extracted:', cityName);
                            }
                        }

                        console.log('🎯 Final selected city name:', cityName);
                    }

                    // Cache city name w state
                    window.WizardState.update('location.cityName', cityName);
                }

                // CZWARTA PRÓBA: Dodatkowe fallback-i dla problematycznych przypadków
                cityName = this.improveAndValidateCityName(cityName);

                console.log('🏙️ City identified for AI estimation:', cityName);

                // Wywołaj AI API endpoint
                const response = await fetch('/api/location/estimate-population', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        city: cityName,
                        radius: radiusKm,
                        address: this.currentAddress
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || response.statusText}`);
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'API request failed');
                }

                const populationData = result.data;
                console.log('🤖 AI Population Estimation - response:', populationData);

                console.log('🔍 API Response struktura:', populationData);
                console.log('🔍 Szczegółowa analiza pól API:', {
                    'estimated_population': populationData.estimated_population,
                    'population': populationData.population,
                    'households': populationData.households,
                    'pet_owners': populationData.pet_owners,
                    'potential_clients': populationData.potential_clients,
                    'area_type': populationData.area_type,
                    'notes': populationData.notes,
                    'wszystkie_klucze': Object.keys(populationData)
                });
                console.log('📊 Rozpoczynam konwersję danych API do metrics...');

                // Przekształć AI response na format zgodny z obecnym kodem
                // API zwraca: { estimated_population, households, pet_owners, potential_clients, area_type, notes, ... }
                const metrics = {
                    population: populationData.estimated_population, // ✅ Fixed: używamy estimated_population z API
                    areaKm2: populationData.area_km2 || Math.PI * 10 * 10,
                    densityPerKm2: populationData.density_per_km2 || Math.round(populationData.estimated_population / (Math.PI * 10 * 10)),
                    areaType: populationData.area_type || 'obszar miejski', // ✅ Fixed: area_type z API
                    households: populationData.households || Math.round(populationData.estimated_population / 2.3),
                    petOwningHouseholds: populationData.pet_owners || Math.round((populationData.estimated_population / 2.3) * 0.38), // ✅ Fixed: pet_owners z API
                    potentialClients: populationData.potential_clients || Math.round((populationData.estimated_population / 2.3) * 0.38 * 0.15), // ✅ Fixed: potential_clients z API
                    confidence: populationData.confidence || 'średnia',
                    aiGenerated: populationData.aiGenerated || true,
                    source: populationData.source || 'ai_estimation',
                    notes: populationData.notes || 'Brak dodatkowych informacji' // ✅ Added: notes z API
                };

                // Zaktualizuj lokalne właściwości - poprawka mapowania API response
                this._estimatedPopulation = populationData.estimated_population || populationData.population;
                this._businessMetrics = metrics;

                console.log('🔧 Fixed mapping:', {
                    'populationData.estimated_population': populationData.estimated_population,
                    'populationData.population': populationData.population,
                    'final _estimatedPopulation': this._estimatedPopulation
                });

                // Aktualizuj Alpine.js reactive properties
                this.updatePopulationTexts();

                console.log('🎯 AI Population data updated, triggering Alpine refresh...');
                console.log('📊 AI Estymacja populacji zakończona:', metrics);
                console.log('✅ Dane zapisane w WizardState:', {
                    populationText: window.WizardState.get('location.populationText'),
                    businessMetrics: window.WizardState.get('location.businessMetrics'),
                    businessMetricsFormatted: window.WizardState.get('location.businessMetricsFormatted')
                });

                return metrics;

            } catch (error) {
                console.error('❌ Błąd AI estymacji populacji:', error);

                // Fallback do prostej estymacji jeśli AI nie działa
                return this.estimatePopulationFallback(radiusKm);
            }
        },

        /**
         * Prosta estymacja populacji jako fallback gdy AI nie działa
         */
        estimatePopulationFallback(radiusKm) {
            console.log('🔄 Using fallback population estimation for radius:', radiusKm);

            // Oblicz powierzchnię koła (km²)
            const areaKm2 = Math.PI * Math.pow(radiusKm, 2);

            // Użyj średniej gęstości zaludnienia dla Polski (125 osób/km²)
            const densityPerKm2 = 1200; // gęstość dla obszarów podmiejskich
            const estimatedPopulation = Math.round(areaKm2 * densityPerKm2);

            // Podstawowe metryki biznesowe
            const averageHouseholdSize = 2.3;
            const petOwnershipRate = 0.38;
            const households = Math.round(estimatedPopulation / averageHouseholdSize);
            const petOwningHouseholds = Math.round(households * petOwnershipRate);
            const potentialClients = Math.round(petOwningHouseholds * 0.15);

            const metrics = {
                population: estimatedPopulation,
                areaKm2: Math.round(areaKm2 * 10) / 10,
                densityPerKm2,
                areaType: 'przedmieścia',
                households,
                petOwningHouseholds,
                potentialClients,
                confidence: 'niska',
                aiGenerated: false,
                source: 'fallback_estimation',
                notes: 'Estymacja fallback oparta na statystykach GUS'
            };

            // Zaktualizuj lokalne właściwości
            this._estimatedPopulation = estimatedPopulation;
            this._businessMetrics = metrics;

            // Aktualizuj Alpine.js reactive properties
            this.updatePopulationTexts();

            console.log('📊 Fallback estymacja populacji zakończona:', metrics);
            return metrics;
        },

        /**
         * Cleanup timeouts
         */
        destroy() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            if (this.populationTimeout) {
                clearTimeout(this.populationTimeout);
            }

            // Cleanup mapy
            if (this.map) {
                this.map.remove();
                this.map = null;
                this.mapInitialized = false;
            }
        },

        /**
         * Ulepszenia i walidacja nazwy miasta dla problematycznych przypadków
         */
        improveAndValidateCityName(cityName) {
            if (!cityName || cityName === 'Warszawa') {
                console.log('🔄 Using default fallback city');
                return 'Warszawa';
            }

            // Wyczyść nazwę miasta z niepotrzebnych części
            let cleanedCity = cityName.trim();

            // Usuń prefiksy administracyjne
            cleanedCity = cleanedCity.replace(/^(gmina|powiat|województwo|woj\.)\s+/gi, '');

            // Usuń sufiksy administracyjne w nawiasach
            cleanedCity = cleanedCity.replace(/\s*\([^)]*\)\s*$/, '');

            // Usuń dziwne końcówki jak "Municipality" lub "County"
            cleanedCity = cleanedCity.replace(/\s+(municipality|county|district)$/gi, '');

            // Przytnij wielokrotne spacje
            cleanedCity = cleanedCity.replace(/\s+/g, ' ').trim();

            // Walidacja czy to sensowna nazwa miasta
            if (this.isValidPolishCityName(cleanedCity)) {
                console.log(`✅ City name improved: "${cityName}" → "${cleanedCity}"`);
                return cleanedCity;
            }

            // Jeśli nazwa nie jest sensowna, spróbuj ekstraktować z oryginalnej nazwy
            const extractedCity = this.extractCityFromComplexName(cityName);
            if (extractedCity && this.isValidPolishCityName(extractedCity)) {
                console.log(`🔧 City extracted from complex name: "${cityName}" → "${extractedCity}"`);
                return extractedCity;
            }

            console.log(`⚠️ Could not improve city name "${cityName}", using fallback`);
            return 'Warszawa'; // Ultimate fallback
        },

        /**
         * Sprawdza czy nazwa jest sensowną polską nazwą miasta
         */
        isValidPolishCityName(name) {
            if (!name || name.length < 2 || name.length > 50) {
                return false;
            }

            // Sprawdź czy nie zawiera niepożądanych znaków
            if (/[0-9@#$%^&*()_+=\[\]{}|\\:";'<>?,./]/.test(name)) {
                return false;
            }

            // Sprawdź czy nie jest to oczywisty kod pocztowy, ulica itp.
            const invalidPatterns = [
                /^\d{2}-\d{3}$/,                    // kod pocztowy
                /^(ul\.|al\.|pl\.|os\.)/i,          // prefiksy ulic
                /^(north|south|east|west)/i,        // kierunki angielskie
                /^(poland|polska)$/i,               // nazwy krajów
                /^[0-9\-\s]+$/                      // same cyfry i myślniki
            ];

            for (const pattern of invalidPatterns) {
                if (pattern.test(name)) {
                    return false;
                }
            }

            // Sprawdź czy zawiera polskie końcówki miejscowości (pozytywna walidacja)
            const polishCityPatterns = [
                /-?ów$|-?owa$|-?owo$/i,            // końcówki polskich miast
                /-?ice$|-?icy$/i,                   // końcówki polskich miast
                /-?awa$|-?ew$|-?yn$|-?in$/i,       // końcówki polskich miast
                /^(nowy|nowa|stary|stara)\s/i,     // prefiksy typu "Nowy", "Stary"
                /[aąeęiłnśóuwyząę]/i               // polskie znaki w nazwie
            ];

            // Jeśli ma polskie wzorce, prawdopodobnie jest OK
            for (const pattern of polishCityPatterns) {
                if (pattern.test(name)) {
                    return true;
                }
            }

            // Dla pozostałych przypadków - przyjmij jeśli nie ma oczywistych problemów
            return name.split(' ').length <= 3; // max 3 słowa w nazwie miasta
        },

        /**
         * Ekstraktuje miasto ze złożonej nazwy
         */
        extractCityFromComplexName(complexName) {
            // Podziel na części i znajdź najbardziej prawdopodobną część
            const parts = complexName.split(/[,\-\(\)\/]/)
                .map(part => part.trim())
                .filter(part => part.length > 2);

            // Znajdź część która wygląda jak polskie miasto
            for (const part of parts) {
                const cleaned = part.replace(/^(ul\.|al\.|pl\.|os\.)\s*/i, '').trim();
                if (cleaned && this.isValidPolishCityName(cleaned)) {
                    return cleaned;
                }
            }

            // Sprawdź też pojedyncze słowa w długich nazwach
            const words = complexName.split(/\s+/)
                .filter(word => word.length > 3)
                .filter(word => !/^(ul|al|pl|os|nr|dom|mieszkanie)$/i.test(word));

            for (const word of words) {
                if (this.isValidPolishCityName(word)) {
                    return word;
                }
            }

            return null;
        },

        /**
         * Sprawdza czy string wygląda jak nazwa miasta
         */
        looksLikeCityName(name) {
            if (!name || name.length < 3) return false;

            // Sprawdź czy nie zawiera oczywistych oznak że to nie miasto
            const badPatterns = [
                /^\d/,                          // zaczyna się cyfrą
                /[0-9]{3,}/,                    // zawiera długie liczby
                /^(ul\.|al\.|pl\.|os\.)/i,     // prefiksy ulic
            ];

            for (const pattern of badPatterns) {
                if (pattern.test(name)) return false;
            }

            return true;
        },

        /**
         * Sprawdza czy nazwa to dzielnica Warszawy
         */
        isWarsawDistrict(name) {
            if (!name) return false;

            const normalized = name.toLowerCase()
                .replace(/\s+/g, ' ')
                .replace(/-/g, ' ')
                .trim();

            // 18 oficjalnych dzielnic Warszawy
            const officialDistricts = [
                'bemowo', 'białołęka', 'bialoleka', 'bielany', 'mokotów', 'mokotow',
                'ochota', 'praga południe', 'praga poludnie', 'praga północ', 'praga polnoc',
                'rembertów', 'rembertow', 'śródmieście', 'srodmiescie', 'targówek', 'targowek',
                'ursus', 'ursynów', 'ursynow', 'wawer', 'wesoła', 'wesola',
                'wilanów', 'wilanow', 'włochy', 'wlochy', 'wola', 'żoliborz', 'zoliborz'
            ];

            // Popularne osiedla i nieoficjalne nazwy
            const popularAreas = [
                'powiśle', 'powisle', 'powiśle solec', 'powisle solec',
                'saska kępa', 'saska kepa', 'kabaty', 'grochów', 'grochow',
                'marymont', 'żerań', 'zeran', 'tarchomin', 'muranów', 'muranow',
                'stara ochota', 'rakowiec', 'koło', 'kolo', 'sadyba', 'służewiec', 'sluzewiec',
                'gocław', 'goclaw', 'kamionek', 'sielce', 'filtry', 'czerniaków', 'czerniakow',
                'natolin', 'stegny', 'czyste', 'mirów', 'mirow', 'wierzbno',
                'jelonki', 'salomea', 'skorosze', 'okęcie', 'okecie',
                'bródno', 'brodno', 'zacisze', 'pelcowizna', 'prądnik', 'pradnik'
            ];

            // Sprawdź czy exact match
            if (officialDistricts.includes(normalized) || popularAreas.includes(normalized)) {
                return true;
            }

            // Sprawdź warianty z myślnikiem (np. "Praga-Południe")
            const dashVariants = [
                'praga-południe', 'praga-poludnie', 'praga-północ', 'praga-polnoc',
                'powiśle-solec', 'powisle-solec'
            ];

            if (dashVariants.includes(normalized.replace(/\s+/g, '-'))) {
                return true;
            }

            // Sprawdź czy zawiera nazwę dzielnicy
            for (const district of [...officialDistricts, ...popularAreas]) {
                if (normalized.includes(district) && district.length > 3) {
                    return true;
                }
            }

            return false;
        },

        /**
         * Zgaduje miasto na podstawie kodu pocztowego
         */
        guessCityFromPostcode(postcode) {
            if (!postcode) return 'Warszawa';

            // Podstawowe mapowanie kodów pocztowych na miasta
            const postcodeMap = {
                '00': 'Warszawa',
                '01': 'Warszawa',
                '02': 'Warszawa',
                '03': 'Warszawa',
                '04': 'Warszawa',
                '05': 'Warszawa', // obszar warszawski
                '80': 'Gdańsk',
                '81': 'Gdynia',
                '30': 'Kraków',
                '31': 'Kraków',
                '50': 'Wrocław',
                '60': 'Poznań',
                '90': 'Łódź',
                '40': 'Katowice'
            };

            const prefix = postcode.substring(0, 2);
            return postcodeMap[prefix] || 'Warszawa';
        },

        /**
         * Czyści i waliduje numer domu
         */
        cleanHouseNumber(houseNumber) {
            if (!houseNumber) return null;

            const cleaned = houseNumber.toString().trim();

            // Sprawdź czy to sensowny numer domu
            if (/^[0-9]+[a-zA-Z]?(\s*[\/\-]\s*[0-9]+[a-zA-Z]?)?$/.test(cleaned)) {
                return cleaned;
            }

            // Jeśli wygląda dziwnie, odrzuć
            console.log(`⚠️ Invalid house number rejected: "${houseNumber}"`);
            return null;
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep5;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep5 = wizardStep5;

    // Globalna funkcja pomocnicza do wywoływania manualRefreshEstimation
    window.wizardStep5.manualRefreshEstimation = function() {
        // Znajdź aktywny komponent Alpine.js na stronie
        const step5Container = document.querySelector('[x-data*="wizardStep5"]');
        if (step5Container && step5Container._x_dataStack) {
            const component = step5Container._x_dataStack[0];
            if (component && typeof component.manualRefreshEstimation === 'function') {
                console.log('🔄 Wywołuję manualRefreshEstimation z globalnej funkcji');
                component.manualRefreshEstimation();
            } else {
                console.warn('⚠️ Nie znaleziono metody manualRefreshEstimation w komponencie');
            }
        } else {
            console.warn('⚠️ Nie znaleziono aktywnego komponentu wizardStep5');
        }
    };
}