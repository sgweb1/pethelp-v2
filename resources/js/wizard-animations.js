/**
 * Animacje i micro-interactions dla Pet Sitter Wizard
 *
 * System obsługuje płynne przejścia między krokami, loading states,
 * feedback dla użytkownika i animowane elementy UI.
 */

// Alpine.js komponent główny dla wizard'a
document.addEventListener('alpine:init', () => {
    // Główny komponent wizard'a z animacjami
    Alpine.data('petSitterWizard', () => ({
        showWizard: false,
        currentStep: 1,

        // Stan animacji
        isTransitioning: false,
        currentDirection: 'forward',
        loadingStates: {
            validation: false,
            saving: false,
            stepChange: false,
            autoSaving: false
        },

        // Auto-save state
        autoSaveTimer: null,
        lastAutoSave: null,

        // Konfiguracja animacji
        animationConfig: {
            stepTransition: {
                duration: 400,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            },
            feedback: {
                duration: 300,
                delay: 150
            },
            microInteractions: {
                duration: 200,
                scale: 1.02
            }
        },

        init() {
            // Inicjalizacja na podstawie danych z Livewire
            this.showWizard = this.$wire.isActive || false;
            this.currentStep = this.$wire.currentStep || 1;

            // Setup event listeners
            this.setupEventListeners();
            this.initializeTransitions();

            // Listen for Livewire events
            this.$wire.on('wizard-activated', () => {
                this.showWizard = true;
                document.body.style.overflow = 'hidden';
            });

            this.$wire.on('wizard-deactivated', () => {
                this.showWizard = false;
                document.body.style.overflow = 'auto';
            });
        },

        handleStepChange(data) {
            if (!data || typeof data.step === 'undefined') {
                console.warn('handleStepChange called with invalid data:', data);
                return;
            }

            this.currentStep = data.step;

            // Smooth scroll to top on step change
            const mainElement = this.$el.querySelector('main');
            if (mainElement) {
                mainElement.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        },

        // Cleanup on component destroy
        destroy() {
            document.body.style.overflow = 'auto';
        },

        /**
         * Konfiguruje nasłuchiwanie eventów z Livewire.
         */
        setupEventListeners() {
            // Event przejścia między krokami
            this.$wire.on('step-transition-start', (data) => {
                this.startStepTransition(data.direction);
            });

            // Event zmiany kroku
            this.$wire.on('step-changed', (data) => {
                this.completeStepTransition(data);
            });

            // Event walidacji
            this.$wire.on('validation-failed', (data) => {
                // Sprawdź czy data jest array (Livewire wysyła array z jednym elementem)
                if (Array.isArray(data) && data.length > 0) {
                    data = data[0];
                }

                if (data && (data.errors || data.validationErrors)) {
                    this.showValidationErrors(data.errors || data.validationErrors);
                } else if (data && typeof data === 'object') {
                    // Jeśli data samo zawiera błędy walidacji
                    this.showValidationErrors(data);
                } else {
                    console.warn('validation-failed event received invalid data:', data);
                }
            });

            // Event sukcesu z requestAnimationFrame zamiast setTimeout
            this.$wire.on('hide-success-feedback-after', (data) => {
                const delay = data.delay || 1000;
                const startTime = performance.now();

                const checkHide = (currentTime) => {
                    if (currentTime - startTime >= delay) {
                        this.$wire.hideSuccessFeedback();
                    } else {
                        requestAnimationFrame(checkHide);
                    }
                };

                requestAnimationFrame(checkHide);
            });

            // Event aktualizacji pól
            this.$wire.on('field-updated', (data) => {
                this.animateFieldUpdate(data);
            });

            // Event selekcji opcji
            this.$wire.on('option-selected', (data) => {
                this.animateOptionSelection(data);
            });

            // Event podglądu opcji
            this.$wire.on('option-preview', (data) => {
                this.showOptionPreview(data);
            });

            // Event highlight elementu
            this.$wire.on('highlight-element', (data) => {
                this.highlightElement(data);
            });

            // Event tooltip
            this.$wire.on('show-tooltip', (data) => {
                this.showTooltip(data);
            });

            // Auto-save events
            this.$wire.on('trigger-auto-save', (data) => {
                this.triggerAutoSave(data);
            });

            this.$wire.on('auto-save-success', (data) => {
                this.showAutoSaveSuccess(data);
            });

            this.$wire.on('auto-save-error', (data) => {
                this.showAutoSaveError(data);
            });
        },

        /**
         * Inicjalizuje system przejść CSS.
         */
        initializeTransitions() {
            // Dodaj klasy CSS dla transitions jeśli nie istnieją
            if (!document.querySelector('#wizard-animations-styles')) {
                const style = document.createElement('style');
                style.id = 'wizard-animations-styles';
                style.textContent = `
                    .wizard-step-enter {
                        opacity: 0;
                        transform: translateX(20px);
                    }

                    .wizard-step-enter-active {
                        transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
                    }

                    .wizard-step-enter-to {
                        opacity: 1;
                        transform: translateX(0);
                    }

                    .wizard-step-leave {
                        opacity: 1;
                        transform: translateX(0);
                    }

                    .wizard-step-leave-active {
                        transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
                    }

                    .wizard-step-leave-to {
                        opacity: 0;
                        transform: translateX(-20px);
                    }

                    .option-selected {
                        animation: optionSelect 300ms cubic-bezier(0.4, 0, 0.2, 1);
                    }

                    .field-valid {
                        animation: fieldSuccess 400ms ease-out;
                    }

                    .validation-error {
                        animation: shake 400ms ease-in-out;
                    }

                    .loading-pulse {
                        animation: pulse 1.5s ease-in-out infinite;
                    }

                    @keyframes optionSelect {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.02); }
                        100% { transform: scale(1); }
                    }

                    @keyframes fieldSuccess {
                        0% { border-color: #e5e7eb; }
                        50% { border-color: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
                        100% { border-color: #10b981; }
                    }

                    @keyframes shake {
                        0%, 100% { transform: translateX(0); }
                        25% { transform: translateX(-4px); }
                        75% { transform: translateX(4px); }
                    }

                    @keyframes pulse {
                        0%, 100% { opacity: 1; }
                        50% { opacity: 0.5; }
                    }

                    .micro-bounce {
                        animation: microBounce 200ms ease-out;
                    }

                    @keyframes microBounce {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.05); }
                        100% { transform: scale(1); }
                    }

                    .auto-save-indicator {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #f3f4f6;
                        border: 1px solid #e5e7eb;
                        border-radius: 8px;
                        padding: 8px 12px;
                        font-size: 12px;
                        color: #6b7280;
                        z-index: 1000;
                        opacity: 0;
                        transform: translateX(100px);
                        transition: all 300ms ease-out;
                        pointer-events: none;
                    }

                    .auto-save-indicator.visible {
                        opacity: 1;
                        transform: translateX(0);
                    }

                    .auto-save-indicator.saving {
                        background: #fef3c7;
                        border-color: #f59e0b;
                        color: #92400e;
                    }

                    .auto-save-indicator.success {
                        background: #d1fae5;
                        border-color: #10b981;
                        color: #065f46;
                    }

                    .auto-save-indicator.error {
                        background: #fee2e2;
                        border-color: #f87171;
                        color: #991b1b;
                    }

                    .auto-save-spinner {
                        display: inline-block;
                        width: 12px;
                        height: 12px;
                        border: 2px solid transparent;
                        border-top-color: currentColor;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin-right: 6px;
                    }

                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
        },

        /**
         * Rozpoczyna animację przejścia między krokami.
         */
        startStepTransition(direction) {
            this.isTransitioning = true;
            this.currentDirection = direction;
            this.loadingStates.stepChange = true;

            // Animuj wyjście aktualnego kroku
            const currentStep = document.querySelector('.wizard-step.active');
            if (currentStep) {
                currentStep.classList.add('wizard-step-leave');
                currentStep.classList.add('wizard-step-leave-active');
            }
        },

        /**
         * Kończy animację przejścia między krokami.
         */
        completeStepTransition(data) {
            // Użyj requestAnimationFrame zamiast setTimeout
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    // Usuń klasy animacji z poprzedniego kroku
                    const allSteps = document.querySelectorAll('.wizard-step');
                    allSteps.forEach(step => {
                        step.classList.remove(
                            'wizard-step-leave',
                            'wizard-step-leave-active',
                            'wizard-step-leave-to'
                        );
                    });

                    // Animuj wejście nowego kroku
                    const newStep = document.querySelector(`[data-step="${data.step}"]`);
                    if (newStep) {
                        newStep.classList.add('wizard-step-enter');
                        requestAnimationFrame(() => {
                            newStep.classList.add('wizard-step-enter-active');
                            newStep.classList.remove('wizard-step-enter');
                            newStep.classList.add('wizard-step-enter-to');
                        });
                    }

                    this.isTransitioning = false;
                    this.loadingStates.stepChange = false;
                });
            });
        },

        /**
         * Pokazuje animowane błędy walidacji.
         */
        showValidationErrors(errors) {
            if (!errors || typeof errors !== 'object') {
                console.warn('showValidationErrors called with invalid errors:', errors);
                return;
            }

            Object.keys(errors).forEach(field => {
                const element = document.querySelector(`[name="${field}"]`);
                if (element) {
                    element.classList.add('validation-error');
                    element.addEventListener('animationend', () => {
                        element.classList.remove('validation-error');
                    }, { once: true });
                }
            });
        },

        /**
         * Animuje aktualizację pola formularza.
         */
        animateFieldUpdate(data) {
            // Sprawdź czy data jest array (Livewire wysyła array z jednym elementem)
            if (Array.isArray(data) && data.length > 0) {
                data = data[0];
            }

            if (!data || typeof data !== 'object') {
                console.warn('animateFieldUpdate called with invalid data:', data);
                return;
            }

            // Sprawdź czy mamy wymagane właściwości
            if (!data.field && !data.name) {
                console.warn('animateFieldUpdate: missing field/name property in data:', data);
                return;
            }

            const fieldName = data.field || data.name;
            const element = document.querySelector(`[name="${fieldName}"]`);

            if (element && (data.isValid || data.valid)) {
                element.classList.add('field-valid');
                element.addEventListener('animationend', () => {
                    element.classList.remove('field-valid');
                }, { once: true });
            }
        },

        /**
         * Animuje wybór opcji.
         */
        animateOptionSelection(data) {
            const element = document.querySelector(`[data-option="${data.field}-${data.value}"]`);
            if (element) {
                element.classList.add('option-selected');
                element.addEventListener('animationend', () => {
                    element.classList.remove('option-selected');
                }, { once: true });
            }
        },

        /**
         * Pokazuje podgląd opcji podczas hover.
         */
        showOptionPreview(data) {
            const element = document.querySelector(`[data-option="${data.field}-${data.value}"]`);
            if (element) {
                element.style.transform = 'scale(1.02)';
                element.style.transition = 'transform 150ms ease-out';

                element.addEventListener('mouseleave', () => {
                    element.style.transform = 'scale(1)';
                }, { once: true });
            }
        },

        /**
         * Podświetla element pulsowaniem.
         */
        highlightElement(data) {
            const element = document.getElementById(data.elementId);
            if (element) {
                element.classList.add('loading-pulse');
                // Użyj animationend event zamiast arbitrary timeout
                const animationEnd = () => {
                    element.classList.remove('loading-pulse');
                    element.removeEventListener('animationend', animationEnd);
                };
                element.addEventListener('animationend', animationEnd);

                // Fallback z requestAnimationFrame
                const duration = data.duration || 1500;
                const startTime = performance.now();

                const checkRemove = (currentTime) => {
                    if (currentTime - startTime >= duration) {
                        if (element.classList.contains('loading-pulse')) {
                            element.classList.remove('loading-pulse');
                        }
                    } else {
                        requestAnimationFrame(checkRemove);
                    }
                };

                requestAnimationFrame(checkRemove);
            }
        },

        /**
         * Pokazuje tooltip z animacją.
         */
        showTooltip(data) {
            // Implementacja tooltip'a (można rozszerzyć)
            console.log('Tooltip:', data.content);
        },

        /**
         * Utylity dla loading states.
         */
        setLoadingState(type, value) {
            this.loadingStates[type] = value;
        },

        isLoading(type) {
            return this.loadingStates[type];
        },

        /**
         * Animuje kliknięcie przycisku.
         */
        animateButtonClick(button) {
            button.classList.add('micro-bounce');
            button.addEventListener('animationend', () => {
                button.classList.remove('micro-bounce');
            }, { once: true });
        },

        /**
         * Progress bar animacji.
         */
        animateProgress(percentage) {
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.transition = 'width 400ms ease-out';
                progressBar.style.width = `${percentage}%`;
            }
        },

        /**
         * Triggeruje debounced auto-save.
         */
        triggerAutoSave(data) {
            if (!data) {
                console.warn('triggerAutoSave called with invalid data:', data);
                return;
            }

            // Anuluj poprzedni timer
            if (this.autoSaveTimer) {
                cancelAnimationFrame(this.autoSaveTimer);
            }

            // Pokaż wskaźnik auto-save jeśli wymagany
            if (data.showIndicator) {
                this.showAutoSaveIndicator('waiting');
            }

            // Ustaw nowy timer z requestAnimationFrame
            const delay = data.delay || 1500;
            const startTime = performance.now();

            const checkAutoSave = (currentTime) => {
                if (currentTime - startTime >= delay) {
                    this.performAutoSave();
                } else {
                    this.autoSaveTimer = requestAnimationFrame(checkAutoSave);
                }
            };

            this.autoSaveTimer = requestAnimationFrame(checkAutoSave);
        },

        /**
         * Wykonuje auto-save przez wywołanie metody Livewire.
         */
        performAutoSave() {
            this.loadingStates.autoSaving = true;
            this.showAutoSaveIndicator('saving');

            // Wywołaj metodę Livewire auto-save
            this.$wire.performAutoSave();
        },

        /**
         * Pokazuje wskaźnik auto-save.
         */
        showAutoSaveIndicator(type) {
            let indicator = document.getElementById('auto-save-indicator');

            // Utwórz wskaźnik jeśli nie istnieje
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'auto-save-indicator';
                indicator.className = 'auto-save-indicator';
                document.body.appendChild(indicator);
            }

            // Resetuj klasy
            indicator.className = 'auto-save-indicator';

            // Ustaw treść i style na podstawie typu
            let content = '';
            switch (type) {
                case 'waiting':
                    content = 'Przygotowywanie auto-zapisu...';
                    break;
                case 'saving':
                    content = '<span class="auto-save-spinner"></span>Zapisywanie...';
                    indicator.classList.add('saving');
                    break;
                case 'success':
                    content = '✓ Zapisano automatycznie';
                    indicator.classList.add('success');
                    break;
                case 'error':
                    content = '⚠ Błąd auto-zapisu';
                    indicator.classList.add('error');
                    break;
            }

            indicator.innerHTML = content;
            indicator.classList.add('visible');

            // Auto-ukryj po pewnym czasie z requestAnimationFrame
            if (type === 'success' || type === 'error') {
                const delay = type === 'success' ? 2000 : 4000;
                const startTime = performance.now();

                const checkHide = (currentTime) => {
                    if (currentTime - startTime >= delay) {
                        this.hideAutoSaveIndicator();
                    } else {
                        requestAnimationFrame(checkHide);
                    }
                };

                requestAnimationFrame(checkHide);
            }
        },

        /**
         * Ukrywa wskaźnik auto-save.
         */
        hideAutoSaveIndicator() {
            const indicator = document.getElementById('auto-save-indicator');
            if (indicator) {
                indicator.classList.remove('visible');
            }
        },

        /**
         * Obsługuje pomyślny auto-save.
         */
        showAutoSaveSuccess(data) {
            this.loadingStates.autoSaving = false;
            this.lastAutoSave = data.timestamp;
            this.showAutoSaveIndicator('success');
        },

        /**
         * Obsługuje błąd auto-save.
         */
        showAutoSaveError(data) {
            this.loadingStates.autoSaving = false;
            this.showAutoSaveIndicator('error');
            console.error('Auto-save error:', data.message);
        }
    }));
});

/**
 * Funkcje pomocnicze dla animacji.
 */
window.WizardAnimations = {
    /**
     * Stagger animacja dla listy elementów.
     */
    staggerAnimation(elements, delay = 50) {
        elements.forEach((element, index) => {
            // Użyj requestAnimationFrame z obliczonym opóźnieniem
            const totalDelay = index * delay;
            const startTime = performance.now();

            const animateElement = (currentTime) => {
                if (currentTime - startTime >= totalDelay) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                } else {
                    requestAnimationFrame(animateElement);
                }
            };

            requestAnimationFrame(animateElement);
        });
    },

    /**
     * Bounce effect dla ważnych akcji.
     */
    bounceElement(element) {
        element.style.animation = 'microBounce 200ms ease-out';
        element.addEventListener('animationend', () => {
            element.style.animation = '';
        }, { once: true });
    },

    /**
     * Fade in animation.
     */
    fadeIn(element, duration = 300) {
        element.style.opacity = '0';
        element.style.transition = `opacity ${duration}ms ease-in-out`;
        requestAnimationFrame(() => {
            element.style.opacity = '1';
        });
    }
};