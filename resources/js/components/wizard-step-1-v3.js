/**
 * Pet Sitter Wizard - Step 1 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 1 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 1 - Motivation
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep1() {
    return {
        // === BRAK LOKALNYCH ZMIENNYCH - WSZYSTKO Z GLOBAL STATE ===

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('🏗️ Step 1 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            // Inicjalizuj state jeśli puste
            this.initializeStateIfNeeded();

            // Setup Livewire sync jeśli potrzebne
            this.setupLivewireSync();

            console.log('✅ Step 1 state initialized:', {
                motivation: this.motivation,
                characterCount: this.characterCount,
                isValid: this.isValid
            });
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewireMotivation = this.$wire?.motivation || '';
            const currentMotivation = window.WizardState.get('personalData.motivation');

            if (!currentMotivation && livewireMotivation) {
                window.WizardState.update('personalData.motivation', livewireMotivation);
            }

            // Update derived state
            this.updateDerivedState();
        },

        /**
         * Setup synchronizacji z Livewire
         */
        setupLivewireSync() {
            // Listen dla AI suggestion applied
            document.addEventListener('ai-suggestion-applied', (event) => {
                if (event.detail.field === 'motivation') {
                    console.log('🎯 AI suggestion applied in step 1:', event.detail);
                    this.syncFromLivewire();

                    // Wymuś aktualizację textarea
                    const textarea = document.getElementById('motivation');
                    if (textarea && textarea.value !== this.motivation) {
                        textarea.value = this.motivation;
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE ===

        /**
         * Aktualny tekst motywacji z globalnego state
         */
        get motivation() {
            return window.WizardState?.get('personalData.motivation') || '';
        },

        /**
         * Liczba znaków w motywacji
         */
        get characterCount() {
            return this.motivation.length;
        },

        /**
         * Czy motywacja jest walida (min 50 znaków)
         */
        get isValid() {
            return this.characterCount >= 50;
        },

        /**
         * Czy użytkownik zaczął pisać
         */
        get hasStartedTyping() {
            return this.characterCount > 0;
        },

        /**
         * Progress motywacji w procentach
         */
        get progressPercentage() {
            return Math.min((this.characterCount / 50) * 100, 100);
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Aktualizuje motywację przez global state
         */
        updateMotivation(value) {
            window.WizardState?.update('personalData.motivation', value);
            this.updateDerivedState();
            this.updateLivewire('motivation', value);

            // Wymuś aktualizację textarea
            this.$nextTick(() => {
                const textarea = document.getElementById('motivation');
                if (textarea) {
                    textarea.value = value;
                    // Trigger input event to update Alpine.js binding
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    // Focus on textarea for user convenience
                    textarea.focus();
                }
            });

            console.log('📝 Motivation updated:', {
                length: value.length,
                isValid: value.length >= 50
            });
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const motivation = this.motivation;
            const characterCount = motivation.length;
            const isValid = characterCount >= 50;
            const hasStartedTyping = characterCount > 0;

            // Update meta state jeśli potrzebne
            window.WizardState.update('personalData.motivationLength', characterCount);
            window.WizardState.update('personalData.hasStartedTyping', hasStartedTyping);

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 1) {
                window.WizardState.state.meta.canProceed = isValid;
                window.WizardState.state.meta.isValid = isValid;
            }
        },

        /**
         * Synchronizuje z Livewire
         */
        updateLivewire(property, value) {
            if (window.Livewire && this.$wire) {
                try {
                    this.$wire.set(property, value, false);
                } catch (error) {
                    console.error('🔄 Livewire sync error:', error);
                }
            }
        },

        /**
         * Synchronizacja z Livewire po AI suggestions
         */
        syncFromLivewire() {
            if (window.Livewire && this.$wire) {
                try {
                    const wireMotivation = this.$wire.motivation || '';
                    const currentMotivation = this.motivation;

                    if (wireMotivation !== currentMotivation) {
                        console.log('🔄 Syncing from Livewire:', {
                            old: currentMotivation.length,
                            new: wireMotivation.length
                        });

                        // Aktualizuj global state
                        window.WizardState.update('personalData.motivation', wireMotivation);
                        this.updateDerivedState();

                        // Wymuś aktualizację textarea
                        this.$nextTick(() => {
                            const textarea = document.getElementById('motivation');
                            if (textarea) {
                                textarea.value = wireMotivation;
                                // Trigger input event to update Alpine.js binding
                                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                console.log('✅ Textarea forcefully updated with AI content');
                            }
                        });
                    }
                } catch (error) {
                    console.error('🔄 Sync from Livewire error:', error);
                }
            }
        },

        // === UI HELPER METHODS ===

        /**
         * Zwraca klasy CSS dla textarea na podstawie stanu
         */
        getTextareaClasses() {
            return {
                'border-emerald-500 bg-emerald-50': this.isValid && this.hasStartedTyping,
                'border-red-300 bg-red-50': this.hasStartedTyping && !this.isValid && this.characterCount > 0,
                'border-gray-300': !this.hasStartedTyping
            };
        },

        /**
         * Zwraca klasy CSS dla progress indicators
         */
        getProgressClasses() {
            return {
                'text-emerald-600': this.isValid,
                'text-red-500': this.hasStartedTyping && !this.isValid && this.characterCount > 0,
                'text-gray-400': !this.hasStartedTyping || this.characterCount === 0
            };
        },

        /**
         * Zwraca styl dla progress bar
         */
        getProgressBarStyle() {
            return `width: ${this.progressPercentage}%`;
        },

        /**
         * Zwraca klasy CSS dla progress bar
         */
        getProgressBarClasses() {
            return {
                'bg-gradient-to-r from-emerald-400 to-emerald-600': this.characterCount >= 50,
                'bg-gradient-to-r from-blue-400 to-blue-600': this.characterCount < 50 && this.characterCount > 0,
                'bg-gray-300': this.characterCount === 0
            };
        },

        /**
         * Zwraca klasy CSS dla validation status
         */
        getValidationClasses() {
            return {
                'text-emerald-600': this.isValid,
                'text-gray-500': !this.isValid
            };
        },

        /**
         * Zwraca klasy CSS dla check icon
         */
        getCheckIconClasses() {
            return {
                'text-emerald-500': this.isValid,
                'text-gray-400': !this.isValid
            };
        },

        /**
         * Zwraca tekst dla validation message
         */
        getValidationMessage() {
            return this.isValid
                ? 'Minimalne wymagania spełnione ✓'
                : 'Minimalne 50 znaków wymagane';
        }
    };
}

// Eksport dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep1;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep1 = wizardStep1;
}