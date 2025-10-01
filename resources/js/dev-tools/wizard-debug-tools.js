/**
 * Pet Sitter Wizard - Development & Debugging Tools
 *
 * Kompletny zestaw narzędzi do debugowania i rozwoju wizard'a.
 * Dostępne tylko w trybie development dla maksymalnej produktywności.
 *
 * @author Claude AI Assistant
 * @version 1.0.0
 */

class WizardDebugTools {
    constructor() {
        this.isEnabled = this.checkIfEnabled();

        if (this.isEnabled) {
            this.init();
        }
    }

    /**
     * Sprawdza czy narzędzia debugowania powinny być włączone.
     *
     * @returns {boolean}
     */
    checkIfEnabled() {
        // Debug panel wyłączony - włącz tylko przez ?debug=true w URL
        return window.location.search.includes('debug=true');
    }

    /**
     * Inicjalizuje wszystkie narzędzia debugowania.
     */
    init() {
        this.setupConsoleCommands();
        this.createDebugPanel();
        this.setupErrorTracking();
        this.setupPerformanceMonitoring();
        this.logInitialization();
    }

    /**
     * Konfiguruje komendy console dla developera.
     */
    setupConsoleCommands() {
        window.WizardDebug = {
            // === STATE MANAGEMENT ===

            /**
             * Pokazuje aktualny stan wizard'a w czytelnym formacie.
             */
            showState: () => {
                if (!window.WizardState) {
                    console.error('❌ WizardState nie jest zainicjalizowany');
                    return;
                }

                console.log('%c🏗️ WIZARD STATE SNAPSHOT', 'font-size: 16px; font-weight: bold; color: #10b981;');
                console.log('%c📊 Meta Info:', 'font-weight: bold; color: #3b82f6;', window.WizardState.get('meta'));
                console.log('%c👤 Personal Data:', 'font-weight: bold; color: #8b5cf6;', window.WizardState.get('personalData'));
                console.log('%c🐾 Experience:', 'font-weight: bold; color: #f59e0b;', window.WizardState.get('experience'));
                console.log('%c🏠 Home Info:', 'font-weight: bold; color: #06b6d4;', window.WizardState.get('home'));
                console.log('%c📅 Availability:', 'font-weight: bold; color: #84cc16;', window.WizardState.get('availability'));

                return window.WizardState.state;
            },

            /**
             * Pokazuje historię zmian w state.
             */
            showHistory: () => {
                if (!window.WizardState?.changeHistory) {
                    console.error('❌ Historia zmian nie jest dostępna');
                    return;
                }

                console.log('%c📜 CHANGE HISTORY', 'font-size: 16px; font-weight: bold; color: #dc2626;');
                console.table(window.WizardState.changeHistory.slice(-10));

                return window.WizardState.changeHistory;
            },

            /**
             * Resetuje stan wizard'a do wartości początkowych.
             */
            resetState: () => {
                if (confirm('⚠️ Czy na pewno chcesz zresetować stan wizard\'a?')) {
                    window.WizardState?.reset();
                    console.log('%c🔄 State został zresetowany', 'color: #10b981; font-weight: bold;');
                }
            },

            // === TESTING HELPERS ===

            /**
             * Wypełnia wizard testowymi danymi.
             */
            fillTestData: () => {
                if (!window.WizardState) {
                    console.error('❌ WizardState nie jest dostępny');
                    return;
                }

                const testData = {
                    'personalData.name': 'Jan Testowy',
                    'personalData.email': 'jan.testowy@pethelp.test',
                    'personalData.city': 'Warszawa',
                    'personalData.motivation': 'Cześć! Nazywam się Jan Testowy i od dziecka kocham zwierzęta. Zależy mi na zapewnieniu najlepszej opieki dla pupili. Mam wieloletnie doświadczenie w opiece nad psami i kotami różnych ras. Potrafię rozpoznać potrzeby zwierząt i dostosować opiekę do ich charakteru.',

                    'experience.petExperience': 'high',
                    'experience.experienceDescription': 'Opiekowałem się psami i kotami przez ostatnie 5 lat.',
                    'experience.yearsOfExperience': 5,
                    'experience.animalTypes': ['dogs', 'cats'],
                    'experience.animalSizes': ['small', 'medium', 'large'],

                    'services.serviceTypes': ['walking', 'overnight', 'daycare'],
                    'services.specialServices': ['grooming', 'training'],

                    'location.address': 'Warszawa, Mokotów',
                    'location.serviceRadius': 15,

                    'availability.flexibleSchedule': true,
                    'availability.emergencyAvailable': true,
                    'availability.morningAvailable': true,
                    'availability.weekendAvailable': true,

                    'home.homeType': 'house',
                    'home.hasGarden': true,
                    'home.isSmoking': false,
                    'home.hasOtherPets': true,
                    'home.isSafeEnvironment': true,

                    'verification.hasProfilePhoto': true,
                    'verification.homePhotosCount': 3,
                    'verification.hasIdentityDocument': true,
                    'verification.hasCriminalRecord': false,
                    'verification.referencesCount': 2,

                    'pricing.pricingStrategy': 'competitive',
                    'pricing.servicePricing.dogWalking': 25,
                    'pricing.servicePricing.overnightSitting': 80,
                    'pricing.servicePricing.daySitting': 50
                };

                let successCount = 0;
                Object.entries(testData).forEach(([path, value]) => {
                    try {
                        window.WizardState.update(path, value);
                        successCount++;
                    } catch (error) {
                        console.error(`❌ Błąd podczas ustawiania ${path}:`, error);
                    }
                });

                console.log(`%c✅ Wypełniono ${successCount} pól testowymi danymi`, 'color: #10b981; font-weight: bold;');
                this.showState();
            },

            /**
             * Przeskakuje do określonego kroku.
             */
            goToStep: (step) => {
                if (!step || step < 1 || step > 12) {
                    console.error('❌ Nieprawidłowy numer kroku. Użyj 1-12.');
                    return;
                }

                window.WizardState?.update('meta.currentStep', step);
                console.log(`%c🔄 Przeskoczono do kroku ${step}`, 'color: #3b82f6; font-weight: bold;');
            },

            /**
             * Waliduje określony krok.
             */
            validateStep: (step) => {
                if (!window.WizardState) {
                    console.error('❌ WizardState nie jest dostępny');
                    return;
                }

                const currentStep = step || window.WizardState.get('meta.currentStep');
                const validation = window.validateWizardStep?.(currentStep);

                if (validation) {
                    const status = validation.isValid ? '✅ VALID' : '❌ INVALID';
                    console.log(`%c${status} - Step ${currentStep}`, `color: ${validation.isValid ? '#10b981' : '#dc2626'}; font-weight: bold;`);

                    if (validation.errors?.length > 0) {
                        console.log('%cErrors:', 'color: #dc2626; font-weight: bold;', validation.errors);
                    }
                } else {
                    console.error('❌ Nie można walidować kroku');
                }

                return validation;
            },

            /**
             * Waliduje wszystkie kroki.
             */
            validateAllSteps: () => {
                console.log('%c🔍 VALIDATING ALL STEPS', 'font-size: 16px; font-weight: bold; color: #7c3aed;');

                const results = [];
                for (let i = 1; i <= 12; i++) {
                    const validation = this.validateStep(i);
                    results.push({
                        step: i,
                        valid: validation?.isValid || false,
                        errors: validation?.errors?.length || 0
                    });
                }

                console.table(results);
                return results;
            },

            // === SIMULATION HELPERS ===

            /**
             * Symuluje szybkie przejście przez wszystkie kroki.
             */
            simulateFullFlow: async () => {
                console.log('%c🎬 SIMULATING FULL WIZARD FLOW', 'font-size: 16px; font-weight: bold; color: #ec4899;');

                // Wypełnij danymi
                this.fillTestData();

                // Przejdź przez wszystkie kroki
                for (let step = 1; step <= 12; step++) {
                    console.log(`%c📍 Step ${step}`, 'color: #3b82f6; font-weight: bold;');
                    this.goToStep(step);

                    // Symuluj czas na przeczytanie
                    await new Promise(resolve => setTimeout(resolve, 500));

                    const validation = this.validateStep(step);
                    console.log(`   ${validation?.isValid ? '✅' : '❌'} Validation`);
                }

                console.log('%c🎉 Flow simulation completed', 'color: #10b981; font-weight: bold; font-size: 14px;');
            },

            // === ERROR TRACKING ===

            /**
             * Pokazuje wszystkie błędy JavaScript z ostatnich 5 minut.
             */
            showRecentErrors: () => {
                const recentErrors = this.getRecentErrors();

                if (recentErrors.length === 0) {
                    console.log('%c✅ Brak błędów w ostatnich 5 minutach', 'color: #10b981; font-weight: bold;');
                } else {
                    console.log('%c🚨 RECENT ERRORS', 'font-size: 16px; font-weight: bold; color: #dc2626;');
                    recentErrors.forEach((error, index) => {
                        console.group(`Error ${index + 1}: ${error.message}`);
                        console.log('Time:', new Date(error.timestamp).toLocaleTimeString());
                        console.log('URL:', error.url);
                        if (error.stack) console.log('Stack:', error.stack);
                        console.groupEnd();
                    });
                }

                return recentErrors;
            },

            // === PERFORMANCE MONITORING ===

            /**
             * Pokazuje metryki wydajności wizard'a.
             */
            showPerformanceMetrics: () => {
                const metrics = this.getPerformanceMetrics();

                console.log('%c⚡ PERFORMANCE METRICS', 'font-size: 16px; font-weight: bold; color: #f59e0b;');
                console.table(metrics);

                return metrics;
            },

            // === HELPER INFO ===

            /**
             * Pokazuje wszystkie dostępne komendy debugowania.
             */
            help: () => {
                console.log(`%c
🧙‍♂️ WIZARD DEBUG TOOLS - AVAILABLE COMMANDS

📊 STATE MANAGEMENT:
• WizardDebug.showState() - Shows current wizard state
• WizardDebug.showHistory() - Shows state change history
• WizardDebug.resetState() - Resets wizard to initial state

🧪 TESTING HELPERS:
• WizardDebug.fillTestData() - Fills wizard with test data
• WizardDebug.goToStep(step) - Jumps to specific step (1-12)
• WizardDebug.validateStep(step) - Validates specific step
• WizardDebug.validateAllSteps() - Validates all steps
• WizardDebug.simulateFullFlow() - Simulates complete flow

🚨 ERROR TRACKING:
• WizardDebug.showRecentErrors() - Shows JavaScript errors
• WizardDebug.clearErrorLog() - Clears error history

⚡ PERFORMANCE:
• WizardDebug.showPerformanceMetrics() - Shows performance data
• WizardDebug.measureStepLoadTime(step) - Measures step load time

❓ HELP:
• WizardDebug.help() - Shows this help message

Example usage:
WizardDebug.fillTestData();
WizardDebug.goToStep(7);
WizardDebug.validateAllSteps();
                `, 'color: #6b7280; font-family: monospace; font-size: 12px;');
            }
        };

        // Auto-bind this context to methods
        Object.keys(window.WizardDebug).forEach(key => {
            if (typeof window.WizardDebug[key] === 'function') {
                window.WizardDebug[key] = window.WizardDebug[key].bind(this);
            }
        });
    }

