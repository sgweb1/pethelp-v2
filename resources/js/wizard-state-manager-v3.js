/**
 * Pet Sitter Wizard - Centralized State Manager v3.0
 *
 * Implementuje wzorzec "Single Source of Truth" (SSOT) dla kompletnego
 * zarządzania stanem wizard'a rejestracji pet sittera.
 *
 * ZALETY ARCHITEKTURY V3:
 * - Jeden centralny state - koniec z duplikacją zmiennych
 * - Automatyczna synchronizacja między krokami
 * - Reactive updates wszystkich komponentów
 * - Zintegrowane debugging i development tools
 * - Pełna kompatybilność z Livewire
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 * @since 2025-09-29
 */

class WizardStateManager {
    constructor() {
        /**
         * Centralny state - SINGLE SOURCE OF TRUTH
         * Wszystkie dane wizard'a są przechowywane tutaj.
         */
        this.state = {
            // Meta informacje o wizard'ie
            meta: {
                currentStep: 1,
                maxSteps: 12,
                isActive: false,
                progressPercentage: 0,
                completionPercentage: 0,
                completedSteps: [],
                isValid: false,
                canProceed: false
            },

            // Krok 1: Dane osobowe i motywacja
            personalData: {
                name: '',
                email: '',
                city: '',
                motivation: '',
                motivationLength: 0,
                hasStartedTyping: false
            },

            // Krok 2-3: Doświadczenie z zwierzętami
            experience: {
                petExperience: [], // Powinno być array, nie string
                experienceDescription: '',
                yearsOfExperience: 0,
                animalTypes: [],
                animalSizes: [],
                hasExperienceWithDogs: false,
                hasExperienceWithCats: false,
                hasExperienceWithSmallAnimals: false
            },

            // Krok 4: Typy usług
            services: {
                serviceTypes: [],
                specialServices: [],
                canWalkDogs: false,
                canOvernightSit: false,
                canDaySit: false,
                canPetGrooming: false
            },

            // Krok 5: Adres i obszar działania
            location: {
                address: '',
                serviceRadius: 10,
                coordinates: {
                    lat: null,
                    lng: null
                }
            },

            // Krok 6: Dostępność czasowa
            availability: {
                weeklyAvailability: {},
                flexibleSchedule: false,
                emergencyAvailable: false,
                morningAvailable: false,
                afternoonAvailable: false,
                eveningAvailable: false,
                weekendAvailable: false
            },

            // Krok 7: Informacje o domu
            home: {
                homeType: '',
                hasGarden: false,
                isSmoking: false,
                hasOtherPets: false,
                otherPets: [],
                isSafeEnvironment: true,
                hasSecureFencing: false
            },

            // Krok 8-9: Weryfikacja tożsamości i dokumenty
            verification: {
                hasProfilePhoto: false,
                homePhotosCount: 0,
                hasIdentityDocument: false,
                hasCriminalRecord: false,
                referencesCount: 0,
                isVerified: false
            },

            // Krok 10: Cennik
            pricing: {
                pricingStrategy: 'competitive', // competitive, premium, budget
                servicePricing: {
                    dogWalking: 0,
                    overnightSitting: 0,
                    daySitting: 0,
                    petGrooming: 0
                }
            },

            // Krok 11-12: Warunki i finalizacja
            terms: {
                agreedToTerms: false,
                marketingConsent: false,
                privacyPolicyAccepted: false,
                completedAt: null
            }
        };

        /**
         * Obserwatorzy zmian stanu
         * @type {Function[]}
         */
        this.watchers = [];

        /**
         * Referencja do instancji Livewire
         * @type {Object|null}
         */
        this.livewire = null;

        /**
         * Tryb debugowania - włączony w development
         * @type {boolean}
         */
        this.debugging = process.env.NODE_ENV === 'development' || window.location.hostname === 'pethelp.test';

        /**
         * Historia zmian dla debugowania
         * @type {Array}
         */
        this.changeHistory = [];

        /**
         * Maksymalna liczba zapisywanych zmian w historii
         * @type {number}
         */
        this.maxHistorySize = 100;

        // Inicjalizacja
        this.init();
    }

    /**
     * Inicjalizuje state manager.
     *
     * @returns {void}
     */
    init() {
        if (this.debugging) {
            console.log('🏗️ WizardStateManager v3.0 INITIALIZED');
            console.log('📊 Initial state:', this.state);
        }

        // Globalna dostępność
        window.WizardState = this;

        // Event listener dla Livewire
        this.setupLivewireIntegration();

        // Development helpers
        this.setupDevHelpers();
    }

    /**
     * Aktualizuje wartość w state używając ścieżki dot notation.
     *
     * @param {string} path - Ścieżka do właściwości (np. 'home.hasGarden')
     * @param {*} value - Nowa wartość
     * @returns {void}
     */
    update(path, value) {
        const oldValue = this.get(path);

        // Sprawdź czy wartość faktycznie się zmieniła
        if (oldValue === value) {
            return;
        }

        this.set(path, value);

        // Zapisz zmianę w historii
        this.recordChange(path, oldValue, value);

        // Auto-sync z Livewire
        this.syncToLivewire(path, value);

        // Powiadom obserwatorów
        this.notify(path, value, oldValue);

        // Debug log
        if (this.debugging) {
            console.log(`🔄 State updated: ${path}`, {
                old: oldValue,
                new: value,
                timestamp: new Date().toISOString()
            });
        }
    }

