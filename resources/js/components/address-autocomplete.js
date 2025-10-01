/**
 * Komponent Address Autocomplete z integracją mapy
 *
 * Zapewnia zaawansowane podpowiadanie adresów z mapą i możliwością korekty punktu.
 * Wykorzystuje OpenStreetMap Nominatim API oraz OpenLayers do wyświetlania mapy.
 *
 * @version 1.0.0 ProductStrategyAnalyst Implementation
 */
export default () => ({
    // Dane podstawowe
    query: '',
    suggestions: [],
    selectedSuggestion: null,
    showSuggestions: false,
    loading: false,
    error: null,

    // Mapa
    map: null,
    vectorSource: null,
    vectorLayer: null,
    marker: null,
    ol: null,
    mapLoaded: false,

    // Współrzędne
    latitude: 0,
    longitude: 0,

    // Timeouts
    searchTimeout: null,

    // Konfiguracja
    config: {
        debounceMs: 300,
        minChars: 3,
        maxSuggestions: 5,
        mapZoom: 15,
        markerDragEnabled: true
    },

    /**
     * Inicjalizacja komponentu
     */
    async init() {
        console.log('🏠 Initializing AddressAutocomplete component');
        console.log('🧪 Alpine refs available:', Object.keys(this.$refs || {}));
        console.log('🧪 DOM element:', this.$el?.tagName);

        // Inicjalizuj mapę w tle
        setTimeout(() => {
            console.log('🗺️ Starting map initialization...');
            this.initMap();
        }, 100);

        // Backup plan - ukryj overlay po 3 sekundach niezależnie od statusu
        setTimeout(() => {
            console.log('🔄 Backup: Attempting to hide map overlay after 3s...');
            this.hideMapLoadingOverlay();
        }, 3000);

        // Nasłuchuj klików na zewnątrz
        this.setupClickOutside();

        // Ustaw wartość początkową z Livewire
        this.query = this.$wire.get('address') || '';
        this.latitude = this.$wire.get('latitude') || 0;
        this.longitude = this.$wire.get('longitude') || 0;

        // Jeśli mamy współrzędne, pokaż na mapie i aktualizuj wyświetlanie
        if (this.latitude && this.longitude) {
            this.updateCoordinatesDisplay(this.latitude, this.longitude);
            setTimeout(() => {
                this.showLocationOnMap(this.latitude, this.longitude);
            }, 1000);
        }
    },

    /**
     * Inicjalizacja mapy OpenLayers
     */
    async initMap() {
        try {
            console.log('🗺️ Loading OpenLayers library...');
            await this.loadOpenLayers();
            console.log('🗺️ Setting up map...');
            this.setupMap();
            console.log('🗺️ Map initialized successfully');
        } catch (error) {
            console.error('❌ Map initialization failed:', error);
            this.error = 'Nie udało się załadować mapy';

            // Ukryj overlay nawet w przypadku błędu
            this.hideMapLoadingOverlay();
        }
    },

    /**
     * Ładowanie biblioteki OpenLayers
     */
    async loadOpenLayers() {
        // Sprawdź czy już załadowana
        if (window.ol) {
            this.ol = window.ol;
            return;
        }

        // Załaduj CSS
        if (!document.querySelector('link[href*="ol.css"]')) {
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = 'https://cdn.jsdelivr.net/npm/ol@8.2.0/ol.css';
            document.head.appendChild(cssLink);
        }

        // Załaduj JavaScript
        return new Promise((resolve, reject) => {
            if (document.querySelector('script[src*="ol@8.2.0"]')) {
                // Już istnieje, czekaj na załadowanie
                const checkInterval = setInterval(() => {
                    if (window.ol) {
                        clearInterval(checkInterval);
                        this.ol = window.ol;
                        resolve();
                    }
                }, 100);

                setTimeout(() => {
                    clearInterval(checkInterval);
                    reject(new Error('OpenLayers load timeout'));
                }, 10000);
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/ol@8.2.0/dist/ol.js';
            script.onload = () => {
                this.ol = window.ol;
                resolve();
            };
            script.onerror = () => reject(new Error('Failed to load OpenLayers'));
            document.head.appendChild(script);
        });
    },

    /**
     * Konfiguracja mapy
     */
    setupMap() {
        const mapContainer = this.$refs.mapContainer;
        console.log('🗺️ Setting up map - container:', mapContainer, 'ol:', !!this.ol);

        if (!mapContainer) {
            console.error('❌ Map container not found in $refs');
            return;
        }

        if (!this.ol) {
            console.error('❌ OpenLayers library not available');
            return;
        }

        // Twórz vector source dla markera
        this.vectorSource = new this.ol.source.Vector();
        this.vectorLayer = new this.ol.layer.Vector({
            source: this.vectorSource,
            style: this.createMarkerStyle()
        });

        // Inicjalizuj mapę
        this.map = new this.ol.Map({
            target: mapContainer,
            layers: [
                new this.ol.layer.Tile({
                    source: new this.ol.source.OSM()
                }),
                this.vectorLayer
            ],
            view: new this.ol.View({
                center: this.ol.proj.fromLonLat([19.0, 52.0]), // Polska centralnie
                zoom: 6
            })
        });

        this.mapLoaded = true;

        // Ukryj loading overlay - spróbuj różne metody
        this.hideMapLoadingOverlay();

        // Nasłuchuj kliknięć na mapie
        this.map.on('click', (event) => {
            if (this.config.markerDragEnabled) {
                this.handleMapClick(event);
            }
        });

        console.log('✅ Map setup completed');
    },

    /**
     * Styl markera na mapie
     */
    createMarkerStyle() {
        if (!this.ol) return null;

        return new this.ol.style.Style({
            image: new this.ol.style.Circle({
                radius: 8,
                fill: new this.ol.style.Fill({ color: '#EF4444' }),
                stroke: new this.ol.style.Stroke({ color: '#FFFFFF', width: 3 })
            })
        });
    },

    /**
     * Obsługa kliknięć na mapie
     */
    async handleMapClick(event) {
        const coordinates = this.ol.proj.toLonLat(event.coordinate);
        const [longitude, latitude] = coordinates;

        this.updateLocation(latitude, longitude);

        // Reverse geocoding
        try {
            const address = await this.reverseGeocode(latitude, longitude);
            if (address) {
                this.query = address;
                this.$wire.set('address', address);
            }
        } catch (error) {
            console.warn('Reverse geocoding failed:', error);
        }
    },

    /**
     * Wyszukiwanie adresów
     */
    async searchAddresses() {
        if (this.query.length < this.config.minChars) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }

        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(async () => {
            this.loading = true;

            try {
                const suggestions = await this.fetchAddressSuggestions(this.query);
                this.suggestions = suggestions;
                this.showSuggestions = suggestions.length > 0;
            } catch (error) {
                console.error('Address search failed:', error);
                this.error = 'Nie udało się wyszukać adresów';
            } finally {
                this.loading = false;
            }
        }, this.config.debounceMs);
    },

    /**
     * Pobieranie sugestii adresów z Nominatim
     */
    async fetchAddressSuggestions(query) {
        const params = new URLSearchParams({
            q: query,
            format: 'json',
            addressdetails: '1',
            limit: this.config.maxSuggestions.toString(),
            countrycodes: 'pl', // Ogranicz do Polski
            'accept-language': 'pl'
        });

        const response = await fetch(`https://nominatim.openstreetmap.org/search?${params}`);

        if (!response.ok) {
            throw new Error('Nominatim API error');
        }

        const data = await response.json();

        return data.map(item => ({
            id: item.place_id,
            display_name: item.display_name,
            latitude: parseFloat(item.lat),
            longitude: parseFloat(item.lon),
            address: this.formatAddress(item.address),
            type: item.type,
            importance: item.importance
        }));
    },

    /**
     * Formatowanie adresu na podstawie komponentów
     */
    formatAddress(addressComponents) {
        const parts = [];

        if (addressComponents.house_number && addressComponents.road) {
            parts.push(`${addressComponents.road} ${addressComponents.house_number}`);
        } else if (addressComponents.road) {
            parts.push(addressComponents.road);
        }

        if (addressComponents.postcode) {
            parts.push(addressComponents.postcode);
        }

        if (addressComponents.city || addressComponents.town || addressComponents.village) {
            parts.push(addressComponents.city || addressComponents.town || addressComponents.village);
        }

        return parts.join(', ');
    },

    /**
     * Wybór sugestii z listy
     */
    selectSuggestion(suggestion) {
        this.selectedSuggestion = suggestion;
        this.query = suggestion.display_name;
        this.showSuggestions = false;

        // Aktualizuj współrzędne
        this.updateLocation(suggestion.latitude, suggestion.longitude);

        // Pokaż na mapie
        this.showLocationOnMap(suggestion.latitude, suggestion.longitude);

        // Syncronizuj z Livewire
        this.$wire.set('address', this.query);
    },

    /**
     * Aktualizacja lokalizacji
     */
    updateLocation(latitude, longitude) {
        this.latitude = latitude;
        this.longitude = longitude;

        // Syncronizuj z Livewire
        this.$wire.set('latitude', latitude);
        this.$wire.set('longitude', longitude);

        // Aktualizuj wyświetlanie współrzędnych
        this.updateCoordinatesDisplay(latitude, longitude);

        console.log(`📍 Location updated: ${latitude}, ${longitude}`);
    },

    /**
     * Aktualizacja wyświetlania współrzędnych
     */
    updateCoordinatesDisplay(latitude, longitude) {
        const coordsDisplay = document.getElementById('coordinates-display');
        const latDisplay = document.getElementById('lat-display');
        const lngDisplay = document.getElementById('lng-display');

        if (coordsDisplay && latDisplay && lngDisplay && latitude && longitude) {
            latDisplay.textContent = latitude.toFixed(6);
            lngDisplay.textContent = longitude.toFixed(6);
            coordsDisplay.style.display = 'inline';
        } else if (coordsDisplay) {
            coordsDisplay.style.display = 'none';
        }
    },

    /**
     * Pokazanie lokalizacji na mapie
     */
    showLocationOnMap(latitude, longitude) {
        if (!this.map || !this.ol || !this.vectorSource) {
            // Spróbuj ponownie za chwilę
            setTimeout(() => {
                this.showLocationOnMap(latitude, longitude);
            }, 500);
            return;
        }

        // Usuń poprzedni marker
        this.vectorSource.clear();

        // Dodaj nowy marker
        const coordinates = this.ol.proj.fromLonLat([longitude, latitude]);
        this.marker = new this.ol.Feature({
            geometry: new this.ol.geom.Point(coordinates)
        });

        this.vectorSource.addFeature(this.marker);

        // Wyśrodkuj mapę na lokalizacji
        this.map.getView().animate({
            center: coordinates,
            zoom: this.config.mapZoom,
            duration: 1000
        });

        console.log(`🗺️ Marker placed at: ${latitude}, ${longitude}`);
    },

    /**
     * Reverse geocoding - zamiana współrzędnych na adres
     */
    async reverseGeocode(latitude, longitude) {
        const params = new URLSearchParams({
            lat: latitude.toString(),
            lon: longitude.toString(),
            format: 'json',
            zoom: '18',
            addressdetails: '1',
            'accept-language': 'pl'
        });

        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?${params}`);

        if (!response.ok) {
            throw new Error('Reverse geocoding failed');
        }

        const data = await response.json();
        return data.display_name;
    },

    /**
     * Obsługa kliknięć na zewnątrz
     */
    setupClickOutside() {
        document.addEventListener('click', (event) => {
            if (!this.$el.contains(event.target)) {
                this.showSuggestions = false;
            }
        });
    },

    /**
     * Wykrywanie obecnej lokalizacji użytkownika
     */
    async detectCurrentLocation() {
        if (!navigator.geolocation) {
            this.error = 'Geolokalizacja nie jest obsługiwana przez tę przeglądarkę';
            return;
        }

        this.loading = true;

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                });
            });

            const { latitude, longitude } = position.coords;

            // Reverse geocoding do pobrania adresu
            const address = await this.reverseGeocode(latitude, longitude);

            this.query = address;
            this.updateLocation(latitude, longitude);
            this.showLocationOnMap(latitude, longitude);

            // Syncronizuj z Livewire
            this.$wire.set('address', address);

        } catch (error) {
            console.error('Geolocation error:', error);
            this.error = 'Nie udało się pobrać lokalizacji';
        } finally {
            this.loading = false;
        }
    },

    /**
     * Czyszczenie formularza
     */
    clearAddress() {
        this.query = '';
        this.suggestions = [];
        this.selectedSuggestion = null;
        this.showSuggestions = false;
        this.latitude = 0;
        this.longitude = 0;

        if (this.vectorSource) {
            this.vectorSource.clear();
        }

        this.$wire.set('address', '');
        this.$wire.set('latitude', 0);
        this.$wire.set('longitude', 0);
    },

    /**
     * Obsługa zmiany w polu input
     */
    handleInput() {
        this.$wire.set('address', this.query);
        this.searchAddresses();
    },

    /**
     * Obsługa klawiszy w polu input
     */
    handleKeydown(event) {
        if (event.key === 'Escape') {
            this.showSuggestions = false;
        }
    },

    /**
     * Ukrywa overlay ładowania mapy używając różnych metod
     */
    hideMapLoadingOverlay() {
        let overlayFound = false;

        // Metoda 1: getElementById
        const loadingOverlay = document.getElementById('map-loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
            loadingOverlay.style.visibility = 'hidden';
            loadingOverlay.style.opacity = '0';
            loadingOverlay.style.zIndex = '-1';
            loadingOverlay.classList.add('hidden');
            console.log('🗺️ Map loading overlay hidden successfully (method 1)');
            overlayFound = true;
        }

        // Metoda 2: querySelector w kontekście this.$el
        if (this.$el && !overlayFound) {
            const overlayInContext = this.$el.querySelector('#map-loading-overlay');
            if (overlayInContext) {
                overlayInContext.style.display = 'none';
                overlayInContext.style.visibility = 'hidden';
                overlayInContext.style.opacity = '0';
                overlayInContext.style.zIndex = '-1';
                overlayInContext.classList.add('hidden');
                console.log('🗺️ Map loading overlay hidden successfully (method 2)');
                overlayFound = true;
            }
        }

        // Metoda 3: querySelector w kontekście kontenera mapy
        const mapContainer = this.$refs?.mapContainer;
        if (mapContainer && !overlayFound) {
            const overlayInMap = mapContainer.querySelector('#map-loading-overlay');
            if (overlayInMap) {
                overlayInMap.style.display = 'none';
                overlayInMap.style.visibility = 'hidden';
                overlayInMap.style.opacity = '0';
                overlayInMap.style.zIndex = '-1';
                overlayInMap.classList.add('hidden');
                console.log('🗺️ Map loading overlay hidden successfully (method 3)');
                overlayFound = true;
            }
        }

        // Metoda 4: querySelectorAll jako fallback - ukryj wszystkie
        const allOverlays = document.querySelectorAll('#map-loading-overlay');
        if (allOverlays.length > 0) {
            allOverlays.forEach((overlay, index) => {
                overlay.style.display = 'none';
                overlay.style.visibility = 'hidden';
                overlay.style.opacity = '0';
                overlay.style.zIndex = '-1';
                overlay.classList.add('hidden');
                console.log(`🗺️ Map loading overlay ${index + 1} hidden successfully (method 4)`);
                overlayFound = true;
            });
        }

        // Metoda 5: Nuclear option - find by class and content
        if (!overlayFound) {
            const overlaysByText = Array.from(document.querySelectorAll('div')).filter(div =>
                div.textContent && div.textContent.includes('Ładowanie mapy')
            );
            if (overlaysByText.length > 0) {
                overlaysByText.forEach((overlay, index) => {
                    overlay.style.display = 'none';
                    overlay.style.visibility = 'hidden';
                    overlay.style.opacity = '0';
                    overlay.style.zIndex = '-1';
                    overlay.classList.add('hidden');
                    console.log(`🗺️ Map loading overlay found by text ${index + 1} hidden successfully (method 5)`);
                    overlayFound = true;
                });
            }
        }

        if (!overlayFound) {
            console.warn('⚠️ Map loading overlay not found with any method');
        }
    }
});