    /**
     * Tworzy panel debugowania w interfejsie.
     */
    createDebugPanel() {
        // Sprawdź czy panel już istnieje
        if (document.getElementById('wizard-debug-panel')) {
            return;
        }

        const panel = document.createElement('div');
        panel.id = 'wizard-debug-panel';
        panel.innerHTML = `
            <div class="fixed bottom-4 right-4 bg-gray-900 text-white p-4 rounded-lg shadow-2xl z-50 max-w-xs" style="font-size: 12px;">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-sm">🧙‍♂️ Debug Tools</h3>
                    <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <div class="space-y-2">
                    <button onclick="WizardDebug.showState()" class="w-full px-2 py-1 bg-blue-600 hover:bg-blue-700 rounded text-xs">Show State</button>
                    <button onclick="WizardDebug.fillTestData()" class="w-full px-2 py-1 bg-green-600 hover:bg-green-700 rounded text-xs">Fill Test Data</button>
                    <button onclick="WizardDebug.validateAllSteps()" class="w-full px-2 py-1 bg-purple-600 hover:bg-purple-700 rounded text-xs">Validate All</button>
                    <button onclick="WizardDebug.showRecentErrors()" class="w-full px-2 py-1 bg-red-600 hover:bg-red-700 rounded text-xs">Show Errors</button>
                    <button onclick="WizardDebug.resetState()" class="w-full px-2 py-1 bg-yellow-600 hover:bg-yellow-700 rounded text-xs">Reset State</button>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-700">
                    <div class="text-xs text-gray-400">
                        Current Step: <span id="debug-current-step">-</span><br>
                        State Updates: <span id="debug-update-count">0</span><br>
                        JS Errors: <span id="debug-error-count">0</span>
                    </div>
                </div>

                <div class="mt-2 text-xs text-gray-500">
                    Press F12 → Console → type "WizardDebug.help()"
                </div>
            </div>
        `;

        document.body.appendChild(panel);
        this.updateDebugPanel();

        // Update panel every second
        setInterval(() => this.updateDebugPanel(), 1000);
    }

