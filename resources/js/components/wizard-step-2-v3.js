/**
 * Pet Sitter Wizard - Step 2 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 2 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 2 - Pet Experience
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep2() {
    return {
        // === LOKALNE REAKTYWNE WŁAŚCIWOŚCI ===
        _petExperience: [],
        _yearsOfExperience: '',
        _experienceDescription: '',
        _characterCount: 0,

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('🐕 Step 2 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();
            this.setupLivewireSync();

            console.log('✅ Step 2 state initialized:', {
                petExperience: this.petExperience,
                yearsOfExperience: this.yearsOfExperience,
                experienceDescription: this.experienceDescription?.length || 0,
                characterCount: this.characterCount
            });
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewirePetExperience = this.$wire?.petExperience || [];
            const livewireYears = this.$wire?.yearsOfExperience || '';
            const livewireDescription = this.$wire?.experienceDescription || '';

            if (!Array.isArray(this.petExperience) && Array.isArray(livewirePetExperience)) {
                window.WizardState.update('experience.petExperience', livewirePetExperience);
            }

            if (!this.yearsOfExperience && livewireYears) {
                const yearsInt = parseInt(livewireYears, 10) || 0;
                window.WizardState.update('experience.yearsOfExperience', yearsInt);
            }

            if (!this.experienceDescription && livewireDescription) {
                window.WizardState.update('experience.experienceDescription', livewireDescription);
            }

            // Ensure arrays are initialized
            if (!Array.isArray(this.petExperience)) {
                window.WizardState.update('experience.petExperience', []);
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
                if (event.detail.field === 'experienceDescription') {
                    console.log('🎯 AI suggestion applied in step 2:', event.detail);
                    this.syncFromLivewire();
                }
            });
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE Z SYNCHRONIZACJĄ ===

        /**
         * Lista typów doświadczenia z globalnego state
         */
        get petExperience() {
            const value = window.WizardState?.get('experience.petExperience') || [];
            this._petExperience = value;
            return value;
        },

        /**
         * Lata doświadczenia z globalnego state
         */
        get yearsOfExperience() {
            const value = window.WizardState?.get('experience.yearsOfExperience') || '';
            this._yearsOfExperience = value;
            return value;
        },

        /**
         * Opis doświadczenia z globalnego state
         */
        get experienceDescription() {
            const value = window.WizardState?.get('experience.experienceDescription') || '';
            this._experienceDescription = value;
            return value;
        },

        /**
         * Liczba znaków w opisie doświadczenia
         */
        get characterCount() {
            return this.experienceDescription.length;
        },

        /**
         * Czy opis doświadczenia jest wystarczający (min 100 znaków)
         */
        get isDescriptionValid() {
            return this.characterCount >= 100;
        },

        /**
         * Progress w procentach (0-100) dla progress bar
         */
        get progressPercentage() {
            return Math.min((this.characterCount / 100) * 100, 100);
        },

        /**
         * Czy użytkownik zaczął pisać opis
         */
        get hasStartedTyping() {
            return this.characterCount > 0;
        },

        /**
         * Progress opisu w procentach
         */
        get descriptionProgress() {
            return Math.min((this.characterCount / 100) * 100, 100);
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Przełącza typ doświadczenia w liście
         */
        togglePetExperience(experienceType) {
            const currentExperience = [...this.petExperience];
            const index = currentExperience.indexOf(experienceType);

            if (index > -1) {
                // Usuń z listy
                currentExperience.splice(index, 1);
            } else {
                // Dodaj do listy
                currentExperience.push(experienceType);
            }

            // Aktualizuj global state
            window.WizardState.update('experience.petExperience', currentExperience);

            // Aktualizuj lokalną właściwość dla reaktywności Alpine
            this._petExperience = [...currentExperience];

            // Synchronizuj z Livewire
            this.syncWithLivewire('petExperience', currentExperience);

            console.log(`🐾 Pet experience ${experienceType} toggled. Current:`, currentExperience);
        },

        /**
         * Wymusza aktualizację UI po zmianie state
         */
        forceUIUpdate(experienceType) {
            // Znajdź element i wymuś aktualizację klasy
            this.$nextTick(() => {
                const element = document.querySelector(`label[data-experience="${experienceType}"]`);
                if (element) {
                    const isSelected = this.isPetExperienceSelected(experienceType);
                    console.log(`🎨 UI Update for ${experienceType}:`, { isSelected, element });

                    if (isSelected) {
                        element.classList.add('selected');
                        console.log(`✅ Added 'selected' class to ${experienceType}`);
                    } else {
                        element.classList.remove('selected');
                        console.log(`❌ Removed 'selected' class from ${experienceType}`);
                    }
                } else {
                    console.warn(`⚠️ Element not found for data-experience="${experienceType}"`);
                }
            });
        },

        /**
         * Sprawdza czy dany typ doświadczenia jest wybrany
         */
        isPetExperienceSelected(experienceType) {
            return this.petExperience.includes(experienceType);
        },

        /**
         * Aktualizuje lata doświadczenia
         */
        updateYearsOfExperience(value) {
            const yearsInt = parseInt(value, 10) || 0;
            window.WizardState.update('experience.yearsOfExperience', yearsInt);
            this.syncWithLivewire('yearsOfExperience', yearsInt);

            console.log('📅 Years of experience updated:', yearsInt);
        },

        /**
         * Aktualizuje opis doświadczenia
         */
        updateExperienceDescription(value) {
            window.WizardState.update('experience.experienceDescription', value);
            this.updateDerivedState();
            this.syncWithLivewire('experienceDescription', value);

            console.log('📝 Experience description updated:', {
                length: value.length,
                isValid: value.length >= 100
            });
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const description = this.experienceDescription;
            const characterCount = description.length;
            const isValid = characterCount >= 100 && this.petExperience.length > 0;
            const hasStartedTyping = characterCount > 0;

            // Update derived properties
            window.WizardState.update('experience.characterCount', characterCount);
            window.WizardState.update('experience.hasStartedTyping', hasStartedTyping);

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 2) {
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

        /**
         * Synchronizacja z Livewire po AI suggestions
         */
        syncFromLivewire() {
            if (window.Livewire && this.$wire) {
                try {
                    const wireDescription = this.$wire.experienceDescription || '';
                    const currentDescription = this.experienceDescription;

                    if (wireDescription !== currentDescription) {
                        console.log('🔄 Syncing from Livewire:', {
                            old: currentDescription.length,
                            new: wireDescription.length
                        });

                        // Aktualizuj global state
                        window.WizardState.update('experience.experienceDescription', wireDescription);
                        this.updateDerivedState();

                        // Wymuś aktualizację textarea
                        this.$nextTick(() => {
                            const textarea = document.getElementById('experienceDescription');
                            if (textarea) {
                                textarea.value = wireDescription;
                                // Trigger input event to update Alpine.js binding
                                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                console.log('✅ Experience textarea forcefully updated with AI content');
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
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.petExperience.length > 0 && this.isDescriptionValid;
        },

        /**
         * Zwraca klasy CSS dla textarea na podstawie stanu
         */
        getTextareaClasses() {
            return {
                'border-emerald-500 bg-emerald-50': this.isDescriptionValid && this.hasStartedTyping,
                'border-red-300 bg-red-50': this.hasStartedTyping && !this.isDescriptionValid && this.characterCount > 0,
                'border-gray-300': !this.hasStartedTyping
            };
        },

        /**
         * Zwraca klasy CSS dla progress indicators
         */
        getProgressClasses() {
            return {
                'text-emerald-600': this.isDescriptionValid,
                'text-red-500': this.hasStartedTyping && !this.isDescriptionValid && this.characterCount > 0,
                'text-gray-400': !this.hasStartedTyping || this.characterCount === 0
            };
        },

        /**
         * Zwraca styl dla progress bar
         */
        getProgressBarStyle() {
            return `width: ${this.descriptionProgress}%`;
        },

        /**
         * Zwraca tekst dla validation message
         */
        getValidationMessage() {
            if (this.petExperience.length === 0) {
                return 'Wybierz przynajmniej jeden typ doświadczenia';
            }
            if (!this.isDescriptionValid) {
                return `Opisz swoje doświadczenie (${this.characterCount}/100)`;
            }
            return 'Wymagania spełnione ✓';
        },

        // === HELPER METHODS ===

        /**
         * Zwraca podsumowanie doświadczenia
         */
        getExperienceSummary() {
            return {
                experienceTypes: this.petExperience,
                experienceTypesCount: this.petExperience.length,
                yearsOfExperience: this.yearsOfExperience,
                descriptionLength: this.characterCount,
                isDescriptionValid: this.isDescriptionValid,
                isComplete: this.isStepValid()
            };
        },

        /**
         * Zwraca czytelne nazwy typów doświadczenia
         */
        getExperienceTypeLabels() {
            const labels = {
                'own_pets': 'Własne zwierzęta',
                'family_pets': 'Zwierzęta rodziny/przyjaciół',
                'volunteering': 'Wolontariat w schronisku',
                'professional': 'Praca zawodowa',
                'training': 'Kursy/szkolenia',
                'veterinary': 'Doświadczenie weterynaryjne'
            };

            return this.petExperience.map(type => labels[type] || type);
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep2;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep2 = wizardStep2;
}