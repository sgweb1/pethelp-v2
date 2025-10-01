/**
 * Centralne zarządzanie stanem wizarda Pet Sitter.
 *
 * Implementuje spójną architekturę synchronizacji między Alpine.js a Livewire
 * dla wszystkich kroków wizarda, eliminując problemy międzykrokowe.
 *
 * @version 2.0.0
 * @author Claude AI Assistant (Alpine.js & Livewire Specialist)
 */

/**
 * Globalny menedżer stanu wizarda - singleton pattern.
 */
window.WizardStateManager = {
    // ===== STAN GLOBALNY =====
    /**
     * Aktualny krok wizarda.
     */
    currentStep: 1,

    /**
     * Cache danych wszystkich kroków.
     */
    stepsData: new Map(),

    /**
     * Stan synchronizacji z Livewire.
     */
    syncStatus: {
        isConnected: false,
        lastSync: null,
        pendingSyncs: new Set(),
        errors: []
    },

    /**
     * Referencja do głównego komponentu Livewire.
     */
    $wire: null,

    /**
     * Debounce timers dla różnych operacji.
     */
    debounceTimers: new Map(),

    // ===== INICJALIZACJA =====
    /**
     * Inicjalizuje menedżer stanu wizarda.
     *
     * @param {object} $wire - Referencja Livewire
     * @param {number} initialStep - Początkowy krok
     */
    init($wire, initialStep = 1) {
        console.log('🧠 WizardStateManager: Initializing...', {
            initialStep,
            hasWire: !!$wire
        });

        this.$wire = $wire;
        this.currentStep = initialStep;
        this.syncStatus.isConnected = !!$wire;
        this.syncStatus.lastSync = new Date();

        // Inicjalizuj event listeners
        this.setupEventListeners();

        console.log('🧠 WizardStateManager: Initialized successfully');
    },

    /**
     * Konfiguruje event listeners dla komunikacji między komponentami.
     */
    setupEventListeners() {
        // Listen to step changes
        document.addEventListener('wizard-step-changed', (event) => {
            this.handleStepChange(event.detail);
        });

        // Listen to data updates
        document.addEventListener('wizard-data-updated', (event) => {
            this.handleDataUpdate(event.detail);
        });

        // Listen to Livewire errors
        document.addEventListener('livewire:error', (event) => {
            this.handleLivewireError(event.detail);
        });
    },

    // ===== ZARZĄDZANIE KROKAMI =====
    /**
     * Zmienia aktualny krok wizarda.
     *
     * @param {object} stepData - Dane nowego kroku
     */
    handleStepChange(stepData) {
        console.log('🧠 Step change detected:', stepData);

        this.currentStep = stepData.step;

        // Wysyłaj event do wszystkich komponentów kroków
        this.broadcastEvent('step-activated', {
            step: stepData.step,
            direction: stepData.direction || 'forward'
        });
    },

    /**
     * Pobiera dane dla konkretnego kroku.
     *
     * @param {number} step - Numer kroku
     * @returns {object} Dane kroku
     */
    getStepData(step) {
        return this.stepsData.get(step) || {};
    },

    /**
     * Ustawia dane dla konkretnego kroku.
     *
     * @param {number} step - Numer kroku
     * @param {object} data - Dane do zapisania
     */
    setStepData(step, data) {
        const currentData = this.stepsData.get(step) || {};
        const updatedData = { ...currentData, ...data };

        this.stepsData.set(step, updatedData);

        console.log(`🧠 Step ${step} data updated:`, updatedData);

        // Wysyłaj event o zmianie danych
        this.broadcastEvent('step-data-changed', {
            step,
            data: updatedData
        });
    },

    // ===== SYNCHRONIZACJA Z LIVEWIRE =====
    /**
     * Bezpiecznie synchronizuje właściwość z Livewire.
     *
     * @param {string} property - Nazwa właściwości
     * @param {any} value - Wartość
     * @param {object} options - Opcje synchronizacji
     */
    syncProperty(property, value, options = {}) {
        const {
            debounce = 0,
            immediate = false,
            retry = true
        } = options;

        if (!this.$wire) {
            console.warn('⚠️ WizardStateManager: $wire not available');
            return Promise.reject(new Error('Livewire not connected'));
        }

        const syncId = `${property}_${Date.now()}`;
        this.syncStatus.pendingSyncs.add(syncId);

        // Debounce jeśli wymagane
        if (debounce > 0 && !immediate) {
            return this.debouncedSync(property, value, debounce, syncId);
        }

        // Natychmiastowa synchronizacja
        return this.performSync(property, value, syncId, retry);
    },

    /**
     * Wykonuje debouncedą synchronizację.
     *
     * @param {string} property - Nazwa właściwości
     * @param {any} value - Wartość
     * @param {number} delay - Opóźnienie w ms
     * @param {string} syncId - ID synchronizacji
     */
    debouncedSync(property, value, delay, syncId) {
        return new Promise((resolve, reject) => {
            // Anuluj poprzedni timer dla tej właściwości
            const previousTimer = this.debounceTimers.get(property);
            if (previousTimer) {
                clearTimeout(previousTimer);
            }

            // Ustaw nowy timer
            const timer = setTimeout(async () => {
                try {
                    await this.performSync(property, value, syncId, true);
                    this.debounceTimers.delete(property);
                    resolve();
                } catch (error) {
                    reject(error);
                }
            }, delay);

            this.debounceTimers.set(property, timer);
        });
    },

    /**
     * Wykonuje faktyczną synchronizację z Livewire.
     *
     * @param {string} property - Nazwa właściwości
     * @param {any} value - Wartość
     * @param {string} syncId - ID synchronizacji
     * @param {boolean} retry - Czy ponawiać przy błędzie
     */
    async performSync(property, value, syncId, retry = true) {
        try {
            console.log(`🔄 Syncing ${property}:`, value);

            await this.$wire.set(property, value, false);

            // Usuń z pending syncs
            this.syncStatus.pendingSyncs.delete(syncId);
            this.syncStatus.lastSync = new Date();

            console.log(`✅ Successfully synced ${property}`);

            // Broadcast sukces
            this.broadcastEvent('property-synced', {
                property,
                value,
                success: true
            });

            return true;

        } catch (error) {
            console.error(`❌ Failed to sync ${property}:`, error);

            this.syncStatus.errors.push({
                property,
                value,
                error: error.message,
                timestamp: new Date(),
                syncId
            });

            // Retry logic
            if (retry && this.syncStatus.errors.length < 3) {
                console.log(`🔄 Retrying sync for ${property}...`);

                return new Promise((resolve, reject) => {
                    setTimeout(async () => {
                        try {
                            await this.performSync(property, value, syncId, false);
                            resolve(true);
                        } catch (retryError) {
                            reject(retryError);
                        }
                    }, 1000);
                });
            }

            // Broadcast błąd
            this.broadcastEvent('property-sync-failed', {
                property,
                value,
                error: error.message
            });

            throw error;
        }
    },

    /**
     * Synchronizuje wiele właściwości naraz.
     *
     * @param {object} data - Obiekt z właściwościami do synchronizacji
     * @param {object} options - Opcje synchronizacji
     */
    async syncMultiple(data, options = {}) {
        console.log('🔄 Syncing multiple properties:', Object.keys(data));

        const promises = Object.entries(data).map(([property, value]) => {
            return this.syncProperty(property, value, options);
        });

        try {
            await Promise.all(promises);
            console.log('✅ All properties synced successfully');
        } catch (error) {
            console.error('❌ Some properties failed to sync:', error);
            throw error;
        }
    },

    // ===== OBSŁUGA BŁĘDÓW =====
    /**
     * Obsługuje błędy Livewire.
     *
     * @param {object} errorData - Dane błędu
     */
    handleLivewireError(errorData) {
        console.error('❌ Livewire error:', errorData);

        this.syncStatus.errors.push({
            type: 'livewire_error',
            data: errorData,
            timestamp: new Date()
        });

        // Broadcast błąd do komponentów
        this.broadcastEvent('livewire-error', errorData);
    },

    /**
     * Obsługuje aktualizacje danych.
     *
     * @param {object} updateData - Dane aktualizacji
     */
    handleDataUpdate(updateData) {
        const { step, property, value } = updateData;

        console.log(`🔄 Data update: step ${step}, ${property} = `, value);

        // Aktualizuj cache
        const stepData = this.getStepData(step);
        stepData[property] = value;
        this.setStepData(step, stepData);

        // Synchronizuj z Livewire
        this.syncProperty(property, value, { debounce: 300 });
    },

    // ===== UTILITY METHODS =====
    /**
     * Wysyła event do wszystkich komponentów.
     *
     * @param {string} eventName - Nazwa eventu
     * @param {object} data - Dane eventu
     */
    broadcastEvent(eventName, data) {
        const event = new CustomEvent(`wizard-${eventName}`, {
            detail: data,
            bubbles: true
        });

        document.dispatchEvent(event);
    },

    /**
     * Sprawdza status połączenia z Livewire.
     *
     * @returns {boolean}
     */
    isConnected() {
        return this.syncStatus.isConnected && !!this.$wire;
    },

    /**
     * Pobiera statystyki synchronizacji.
     *
     * @returns {object}
     */
    getSyncStats() {
        return {
            isConnected: this.isConnected(),
            lastSync: this.syncStatus.lastSync,
            pendingSyncs: this.syncStatus.pendingSyncs.size,
            errors: this.syncStatus.errors.length,
            cachedSteps: this.stepsData.size
        };
    },

    /**
     * Czyści cache i resetuje stan.
     */
    reset() {
        console.log('🧠 WizardStateManager: Resetting state...');

        this.stepsData.clear();
        this.syncStatus.errors = [];
        this.syncStatus.pendingSyncs.clear();

        // Anuluj wszystkie debounce timers
        for (const timer of this.debounceTimers.values()) {
            clearTimeout(timer);
        }
        this.debounceTimers.clear();

        console.log('🧠 WizardStateManager: State reset complete');
    },

    /**
     * Debuguje aktualny stan menedżera.
     */
    debug() {
        console.group('🧠 WizardStateManager Debug');
        console.log('Current Step:', this.currentStep);
        console.log('Connection Status:', this.isConnected());
        console.log('Sync Stats:', this.getSyncStats());
        console.log('Steps Data:', Object.fromEntries(this.stepsData));
        console.log('Recent Errors:', this.syncStatus.errors.slice(-5));
        console.groupEnd();
    }
};

