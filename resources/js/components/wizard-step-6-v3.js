/**
 * Pet Sitter Wizard - Step 6 v3.0 (Stateless)
 *
 * Refaktoryzowany krok 6 wizard'a do architektury v3.0 z centralized state management.
 * Brak lokalnego state - wszystko przez WizardStateManager.
 *
 * @author Claude AI Assistant
 * @version 3.0.0
 */

/**
 * Stateless komponent dla Step 6 - Availability
 * Wszystkie zmienne pochodzą z globalnego WizardState
 */
function wizardStep6() {
    return {
        // === REACTIVE PROXY FOR GLOBAL STATE ===
        // Local reactive variables to trigger Alpine.js updates
        _reactiveUpdate: 0, // Force reactive updates when this changes
        showHintsPanel: false, // Local UI state for hints panel visibility

        /**
         * Inicjalizacja komponenty - stateless
         */
        init() {
            console.log('📅 Step 6 v3.0 initialized (stateless) - START');

            // Log all available methods for debugging
            console.log('🔍 Component methods check:', {
                isDayEnabled: typeof this.isDayEnabled,
                getDayClasses: typeof this.getDayClasses,
                getEmergencyClasses: typeof this.getEmergencyClasses,
                enabledDaysCount: typeof this.enabledDaysCount,
                toggleDay: typeof this.toggleDay
            });

            // Upewnij się że WizardStateManager jest dostępny
            if (!window.WizardState) {
                console.error('❌ WizardStateManager nie jest dostępny');
                return;
            }

            try {
                this.initializeStateIfNeeded();

                console.log('✅ Step 6 state initialized:', {
                    weeklyAvailability: Object.keys(this.weeklyAvailability).length,
                    flexibleSchedule: this.flexibleSchedule,
                    emergencyAvailable: this.emergencyAvailable,
                    wizardStateManager: !!window.WizardState,
                    testIsDayEnabled: this.isDayEnabled('monday'),
                    testEnabledDaysCount: this.enabledDaysCount
                });

                // Force Alpine.js update to ensure reactive bindings work
                this.$nextTick(() => {
                    console.log('🔄 Step 6 Alpine.js nextTick update complete');
                });

            } catch (error) {
                console.error('❌ Step 6 initialization error:', error);
            }

            console.log('📅 Step 6 v3.0 initialized (stateless) - END');
        },

        /**
         * Force reactive update dla Alpine.js
         */
        forceReactiveUpdate() {
            this._reactiveUpdate++;
            console.log('🔄 Forced reactive update:', this._reactiveUpdate);
        },

        /**
         * Inicjalizuje state jeśli jest pusty
         */
        initializeStateIfNeeded() {
            // Pobierz dane z Livewire jako fallback jeśli potrzebne
            const currentAvailability = window.WizardState.get('availability.weeklyAvailability');

            if (!currentAvailability || Object.keys(currentAvailability).length === 0) {
                // Inicjalizuj podstawową dostępność
                const defaultAvailability = {
                    monday: { enabled: false, start: '09:00', end: '17:00' },
                    tuesday: { enabled: false, start: '09:00', end: '17:00' },
                    wednesday: { enabled: false, start: '09:00', end: '17:00' },
                    thursday: { enabled: false, start: '09:00', end: '17:00' },
                    friday: { enabled: false, start: '09:00', end: '17:00' },
                    saturday: { enabled: false, start: '10:00', end: '16:00' },
                    sunday: { enabled: false, start: '10:00', end: '16:00' }
                };

                window.WizardState.update('availability.weeklyAvailability', defaultAvailability);
            }

            // Upewnij się że boolean flags mają sensowne wartości w WizardState
            const currentFlexible = window.WizardState?.get('availability.flexibleSchedule');
            const currentEmergency = window.WizardState?.get('availability.emergencyAvailable');

            if (currentFlexible === null || currentFlexible === undefined) {
                window.WizardState.update('availability.flexibleSchedule', false);
            }

            if (currentEmergency === null || currentEmergency === undefined) {
                window.WizardState.update('availability.emergencyAvailable', false);
            }
        },

        // === COMPUTED PROPERTIES - Z GLOBAL STATE ===

        /**
         * Harmonogram tygodniowy z globalnego state
         */
        get weeklyAvailability() {
            try {
                if (!window.WizardState) {
                    console.warn('⚠️ WizardState not available in weeklyAvailability getter');
                    return {};
                }
                const availability = window.WizardState.get('availability.weeklyAvailability');
                return availability || {};
            } catch (error) {
                console.error('❌ Error getting weeklyAvailability:', error);
                return {};
            }
        },

        /**
         * Czy elastyczny harmonogram
         */
        get flexibleSchedule() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('availability.flexibleSchedule') || false;
        },

        /**
         * Czy dostępny w nagłych przypadkach
         */
        get emergencyAvailable() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return window.WizardState?.get('availability.emergencyAvailable') || false;
        },

        /**
         * Liczba włączonych dni w tygodniu
         */
        get enabledDaysCount() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return Object.values(this.weeklyAvailability).filter(day => day.enabled).length;
        },

        // === CROSS-STEP VARIABLES - Z GLOBAL STATE ===
        // Te zmienne były wcześniej duplikowane lokalnie, teraz z global state

        /**
         * Czy ma ogród (z step-7)
         */
        get hasGarden() {
            return window.WizardState?.get('home.hasGarden') || false;
        },

        /**
         * Czy pali (z step-7)
         */
        get isSmoking() {
            return window.WizardState?.get('home.isSmoking') || false;
        },

        /**
         * Typ domu (z step-7)
         */
        get homeType() {
            return window.WizardState?.get('home.homeType') || '';
        },

        // === METHODS - OPERACJE NA GLOBAL STATE ===

        /**
         * Sprawdza czy dzień jest włączony
         */
        isDayEnabled(day) {
            try {
                // Force reactivity by accessing _reactiveUpdate
                this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

                const availability = this.weeklyAvailability;
                if (!availability || typeof availability !== 'object') {
                    console.warn('⚠️ weeklyAvailability is not available:', availability);
                    return false;
                }
                return availability[day]?.enabled || false;
            } catch (error) {
                console.error(`❌ Error checking day enabled for ${day}:`, error);
                return false;
            }
        },

        /**
         * Przełącza dzień on/off
         */
        toggleDay(day) {
            const currentSchedule = { ...this.weeklyAvailability };

            if (!currentSchedule[day]) {
                currentSchedule[day] = {
                    enabled: true,
                    start: day === 'saturday' || day === 'sunday' ? '10:00' : '09:00',
                    end: day === 'saturday' || day === 'sunday' ? '16:00' : '17:00'
                };
            } else {
                currentSchedule[day] = {
                    ...currentSchedule[day],
                    enabled: !currentSchedule[day].enabled
                };
            }

            window.WizardState.update('availability.weeklyAvailability', currentSchedule);
            this.syncWithLivewire('weeklyAvailability', currentSchedule);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log(`📅 Day ${day} toggled:`, {
                enabled: currentSchedule[day].enabled,
                totalEnabledDays: this.enabledDaysCount
            });
        },

        /**
         * Pobiera czas dla dnia
         */
        getDayTime(day, type) {
            return this.weeklyAvailability[day]?.[type] || (type === 'start' ? '09:00' : '17:00');
        },

        /**
         * Aktualizuje czas dla dnia
         */
        updateTime(day, type, value) {
            const currentSchedule = { ...this.weeklyAvailability };

            if (!currentSchedule[day]) {
                currentSchedule[day] = { enabled: true, start: '09:00', end: '17:00' };
            }

            // Aktualizuj wartość
            const updatedDay = {
                ...currentSchedule[day],
                [type]: value
            };

            // WALIDACJA CZASÓW: start musi być wcześniej niż end
            if (updatedDay.start >= updatedDay.end) {
                console.warn(`⚠️ Invalid time range for ${day}: ${updatedDay.start} - ${updatedDay.end}`);

                // Autokorekta: jeśli ustawiamy start, przesuwamy end o godzinę
                if (type === 'start') {
                    const startTime = new Date(`2000-01-01T${value}`);
                    startTime.setHours(startTime.getHours() + 1);
                    updatedDay.end = startTime.toTimeString().slice(0, 5);
                    console.log(`🔧 Auto-corrected end time to: ${updatedDay.end}`);
                }
                // Jeśli ustawiamy end, przesuwamy start o godzinę wstecz
                else if (type === 'end') {
                    const endTime = new Date(`2000-01-01T${value}`);
                    endTime.setHours(endTime.getHours() - 1);
                    updatedDay.start = endTime.toTimeString().slice(0, 5);
                    console.log(`🔧 Auto-corrected start time to: ${updatedDay.start}`);
                }
            }

            currentSchedule[day] = updatedDay;

            window.WizardState.update('availability.weeklyAvailability', currentSchedule);
            this.syncWithLivewire('weeklyAvailability', currentSchedule);

            console.log(`⏰ Time updated for ${day}:`, currentSchedule[day]);
        },

        /**
         * Przełącza elastyczny harmonogram
         */
        toggleFlexible() {
            const newValue = !this.flexibleSchedule;
            window.WizardState.update('availability.flexibleSchedule', newValue);
            this.syncWithLivewire('flexibleSchedule', newValue);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🕐 Flexible schedule toggled:', newValue);
        },

        /**
         * Przełącza dostępność w nagłych przypadkach
         */
        toggleEmergency() {
            const newValue = !this.emergencyAvailable;
            window.WizardState.update('availability.emergencyAvailable', newValue);
            this.syncWithLivewire('emergencyAvailable', newValue);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('🚨 Emergency availability toggled:', newValue);
        },

        /**
         * Przełącza widoczność panelu wskazówek
         */
        toggleHintsPanel() {
            this.showHintsPanel = !this.showHintsPanel;
            console.log('💡 Hints panel toggled:', this.showHintsPanel);
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
         * Zwraca klasy CSS dla dnia tygodnia
         */
        getDayClasses(day) {
            return {
                'border-emerald-500 bg-emerald-50': this.isDayEnabled(day),
                'border-gray-200': !this.isDayEnabled(day)
            };
        },

        /**
         * Zwraca klasy CSS dla flexible schedule
         */
        getFlexibleClasses() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return {
                'border-emerald-500 bg-emerald-50': this.flexibleSchedule,
                'border-gray-200': !this.flexibleSchedule
            };
        },

        /**
         * Zwraca klasy CSS dla emergency availability
         */
        getEmergencyClasses() {
            // Force reactivity by accessing _reactiveUpdate
            this._reactiveUpdate; // Ensure Alpine.js tracks this dependency

            return {
                'border-red-500 bg-red-50': this.emergencyAvailable,
                'border-gray-200': !this.emergencyAvailable
            };
        },

        /**
         * Sprawdza czy krok jest walid
         */
        isStepValid() {
            return this.enabledDaysCount > 0;
        },

        /**
         * Aktualizuje meta state dla step validation
         */
        updateStepValidation() {
            const currentStep = window.WizardState.get('meta.currentStep');
            if (currentStep === 6) {
                const isValid = this.isStepValid();
                window.WizardState.state.meta.canProceed = isValid;
                window.WizardState.state.meta.isValid = isValid;
            }
        },

        // === QUICK SETUP METHODS ===

        /**
         * Aplikuje predefiniowany szablon harmonogramu
         */
        applyScheduleTemplate(templateName) {
            console.log('🎨 Applying schedule template:', templateName);

            const templates = {
                'full_time': {
                    schedule: {
                        monday: { enabled: true, start: '09:00', end: '17:00' },
                        tuesday: { enabled: true, start: '09:00', end: '17:00' },
                        wednesday: { enabled: true, start: '09:00', end: '17:00' },
                        thursday: { enabled: true, start: '09:00', end: '17:00' },
                        friday: { enabled: true, start: '09:00', end: '17:00' },
                        saturday: { enabled: false, start: '10:00', end: '16:00' },
                        sunday: { enabled: false, start: '10:00', end: '16:00' }
                    },
                    flexible: false
                },
                'part_time': {
                    schedule: {
                        monday: { enabled: true, start: '10:00', end: '14:00' },
                        tuesday: { enabled: false, start: '10:00', end: '14:00' },
                        wednesday: { enabled: true, start: '10:00', end: '14:00' },
                        thursday: { enabled: false, start: '10:00', end: '14:00' },
                        friday: { enabled: true, start: '10:00', end: '14:00' },
                        saturday: { enabled: false, start: '10:00', end: '16:00' },
                        sunday: { enabled: false, start: '10:00', end: '16:00' }
                    },
                    flexible: false
                },
                'weekends': {
                    schedule: {
                        monday: { enabled: false, start: '09:00', end: '17:00' },
                        tuesday: { enabled: false, start: '09:00', end: '17:00' },
                        wednesday: { enabled: false, start: '09:00', end: '17:00' },
                        thursday: { enabled: false, start: '09:00', end: '17:00' },
                        friday: { enabled: false, start: '09:00', end: '17:00' },
                        saturday: { enabled: true, start: '10:00', end: '16:00' },
                        sunday: { enabled: true, start: '10:00', end: '16:00' }
                    },
                    flexible: false
                },
                'morning': {
                    schedule: {
                        monday: { enabled: true, start: '07:00', end: '12:00' },
                        tuesday: { enabled: true, start: '07:00', end: '12:00' },
                        wednesday: { enabled: true, start: '07:00', end: '12:00' },
                        thursday: { enabled: true, start: '07:00', end: '12:00' },
                        friday: { enabled: true, start: '07:00', end: '12:00' },
                        saturday: { enabled: true, start: '08:00', end: '12:00' },
                        sunday: { enabled: true, start: '08:00', end: '12:00' }
                    },
                    flexible: false
                },
                'evening': {
                    schedule: {
                        monday: { enabled: true, start: '15:00', end: '20:00' },
                        tuesday: { enabled: true, start: '15:00', end: '20:00' },
                        wednesday: { enabled: true, start: '15:00', end: '20:00' },
                        thursday: { enabled: true, start: '15:00', end: '20:00' },
                        friday: { enabled: true, start: '15:00', end: '20:00' },
                        saturday: { enabled: true, start: '16:00', end: '20:00' },
                        sunday: { enabled: true, start: '16:00', end: '20:00' }
                    },
                    flexible: false
                },
                'night': {
                    schedule: {
                        monday: { enabled: true, start: '20:00', end: '08:00' },
                        tuesday: { enabled: true, start: '20:00', end: '08:00' },
                        wednesday: { enabled: true, start: '20:00', end: '08:00' },
                        thursday: { enabled: true, start: '20:00', end: '08:00' },
                        friday: { enabled: true, start: '20:00', end: '08:00' },
                        saturday: { enabled: true, start: '20:00', end: '08:00' },
                        sunday: { enabled: true, start: '20:00', end: '08:00' }
                    },
                    flexible: false,
                    emergency: true
                }
            };

            const template = templates[templateName];
            if (!template) {
                console.error('❌ Unknown template:', templateName);
                return;
            }

            // Aplikuj szablon
            window.WizardState.update('availability.weeklyAvailability', template.schedule);
            window.WizardState.update('availability.flexibleSchedule', template.flexible);

            if (template.emergency !== undefined) {
                window.WizardState.update('availability.emergencyAvailable', template.emergency);
            }

            this.syncWithLivewire('weeklyAvailability', template.schedule);
            this.syncWithLivewire('flexibleSchedule', template.flexible);

            if (template.emergency !== undefined) {
                this.syncWithLivewire('emergencyAvailable', template.emergency);
            }

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();

            console.log('✅ Template applied:', templateName, template);
        },

        /**
         * Ustawia dostępność tylko dla dni roboczych (Pn-Pt, 9-17)
         */
        setWeekdaysOnly() {
            console.log('🚀 Quick setup: Weekdays only');
            const weekdaysSchedule = {
                monday: { enabled: true, start: '09:00', end: '17:00' },
                tuesday: { enabled: true, start: '09:00', end: '17:00' },
                wednesday: { enabled: true, start: '09:00', end: '17:00' },
                thursday: { enabled: true, start: '09:00', end: '17:00' },
                friday: { enabled: true, start: '09:00', end: '17:00' },
                saturday: { enabled: false, start: '10:00', end: '16:00' },
                sunday: { enabled: false, start: '10:00', end: '16:00' }
            };

            window.WizardState.update('availability.weeklyAvailability', weekdaysSchedule);
            window.WizardState.update('availability.flexibleSchedule', false);
            this.syncWithLivewire('weeklyAvailability', weekdaysSchedule);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();
        },

        /**
         * Ustawia dostępność dla całego tygodnia (9-17)
         */
        setFullWeek() {
            console.log('🚀 Quick setup: Full week');
            const fullWeekSchedule = {
                monday: { enabled: true, start: '09:00', end: '17:00' },
                tuesday: { enabled: true, start: '09:00', end: '17:00' },
                wednesday: { enabled: true, start: '09:00', end: '17:00' },
                thursday: { enabled: true, start: '09:00', end: '17:00' },
                friday: { enabled: true, start: '09:00', end: '17:00' },
                saturday: { enabled: true, start: '09:00', end: '17:00' },
                sunday: { enabled: true, start: '09:00', end: '17:00' }
            };

            window.WizardState.update('availability.weeklyAvailability', fullWeekSchedule);
            window.WizardState.update('availability.flexibleSchedule', false);
            this.syncWithLivewire('weeklyAvailability', fullWeekSchedule);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();
        },

        /**
         * Ustawia dostępność tylko dla weekendów (10-16)
         */
        setWeekendsOnly() {
            console.log('🚀 Quick setup: Weekends only');
            const weekendsSchedule = {
                monday: { enabled: false, start: '09:00', end: '17:00' },
                tuesday: { enabled: false, start: '09:00', end: '17:00' },
                wednesday: { enabled: false, start: '09:00', end: '17:00' },
                thursday: { enabled: false, start: '09:00', end: '17:00' },
                friday: { enabled: false, start: '09:00', end: '17:00' },
                saturday: { enabled: true, start: '10:00', end: '16:00' },
                sunday: { enabled: true, start: '10:00', end: '16:00' }
            };

            window.WizardState.update('availability.weeklyAvailability', weekendsSchedule);
            window.WizardState.update('availability.flexibleSchedule', false);
            this.syncWithLivewire('weeklyAvailability', weekendsSchedule);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();
        },

        /**
         * Włącza elastyczny harmonogram (brak sztywnych godzin)
         */
        setFlexibleSchedule() {
            console.log('🚀 Quick setup: Flexible schedule');
            const flexibleSchedule = {
                monday: { enabled: true, start: '08:00', end: '20:00' },
                tuesday: { enabled: true, start: '08:00', end: '20:00' },
                wednesday: { enabled: true, start: '08:00', end: '20:00' },
                thursday: { enabled: true, start: '08:00', end: '20:00' },
                friday: { enabled: true, start: '08:00', end: '20:00' },
                saturday: { enabled: true, start: '08:00', end: '20:00' },
                sunday: { enabled: true, start: '08:00', end: '20:00' }
            };

            window.WizardState.update('availability.weeklyAvailability', flexibleSchedule);
            window.WizardState.update('availability.flexibleSchedule', true);
            this.syncWithLivewire('weeklyAvailability', flexibleSchedule);
            this.syncWithLivewire('flexibleSchedule', true);

            // Force reactive update dla Alpine.js
            this.forceReactiveUpdate();
        },

        // === HELPER METHODS ===

        /**
         * Zwraca listę włączonych dni
         */
        getEnabledDays() {
            return Object.entries(this.weeklyAvailability)
                .filter(([day, config]) => config.enabled)
                .map(([day, config]) => ({ day, ...config }));
        },

        /**
         * Zwraca podsumowanie dostępności
         */
        getAvailabilitySummary() {
            const enabledDays = this.getEnabledDays();
            return {
                totalDays: enabledDays.length,
                flexibleSchedule: this.flexibleSchedule,
                emergencyAvailable: this.emergencyAvailable,
                weekendAvailable: enabledDays.some(day =>
                    day.day === 'saturday' || day.day === 'sunday'
                )
            };
        }
    };
}