    /**
     * Pobiera wartość z state używając ścieżki dot notation.
     *
     * @param {string} path - Ścieżka do właściwości
     * @returns {*} Wartość lub undefined jeśli nie istnieje
     */
    get(path) {
        return path.split('.').reduce((obj, key) => obj?.[key], this.state);
    }

    /**
     * Ustawia wartość w state używając ścieżki dot notation.
     *
     * @param {string} path - Ścieżka do właściwości
     * @param {*} value - Wartość do ustawienia
     * @returns {void}
     */
    set(path, value) {
        const keys = path.split('.');
        const lastKey = keys.pop();
        const target = keys.reduce((obj, key) => {
            if (!obj[key]) obj[key] = {};
            return obj[key];
        }, this.state);

        target[lastKey] = value;

        // Aktualizuj meta informacje
        this.updateMetaState();
    }

    /**
     * Aktualizuje meta informacje o wizard'ie.
     *
     * @returns {void}
     */
    updateMetaState() {
        // Oblicz progress
        const totalSteps = this.state.meta.maxSteps;
        const currentStep = this.state.meta.currentStep;
        this.state.meta.progressPercentage = Math.round((currentStep / totalSteps) * 100);

        // Sprawdź czy current step jest ukończony
        const stepValidation = this.validateCurrentStep();
        this.state.meta.canProceed = stepValidation.isValid;
        this.state.meta.isValid = stepValidation.isValid;
    }

    /**
     * Waliduje aktualny krok.
     *
     * @returns {Object} Wynik walidacji
     */
    validateCurrentStep() {
        const step = this.state.meta.currentStep;

        switch (step) {
            case 1:
                return {
                    isValid: this.state.personalData.motivation.length >= 50,
                    errors: this.state.personalData.motivation.length < 50 ? ['Motywacja musi mieć co najmniej 50 znaków'] : []
                };

            case 2:
                return {
                    isValid: this.state.experience.petExperience.length > 0 && this.state.experience.animalTypes.length > 0,
                    errors: []
                };

            case 3:
                return {
                    isValid: this.state.experience.yearsOfExperience > 0,
                    errors: []
                };

            case 4:
                return {
                    isValid: this.state.services.serviceTypes.length > 0,
                    errors: []
                };

            case 6:
                return {
                    isValid: Object.keys(this.state.availability.weeklyAvailability).length > 0,
                    errors: []
                };

            case 7:
                return {
                    isValid: this.state.home.homeType.length > 0,
                    errors: []
                };

            default:
                return { isValid: true, errors: [] };
        }
    }

    /**
     * Synchronizuje dane z komponentem Livewire.
     *
     * @param {string} path - Ścieżka do właściwości
     * @param {*} value - Wartość do synchronizacji
     * @returns {void}
     */
    syncToLivewire(path, value) {
        if (!this.livewire) {
            // Spróbuj znaleźć instancję Livewire
            const wizardElement = document.querySelector('[wire\\:id]');
            if (wizardElement && window.Livewire) {
                this.livewire = window.Livewire.find(wizardElement.getAttribute('wire:id'));
            }
        }

        if (this.livewire && this.livewire.set) {
            try {
                // Mapuj ścieżkę z state na właściwość Livewire
                const livewireProperty = this.mapPathToLivewire(path);
                if (livewireProperty) {
                    this.livewire.set(livewireProperty, value, false);

                    if (this.debugging) {
                        console.log(`🔗 Synced to Livewire: ${livewireProperty} = ${value}`);
                    }
                }
            } catch (error) {
                console.error('❌ Livewire sync error:', error);
            }
        }
    }

    /**
     * Mapuje ścieżkę state na właściwość Livewire.
     *
     * @param {string} path - Ścieżka w state
     * @returns {string|null} Właściwość Livewire lub null
     */
    mapPathToLivewire(path) {
        // Mapowanie ścieżek z centralnego state na właściwości Livewire
        const mapping = {
            'personalData.motivation': 'motivation',
            'personalData.name': 'name',
            'personalData.email': 'email',
            'personalData.city': 'city',
            'experience.petExperience': 'petExperience',
            'experience.animalTypes': 'animalTypes',
            'experience.animalSizes': 'animalSizes',
            'services.serviceTypes': 'serviceTypes',
            'location.address': 'address',
            'location.serviceRadius': 'serviceRadius',
            'availability.flexibleSchedule': 'flexibleSchedule',
            'availability.emergencyAvailable': 'emergencyAvailable',
            'home.homeType': 'homeType',
            'home.hasGarden': 'hasGarden',
            'home.isSmoking': 'isSmoking',
            'home.hasOtherPets': 'hasOtherPets',
            'meta.currentStep': 'currentStep'
        };

        return mapping[path] || null;
    }