/**
 * Factory function dla komponentów kroków wizarda.
 * Zapewnia spójną architekturę i komunikację z menedżerem stanu.
 */
window.createWizardStepComponent = function(stepNumber, initialData = {}) {
    return {
        // ===== KONFIGURACJA KROKU =====
        stepNumber,

        // ===== LIFECYCLE =====
        init() {
            console.log(`📋 Step ${stepNumber}: Initializing component`);

            // Zarejestruj krok w menedżerze stanu
            WizardStateManager.setStepData(stepNumber, initialData);

            // Nasłuchuj zmian kroku
            this.setupStepListeners();

            // Synchronizuj initial data
            this.syncInitialData();

            console.log(`📋 Step ${stepNumber}: Component initialized`);
        },

        /**
         * Konfiguruje nasłuchiwanie zmian kroków.
         */
        setupStepListeners() {
            document.addEventListener('wizard-step-activated', (event) => {
                if (event.detail.step === stepNumber) {
                    this.onStepActivated(event.detail);
                } else {
                    this.onStepDeactivated(event.detail);
                }
            });
        },

        /**
         * Wywoływane gdy krok zostaje aktywowany.
         */
        onStepActivated(data) {
            console.log(`📋 Step ${stepNumber}: Activated`);
        },

        /**
         * Wywoływane gdy krok zostaje dezaktywowany.
         */
        onStepDeactivated(data) {
            console.log(`📋 Step ${stepNumber}: Deactivated`);
        },

        /**
         * Synchronizuje początkowe dane kroku.
         */
        syncInitialData() {
            if (Object.keys(initialData).length > 0) {
                WizardStateManager.syncMultiple(initialData, { debounce: 0 });
            }
        },

        // ===== HELPER METHODS =====
        /**
         * Aktualizuje właściwość kroku.
         */
        updateProperty(property, value) {
            // Aktualizuj lokalny stan (implementowane przez konkretny komponent)
            if (this[property] !== undefined) {
                this[property] = value;
            }

            // Wysyłaj event do menedżera stanu
            document.dispatchEvent(new CustomEvent('wizard-data-updated', {
                detail: {
                    step: stepNumber,
                    property,
                    value
                }
            }));
        },

        /**
         * Sprawdza czy krok jest kompletny.
         */
        isComplete() {
            // Implementowane przez konkretne komponenty
            return true;
        }
    };
};

console.log('🧠 WizardStateManager: Loaded successfully');