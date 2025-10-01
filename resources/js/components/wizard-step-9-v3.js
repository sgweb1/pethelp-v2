/**
 * Pet Sitter Wizard - Step 9 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 9 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 9 - Weryfikacja i dokumenty
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep9() {
    return {
        // === REACTIVE PROXY FOR GLOBAL STATE ===
        _reactiveUpdate: 0, // Force reactive updates when this changes
        _eventListenerRegistered: false, // Flaga zapobiegająca duplikacji event listenera

        // === DIRECT REACTIVE PROPERTIES (dla Alpine.js) ===
        identityDocument: null,
        hasCriminalRecordDeclaration: false,
        references: [],
        hasIdentityDocument: false,
        hasReferences: false,
        referencesCount: 0,
        canAddMoreReferences: true,
        maxReferences: 3,

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('📋 Step 9 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();

            // Nasłuchuj na event document-uploaded z Livewire - tylko raz!
            if (!this._eventListenerRegistered) {
                this._eventListenerRegistered = true;

                this.$wire.on('document-uploaded', (event) => {
                    console.log('📄 Document uploaded event received:', event);
                    const eventData = Array.isArray(event) && event.length > 0 ? event[0] : event;

                    if (eventData && eventData.type === 'identity' && eventData.data) {
                        console.log('📄 Updating identity document with data:', eventData.data);
                        window.WizardState.update('verification.identityDocument', eventData.data);
                        this.identityDocument = eventData.data;
                        this.hasIdentityDocument = true;
                        console.log('📄 Identity document updated from Livewire event');
                    }
                });

                console.log('📄 Event listener registered');
            } else {
                console.log('📄 Event listener already registered, skipping');
            }

            // Synchronizuj cache przy inicjalizacji
            this.syncCache();

            // Nasłuchuj na zmiany hasCriminalRecordDeclaration z Livewire
            this.$watch('$wire.hasCriminalRecordDeclaration', (value) => {
                console.log('🛡️ hasCriminalRecordDeclaration changed from Livewire:', value);
                this.hasCriminalRecordDeclaration = value;
                window.WizardState.update('verification.hasCriminalRecordDeclaration', value);
                this.updateDerivedState();
            });

            console.log('✅ Step 9 state initialized:', {
                identityDocument: !!this.identityDocument,
                hasCriminalRecordDeclaration: this.hasCriminalRecordDeclaration,
                references: this.references?.length || 0
            });
        },

        /**
         * Synchronizuje lokalne properties z globalnym state
         */
        syncCache() {
            this.identityDocument = window.WizardState?.get('verification.identityDocument') || null;
            this.hasCriminalRecordDeclaration = window.WizardState?.get('verification.hasCriminalRecordDeclaration') || false;
            this.references = window.WizardState?.get('verification.references') || [];
            this.hasIdentityDocument = !!this.identityDocument;
            this.hasReferences = this.references.length > 0;
            this.referencesCount = this.references.length;
            this.canAddMoreReferences = this.referencesCount < this.maxReferences;
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewireIdentityDocument = this.$wire?.identityDocument || null;
            const livewireHasCriminalRecordDeclaration = this.$wire?.hasCriminalRecordDeclaration || false;
            const livewireReferences = this.$wire?.references || [];

            console.log('📋 Step 9 initializing with Livewire data:', {
                livewireIdentityDocument: !!livewireIdentityDocument,
                livewireHasCriminalRecordDeclaration,
                livewireReferences: livewireReferences?.length || 0,
                currentIdentityDocument: !!this.identityDocument,
                currentReferences: this.references?.length || 0
            });

            // Always sync with Livewire data if available
            if (livewireIdentityDocument) {
                window.WizardState.update('verification.identityDocument', livewireIdentityDocument);
                console.log('📄 Identity document loaded from Livewire');
            } else if (!this.identityDocument) {
                window.WizardState.update('verification.identityDocument', null);
            }

            // Sync criminal record declaration
            window.WizardState.update('verification.hasCriminalRecordDeclaration', livewireHasCriminalRecordDeclaration);

            if (Array.isArray(livewireReferences) && livewireReferences.length > 0) {
                window.WizardState.update('verification.references', livewireReferences);
                console.log('👥 References loaded from Livewire:', livewireReferences.length);
            } else if (!Array.isArray(this.references)) {
                window.WizardState.update('verification.references', []);
            }

            // Update derived state
            this.updateDerivedState();
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Usuwa dokument tożsamości
         */
        async removeIdentityDocument() {
            console.log('📄 Removing identity document...');

            // Wywołaj Livewire method który zapisze do draftu
            await this.$wire.removeIdentityDocument();

            // Zaktualizuj lokalny state
            window.WizardState.update('verification.identityDocument', null);
            this.identityDocument = null;
            this.hasIdentityDocument = false;
            this.updateDerivedState();

            console.log('📄 Identity document removed and saved');
        },

        /**
         * Dodaje nową referencję
         */
        addReference() {
            if (this.referencesCount >= this.maxReferences) {
                alert(`Możesz dodać maksymalnie ${this.maxReferences} referencje.`);
                return;
            }

            const currentReferences = [...this.references];
            currentReferences.push({
                name: '',
                phone: '',
                relation: ''
            });

            window.WizardState.update('verification.references', currentReferences);
            this.references = currentReferences;
            this.referencesCount = currentReferences.length;
            this.hasReferences = true;
            this.canAddMoreReferences = currentReferences.length < this.maxReferences;
            this.updateDerivedState();
            this.syncWithLivewire('references', currentReferences);

            console.log('👥 Reference added. Total:', currentReferences.length);
        },

        /**
         * Usuwa referencję po indeksie
         */
        removeReference(index) {
            const currentReferences = [...this.references];
            if (index >= 0 && index < currentReferences.length) {
                currentReferences.splice(index, 1);

                window.WizardState.update('verification.references', currentReferences);
                this.references = currentReferences;
                this.referencesCount = currentReferences.length;
                this.hasReferences = currentReferences.length > 0;
                this.canAddMoreReferences = currentReferences.length < this.maxReferences;
                this.updateDerivedState();
                this.syncWithLivewire('references', currentReferences);

                console.log('👥 Reference removed. Total:', currentReferences.length);
            }
        },

        /**
         * Aktualizuje referencję
         */
        updateReference(index, field, value) {
            const currentReferences = [...this.references];
            if (index >= 0 && index < currentReferences.length) {
                currentReferences[index][field] = value;

                window.WizardState.update('verification.references', currentReferences);
                this.references = currentReferences;
                this.syncWithLivewire('references', currentReferences);
            }
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const hasRequiredDocuments = this.hasIdentityDocument;
            const isValid = hasRequiredDocuments; // Identity document is required

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 9) {
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
         * Formatuje rozmiar pliku
         */
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        /**
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.hasIdentityDocument;
        },

        /**
         * Zwraca klasy CSS dla karty statusu
         */
        getStatusCardClasses(hasDocument) {
            return {
                'border-green-500 bg-green-50': hasDocument,
                'border-gray-200': !hasDocument
            };
        },

        /**
         * Zwraca podsumowanie weryfikacji
         */
        getVerificationSummary() {
            return {
                hasIdentityDocument: this.hasIdentityDocument,
                hasCriminalRecordDeclaration: this.hasCriminalRecordDeclaration,
                referencesCount: this.referencesCount,
                hasReferences: this.hasReferences,
                isComplete: this.isStepValid()
            };
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep9;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep9 = wizardStep9;
}