    /**
     * Dodaje obserwatora zmian stanu.
     *
     * @param {Function} callback - Funkcja wywołana przy zmianie
     * @returns {void}
     */
    watch(callback) {
        if (typeof callback === 'function') {
            this.watchers.push(callback);
        }
    }

    /**
     * Powiadamia wszystkich obserwatorów o zmianie.
     *
     * @param {string} path - Ścieżka do zmienionej właściwości
     * @param {*} newValue - Nowa wartość
     * @param {*} oldValue - Stara wartość
     * @returns {void}
     */
    notify(path, newValue, oldValue) {
        this.watchers.forEach(callback => {
            try {
                callback(path, newValue, oldValue);
            } catch (error) {
                console.error('❌ Watcher error:', error);
            }
        });
    }

    /**
     * Zapisuje zmianę w historii dla debugowania.
     *
     * @param {string} path - Ścieżka do właściwości
     * @param {*} oldValue - Stara wartość
     * @param {*} newValue - Nowa wartość
     * @returns {void}
     */
    recordChange(path, oldValue, newValue) {
        this.changeHistory.push({
            timestamp: new Date().toISOString(),
            path,
            oldValue,
            newValue,
            stack: new Error().stack
        });

        // Ogranicz rozmiar historii
        if (this.changeHistory.length > this.maxHistorySize) {
            this.changeHistory.shift();
        }
    }

    /**
     * Konfiguruje integrację z Livewire.
     *
     * @returns {void}
     */
    setupLivewireIntegration() {
        if (typeof window !== 'undefined') {
            // Nasłuchuj event Livewire loaded
            document.addEventListener('livewire:initialized', () => {
                if (this.debugging) {
                    console.log('🔗 Livewire integration initialized');
                }
            });

            // Nasłuchuj navigację
            document.addEventListener('livewire:navigated', () => {
                this.livewire = null; // Reset referencji
            });
        }
    }

    /**
     * Konfiguruje development helpers.
     *
     * @returns {void}
     */
    setupDevHelpers() {
        if (this.debugging && typeof window !== 'undefined') {
            // Globalne helpery dla developera
            window.dumpWizardState = () => {
                console.table(this.state);
                return this.state;
            };

            window.wizardHistory = () => {
                console.table(this.changeHistory);
                return this.changeHistory;
            };

            window.validateWizardStep = (step) => {
                const originalStep = this.state.meta.currentStep;
                this.state.meta.currentStep = step || originalStep;
                const result = this.validateCurrentStep();
                this.state.meta.currentStep = originalStep;
                console.log(`✅ Step ${step || originalStep} validation:`, result);
                return result;
            };

            window.resetWizardState = () => {
                console.log('🔄 Resetting wizard state...');
                this.state = new WizardStateManager().state;
                console.log('✅ State reset complete');
            };

            // Console info
            console.log(`
🧙‍♂️ WizardStateManager v3.0 Development Mode

Available commands:
• dumpWizardState() - Shows current state
• wizardHistory() - Shows change history
• validateWizardStep(step) - Validates specific step
• resetWizardState() - Resets state to initial
• window.WizardState.update(path, value) - Updates state
• window.WizardState.get(path) - Gets state value

Example usage:
WizardState.update('home.hasGarden', true)
WizardState.get('personalData.motivation')
            `);
        }
    }

    /**
     * Eksportuje kompletny state do JSON.
     *
     * @returns {string} JSON representation of state
     */
    exportState() {
        return JSON.stringify(this.state, null, 2);
    }

    /**
     * Importuje state z JSON.
     *
     * @param {string} jsonState - JSON state to import
     * @returns {boolean} Success status
     */
    importState(jsonState) {
        try {
            const imported = JSON.parse(jsonState);
            this.state = imported;
            this.updateMetaState();
            this.notify('*', this.state, {});
            return true;
        } catch (error) {
            console.error('❌ Import state error:', error);
            return false;
        }
    }

    /**
     * Resetuje state do wartości początkowych.
     *
     * @returns {void}
     */
    reset() {
        const initialState = new WizardStateManager().state;
        this.state = initialState;
        this.changeHistory = [];
        this.notify('*', this.state, {});

        if (this.debugging) {
            console.log('🔄 WizardStateManager reset to initial state');
        }
    }
}

// Inicjalizacja globalna - dostępna wszędzie w aplikacji
if (typeof window !== 'undefined') {
    // Stwórz globalną klasę
    window.WizardStateManager = WizardStateManager;

    // Automatyczna inicjalizacja gdy DOM jest gotowy - PRZYPISZ DO window.WizardState!
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.WizardState = new WizardStateManager();
            console.log('✅ WizardStateManager initialized on DOMContentLoaded');
        });
    } else {
        window.WizardState = new WizardStateManager();
        console.log('✅ WizardStateManager initialized immediately');
    }
}

// Export dla modułów ES6
export default WizardStateManager;