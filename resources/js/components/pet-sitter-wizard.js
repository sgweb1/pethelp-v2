/**
 * Alpine.js komponent dla Pet Sitter Wizard z AI Assistant integration.
 *
 * Zarządza stanem wizarda, interakcjami AI, smooth transitions,
 * progress tracking i responsive behavior.
 *
 * @version 1.0.0
 * @author Claude AI Assistant
 */
export default () => ({
    // ===== STATE MANAGEMENT =====

    /**
     * Czy wizard jest w trybie fullscreen.
     */
    showWizard: false,

    /**
     * Czy panel AI jest widoczny (czysto Alpine state).
     */
    showAIPanel: true,

    /**
     * Aktualny krok wizarda.
     */
    currentStep: 1,

    /**
     * Stan panelu AI Assistant.
     */
    aiPanel: {
        visible: true,
        loading: false,
        suggestions: null,
        lastRefresh: null,
        animating: false
    },

    /**
     * Stan responsywny.
     */
    responsive: {
        isMobile: false,
        showMobileAI: false
    },

    /**
     * Cache dla sugestii AI.
     */
    aiCache: new Map(),

    /**
     * Ikony używane przez komponent (używają SafeSVGIcons API).
     */
    iconNames: {
        spinner: 'loading',
        location: 'location'
    },

    // ===== STEP 7 VARIABLES =====
    /**
     * Zmienne dla kroku 7 - Opowiedz o swoim domu.
     * Dostępne globalnie dla wszystkich kroków aby uniknąć błędów ReferenceError.
     */
    homeType: '',
    hasGarden: false,
    isSmoking: false,
    hasOtherPets: false,
    otherPets: [],

    // ===== STEP 6 VARIABLES =====
    /**
     * Zmienne dla kroku 6 - Dostępność.
     */
    flexibleSchedule: false,
    emergencyAvailable: false,

    // ===== LIFECYCLE HOOKS =====

    /**
     * Inicjalizacja komponentu.
     */
    init() {
        console.log('🧙‍♂️ Pet Sitter Wizard: Initializing...');

        // Inicjalizuj WizardStateManager z referencją do $wire
        if (window.WizardStateManager && this.$wire) {
            window.WizardStateManager.init(this.$wire, this.$wire.currentStep || 1);
            console.log('🧠 WizardStateManager connected to main wizard component');
        }

        this.checkResponsive();
        this.setupEventListeners();
        this.initializeKeyboardShortcuts();
        this.initPerformanceOptimizations();

        // Synchronizuj initial state z Livewire (bezpiecznie)
        try {
            this.showAIPanel = this.$wire?.showAIPanel ?? true;
        } catch (e) {
            console.warn('Could not sync showAIPanel from Livewire, using default true');
            this.showAIPanel = true;
        }

        // Synchronizuj zmienne z kroku 7 z Livewire
        this.syncStep7Variables();

        // Synchronizuj zmienne z kroku 6 z Livewire
        this.syncStep6Variables();

        // Inicjalizuj funkcjonalności specyficzne dla kroku po załadowaniu
        this.initializeStepSpecificFeatures();

        console.log('🧙‍♂️ Pet Sitter Wizard: Initialized successfully');
    },

    /**
     * Sprawdza breakpointy responsywne.
     */
    checkResponsive() {
        this.responsive.isMobile = window.innerWidth < 1024;

        // Auto-hide AI panel na mobile
        if (this.responsive.isMobile) {
            this.aiPanel.visible = false;
        }
    },

    /**
     * Konfiguruje event listeners.
     */
    setupEventListeners() {
        // Resize listener
        window.addEventListener('resize', this.debounceResize.bind(this));

        // AI suggestions refresh
        this.$wire.on('ai-suggestions-refreshed', (data) => {
            this.handleAISuggestionsRefresh(data);
        });

        // Step change notifications
        this.$wire.on('step-changed', (data) => {
            console.log('Livewire step-changed event received:', data);
            this.handleStepChange(data);
        });

        // AI panel toggle
        this.$wire.on('ai-panel-toggled', (data) => {
            this.aiPanel.visible = data.visible;
        });
    },

    /**
     * Debounced resize handler.
     */
    debounceResize: window.debounce(function() {
        this.checkResponsive();
    }, 250),

    /**
     * Konfiguruje skróty klawiszowe.
     */
    initializeKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (!this.showWizard) return;

            // ESC - zamknij wizard
            if (e.key === 'Escape') {
                this.closeWizard();
            }

            // Alt + A - toggle AI panel
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                this.toggleAIPanel();
            }

            // Alt + R - refresh AI suggestions
            if (e.altKey && e.key === 'r') {
                e.preventDefault();
                this.refreshAISuggestions();
            }

            // Arrow keys navigation
            if (e.ctrlKey) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.previousStep();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.nextStep();
                }
            }
        });
    },

    // ===== WIZARD NAVIGATION =====

    /**
     * Przechodzi do następnego kroku.
     */
    nextStep() {
        this.$wire.nextStep().then(() => {
            this.onStepChanged();
        });
    },

    /**
     * Wraca do poprzedniego kroku.
     */
    previousStep() {
        this.$wire.previousStep().then(() => {
            this.onStepChanged();
        });
    },

    /**
     * Przechodzi do konkretnego kroku.
     */
    goToStep(step) {
        if (step >= 1 && step <= 11) {
            this.$wire.set('currentStep', step).then(() => {
                this.onStepChanged();
            });
        }
    },

    /**
     * Handler dla zmiany kroku.
     */
    onStepChanged() {
        console.log('onStepChanged called, new currentStep:', this.$wire.currentStep);

        const oldStep = this.currentStep;
        this.currentStep = this.$wire.currentStep;

        // Powiadom WizardStateManager o zmianie kroku
        if (window.WizardStateManager) {
            document.dispatchEvent(new CustomEvent('wizard-step-changed', {
                detail: {
                    step: this.currentStep,
                    previousStep: oldStep,
                    direction: this.currentStep > oldStep ? 'forward' : 'backward'
                }
            }));
        }

        this.preloadAISuggestions();
        this.animateStepTransition();

        // Inicjalizuj funkcjonalności specyficzne dla kroku
        this.initializeStepSpecificFeatures();
    },

    /**
     * Animuje przejście między krokami.
     */
    animateStepTransition() {
        const mainContent = document.querySelector('.wizard-main-content');
        if (mainContent) {
            mainContent.style.opacity = '0.7';
            mainContent.style.transform = 'translateY(10px)';

            // Użyj requestAnimationFrame zamiast setTimeout dla lepszej wydajności
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    mainContent.style.opacity = '1';
                    mainContent.style.transform = 'translateY(0)';
                });
            });
        }
    },

    /**
     * Zamyka wizard.
     */
    closeWizard() {
        if (confirm('Czy na pewno chcesz opuścić formularz? Niezapisane zmiany zostaną utracone.')) {
            this.$wire.deactivateWizard();
            this.showWizard = false;
        }
    },

    // ===== AI ASSISTANT MANAGEMENT =====

    /**
     * Przełącza widoczność panelu AI (tylko Alpine.js, bez Livewire).
     */
    toggleAIPanel() {
        this.showAIPanel = !this.showAIPanel;
        this.aiPanel.visible = this.showAIPanel;

        // Smooth animation z requestAnimationFrame
        this.aiPanel.animating = true;
        requestAnimationFrame(() => {
            // Użyj transition event listener zamiast arbitrary timeout
            const panel = document.querySelector('.ai-panel');
            if (panel) {
                const transitionEnd = () => {
                    this.aiPanel.animating = false;
                    panel.removeEventListener('transitionend', transitionEnd);
                };
                panel.addEventListener('transitionend', transitionEnd);
                // Fallback z requestAnimationFrame
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        if (this.aiPanel.animating) {
                            this.aiPanel.animating = false;
                        }
                    });
                });
            } else {
                this.aiPanel.animating = false;
            }
        });
    },

    /**
     * Toggluje mobile AI panel.
     */
    toggleMobileAI() {
        this.responsive.showMobileAI = !this.responsive.showMobileAI;
    },

    /**
     * Odświeża sugestie AI dla aktualnego kroku.
     */
    async refreshAISuggestions() {
        if (this.aiPanel.loading) return;

        this.aiPanel.loading = true;

        try {
            // Wyczyść cache dla aktualnego kroku
            this.clearAICacheForStep(this.currentStep);

            // Wywołaj refresh w Livewire
            await this.$wire.refreshAISuggestions();

            this.aiPanel.lastRefresh = new Date();

            // Pokazaj toast notification
            window.showToast('Sugestie AI zostały odświeżone', 'success', 3000);

        } catch (error) {
            console.error('Failed to refresh AI suggestions:', error);
            window.showToast('Nie udało się odświeżyć sugestii AI', 'error', 5000);
        } finally {
            this.aiPanel.loading = false;
        }
    },

    /**
     * Preloaduje sugestie AI dla aktualnego kroku.
     */
    async preloadAISuggestions() {
        const cacheKey = `step_${this.currentStep}`;

        if (this.aiCache.has(cacheKey)) {
            return; // Już w cache
        }

        try {
            // Fetch suggestions in background
            const response = await fetch(`/api/ai/suggestions/${this.currentStep}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    wizard_data: this.$wire.wizardData || {},
                    context: {
                        user_type: 'pet_sitter',
                        preload: true
                    }
                })
            });

            if (response.ok) {
                const data = await response.json();
                this.aiCache.set(cacheKey, data.data);
            }
        } catch (error) {
            console.warn('Failed to preload AI suggestions:', error);
        }
    },

    /**
     * Czyści cache AI dla konkretnego kroku.
     */
    clearAICacheForStep(step) {
        const cacheKey = `step_${step}`;
        this.aiCache.delete(cacheKey);
    },

    /**
     * Handler dla odświeżenia sugestii AI.
     */
    handleAISuggestionsRefresh(data) {
        this.aiPanel.suggestions = data.suggestions;
        this.aiPanel.lastRefresh = new Date();

        // Update cache
        const cacheKey = `step_${data.step}`;
        this.aiCache.set(cacheKey, data.suggestions);

        // Animate update
        const aiCard = document.querySelector('.ai-suggestions-card');
        if (aiCard) {
            aiCard.classList.add('animate-pulse');
            // Użyj animationend event zamiast arbitrary timeout
            const animationEnd = () => {
                aiCard.classList.remove('animate-pulse');
                aiCard.removeEventListener('animationend', animationEnd);
            };
            aiCard.addEventListener('animationend', animationEnd);
            // Fallback z requestAnimationFrame
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (aiCard.classList.contains('animate-pulse')) {
                        aiCard.classList.remove('animate-pulse');
                    }
                });
            });
        }
    },

    /**
     * Handler dla zmiany kroku.
     */
    handleStepChange(data) {
        this.currentStep = data.step;
        this.preloadAISuggestions();
    },

    // ===== UTILITY METHODS =====

    /**
     * Sprawdza czy dany krok jest ukończony.
     */
    isStepCompleted(step) {
        return step < this.currentStep;
    },

    /**
     * Sprawdza czy dany krok jest aktualny.
     */
    isStepCurrent(step) {
        return step === this.currentStep;
    },

    /**
     * Sprawdza czy można przejść do kroku.
     */
    canGoToStep(step) {
        return step <= this.currentStep;
    },

    /**
     * Formatuje czas ostatniego odświeżenia AI.
     */
    getLastRefreshTime() {
        if (!this.aiPanel.lastRefresh) return '';

        const diff = Date.now() - this.aiPanel.lastRefresh;
        const minutes = Math.floor(diff / 60000);

        if (minutes < 1) return 'przed chwilą';
        if (minutes === 1) return '1 minutę temu';
        if (minutes < 5) return `${minutes} minuty temu`;
        return `${minutes} minut temu`;
    },

    /**
     * Kopiuje sugestię AI do schowka.
     */
    async copySuggestion(suggestion) {
        try {
            await navigator.clipboard.writeText(suggestion);
            window.showToast('Sugestia skopiowana do schowka', 'success', 2000);
        } catch (error) {
            console.error('Failed to copy suggestion:', error);
            window.showToast('Nie udało się skopiować sugestii', 'error', 3000);
        }
    },

    /**
     * Udostępnia sugestię (jeśli API Web Share jest dostępne).
     */
    async shareSuggestion(suggestion) {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Pet Sitter - Sugestia AI',
                    text: suggestion
                });
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Share failed:', error);
                }
            }
        } else {
            // Fallback to copy
            this.copySuggestion(suggestion);
        }
    },

    /**
     * Ocenia przydatność sugestii AI.
     */
    rateSuggestion(suggestionIndex, helpful) {
        // Wyślij feedback do API
        fetch('/api/ai/feedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                step: this.currentStep,
                suggestion_index: suggestionIndex,
                helpful: helpful,
                timestamp: new Date().toISOString()
            })
        }).catch(error => {
            console.warn('Failed to send AI feedback:', error);
        });

        // Pokazuj toast
        const message = helpful ? 'Dziękujemy za pozytywną ocenę!' : 'Dzięki za feedback, będziemy się starać lepiej!';
        window.showToast(message, 'info', 3000);
    },

    // ===== PERFORMANCE OPTIMIZATIONS =====

    /**
     * Debouncowana wersja refreshAISuggestions.
     */
    debouncedRefreshAI: null,

    /**
     * Throttled wersja preloadAISuggestions.
     */
    throttledPreload: null,

    /**
     * Inicjalizuje optymalizacje wydajności.
     */
    initPerformanceOptimizations() {
        this.debouncedRefreshAI = window.debounce(this.refreshAISuggestions.bind(this), 1000);
        this.throttledPreload = window.throttle(this.preloadAISuggestions.bind(this), 2000);
    },

    // ===== STEP-SPECIFIC FEATURES =====

    /**
     * State dla funkcjonalności mapy (krok 5).
     */
    mapState: {
        map: null,
        vectorSource: null,
        marker: null,
        initialized: false,
        loading: false
    },

    /**
     * State dla autocomplete adresu (krok 5).
     */
    addressState: {
        suggestions: [],
        loading: false,
        selectedIndex: -1,
        query: ''
    },

    /**
     * Inicjalizuje funkcjonalności specyficzne dla aktualnego kroku.
     */
    initializeStepSpecificFeatures() {
        console.log('initializeStepSpecificFeatures called, currentStep:', this.currentStep);

        // Czekaj na renderowanie DOM
        if (this.$nextTick) {
            this.$nextTick(() => {
                console.log('In $nextTick, currentStep:', this.currentStep);
                if (this.currentStep === 5) {
                    this.initializeStep5Features();
                }
            });
        } else {
            // Fallback z requestAnimationFrame zamiast setTimeout
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    console.log('In requestAnimationFrame fallback, currentStep:', this.currentStep);
                    if (this.currentStep === 5) {
                        this.initializeStep5Features();
                    }
                });
            });
        }
    },

    /**
     * Inicjalizuje funkcjonalności dla kroku 5 (lokalizacja i mapa).
     */
    async initializeStep5Features() {
        console.log('Inicjalizuję funkcjonalności kroku 5...');

        // Sprawdź czy elementy DOM są dostępne
        const addressInput = document.getElementById('address-input');
        const mapContainer = document.getElementById('map-container');
        const serviceRadiusSlider = document.getElementById('serviceRadius');

        if (!addressInput || !mapContainer) {
            console.warn('Elementy DOM dla kroku 5 nie są jeszcze dostępne');
            return;
        }

        try {
            // Inicjalizuj autocomplete adresu
            this.initializeAddressAutocomplete();

            // Inicjalizuj mapę
            await this.initializeMap();

            // Inicjalizuj obsługę suwaka promienia
            this.initializeRadiusSlider();

            // Inicjalizuj geolokalizację
            this.initializeGeolocation();

            console.log('Funkcjonalności kroku 5 zainicjalizowane pomyślnie');
        } catch (error) {
            console.error('Błąd podczas inicjalizacji kroku 5:', error);
            this.hideMapLoading();
        }
    },

    /**
     * Inicjalizuje autocomplete adresu.
     */
    initializeAddressAutocomplete() {
        const addressInput = document.getElementById('address-input');
        const suggestionsDropdown = document.getElementById('suggestions-dropdown');
        const clearBtn = document.getElementById('clear-address-btn');

        if (!addressInput || !suggestionsDropdown) return;

        // Obsługa wprowadzania tekstu
        addressInput.addEventListener('input', window.debounce(async (e) => {
            const query = e.target.value.trim();
            this.addressState.query = query;

            if (query.length >= 3) {
                await this.searchAddresses(query);
            } else {
                this.hideSuggestions();
            }

            // Pokaż/ukryj przycisk czyszczenia
            if (clearBtn) {
                clearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }
        }, 300));

        // Obsługa klawiatury
        addressInput.addEventListener('keydown', (e) => {
            this.handleAddressKeydown(e);
        });

        // Przycisk czyszczenia
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearAddress();
            });
        }

        // Ukryj suggestions przy kliknięciu poza
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#address-autocomplete-container')) {
                this.hideSuggestions();
            }
        });
    },

    /**
     * Wyszukuje adresy za pomocą Nominatim API.
     */
    async searchAddresses(query) {
        if (this.addressState.loading) return;

        this.addressState.loading = true;
        this.showAddressLoading();

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&countrycodes=pl&q=${encodeURIComponent(query)}`);

            if (!response.ok) throw new Error('Błąd API Nominatim');

            const data = await response.json();
            this.addressState.suggestions = data;
            this.showSuggestions(data);

        } catch (error) {
            console.error('Błąd wyszukiwania adresów:', error);
            this.addressState.suggestions = [];
            this.hideSuggestions();
        } finally {
            this.addressState.loading = false;
            this.hideAddressLoading();
        }
    },

    /**
     * Pokazuje sugestie adresów.
     */
    showSuggestions(suggestions) {
        const dropdown = document.getElementById('suggestions-dropdown');
        if (!dropdown) return;

        if (suggestions.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'p-3 text-sm text-gray-500';
            noResults.textContent = 'Brak wyników';
            dropdown.innerHTML = '';
            dropdown.appendChild(noResults);
        } else {
            dropdown.innerHTML = '';
            suggestions.forEach((suggestion, index) => {
                const item = document.createElement('div');
                item.className = 'suggestion-item p-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                item.setAttribute('data-index', index);

                const nameDiv = document.createElement('div');
                nameDiv.className = 'font-medium text-gray-900';
                nameDiv.textContent = suggestion.display_name;
                item.appendChild(nameDiv);

                const coordsDiv = document.createElement('div');
                coordsDiv.className = 'text-xs text-gray-500 mt-1';
                coordsDiv.textContent = `Lat: ${parseFloat(suggestion.lat).toFixed(4)}, Lon: ${parseFloat(suggestion.lon).toFixed(4)}`;
                item.appendChild(coordsDiv);

                item.addEventListener('click', () => {
                    this.selectSuggestion(index);
                });

                dropdown.appendChild(item);
            });
        }

        dropdown.style.display = 'block';
        this.addressState.selectedIndex = -1;
    },

    /**
     * Ukrywa sugestie adresów.
     */
    hideSuggestions() {
        const dropdown = document.getElementById('suggestions-dropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
        this.addressState.selectedIndex = -1;
    },

    /**
     * Wybiera sugestię adresu.
     */
    async selectSuggestion(index) {
        const suggestion = this.addressState.suggestions[index];
        if (!suggestion) return;

        const addressInput = document.getElementById('address-input');
        if (addressInput) {
            addressInput.value = suggestion.display_name;
        }

        // Aktualizuj Livewire
        try {
            await this.$wire.set('address', suggestion.display_name);
            await this.$wire.set('latitude', parseFloat(suggestion.lat));
            await this.$wire.set('longitude', parseFloat(suggestion.lon));
        } catch (error) {
            console.error('Błąd aktualizacji Livewire:', error);
        }

        // Aktualizuj mapę
        this.updateMapLocation(parseFloat(suggestion.lat), parseFloat(suggestion.lon));

        this.hideSuggestions();
    },

    /**
     * Obsługa klawiatury w autocomplete.
     */
    handleAddressKeydown(e) {
        const dropdown = document.getElementById('suggestions-dropdown');
        if (!dropdown || dropdown.style.display === 'none') return;

        const suggestions = dropdown.querySelectorAll('.suggestion-item');

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.addressState.selectedIndex = Math.min(
                    this.addressState.selectedIndex + 1,
                    suggestions.length - 1
                );
                this.highlightSuggestion();
                break;

            case 'ArrowUp':
                e.preventDefault();
                this.addressState.selectedIndex = Math.max(
                    this.addressState.selectedIndex - 1,
                    -1
                );
                this.highlightSuggestion();
                break;

            case 'Enter':
                e.preventDefault();
                if (this.addressState.selectedIndex >= 0) {
                    this.selectSuggestion(this.addressState.selectedIndex);
                }
                break;

            case 'Escape':
                this.hideSuggestions();
                break;
        }
    },

    /**
     * Podświetla wybraną sugestię.
     */
    highlightSuggestion() {
        const suggestions = document.querySelectorAll('.suggestion-item');
        suggestions.forEach((item, index) => {
            if (index === this.addressState.selectedIndex) {
                item.classList.add('bg-purple-50');
            } else {
                item.classList.remove('bg-purple-50');
            }
        });
    },

    /**
     * Czyści pole adresu.
     */
    async clearAddress() {
        const addressInput = document.getElementById('address-input');
        if (addressInput) {
            addressInput.value = '';
        }

        try {
            await this.$wire.set('address', '');
            await this.$wire.set('latitude', 0);
            await this.$wire.set('longitude', 0);
        } catch (error) {
            console.error('Błąd czyszczenia adresu:', error);
        }

        this.hideSuggestions();
        this.hideAddressLoading();
        document.getElementById('clear-address-btn').style.display = 'none';
    },

    /**
     * Inicjalizuje mapę OpenLayers.
     */
    async initializeMap() {
        if (this.mapState.initialized) return;

        // Dynamicznie ładuj OpenLayers
        if (!window.ol) {
            await this.loadOpenLayers();
        }

        const mapContainer = document.getElementById('map-container');
        if (!mapContainer) return;

        try {
            // Domyślne współrzędne (Warszawa)
            const defaultLat = this.$wire.get('latitude') || 52.2297;
            const defaultLng = this.$wire.get('longitude') || 21.0122;

            // Utwórz źródło wektorowe dla markerów
            this.mapState.vectorSource = new window.ol.source.Vector();

            // Utwórz warstwę wektorową
            const vectorLayer = new window.ol.layer.Vector({
                source: this.mapState.vectorSource,
                style: new window.ol.style.Style({
                    image: new window.ol.style.Circle({
                        radius: 8,
                        fill: new window.ol.style.Fill({ color: '#8B5CF6' }),
                        stroke: new window.ol.style.Stroke({ color: '#ffffff', width: 2 })
                    })
                })
            });

            // Utwórz mapę
            this.mapState.map = new window.ol.Map({
                target: 'map-container',
                layers: [
                    new window.ol.layer.Tile({
                        source: new window.ol.source.OSM()
                    }),
                    vectorLayer
                ],
                view: new window.ol.View({
                    center: window.ol.proj.fromLonLat([defaultLng, defaultLat]),
                    zoom: 12
                })
            });

            // Dodaj marker na mapę
            this.addMapMarker(defaultLat, defaultLng);

            // Obsługa kliknięcia na mapę
            this.mapState.map.on('click', async (evt) => {
                const coordinate = window.ol.proj.toLonLat(evt.coordinate);
                const [lng, lat] = coordinate;

                await this.handleMapClick(lat, lng);
            });

            this.mapState.initialized = true;
            this.hideMapLoading();

            console.log('Mapa zainicjalizowana pomyślnie');

        } catch (error) {
            console.error('Błąd inicjalizacji mapy:', error);
            this.hideMapLoading();
        }
    },

    /**
     * Ładuje bibliotekę OpenLayers dynamicznie.
     */
    async loadOpenLayers() {
        return new Promise((resolve, reject) => {
            if (window.ol) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/ol@9.2.4/dist/ol.js';
            script.onload = () => {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/ol@9.2.4/ol.css';
                document.head.appendChild(link);
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    },

    /**
     * Dodaje marker na mapę.
     */
    addMapMarker(lat, lng) {
        if (!this.mapState.vectorSource || !window.ol) return;

        // Usuń poprzedni marker
        this.mapState.vectorSource.clear();

        // Dodaj nowy marker
        const marker = new window.ol.Feature({
            geometry: new window.ol.geom.Point(window.ol.proj.fromLonLat([lng, lat]))
        });

        this.mapState.vectorSource.addFeature(marker);
        this.mapState.marker = marker;

        // Wyśrodkuj mapę na markerze
        this.mapState.map.getView().setCenter(window.ol.proj.fromLonLat([lng, lat]));
    },

    /**
     * Aktualizuje lokalizację na mapie.
     */
    updateMapLocation(lat, lng) {
        if (!this.mapState.initialized) return;

        this.addMapMarker(lat, lng);
        this.updateCoordinatesDisplay(lat, lng);
    },

    /**
     * Obsługuje kliknięcie na mapę.
     */
    async handleMapClick(lat, lng) {
        console.log('Kliknięto mapę:', lat, lng);

        try {
            // Aktualizuj marker
            this.addMapMarker(lat, lng);

            // Aktualizuj Livewire
            await this.$wire.set('latitude', lat);
            await this.$wire.set('longitude', lng);

            // Reverse geocoding - znajdź adres dla współrzędnych
            await this.reverseGeocode(lat, lng);

            this.updateCoordinatesDisplay(lat, lng);

        } catch (error) {
            console.error('Błąd obsługi kliknięcia mapy:', error);
        }
    },

    /**
     * Reverse geocoding - znajdź adres dla współrzędnych.
     */
    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);

            if (!response.ok) return;

            const data = await response.json();

            if (data.display_name) {
                const addressInput = document.getElementById('address-input');
                if (addressInput) {
                    addressInput.value = data.display_name;
                }

                await this.$wire.set('address', data.display_name);
            }

        } catch (error) {
            console.warn('Błąd reverse geocoding:', error);
        }
    },

    /**
     * Aktualizuje wyświetlanie współrzędnych.
     */
    updateCoordinatesDisplay(lat, lng) {
        const coordsDisplay = document.getElementById('coordinates-display');
        const latDisplay = document.getElementById('lat-display');
        const lngDisplay = document.getElementById('lng-display');

        if (coordsDisplay && latDisplay && lngDisplay) {
            latDisplay.textContent = lat.toFixed(6);
            lngDisplay.textContent = lng.toFixed(6);
            coordsDisplay.style.display = 'inline';
        }
    },

    /**
     * Inicjalizuje obsługę suwaka promienia.
     */
    initializeRadiusSlider() {
        const slider = document.getElementById('serviceRadius');
        if (!slider) return;

        slider.addEventListener('input', window.debounce(() => {
            this.updateMapRadius();
        }, 300));
    },

    /**
     * Aktualizuje okrąg promienia na mapie.
     */
    updateMapRadius() {
        if (!this.mapState.initialized || !this.mapState.map) return;

        const radius = this.$wire.get('serviceRadius') || 10;
        const lat = this.$wire.get('latitude') || 52.2297;
        const lng = this.$wire.get('longitude') || 21.0122;

        // Znajdź istniejącą warstwę okręgu i usuń ją
        const layers = this.mapState.map.getLayers().getArray();
        const circleLayer = layers.find(layer => layer.get('name') === 'radius-circle');
        if (circleLayer) {
            this.mapState.map.removeLayer(circleLayer);
        }

        // Dodaj nowy okrąg
        const circleFeature = new window.ol.Feature({
            geometry: new window.ol.geom.Circle(
                window.ol.proj.fromLonLat([lng, lat]),
                radius * 1000 // radius w metrach
            )
        });

        const circleSource = new window.ol.source.Vector();
        circleSource.addFeature(circleFeature);

        const newCircleLayer = new window.ol.layer.Vector({
            source: circleSource,
            style: new window.ol.style.Style({
                stroke: new window.ol.style.Stroke({
                    color: '#8B5CF6',
                    width: 2
                }),
                fill: new window.ol.style.Fill({
                    color: 'rgba(139, 92, 246, 0.1)'
                })
            })
        });

        newCircleLayer.set('name', 'radius-circle');
        this.mapState.map.addLayer(newCircleLayer);
    },

    /**
     * Inicjalizuje geolokalizację.
     */
    initializeGeolocation() {
        const geoBtn = document.getElementById('geolocation-btn');
        if (!geoBtn) return;

        geoBtn.addEventListener('click', () => {
            this.getCurrentLocation();
        });
    },

    /**
     * Pobiera aktualną lokalizację użytkownika.
     */
    getCurrentLocation() {
        if (!navigator.geolocation) {
            window.showToast('Geolokalizacja nie jest wspierana', 'error', 3000);
            return;
        }

        const geoBtn = document.getElementById('geolocation-btn');
        if (geoBtn && window.SafeSVGIcons) {
            geoBtn.innerHTML = '';
            window.SafeSVGIcons.createLoadingSpinner(geoBtn, {
                classes: 'animate-spin text-current',
                size: { width: 20, height: 20 }
            });
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const { latitude, longitude } = position.coords;

                // Aktualizuj mapę
                this.updateMapLocation(latitude, longitude);

                // Aktualizuj Livewire
                try {
                    await this.$wire.set('latitude', latitude);
                    await this.$wire.set('longitude', longitude);
                } catch (error) {
                    console.error('Błąd aktualizacji współrzędnych:', error);
                }

                // Reverse geocoding
                await this.reverseGeocode(latitude, longitude);

                window.showToast('Lokalizacja wykryta pomyślnie', 'success', 3000);

                // Przywróć ikonę
                if (geoBtn && window.SafeSVGIcons) {
                    geoBtn.innerHTML = '';
                    window.SafeSVGIcons.createIcon('location', geoBtn, {
                        classes: 'text-current',
                        size: { width: 20, height: 20 }
                    });
                }
            },
            (error) => {
                console.error('Błąd geolokalizacji:', error);
                let message = 'Nie udało się wykryć lokalizacji';

                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Dostęp do lokalizacji został odrzucony';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Lokalizacja niedostępna';
                        break;
                    case error.TIMEOUT:
                        message = 'Timeout przy wykrywaniu lokalizacji';
                        break;
                }

                window.showToast(message, 'error', 5000);

                // Przywróć ikonę
                if (geoBtn && window.SafeSVGIcons) {
                    geoBtn.innerHTML = '';
                    window.SafeSVGIcons.createIcon('location', geoBtn, {
                        classes: 'text-current',
                        size: { width: 20, height: 20 }
                    });
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    },

    /**
     * Pokazuje loading dla adresu.
     */
    showAddressLoading() {
        const loading = document.getElementById('address-loading');
        if (loading) {
            loading.style.display = 'flex';
        }
    },

    /**
     * Ukrywa loading dla adresu.
     */
    hideAddressLoading() {
        const loading = document.getElementById('address-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    },

    /**
     * Ukrywa loading overlay mapy.
     */
    hideMapLoading() {
        const overlay = document.getElementById('map-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    },

    // ===== SYNCHRONIZACJA ZMIENNYCH MIĘDZYKROKOWYCH =====

    /**
     * Synchronizuje zmienne kroku 7 z Livewire backend.
     */
    syncStep7Variables() {
        try {
            this.homeType = this.$wire?.homeType || '';
            this.hasGarden = this.$wire?.hasGarden || false;
            this.isSmoking = this.$wire?.isSmoking || false;
            this.hasOtherPets = this.$wire?.hasOtherPets || false;
            this.otherPets = this.$wire?.otherPets || [];

            console.log('🏠 Step 7 variables synced:', {
                homeType: this.homeType,
                hasGarden: this.hasGarden,
                isSmoking: this.isSmoking,
                hasOtherPets: this.hasOtherPets,
                otherPets: this.otherPets
            });
        } catch (e) {
            console.warn('⚠️ Could not sync Step 7 variables from Livewire:', e);
        }
    },

    /**
     * Synchronizuje zmienne kroku 6 z Livewire backend.
     */
    syncStep6Variables() {
        try {
            this.flexibleSchedule = this.$wire?.flexibleSchedule || false;
            this.emergencyAvailable = this.$wire?.emergencyAvailable || false;

            console.log('📅 Step 6 variables synced:', {
                flexibleSchedule: this.flexibleSchedule,
                emergencyAvailable: this.emergencyAvailable
            });
        } catch (e) {
            console.warn('⚠️ Could not sync Step 6 variables from Livewire:', e);
        }
    }
});