// Export dla modułów ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = wizardStep6;
}

// Globalna dostępność
if (typeof window !== 'undefined') {
    window.wizardStep6 = wizardStep6;

    // Globalne helper functions dla quick setup (podobnie jak w step-5)
    window.setWeekdaysOnly = function() {
        const component = findActiveWizardStep6Component();
        if (component) {
            component.setWeekdaysOnly();
        } else {
            console.error('🔴 wizardStep6 component not found for setWeekdaysOnly');
        }
    };

    window.setFullWeek = function() {
        const component = findActiveWizardStep6Component();
        if (component) {
            component.setFullWeek();
        } else {
            console.error('🔴 wizardStep6 component not found for setFullWeek');
        }
    };

    window.setWeekendsOnly = function() {
        const component = findActiveWizardStep6Component();
        if (component) {
            component.setWeekendsOnly();
        } else {
            console.error('🔴 wizardStep6 component not found for setWeekendsOnly');
        }
    };

    window.setFlexibleSchedule = function() {
        const component = findActiveWizardStep6Component();
        if (component) {
            component.setFlexibleSchedule();
        } else {
            console.error('🔴 wizardStep6 component not found for setFlexibleSchedule');
        }
    };

    /**
     * Znajduje aktywny komponent wizardStep6 w DOM
     */
    function findActiveWizardStep6Component() {
        try {
            // Szukaj elementu z x-data="wizardStep6()"
            const wizardElements = document.querySelectorAll('[x-data*="wizardStep6"]');

            for (const element of wizardElements) {
                // Sprawdź czy element jest widoczny
                const isVisible = element.offsetParent !== null;

                if (isVisible && element._x_dataStack && element._x_dataStack.length > 0) {
                    // Znajdź komponent wizardStep6 w stosie Alpine.js
                    for (const dataObject of element._x_dataStack) {
                        if (dataObject && typeof dataObject.setWeekdaysOnly === 'function') {
                            console.log('✅ Found active wizardStep6 component');
                            return dataObject;
                        }
                    }
                }
            }

            console.warn('⚠️ wizardStep6 component not found or not initialized');
            return null;
        } catch (error) {
            console.error('🔴 Error finding wizardStep6 component:', error);
            return null;
        }
    }
}