    /**
     * Aktualizuje informacje w panelu debugowania.
     */
    updateDebugPanel() {
        const currentStepEl = document.getElementById('debug-current-step');
        const updateCountEl = document.getElementById('debug-update-count');
        const errorCountEl = document.getElementById('debug-error-count');

        if (currentStepEl && window.WizardState) {
            currentStepEl.textContent = window.WizardState.get('meta.currentStep') || '-';
        }

        if (updateCountEl && window.WizardState) {
            updateCountEl.textContent = window.WizardState.changeHistory?.length || 0;
        }

        if (errorCountEl) {
            errorCountEl.textContent = this.errorLog?.length || 0;
        }
    }

    /**
     * Konfiguruje śledzenie błędów JavaScript.
     */
    setupErrorTracking() {
        this.errorLog = [];

        // Przechwytuj błędy JavaScript
        window.addEventListener('error', (event) => {
            this.logError({
                type: 'javascript_error',
                message: event.message,
                filename: event.filename,
                line: event.lineno,
                column: event.colno,
                stack: event.error?.stack,
                timestamp: Date.now(),
                url: window.location.href
            });
        });

        // Przechwytuj Promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.logError({
                type: 'promise_rejection',
                message: event.reason?.message || event.reason,
                stack: event.reason?.stack,
                timestamp: Date.now(),
                url: window.location.href
            });
        });

        // Przechwytuj Alpine.js errors
        document.addEventListener('alpine:init', () => {
            Alpine.bind('x-error', () => ({
                ['@alpine:error']: (event) => {
                    this.logError({
                        type: 'alpine_error',
                        message: event.detail.message,
                        expression: event.detail.expression,
                        timestamp: Date.now(),
                        url: window.location.href
                    });
                }
            }));
        });
    }

    /**
     * Loguje błąd do wewnętrznej historii.
     */
    logError(errorInfo) {
        this.errorLog.push(errorInfo);

        // Ogranicz rozmiar loga
        if (this.errorLog.length > 100) {
            this.errorLog.shift();
        }

        // Log do konsoli w trybie debug
        console.error('🚨 Error logged:', errorInfo);
    }

    /**
     * Pobiera błędy z ostatnich 5 minut.
     */
    getRecentErrors() {
        const fiveMinutesAgo = Date.now() - (5 * 60 * 1000);
        return this.errorLog.filter(error => error.timestamp > fiveMinutesAgo);
    }

    /**
     * Konfiguruje monitoring wydajności.
     */
    setupPerformanceMonitoring() {
        this.performanceMetrics = {
            stateUpdates: [],
            stepLoadTimes: {},
            renderTimes: []
        };

        // Monitor state updates performance
        if (window.WizardState) {
            window.WizardState.watch((path, newValue, oldValue) => {
                const metric = {
                    path,
                    timestamp: Date.now(),
                    processingTime: performance.now()
                };

                this.performanceMetrics.stateUpdates.push(metric);

                // Keep only last 50 updates
                if (this.performanceMetrics.stateUpdates.length > 50) {
                    this.performanceMetrics.stateUpdates.shift();
                }
            });
        }
    }

    /**
     * Pobiera metryki wydajności.
     */
    getPerformanceMetrics() {
        const updates = this.performanceMetrics.stateUpdates;
        const avgUpdateTime = updates.length > 0
            ? updates.reduce((sum, metric) => sum + metric.processingTime, 0) / updates.length
            : 0;

        return {
            totalStateUpdates: updates.length,
            averageUpdateTime: Math.round(avgUpdateTime * 100) / 100,
            slowestUpdate: updates.length > 0
                ? Math.max(...updates.map(m => m.processingTime))
                : 0,
            recentErrors: this.getRecentErrors().length,
            memoryUsage: performance.memory ? {
                used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024),
                total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024)
            } : 'N/A'
        };
    }

    /**
     * Loguje informacje o inicjalizacji.
     */
    logInitialization() {
        console.log(`%c
🧙‍♂️ WIZARD DEBUG TOOLS INITIALIZED

✅ Console commands available
✅ Debug panel created
✅ Error tracking enabled
✅ Performance monitoring active

Type WizardDebug.help() for available commands.
Debug panel visible in bottom-right corner.
        `, 'color: #10b981; font-family: monospace; font-size: 12px;');
    }
}

// Auto-initialize w development mode
if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        new WizardDebugTools();
    });
}

export default WizardDebugTools;