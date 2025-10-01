/**
 * Pet Sitter Wizard - Step 10 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 10 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 10 - Cennik
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep10() {
    return {
        // === REACTIVE PROXY FOR GLOBAL STATE ===
        _reactiveUpdate: 0, // Force reactive updates when this changes
        _eventListenerRegistered: false, // Flaga zapobiegająca duplikacji event listenera

        // === DIRECT REACTIVE PROPERTIES (dla Alpine.js) ===
        pricingStrategy: '',
        servicePricing: {},

        // === NOTIFICATION STATE ===
        showNotification: false,
        notificationTitle: '',
        notificationMessage: '',
        notificationType: 'success',

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('💰 Step 10 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();

            // Synchronizuj cache przy inicjalizacji
            this.syncCache();

            // Nasłuchuj na event service-added (z kroku 10)
            this.$wire.on('service-added', (event) => {
                console.log('📢 Service added event received:', event);
                const eventData = Array.isArray(event) && event.length > 0 ? event[0] : event;

                if (eventData.success) {
                    // Pokaż success toast
                    this.showToast('✅ Usługa dodana!', 'Usługa została pomyślnie dodana do Twojej oferty.', 'success');

                    // Odśwież cache i sugestie
                    this.syncCache();
                } else {
                    // Pokaż info toast
                    this.showToast('ℹ️ Informacja', eventData.message || 'Ta usługa jest już dodana.', 'info');
                }
            });

            // Nasłuchuj na event service-types-updated (z kroku 4)
            this.$wire.on('service-types-updated', (event) => {
                console.log('📢 Service types updated event received:', event);
                const eventData = Array.isArray(event) && event.length > 0 ? event[0] : event;

                // Zaktualizuj WizardState
                if (eventData.serviceTypes) {
                    window.WizardState.update('services.serviceTypes', eventData.serviceTypes);
                    console.log('✅ ServiceTypes updated from step 4:', eventData.serviceTypes);

                    // Odśwież lokalny cache
                    this.syncCache();

                    // Wymuś aktualizację widoku Alpine.js
                    this.$nextTick(() => {
                        this._reactiveUpdate++;
                    });
                }
            });

            console.log('✅ Step 10 state initialized:', {
                pricingStrategy: this.pricingStrategy,
                servicePricing: Object.keys(this.servicePricing).length
            });
        },

        /**
         * Synchronizuje lokalne properties z globalnym state
         */
        syncCache() {
            this.pricingStrategy = window.WizardState?.get('pricing.pricingStrategy') || '';
            this.servicePricing = window.WizardState?.get('pricing.servicePricing') || {};
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewirePricingStrategy = this.$wire?.pricingStrategy || '';
            const livewireServicePricing = this.$wire?.servicePricing || {};
            const livewireServiceTypes = this.$wire?.serviceTypes || [];

            console.log('💰 Step 10 initializing with Livewire data:', {
                livewirePricingStrategy,
                livewireServicePricing: Object.keys(livewireServicePricing).length,
                livewireServiceTypes: livewireServiceTypes.length,
                currentPricingStrategy: this.pricingStrategy,
                currentServicePricing: Object.keys(this.servicePricing).length
            });

            // Synchronizuj serviceTypes z kroku 4 - KRYTYCZNE!
            if (livewireServiceTypes && livewireServiceTypes.length > 0) {
                window.WizardState.update('services.serviceTypes', livewireServiceTypes);
                console.log('✅ ServiceTypes synchronized from Livewire:', livewireServiceTypes);
            }

            // Always sync with Livewire data if available
            if (livewirePricingStrategy) {
                window.WizardState.update('pricing.pricingStrategy', livewirePricingStrategy);
            } else if (!this.pricingStrategy) {
                // Initialize default strategy if needed
                window.WizardState.update('pricing.pricingStrategy', 'competitive');
            }

            if (livewireServicePricing && Object.keys(livewireServicePricing).length > 0) {
                window.WizardState.update('pricing.servicePricing', livewireServicePricing);
            } else if (!this.servicePricing || Object.keys(this.servicePricing).length === 0) {
                window.WizardState.update('pricing.servicePricing', {});
            }

            // Update derived state
            this.updateDerivedState();
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE ===

        /**
         * Multiplier dla cen w zależności od strategii
         */
        get priceMultiplier() {
            switch (this.pricingStrategy) {
                case 'budget': return 0.8;
                case 'premium': return 1.3;
                default: return 1.0;
            }
        },

        /**
         * Strategia cenowa - opcje
         */
        get pricingStrategies() {
            return {
                'budget': { icon: '💡', title: 'Budżetowa', desc: 'Niższe ceny, więcej klientów' },
                'competitive': { icon: '⚖️', title: 'Konkurencyjna', desc: 'Ceny na poziomie rynkowym' },
                'premium': { icon: '💎', title: 'Premium', desc: 'Wyższe ceny, premium service' }
            };
        },

        /**
         * Definicja usług z sugerowanymi cenami i ikonami
         */
        get serviceDefinitions() {
            return {
                'dog_walking': { title: 'Spacer z psem (1h)', suggested: 30, unit: 'PLN/h', icon: '🐕' },
                'pet_sitting': { title: 'Opieka w domu właściciela', suggested: 25, unit: 'PLN/h', icon: '🏠' },
                'pet_boarding': { title: 'Opieka u opiekuna', suggested: 80, unit: 'PLN/noc', icon: '🏡' },
                'overnight_care': { title: 'Opieka nocna', suggested: 120, unit: 'PLN/noc', icon: '🌙' },
                'pet_transport': { title: 'Transport zwierząt', suggested: 2, unit: 'PLN/km', icon: '🚗' },
                'vet_visits': { title: 'Wizyta u weterynarza', suggested: 50, unit: 'PLN', icon: '⚕️' },
                'grooming': { title: 'Pielęgnacja', suggested: 40, unit: 'PLN', icon: '✂️' },
                'feeding': { title: 'Karmienie', suggested: 20, unit: 'PLN', icon: '🍽️' }
            };
        },

        /**
         * Czy cennik jest wypełniony
         */
        get hasPricing() {
            return this.pricingStrategy && Object.keys(this.servicePricing).length > 0;
        },

        /**
         * Szacowane miesięczne zarobki na podstawie zaznaczonych usług z kroku 4
         *
         * REAKTYWNE - automatycznie aktualizuje się gdy zmienią się servicePricing
         */
        get estimatedMonthlyEarnings() {
            // Wymuś reaktywność poprzez odczytanie _reactiveUpdate
            // eslint-disable-next-line no-unused-vars
            const _ = this._reactiveUpdate;

            // Pobierz zaznaczone usługi z kroku 4
            const selectedServices = window.WizardState?.get('services.serviceTypes') || [];

            let earnings = {};
            let total = 0;

            // Konserwatywne szacunki miesięczne dla każdego typu usługi
            const estimations = {
                'dog_walking': { hours: 24, label: 'Spacery' }, // 2 spacery/dzień, 3 dni/tydzień
                'pet_sitting': { hours: 8, label: 'Opieka w domu' }, // 2 sesje po 4h
                'pet_boarding': { nights: 4, label: 'Opieka u opiekuna' }, // 4 noce/miesiąc
                'overnight_care': { nights: 3, label: 'Opieka nocna' }, // 3 noce/miesiąc
                'pet_transport': { trips: 8, kmPerTrip: 10, label: 'Transport' }, // 8 wyjazdów po 10km
                'vet_visits': { visits: 2, label: 'Wizyty u wet.' }, // 2 wizyty/miesiąc
                'grooming': { visits: 3, label: 'Pielęgnacja' }, // 3 wizyty/miesiąc
                'feeding': { visits: 8, label: 'Karmienie' } // 8 wizyt/miesiąc
            };

            // Oblicz zarobki tylko dla zaznaczonych usług
            selectedServices.forEach(serviceKey => {
                const price = parseFloat(this.servicePricing[serviceKey]) || 0;
                const estimation = estimations[serviceKey];

                if (price > 0 && estimation) {
                    let monthlyEarning = 0;

                    if (estimation.hours) {
                        monthlyEarning = price * estimation.hours;
                    } else if (estimation.nights) {
                        monthlyEarning = price * estimation.nights;
                    } else if (estimation.trips && estimation.kmPerTrip) {
                        monthlyEarning = price * estimation.trips * estimation.kmPerTrip;
                    } else if (estimation.visits) {
                        monthlyEarning = price * estimation.visits;
                    }

                    if (monthlyEarning > 0) {
                        earnings[serviceKey] = {
                            amount: monthlyEarning,
                            label: estimation.label,
                            details: estimation
                        };
                        total += monthlyEarning;
                    }
                }
            });

            return {
                earnings: earnings,
                total: total,
                hasEarnings: total > 0
            };
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Aktualizuje strategię cenową
         *
         * Automatycznie przelicza wszystkie ceny usług według nowej strategii
         */
        updatePricingStrategy(strategy) {
            const oldStrategy = this.pricingStrategy;
            const oldMultiplier = this.priceMultiplier;

            // Zaktualizuj strategię
            window.WizardState.update('pricing.pricingStrategy', strategy);
            this.pricingStrategy = strategy; // Update local reactive property

            const newMultiplier = this.priceMultiplier;

            // Przelicz wszystkie istniejące ceny według nowej strategii
            if (oldStrategy && oldMultiplier !== newMultiplier) {
                const recalculatedPricing = {};

                Object.entries(this.servicePricing).forEach(([serviceKey, currentPrice]) => {
                    // Oblicz cenę bazową (przed zastosowaniem multipliera)
                    const basePrice = this.serviceDefinitions[serviceKey]?.suggested || currentPrice / oldMultiplier;

                    // Zastosuj nowy multiplier
                    recalculatedPricing[serviceKey] = Math.round(basePrice * newMultiplier);
                });

                // Zaktualizuj ceny
                window.WizardState.update('pricing.servicePricing', recalculatedPricing);
                this.servicePricing = recalculatedPricing;

                // Sync z Livewire
                if (this.$wire) {
                    this.$wire.updateServicePricing(recalculatedPricing);
                }

                console.log('💰 Prices recalculated for new strategy:', {
                    oldStrategy,
                    newStrategy: strategy,
                    oldMultiplier,
                    newMultiplier,
                    recalculatedPricing
                });
            }

            this.updateDerivedState();

            // Wywołaj metodę Livewire
            if (this.$wire) {
                this.$wire.updatePricingStrategy(strategy);
            }

            // Wymuś reaktywną aktualizację widoku
            this._reactiveUpdate++;

            console.log('💰 Pricing strategy updated:', strategy);
        },

        /**
         * Aktualizuje cenę konkretnej usługi
         */
        updateServicePrice(service, price) {
            const currentPricing = { ...this.servicePricing };

            if (price && price > 0) {
                currentPricing[service] = parseFloat(price);
            } else {
                delete currentPricing[service];
            }

            window.WizardState.update('pricing.servicePricing', currentPricing);
            this.servicePricing = currentPricing; // Update local reactive property
            this.updateDerivedState();

            // Wywołaj metodę Livewire zamiast bezpośredniego $wire.set
            if (this.$wire) {
                this.$wire.updateServicePricing(currentPricing);
            }

            // Wymuś reaktywną aktualizację widoku
            this._reactiveUpdate++;

            console.log('💰 Service price updated:', service, price);
        },

        /**
         * Ustawia wszystkie ceny na sugerowane wartości
         */
        setRecommendedPrices() {
            const recommendedPricing = {};

            Object.entries(this.serviceDefinitions).forEach(([key, service]) => {
                recommendedPricing[key] = Math.round(service.suggested * this.priceMultiplier);
            });

            window.WizardState.update('pricing.servicePricing', recommendedPricing);
            this.servicePricing = recommendedPricing; // Update local reactive property
            this.updateDerivedState();

            // Wywołaj metodę Livewire
            if (this.$wire) {
                this.$wire.updateServicePricing(recommendedPricing);
            }

            console.log('💰 Recommended prices set:', recommendedPricing);
        },

        /**
         * Resetuje cennik
         */
        resetPricing() {
            window.WizardState.update('pricing.servicePricing', {});
            this.servicePricing = {}; // Update local reactive property
            this.updateDerivedState();

            // Wywołaj metodę Livewire
            if (this.$wire) {
                this.$wire.updateServicePricing({});
            }

            console.log('💰 Pricing reset');
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const hasStrategy = !!this.pricingStrategy;
            const hasPrices = Object.keys(this.servicePricing).length > 0;
            const isValid = hasStrategy && hasPrices;

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 10) {
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
         * Sprawdza czy usługa jest zaznaczona w kroku 4
         *
         * REAKTYWNA - automatycznie aktualizuje się gdy zmienią się serviceTypes
         */
        isServiceSelected(serviceKey) {
            // Wymuś reaktywność poprzez odczytanie _reactiveUpdate
            // eslint-disable-next-line no-unused-vars
            const _ = this._reactiveUpdate;

            const serviceTypes = window.WizardState?.get('services.serviceTypes') || [];
            return serviceTypes.includes(serviceKey);
        },

        /**
         * Przełącza zaznaczenie usługi (toggle checkbox)
         * REDESIGN v4 - nowa funkcjonalność
         */
        toggleServiceSelection(serviceKey) {
            const serviceTypes = window.WizardState?.get('services.serviceTypes') || [];
            const isCurrentlySelected = serviceTypes.includes(serviceKey);

            if (isCurrentlySelected) {
                // Odznacz usługę
                const updatedServices = serviceTypes.filter(s => s !== serviceKey);
                window.WizardState.update('services.serviceTypes', updatedServices);

                // Usuń cenę dla tej usługi
                const updatedPricing = { ...this.servicePricing };
                delete updatedPricing[serviceKey];
                window.WizardState.update('pricing.servicePricing', updatedPricing);
                this.servicePricing = updatedPricing;

                // Sync z Livewire
                if (this.$wire) {
                    this.$wire.set('serviceTypes', updatedServices, false);
                    this.$wire.updateServicePricing(updatedPricing);
                }

                console.log('❌ Usługa odznaczona:', serviceKey);
            } else {
                // Zaznacz usługę
                const updatedServices = [...serviceTypes, serviceKey];
                window.WizardState.update('services.serviceTypes', updatedServices);

                // Ustaw sugerowaną cenę automatycznie
                const suggestedPrice = this.getSuggestedPrice(serviceKey);
                const updatedPricing = { ...this.servicePricing, [serviceKey]: suggestedPrice };
                window.WizardState.update('pricing.servicePricing', updatedPricing);
                this.servicePricing = updatedPricing;

                // Sync z Livewire
                if (this.$wire) {
                    this.$wire.set('serviceTypes', updatedServices, false);
                    this.$wire.updateServicePricing(updatedPricing);
                }

                console.log('✅ Usługa zaznaczona:', serviceKey, 'z ceną:', suggestedPrice);
            }

            // Odśwież cache i zaktualizuj stan
            this.syncCache();
            this.updateDerivedState();

            // KRYTYCZNE: Wymuś reaktywną aktualizację widoku
            this._reactiveUpdate++;
        },

        /**
         * Zwraca liczbę aktywnych (zaznaczonych) usług
         * REDESIGN v4 - nowa funkcjonalność
         *
         * REAKTYWNA - automatycznie aktualizuje się gdy zmienią się serviceTypes
         */
        getActiveServicesCount() {
            // Wymuś reaktywność poprzez odczytanie _reactiveUpdate
            // eslint-disable-next-line no-unused-vars
            const _ = this._reactiveUpdate;

            const serviceTypes = window.WizardState?.get('services.serviceTypes') || [];
            return serviceTypes.length;
        },

        /**
         * Szybkie dodanie usługi z kroku 10
         */
        async quickAddService(serviceKey) {
            console.log('🚀 Quick add service:', serviceKey);

            try {
                // Sprawdź czy usługa już istnieje
                const serviceTypes = window.WizardState?.get('services.serviceTypes') || [];
                if (serviceTypes.includes(serviceKey)) {
                    console.log('ℹ️ Usługa już dodana:', serviceKey);
                    return;
                }

                // Dodaj usługę do lokalnego state
                const updatedServices = [...serviceTypes, serviceKey];
                window.WizardState.update('services.serviceTypes', updatedServices);

                // Ustaw sugerowaną cenę automatycznie
                const suggestedPrice = this.getSuggestedPrice(serviceKey);
                const updatedPricing = { ...this.servicePricing, [serviceKey]: suggestedPrice };
                window.WizardState.update('pricing.servicePricing', updatedPricing);
                this.servicePricing = updatedPricing;

                // Wywołaj backend (bez await - asynchronicznie)
                this.$wire.quickAddService(serviceKey);

                // Synchronizuj z Livewire
                if (this.$wire) {
                    this.$wire.set('serviceTypes', updatedServices, false);
                    this.$wire.updateServicePricing(updatedPricing);
                }

                // Odśwież cache
                this.syncCache();
                this.updateDerivedState();

                // KRYTYCZNE: Wymuś reaktywną aktualizację widoku
                this._reactiveUpdate++;

                console.log('✅ Usługa dodana:', serviceKey, 'z ceną:', suggestedPrice);
            } catch (error) {
                console.error('❌ Błąd dodawania usługi:', error);
            }
        },

        /**
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.pricingStrategy && Object.keys(this.servicePricing).length > 0;
        },

        /**
         * Zwraca klasy CSS dla strategii cenowej
         */
        getStrategyCardClasses(strategy) {
            return {
                'border-purple-500 bg-purple-50': this.pricingStrategy === strategy,
                'border-gray-200 hover:bg-gray-50': this.pricingStrategy !== strategy
            };
        },

        /**
         * Zwraca sugerowaną cenę dla usługi
         */
        getSuggestedPrice(serviceKey) {
            const service = this.serviceDefinitions[serviceKey];
            if (!service) return 0;

            return Math.round(service.suggested * this.priceMultiplier);
        },

        /**
         * Zwraca aktualną cenę usługi lub sugerowaną jako placeholder
         */
        getServicePrice(serviceKey) {
            return this.servicePricing[serviceKey] || this.getSuggestedPrice(serviceKey);
        },

        /**
         * Formatuje cenę z jednostką
         */
        formatPriceWithUnit(price, unit) {
            if (!price || price <= 0) return `- ${unit}`;
            return `${price} ${unit}`;
        },

        /**
         * Sprawdza czy strategia jest wybrana
         */
        isStrategySelected(strategy) {
            return this.pricingStrategy === strategy;
        },

        /**
         * Zwraca informacje o strategii
         */
        getStrategyInfo(strategy) {
            return this.pricingStrategies[strategy] || {};
        },

        /**
         * Zwraca analizę konkurencji dla danej lokalizacji
         */
        getCompetitiveAnalysis() {
            return {
                'dog_walking': { min: 25, max: 45, avg: 35 },
                'pet_sitting': { min: 20, max: 35, avg: 28 },
                'overnight_care': { min: 100, max: 150, avg: 120 },
                'pet_transport': { min: 1.5, max: 3, avg: 2 }
            };
        },

        // === HELPER METHODS ===

        /**
         * Zwraca podsumowanie cennika
         */
        getPricingSummary() {
            return {
                strategy: this.pricingStrategy,
                strategyInfo: this.getStrategyInfo(this.pricingStrategy),
                servicesCount: Object.keys(this.servicePricing).length,
                totalServices: Object.keys(this.serviceDefinitions).length,
                averagePrice: this.calculateAveragePrice(),
                estimatedEarnings: this.estimatedMonthlyEarnings,
                isComplete: this.isStepValid()
            };
        },

        /**
         * Oblicza średnią cenę za godzinę
         */
        calculateAveragePrice() {
            const hourlyServices = ['dog_walking', 'pet_sitting'];
            const hourlyPrices = hourlyServices
                .map(service => parseFloat(this.servicePricing[service]) || 0)
                .filter(price => price > 0);

            if (hourlyPrices.length === 0) return 0;

            return Math.round(hourlyPrices.reduce((sum, price) => sum + price, 0) / hourlyPrices.length);
        },

        /**
         * Sprawdza czy cennik jest kompletny
         */
        isPricingComplete() {
            const requiredServices = ['dog_walking', 'pet_sitting'];
            return requiredServices.every(service =>
                this.servicePricing[service] && parseFloat(this.servicePricing[service]) > 0
            );
        },

        /**
         * Zwraca komunikat o stanie cennika
         */
        getPricingStatusMessage() {
            if (!this.pricingStrategy) {
                return 'Wybierz strategię cenową';
            }

            const pricesCount = Object.keys(this.servicePricing).length;
            if (pricesCount === 0) {
                return 'Dodaj ceny za swoje usługi';
            }

            if (this.isPricingComplete()) {
                return 'Cennik jest kompletny';
            }

            return `Dodano ${pricesCount} cen`;
        },

        /**
         * Wyświetla toast notification
         */
        showToast(title, message, type = 'success') {
            // Sprawdź czy istnieje globalny toast system
            if (window.Alpine && window.Alpine.store && window.Alpine.store('toast')) {
                window.Alpine.store('toast').show(title, message, type);
            } else {
                // Fallback do prostego alert lub console
                console.log(`${type.toUpperCase()}: ${title} - ${message}`);

                // Możemy też użyć Livewire flash message
                if (this.$wire) {
                    this.$wire.dispatch('notify', {
                        title: title,
                        message: message,
                        type: type
                    });
                }
            }
        },

        /**
         * Zwraca sugerowane usługi, które użytkownik może dodać
         * aby zwiększyć swoje zarobki
         *
         * REAKTYWNA - używa _reactiveUpdate do wymuszenia aktualizacji
         */
        getSuggestedServices() {
            // Wymuś reaktywność poprzez odczytanie _reactiveUpdate
            // eslint-disable-next-line no-unused-vars
            const _ = this._reactiveUpdate;

            // Pobierz zaznaczone usługi z kroku 4
            const selectedServices = window.WizardState?.get('services.serviceTypes') || [];

            // Definicje wszystkich usług z szacunkami
            const estimations = {
                'dog_walking': { hours: 24, label: 'Spacery z psem' },
                'pet_sitting': { hours: 8, label: 'Opieka w domu właściciela' },
                'pet_boarding': { nights: 4, label: 'Opieka u opiekuna' },
                'overnight_care': { nights: 3, label: 'Opieka nocna' },
                'pet_transport': { trips: 8, kmPerTrip: 10, label: 'Transport zwierząt' },
                'vet_visits': { visits: 2, label: 'Wizyty u weterynarza' },
                'grooming': { visits: 3, label: 'Pielęgnacja' },
                'feeding': { visits: 8, label: 'Karmienie' }
            };

            // Sugerowane ceny bazowe (competitive strategy)
            const basePrices = {
                'dog_walking': 30,
                'pet_sitting': 25,
                'pet_boarding': 80,
                'overnight_care': 120,
                'pet_transport': 2,
                'vet_visits': 50,
                'grooming': 40,
                'feeding': 20
            };

            const suggestions = [];

            // Iteruj przez wszystkie usługi
            Object.entries(estimations).forEach(([serviceKey, estimation]) => {
                // Jeśli usługa NIE jest zaznaczona
                if (!selectedServices.includes(serviceKey)) {
                    const basePrice = basePrices[serviceKey];
                    const adjustedPrice = Math.round(basePrice * this.priceMultiplier);

                    let potentialEarning = 0;

                    if (estimation.hours) {
                        potentialEarning = adjustedPrice * estimation.hours;
                    } else if (estimation.nights) {
                        potentialEarning = adjustedPrice * estimation.nights;
                    } else if (estimation.trips && estimation.kmPerTrip) {
                        potentialEarning = adjustedPrice * estimation.trips * estimation.kmPerTrip;
                    } else if (estimation.visits) {
                        potentialEarning = adjustedPrice * estimation.visits;
                    }

                    if (potentialEarning > 0) {
                        suggestions.push({
                            serviceKey: serviceKey,
                            label: estimation.label,
                            details: estimation,
                            potentialEarning: potentialEarning,
                            suggestedPrice: adjustedPrice
                        });
                    }
                }
            });

            // Sortuj po potencjalnych zarobkach (malejąco)
            suggestions.sort((a, b) => b.potentialEarning - a.potentialEarning);

            // Zwróć maksymalnie 3 najlepsze sugestie
            return suggestions.slice(0, 3);
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep10;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep10 = wizardStep10;
}