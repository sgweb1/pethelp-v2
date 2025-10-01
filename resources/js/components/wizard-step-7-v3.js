/**
 * Pet Sitter Wizard - Step 7 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 7 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 7 - Home Information
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep7() {
    return {
        // === REACTIVE PROXY FOR GLOBAL STATE ===
        // Local reactive variables to trigger Alpine.js updates
        _reactiveUpdate: 0, // Force reactive updates when this changes

        // NOTE: _hasGarden i _isSmoking zostały usunięte - używamy computed properties

        get _hasOtherPets() {
            return window.WizardState?.get('home.hasOtherPets') || false;
        },
        set _hasOtherPets(value) {
            window.WizardState?.update('home.hasOtherPets', value);
        },

        get _homeType() {
            return window.WizardState?.get('home.homeType') || '';
        },
        set _homeType(value) {
            window.WizardState?.update('home.homeType', value);
        },

        get _otherPets() {
            return window.WizardState?.get('home.otherPets') || [];
        },
        set _otherPets(value) {
            window.WizardState?.update('home.otherPets', value);
        },

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('🏠 Step 7 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();
            this.syncLocalVariables();

            console.log('✅ Step 7 state initialized:', {
                homeType: this.homeType,
                hasGarden: this.hasGarden,
                isSmoking: this.isSmoking,
                hasOtherPets: this.hasOtherPets,
                otherPetsCount: this.otherPets.length
            });
        },

        /**
         * Force reactive update dla Alpine.js
         */
        forceReactiveUpdate() {
            this._reactiveUpdate++;
            console.log('🔄 Step 7 Forced reactive update:', this._reactiveUpdate);
        },

        /**
         * Synchronizuje lokalne zmienne z globalnym state
         * NOTE: Już nie używane - wszystko przez computed properties
         */
        syncLocalVariables() {
            console.log('🔄 State synchronization (using computed properties):', {
                hasGarden: this.hasGarden,
                isSmoking: this.isSmoking
            });
        },


        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback jeśli potrzebne
            if (!this.homeType) {
                window.WizardState.update('home.homeType', '');
            }

            if (this.hasGarden === undefined) {
                window.WizardState.update('home.hasGarden', false);
            }

            if (this.isSmoking === undefined) {
                window.WizardState.update('home.isSmoking', false);
            }

            if (this.hasOtherPets === undefined) {
                window.WizardState.update('home.hasOtherPets', false);
            }

            if (!Array.isArray(this.otherPets)) {
                window.WizardState.update('home.otherPets', []);
            }
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE ===

        /**
         * Typ domu z globalnego state
         */
        get homeType() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('home.homeType') || '';
        },

        /**
         * Czy ma ogród z globalnego state
         */
        get hasGarden() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('home.hasGarden') || false;
        },

        /**
         * Czy pali z globalnego state
         */
        get isSmoking() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('home.isSmoking') || false;
        },

        /**
         * Czy ma inne zwierzęta z globalnego state
         */
        get hasOtherPets() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('home.hasOtherPets') || false;
        },

        /**
         * Lista innych zwierząt z globalnego state
         */
        get otherPets() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('home.otherPets') || [];
        },

        // === CROSS-STEP VARIABLES - Z GLOBAL STATE ===
        // Te zmienne są używane przez inne kroki

        /**
         * Elastyczny harmonogram (z step-6)
         */
        get flexibleSchedule() {
            return window.WizardState?.get('availability.flexibleSchedule') || false;
        },

        /**
         * Dostępność w nagłych przypadkach (z step-6)
         */
        get emergencyAvailable() {
            return window.WizardState?.get('availability.emergencyAvailable') || false;
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Wybiera typ domu
         */
        selectHomeType(type) {
            window.WizardState.update('home.homeType', type);
            this.syncWithLivewire('homeType', type);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🏠 Home type selected:', type);
        },

        /**
         * Przełącza obecność ogrodu
         */
        toggleGarden() {
            const oldValue = this.hasGarden;
            const newValue = !oldValue;

            console.log('🌱 Garden toggle - BEFORE:', {
                oldValue,
                newValue,
                currentState: window.WizardState?.get('home.hasGarden')
            });

            // Aktualizuj globalny state
            window.WizardState.update('home.hasGarden', newValue);
            this.syncWithLivewire('hasGarden', newValue);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🌱 Garden toggle - AFTER:', {
                newValue,
                updatedState: window.WizardState?.get('home.hasGarden'),
                computedValue: this.hasGarden
            });
        },

        /**
         * Przełącza status palenia
         */
        toggleSmoking() {
            const oldValue = this.isSmoking;
            const newValue = !oldValue;

            console.log('🚭 Smoking toggle - BEFORE:', {
                oldValue,
                newValue,
                currentState: window.WizardState?.get('home.isSmoking')
            });

            // Aktualizuj globalny state
            window.WizardState.update('home.isSmoking', newValue);
            this.syncWithLivewire('isSmoking', newValue);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🚭 Smoking toggle - AFTER:', {
                newValue,
                updatedState: window.WizardState?.get('home.isSmoking'),
                computedValue: this.isSmoking
            });
        },

        /**
         * Przełącza obecność innych zwierząt
         */
        toggleOtherPets() {
            const newValue = !this.hasOtherPets;
            window.WizardState.update('home.hasOtherPets', newValue);
            this.syncWithLivewire('hasOtherPets', newValue);

            // Jeśli wyłączamy innych zwierząt, wyczyść listę
            if (!newValue) {
                window.WizardState.update('home.otherPets', []);
                this.syncWithLivewire('otherPets', []);
            }

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🐾 Other pets toggled:', newValue);
        },

        /**
         * Przełącza konkretne zwierzę w liście
         */
        togglePet(petType) {
            const currentPets = [...this.otherPets];
            const index = currentPets.indexOf(petType);

            if (index > -1) {
                // Usuń z listy
                currentPets.splice(index, 1);
            } else {
                // Dodaj do listy
                currentPets.push(petType);
            }

            window.WizardState.update('home.otherPets', currentPets);
            this.syncWithLivewire('otherPets', currentPets);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log(`🐾 Pet ${petType} toggled. Current pets:`, currentPets);
        },

        /**
         * Sprawdza czy dane zwierzę jest wybrane
         */
        isPetSelected(petType) {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return this.otherPets.includes(petType);
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
            return this.homeType !== '';
        },

        /**
         * Aktualizuje meta state dla step validation
         */
        updateStepValidation() {
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 7) {
                const isValid = this.isStepValid();
                window.WizardState.state.meta.canProceed = isValid;
                window.WizardState.state.meta.isValid = isValid;
            }
        },

        // === HELPER METHODS ===

        /**
         * Zwraca podsumowanie informacji o domu
         */
        getHomeSummary() {
            return {
                homeType: this.homeType,
                hasGarden: this.hasGarden,
                isSmoking: this.isSmoking,
                hasOtherPets: this.hasOtherPets,
                otherPetsCount: this.otherPets.length,
                otherPetTypes: this.otherPets,
                smokeFree: !this.isSmoking,
                petFriendly: this.hasOtherPets
            };
        },

        /**
         * Zwraca czytelny opis typu domu
         */
        getHomeTypeLabel() {
            const labels = {
                'apartment': 'Mieszkanie',
                'house': 'Dom jednorodzinny',
                'studio': 'Kawalerka/Studio',
                'townhouse': 'Dom szeregowy'
            };
            return labels[this.homeType] || this.homeType;
        },

        /**
         * Zwraca listę cech domu
         */
        getHomeFeatures() {
            const features = [];

            if (this.hasGarden) {
                features.push('Ogród/balkon');
            }

            if (!this.isSmoking) {
                features.push('Środowisko bez dymu');
            }

            if (this.hasOtherPets) {
                features.push(`Inne zwierzęta (${this.otherPets.length})`);
            }

            return features;
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep7;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep7 = wizardStep7;
}