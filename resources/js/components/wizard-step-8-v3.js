/**
 * Pet Sitter Wizard - Step 8 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 8 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 8 - Profile Photos
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep8() {
    return {
        // === REACTIVE PROXY FOR GLOBAL STATE ===
        _reactiveUpdate: 0, // Force reactive updates when this changes
        _eventListenerRegistered: false, // Flaga zapobiegająca duplikacji event listenera

        // === DIRECT REACTIVE PROPERTIES (dla Alpine.js) ===
        profilePhoto: null,
        homePhotos: [],
        hasProfilePhoto: false,
        hasHomePhotos: false,
        homePhotosCount: 0,
        canAddMoreHomePhotos: true,
        maxHomePhotos: 5,
        uploadingHomePhotos: false,
        uploadProgress: '',

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('📸 Step 8 v3.0 initialized (stateless)');

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            this.initializeStateIfNeeded();

            // Nasłuchuj na event photo-uploaded z Livewire - tylko raz!
            if (!this._eventListenerRegistered) {
                this._eventListenerRegistered = true;

                this.$wire.on('photo-uploaded', (event) => {
                    console.log('📸 Photo uploaded event received:', event);
                    console.log('📸 Event details:', {
                        hasEvent: !!event,
                        eventType: typeof event,
                        isArray: Array.isArray(event),
                        eventKeys: event ? Object.keys(event) : 'none',
                        firstItem: Array.isArray(event) && event.length > 0 ? event[0] : event
                    });

                    // Livewire może wysłać event jako array z jednym elementem
                    const eventData = Array.isArray(event) && event.length > 0 ? event[0] : event;

                    console.log('📸 Processed event data:', eventData);

                    if (eventData && eventData.type === 'profile' && eventData.data) {
                        console.log('📸 Updating profile photo with data:', eventData.data);
                        window.WizardState.update('photos.profilePhoto', eventData.data);
                        this.profilePhoto = eventData.data;
                        this.hasProfilePhoto = true;

                        console.log('📸 Profile photo updated from Livewire event');
                    } else if (eventData && eventData.type === 'home' && eventData.data) {
                        console.log('📸 Adding home photo with data:', eventData.data);
                        const currentPhotos = [...this.homePhotos];
                        currentPhotos.push(eventData.data);
                        window.WizardState.update('photos.homePhotos', currentPhotos);
                        this.homePhotos = currentPhotos;
                        this.homePhotosCount = currentPhotos.length;
                        this.hasHomePhotos = true;
                        this.canAddMoreHomePhotos = currentPhotos.length < this.maxHomePhotos;

                        console.log('📸 Home photo added from Livewire event');
                    } else {
                        console.warn('📸 Event data does not match expected format:', {
                            eventData,
                            hasType: eventData?.type,
                            hasData: eventData?.data
                        });
                    }
                });

                console.log('📸 Event listener registered');
            } else {
                console.log('📸 Event listener already registered, skipping');
            }

            // Synchronizuj cache przy inicjalizacji
            this.syncCache();

            console.log('✅ Step 8 state initialized:', {
                profilePhoto: !!this.profilePhoto,
                homePhotos: this.homePhotos?.length || 0
            });
        },

        /**
         * Synchronizuje lokalne properties z globalnym state
         */
        syncCache() {
            this.profilePhoto = window.WizardState?.get('photos.profilePhoto') || null;
            this.homePhotos = window.WizardState?.get('photos.homePhotos') || [];
            this.hasProfilePhoto = !!this.profilePhoto;
            this.hasHomePhotos = this.homePhotos.length > 0;
            this.homePhotosCount = this.homePhotos.length;
            this.canAddMoreHomePhotos = this.homePhotosCount < this.maxHomePhotos;
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback
            const livewireProfilePhoto = this.$wire?.profilePhoto || null;
            const livewireHomePhotos = this.$wire?.homePhotos || [];

            console.log('📸 Step 8 initializing with Livewire data:', {
                livewireProfilePhoto: !!livewireProfilePhoto,
                livewireHomePhotos: livewireHomePhotos?.length || 0,
                currentProfilePhoto: !!this.profilePhoto,
                currentHomePhotos: this.homePhotos?.length || 0
            });

            // Always sync with Livewire data if available (for page refresh persistence)
            if (livewireProfilePhoto) {
                window.WizardState.update('photos.profilePhoto', livewireProfilePhoto);
                console.log('📸 Profile photo loaded from Livewire');
            } else if (!this.profilePhoto) {
                window.WizardState.update('photos.profilePhoto', null);
            }

            if (Array.isArray(livewireHomePhotos) && livewireHomePhotos.length > 0) {
                window.WizardState.update('photos.homePhotos', livewireHomePhotos);
                console.log('📸 Home photos loaded from Livewire:', livewireHomePhotos.length);
            } else if (!Array.isArray(this.homePhotos)) {
                window.WizardState.update('photos.homePhotos', []);
            }

            // Update derived state
            this.updateDerivedState();
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===


        /**
         * Usuwa zdjęcie profilowe
         */
        async removeProfilePhoto() {
            console.log('📸 Removing profile photo...');

            // Wywołaj Livewire method który zapisze do draftu
            await this.$wire.removeProfilePhoto();

            // Zaktualizuj lokalny state
            window.WizardState.update('photos.profilePhoto', null);
            this.profilePhoto = null;
            this.hasProfilePhoto = false;
            this.updateDerivedState();

            console.log('📸 Profile photo removed and saved');
        },


        /**
         * Usuwa zdjęcie domu po indeksie
         */
        async removeHomePhoto(index) {
            console.log('📸 Removing home photo at index:', index);

            // Wywołaj Livewire method który zapisze do draftu
            await this.$wire.removeHomePhoto(index);

            // Zaktualizuj lokalny state
            const currentPhotos = [...this.homePhotos];
            if (index >= 0 && index < currentPhotos.length) {
                currentPhotos.splice(index, 1);

                window.WizardState.update('photos.homePhotos', currentPhotos);
                this.homePhotos = currentPhotos;
                this.homePhotosCount = currentPhotos.length;
                this.hasHomePhotos = currentPhotos.length > 0;
                this.canAddMoreHomePhotos = currentPhotos.length < this.maxHomePhotos;
                this.updateDerivedState();

                console.log('📸 Home photo removed and saved. Total:', currentPhotos.length);
            }
        },

        /**
         * Aktualizuje pochodne właściwości w state
         */
        updateDerivedState() {
            if (!window.WizardState) return;

            const hasPhotos = this.hasProfilePhoto || this.hasHomePhotos;
            const isValid = this.hasProfilePhoto; // Profile photo is required

            // Update step validity
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 8) {
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

        // === FILE HANDLING METHODS ===

        /**
         * Obsługuje upload wielu zdjęć domu jednocześnie
         */
        async handleHomePhotosUpload(event) {
            const files = Array.from(event.target.files);
            if (files.length === 0) return;

            // Sprawdź ile można jeszcze dodać
            const availableSlots = this.maxHomePhotos - this.homePhotosCount;
            if (availableSlots <= 0) {
                alert('Osiągnięto maksymalną liczbę zdjęć domu (5).');
                return;
            }

            // Ogranicz do dostępnych slotów
            const filesToUpload = files.slice(0, availableSlots);
            if (filesToUpload.length < files.length) {
                alert(`Można dodać jeszcze ${availableSlots} zdjęć. Wybrano pierwsze ${filesToUpload.length} plików.`);
            }

            this.uploadingHomePhotos = true;
            let uploaded = 0;

            try {
                // Upload każdego pliku osobno przez Livewire
                for (const file of filesToUpload) {
                    this.uploadProgress = `${uploaded + 1}/${filesToUpload.length}`;

                    // Upload przez Livewire
                    await this.$wire.upload('tempHomePhoto', file, () => {
                        console.log(`📸 Uploading home photo ${uploaded + 1}/${filesToUpload.length}...`);
                    });

                    // Poczekaj na auto-save z lifecycle hook
                    await new Promise(resolve => setTimeout(resolve, 500));

                    uploaded++;
                }

                console.log(`📸 Successfully uploaded ${uploaded} home photos`);

                // Reset input
                event.target.value = '';
            } catch (error) {
                console.error('📸 Error uploading home photos:', error);
                alert(`Błąd podczas przesyłania zdjęć. Przesłano ${uploaded} z ${filesToUpload.length} plików.`);
            } finally {
                this.uploadingHomePhotos = false;
                this.uploadProgress = '';
            }
        },

        // === UI HELPER METHODS ===

        /**
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.hasProfilePhoto;
        },

        /**
         * Zwraca klasy CSS dla upload area
         */
        getUploadAreaClasses(hasPhoto = false) {
            return {
                'border-emerald-500 bg-emerald-50': hasPhoto,
                'border-gray-300 border-dashed hover:border-gray-400': !hasPhoto
            };
        },

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

        // === HELPER METHODS ===

        /**
         * Zwraca podsumowanie zdjęć
         */
        getPhotosSummary() {
            return {
                hasProfilePhoto: this.hasProfilePhoto,
                homePhotosCount: this.homePhotosCount,
                canAddMoreHomePhotos: this.canAddMoreHomePhotos,
                maxHomePhotos: this.maxHomePhotos,
                isComplete: this.isStepValid()
            };
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep8;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep8 = wizardStep8;
}