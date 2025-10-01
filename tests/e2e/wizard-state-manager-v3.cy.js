/**
 * Pet Sitter Wizard - E2E Tests dla WizardStateManager v3.0
 *
 * Testy integracyjne dla nowej centralized state management architecture.
 * Weryfikuje działanie Single Source of Truth wzorca i eliminację błędów scope.
 *
 * @author Claude AI Assistant
 * @version 1.0.0
 */

describe('Pet Sitter Wizard - State Manager v3.0', () => {
    beforeEach(() => {
        // Odwiedź stronę wizard'a
        cy.visit('/profil/zostan-pet-sitterem');

        // Upewnij się że page się załadował
        cy.get('body').should('be.visible');
    });

    describe('🏗️ WizardStateManager v3 Initialization', () => {
        it('should initialize WizardStateManager v3 correctly', () => {
            cy.window().should('have.property', 'WizardStateManager');
            cy.window().should('have.property', 'WizardState');

            cy.window().then((win) => {
                expect(win.WizardState).to.exist;
                expect(win.WizardState.state).to.exist;
                expect(win.WizardState.state.meta).to.exist;
                expect(win.WizardState.state.personalData).to.exist;
                expect(win.WizardState.state.home).to.exist;
                expect(win.WizardState.state.availability).to.exist;
            });
        });

        it('should have debug tools available in development', () => {
            cy.window().then((win) => {
                if (win.location.hostname === 'pethelp.test') {
                    expect(win.WizardDebug).to.exist;
                    expect(win.WizardDebug.showState).to.be.a('function');
                    expect(win.WizardDebug.fillTestData).to.be.a('function');
                    expect(win.WizardDebug.validateAllSteps).to.be.a('function');
                }
            });
        });
    });

    describe('🎯 Core State Management Functionality', () => {
        it('should activate wizard and initialize state', () => {
            // Aktywuj wizard
            cy.get('[data-test="activate-wizard"]', { timeout: 10000 }).click();

            // Sprawdź czy wizard jest aktywny
            cy.get('[data-test="wizard-container"]', { timeout: 10000 }).should('be.visible');

            // Sprawdź czy state został zainicjalizowany
            cy.window().then((win) => {
                expect(win.WizardState.get('meta.isActive')).to.be.true;
                expect(win.WizardState.get('meta.currentStep')).to.equal(1);
                expect(win.WizardState.get('meta.maxSteps')).to.equal(12);
            });
        });

        it('should update state via WizardStateManager API', () => {
            cy.get('[data-test="activate-wizard"]').click();
            cy.get('[data-test="wizard-container"]').should('be.visible');

            cy.window().then((win) => {
                // Update state through WizardStateManager
                win.WizardState.update('personalData.motivation', 'Test motivation via state manager');
                win.WizardState.update('home.hasGarden', true);
                win.WizardState.update('availability.flexibleSchedule', true);

                // Verify state was updated
                expect(win.WizardState.get('personalData.motivation')).to.equal('Test motivation via state manager');
                expect(win.WizardState.get('home.hasGarden')).to.be.true;
                expect(win.WizardState.get('availability.flexibleSchedule')).to.be.true;
            });
        });

        it('should maintain state history for debugging', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                const initialHistoryLength = win.WizardState.changeHistory.length;

                // Make some state changes
                win.WizardState.update('personalData.name', 'Test User');
                win.WizardState.update('home.homeType', 'house');

                // Verify history was recorded
                expect(win.WizardState.changeHistory.length).to.be.greaterThan(initialHistoryLength);

                // Check history entries
                const recentChanges = win.WizardState.changeHistory.slice(-2);
                expect(recentChanges[0].path).to.include('personalData.name');
                expect(recentChanges[1].path).to.include('home.homeType');
            });
        });
    });

    describe('🔄 Cross-Step State Persistence', () => {
        it('should maintain state when navigating between steps', () => {
            // Activate wizard
            cy.get('[data-test="activate-wizard"]').click();
            cy.get('[data-test="wizard-container"]').should('be.visible');

            cy.window().then((win) => {
                // Set state via WizardStateManager
                win.WizardState.update('personalData.motivation', 'Test motivation that is long enough to pass validation requirements and contains meaningful content');
                win.WizardState.update('home.hasGarden', true);
                win.WizardState.update('home.homeType', 'house');
                win.WizardState.update('availability.flexibleSchedule', true);

                // Navigate to different steps (simulate step navigation)
                win.WizardState.update('meta.currentStep', 7);

                // Verify state persisted
                expect(win.WizardState.get('personalData.motivation')).to.include('Test motivation');
                expect(win.WizardState.get('home.hasGarden')).to.be.true;
                expect(win.WizardState.get('home.homeType')).to.equal('house');

                // Navigate to step 6
                win.WizardState.update('meta.currentStep', 6);

                // Verify availability state persisted
                expect(win.WizardState.get('availability.flexibleSchedule')).to.be.true;

                // Navigate back to step 1
                win.WizardState.update('meta.currentStep', 1);

                // Verify personal data still persisted
                expect(win.WizardState.get('personalData.motivation')).to.include('Test motivation');
            });
        });

        it('should eliminate Alpine.js scope pollution errors', () => {
            cy.get('[data-test="activate-wizard"]').click();
            cy.get('[data-test="wizard-container"]').should('be.visible');

            // Monitor console errors
            const consoleErrors = [];
            cy.window().then((win) => {
                const originalConsoleError = win.console.error;
                win.console.error = (...args) => {
                    consoleErrors.push(args.join(' '));
                    originalConsoleError(...args);
                };
            });

            cy.window().then((win) => {
                // Set state that previously caused undefined variable errors
                win.WizardState.update('home.hasGarden', true);
                win.WizardState.update('home.isSmoking', false);
                win.WizardState.update('home.homeType', 'apartment');
                win.WizardState.update('availability.flexibleSchedule', true);
                win.WizardState.update('availability.emergencyAvailable', false);

                // Navigate through problem steps (4, 6, 7)
                [4, 6, 7].forEach(step => {
                    win.WizardState.update('meta.currentStep', step);
                });
            });

            // Wait for any async operations
            cy.wait(2000);

            // Verify no Alpine.js undefined variable errors
            cy.then(() => {
                const alpineErrors = consoleErrors.filter(error =>
                    error.includes('hasGarden is not defined') ||
                    error.includes('isSmoking is not defined') ||
                    error.includes('homeType is not defined') ||
                    error.includes('flexibleSchedule is not defined') ||
                    error.includes('emergencyAvailable is not defined')
                );

                expect(alpineErrors).to.be.empty;
            });
        });
    });

    describe('✅ Step Validation System', () => {
        it('should validate steps correctly using WizardStateManager', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                // Test step 1 validation (motivation length)
                let validation = win.WizardState.validateCurrentStep();
                expect(validation.isValid).to.be.false;

                // Add valid motivation
                win.WizardState.update('personalData.motivation',
                    'Valid motivation text that is definitely long enough to pass all validation requirements and contains meaningful content about pet care experience'
                );

                validation = win.WizardState.validateCurrentStep();
                expect(validation.isValid).to.be.true;

                // Test step 7 validation (home type)
                win.WizardState.update('meta.currentStep', 7);
                validation = win.WizardState.validateCurrentStep();
                expect(validation.isValid).to.be.false;

                win.WizardState.update('home.homeType', 'house');
                validation = win.WizardState.validateCurrentStep();
                expect(validation.isValid).to.be.true;
            });
        });

        it('should update meta state with validation results', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                // Initially should not be valid
                expect(win.WizardState.get('meta.canProceed')).to.be.false;

                // Add valid data
                win.WizardState.update('personalData.motivation',
                    'Complete motivation text that meets all validation requirements for step 1'
                );

                // Meta state should update automatically
                expect(win.WizardState.get('meta.canProceed')).to.be.true;
                expect(win.WizardState.get('meta.isValid')).to.be.true;
            });
        });
    });

    describe('🧪 Debug Tools Integration', () => {
        it('should provide debug tools in development environment', function() {
            // Skip test if not in development
            cy.window().then((win) => {
                if (win.location.hostname !== 'pethelp.test') {
                    this.skip();
                }
            });

            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                // Test fillTestData function
                win.WizardDebug.fillTestData();

                // Verify test data was filled
                expect(win.WizardState.get('personalData.name')).to.equal('Jan Testowy');
                expect(win.WizardState.get('personalData.email')).to.equal('jan.testowy@pethelp.test');
                expect(win.WizardState.get('home.hasGarden')).to.be.true;
                expect(win.WizardState.get('availability.flexibleSchedule')).to.be.true;

                // Test state validation
                const validationResults = win.WizardDebug.validateAllSteps();
                expect(validationResults).to.be.an('array');
                expect(validationResults.length).to.equal(12);
            });
        });

        it('should have debug panel in development', function() {
            cy.window().then((win) => {
                if (win.location.hostname !== 'pethelp.test') {
                    this.skip();
                }
            });

            // Check if debug panel exists
            cy.get('#wizard-debug-panel').should('exist');

            // Test debug panel buttons
            cy.get('#wizard-debug-panel button').contains('Show State').should('exist');
            cy.get('#wizard-debug-panel button').contains('Fill Test Data').should('exist');
            cy.get('#wizard-debug-panel button').contains('Validate All').should('exist');
        });
    });

    describe('⚡ Performance Optimization', () => {
        it('should load wizard within acceptable time limits', () => {
            const startTime = Date.now();

            cy.get('[data-test="activate-wizard"]').click();
            cy.get('[data-test="wizard-container"]').should('be.visible').then(() => {
                const loadTime = Date.now() - startTime;
                expect(loadTime).to.be.lessThan(3000); // Max 3 seconds for E2E test
            });
        });

        it('should handle state updates efficiently', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                const startTime = performance.now();

                // Perform multiple state updates
                for (let i = 0; i < 10; i++) {
                    win.WizardState.update(`personalData.motivation`, `Test motivation ${i} with sufficient length`);
                    win.WizardState.update(`home.hasGarden`, i % 2 === 0);
                    win.WizardState.update(`availability.flexibleSchedule`, i % 3 === 0);
                }

                const endTime = performance.now();
                const totalTime = endTime - startTime;

                // Should handle 30 updates in under 100ms
                expect(totalTime).to.be.lessThan(100);

                // Verify final state
                expect(win.WizardState.get('personalData.motivation')).to.include('Test motivation 9');
            });
        });
    });

    describe('🔄 State Export/Import Functionality', () => {
        it('should export and import state correctly', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                // Set some test data
                win.WizardState.update('personalData.name', 'Export Test User');
                win.WizardState.update('home.hasGarden', true);
                win.WizardState.update('meta.currentStep', 5);

                // Export state
                const exportedState = win.WizardState.exportState();
                expect(exportedState).to.be.a('string');

                const parsedState = JSON.parse(exportedState);
                expect(parsedState.personalData.name).to.equal('Export Test User');
                expect(parsedState.home.hasGarden).to.be.true;
                expect(parsedState.meta.currentStep).to.equal(5);

                // Reset state
                win.WizardState.reset();
                expect(win.WizardState.get('personalData.name')).to.equal('');
                expect(win.WizardState.get('meta.currentStep')).to.equal(1);

                // Import state
                const importSuccess = win.WizardState.importState(exportedState);
                expect(importSuccess).to.be.true;

                // Verify imported state
                expect(win.WizardState.get('personalData.name')).to.equal('Export Test User');
                expect(win.WizardState.get('home.hasGarden')).to.be.true;
                expect(win.WizardState.get('meta.currentStep')).to.equal(5);
            });
        });
    });

    describe('🚨 Error Prevention & Recovery', () => {
        it('should not produce setTimeout attribute errors', () => {
            const jsErrors = [];

            // Capture JavaScript errors
            cy.window().then((win) => {
                win.addEventListener('error', (e) => {
                    jsErrors.push(e.message);
                });
            });

            // Navigate through wizard
            cy.get('[data-test="activate-wizard"]').click();
            cy.get('[data-test="wizard-container"]').should('be.visible');

            // Wait for any async operations
            cy.wait(3000);

            // Verify no setTimeout errors
            cy.then(() => {
                const setTimeoutErrors = jsErrors.filter(error =>
                    error.includes('setTimeout') ||
                    error.includes('settimeout') ||
                    error.includes('is not a valid attribute name')
                );

                if (setTimeoutErrors.length > 0) {
                    console.error('Detected setTimeout errors:', setTimeoutErrors);
                }

                expect(setTimeoutErrors).to.be.empty;
            });
        });

        it('should handle missing DOM elements gracefully', () => {
            cy.get('[data-test="activate-wizard"]').click();

            cy.window().then((win) => {
                // Try to update state even if some DOM elements are missing
                win.WizardState.update('personalData.motivation', 'Graceful handling test');

                // Should not throw errors
                expect(win.WizardState.get('personalData.motivation')).to.equal('Graceful handling test');
            });
        });
    });
});