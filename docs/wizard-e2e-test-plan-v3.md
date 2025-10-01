# Pet Sitter Wizard - E2E Test Plan v3.0

## 📋 Komprehensywny Plan Testów End-to-End

### 🎯 Cele Testowania

1. **State Management** - Weryfikacja działania centralized state
2. **Cross-Step Navigation** - Testowanie nawigacji między krokami
3. **Data Persistence** - Sprawdzenie zachowania danych
4. **Error Handling** - Testowanie obsługi błędów
5. **Responsive Design** - Testy na różnych urządzeniach
6. **Performance** - Pomiar wydajności wizard'a

---

## 🛠️ Narzędzia Testowe

### Główne Framework
- **Cypress** - E2E testing framework
- **Playwright** (backup) - Cross-browser testing
- **Laravel Dusk** - Integracja z Laravel backend

### Development Tools
- **Live State Viewer** - Real-time monitoring stanu
- **Debug Console** - Console commands for testing
- **State History Tracker** - Śledzenie zmian w czasie
- **Performance Monitor** - Monitoring wydajności

---

## 📊 Test Scenarios

### 1. **Complete Flow Tests** (Pełny przepływ)

#### Test: Happy Path - Kompletna rejestracja
```javascript
describe('Complete Wizard Flow', () => {
    it('should complete full pet sitter registration', () => {
        // Step 1: Activate wizard
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Step 1: Motivation
        cy.get('[data-test="motivation"]').type('Cześć! Nazywam się Test User i od dziecka kocham zwierzęta. Mam wieloletnie doświadczenie w opiece nad psami i kotami różnych ras i temperamentów.');
        cy.get('[data-test="next-step"]').should('be.enabled').click();

        // Step 2: Experience
        cy.get('[data-test="pet-experience-high"]').click();
        cy.get('[data-test="animal-type-dogs"]').click();
        cy.get('[data-test="animal-type-cats"]').click();
        cy.get('[data-test="next-step"]').click();

        // Continue for all steps...

        // Final verification
        cy.get('[data-test="wizard-complete"]').should('be.visible');
        cy.get('[data-test="success-message"]').should('contain', 'Gratulacje!');
    });
});
```

### 2. **State Management Tests**

#### Test: Cross-Step State Persistence
```javascript
describe('State Management', () => {
    it('should maintain state across steps', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Step 1: Set data
        const motivationText = 'Test motivation with sufficient length to pass validation requirements';
        cy.get('[data-test="motivation"]').type(motivationText);

        // Go to step 7
        cy.get('[data-test="go-to-step-7"]').click();
        cy.get('[data-test="home-type-house"]').click();
        cy.get('[data-test="has-garden"]').click();

        // Back to step 1 - verify data persistence
        cy.get('[data-test="go-to-step-1"]').click();
        cy.get('[data-test="motivation"]').should('have.value', motivationText);

        // Back to step 7 - verify state persistence
        cy.get('[data-test="go-to-step-7"]').click();
        cy.get('[data-test="has-garden"]').should('have.class', 'selected');
        cy.get('[data-test="home-type-house"]').should('have.class', 'selected');
    });

    it('should sync with WizardStateManager', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Test global state access
        cy.window().then((win) => {
            expect(win.WizardState).to.exist;

            // Update via state manager
            win.WizardState.update('home.hasGarden', true);

            // Navigate to step 7
            cy.get('[data-test="go-to-step-7"]').click();

            // Verify UI reflects state
            cy.get('[data-test="has-garden"]').should('have.class', 'selected');
        });
    });
});
```

### 3. **Validation Tests**

#### Test: Form Validation Rules
```javascript
describe('Validation Tests', () => {
    it('should enforce minimum motivation length', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Test insufficient motivation
        cy.get('[data-test="motivation"]').type('Too short');
        cy.get('[data-test="next-step"]').should('be.disabled');
        cy.get('[data-test="error-message"]').should('contain', 'co najmniej 50 znaków');

        // Test sufficient motivation
        const validMotivation = 'This is a sufficiently long motivation text that meets the minimum character requirement';
        cy.get('[data-test="motivation"]').clear().type(validMotivation);
        cy.get('[data-test="next-step"]').should('be.enabled');
    });

    it('should validate each step progressively', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Test step validation via WizardStateManager
        cy.window().then((win) => {
            // Step 1 - should be invalid initially
            let validation = win.validateWizardStep(1);
            expect(validation.isValid).to.be.false;

            // Add valid motivation
            win.WizardState.update('personalData.motivation', 'Valid motivation text that is long enough to pass validation requirements and contains meaningful content');

            // Should be valid now
            validation = win.validateWizardStep(1);
            expect(validation.isValid).to.be.true;
        });
    });
});
```

### 4. **Error Handling Tests**

#### Test: Alpine.js Expression Errors (Bug Fixes)
```javascript
describe('Error Prevention', () => {
    it('should not have Alpine.js undefined variable errors', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Monitor console for Alpine errors
        cy.window().then((win) => {
            const consoleErrors = [];
            const originalError = win.console.error;
            win.console.error = (...args) => {
                consoleErrors.push(args.join(' '));
                originalError(...args);
            };

            // Navigate through all steps
            for (let step = 1; step <= 12; step++) {
                cy.get(`[data-test="go-to-step-${step}"]`).click();
                cy.wait(500);
            }

            // Check for Alpine.js errors
            cy.then(() => {
                const alpineErrors = consoleErrors.filter(error =>
                    error.includes('Alpine Expression Error') ||
                    error.includes('is not defined')
                );
                expect(alpineErrors).to.be.empty;
            });
        });
    });

    it('should handle setTimeout errors gracefully', () => {
        cy.visit('/profil/zostan-pet-sitterem');

        // Monitor for setTimeout attribute errors
        cy.window().then((win) => {
            const jsErrors = [];
            win.addEventListener('error', (e) => {
                jsErrors.push(e.message);
            });

            cy.get('[data-test="activate-wizard"]').click();

            // Navigate through steps
            cy.get('[data-test="go-to-step-1"]').click();
            cy.wait(1000);

            cy.then(() => {
                const setTimeoutErrors = jsErrors.filter(error =>
                    error.includes('setTimeout') ||
                    error.includes('settimeout')
                );
                expect(setTimeoutErrors).to.be.empty;
            });
        });
    });
});
```

### 5. **Performance Tests**

#### Test: Wizard Loading Performance
```javascript
describe('Performance Tests', () => {
    it('should load wizard within acceptable time limits', () => {
        cy.visit('/profil/zostan-pet-sitterem');

        // Measure activation time
        const startTime = Date.now();
        cy.get('[data-test="activate-wizard"]').click();
        cy.get('[data-test="wizard-container"]').should('be.visible').then(() => {
            const loadTime = Date.now() - startTime;
            expect(loadTime).to.be.lessThan(2000); // Max 2 seconds
        });
    });

    it('should navigate between steps quickly', () => {
        cy.visit('/profil/zostan-pet-sitterem');
        cy.get('[data-test="activate-wizard"]').click();

        // Test step navigation performance
        const navigationTimes = [];

        for (let step = 1; step <= 7; step++) {
            const startTime = Date.now();
            cy.get(`[data-test="go-to-step-${step}"]`).click();
            cy.get(`[data-test="step-${step}-content"]`).should('be.visible').then(() => {
                navigationTimes.push(Date.now() - startTime);
            });
        }

        cy.then(() => {
            const avgTime = navigationTimes.reduce((a, b) => a + b, 0) / navigationTimes.length;
            expect(avgTime).to.be.lessThan(500); // Average < 500ms
        });
    });
});
```

### 6. **Cross-Browser Tests**

#### Test: Browser Compatibility
```javascript
describe('Cross-Browser Compatibility', () => {
    ['chrome', 'firefox', 'edge'].forEach(browser => {
        it(`should work correctly in ${browser}`, () => {
            cy.viewport(1280, 720);
            cy.visit('/profil/zostan-pet-sitterem');
            cy.get('[data-test="activate-wizard"]').click();

            // Test core functionality
            cy.get('[data-test="motivation"]').type('Browser compatibility test with sufficient length');
            cy.get('[data-test="next-step"]').should('be.enabled');

            // Test WizardStateManager availability
            cy.window().should('have.property', 'WizardState');
        });
    });
});
```

---

## 🔧 Development Tools Setup

### 1. **Live State Viewer**

```javascript
// resources/js/dev-tools/state-viewer.js
class WizardStateViewer {
    constructor() {
        this.createStatePanel();
        this.setupAutoRefresh();
    }

    createStatePanel() {
        const panel = document.createElement('div');
        panel.id = 'wizard-state-viewer';
        panel.innerHTML = `
            <div class="fixed top-4 right-4 bg-black bg-opacity-90 text-white p-4 rounded-lg z-50 max-w-sm">
                <h3 class="font-bold mb-2">Wizard State (Live)</h3>
                <pre id="state-content" class="text-xs overflow-auto max-h-96"></pre>
                <button onclick="window.WizardState.reset()" class="mt-2 px-2 py-1 bg-red-500 rounded text-xs">Reset</button>
            </div>
        `;
        document.body.appendChild(panel);
    }

    setupAutoRefresh() {
        setInterval(() => {
            if (window.WizardState) {
                document.getElementById('state-content').textContent =
                    JSON.stringify(window.WizardState.state, null, 2);
            }
        }, 1000);
    }
}

// Auto-initialize in development
if (process.env.NODE_ENV === 'development') {
    new WizardStateViewer();
}
```

### 2. **Debug Console Commands**

```javascript
// resources/js/dev-tools/debug-commands.js
window.WizardDebug = {
    // Simulate user completing step 1
    completeStep1() {
        window.WizardState.update('personalData.motivation',
            'Test motivation that is definitely long enough to pass all validation requirements and contains meaningful content about pet sitting experience'
        );
        console.log('✅ Step 1 completed');
    },

    // Fill all steps with test data
    fillAllSteps() {
        const testData = {
            'personalData.name': 'Test User',
            'personalData.email': 'test@example.com',
            'personalData.city': 'Warszawa',
            'personalData.motivation': 'Complete test motivation with sufficient length',
            'experience.petExperience': 'high',
            'experience.animalTypes': ['dogs', 'cats'],
            'services.serviceTypes': ['walking', 'sitting'],
            'home.homeType': 'house',
            'home.hasGarden': true,
            'availability.flexibleSchedule': true
        };

        Object.entries(testData).forEach(([path, value]) => {
            window.WizardState.update(path, value);
        });

        console.log('✅ All steps filled with test data');
    },

    // Jump to specific step
    goToStep(step) {
        window.WizardState.update('meta.currentStep', step);
        console.log(`🔄 Jumped to step ${step}`);
    },

    // Validate all steps
    validateAllSteps() {
        for (let i = 1; i <= 12; i++) {
            const validation = window.validateWizardStep(i);
            console.log(`Step ${i}:`, validation.isValid ? '✅' : '❌', validation.errors);
        }
    }
};
```

### 3. **Performance Monitor**

```javascript
// resources/js/dev-tools/performance-monitor.js
class WizardPerformanceMonitor {
    constructor() {
        this.metrics = {
            stepLoadTimes: {},
            stateUpdateTimes: [],
            renderTimes: {}
        };

        this.setupMonitoring();
    }

    setupMonitoring() {
        // Monitor state updates
        if (window.WizardState) {
            window.WizardState.watch((path, newValue, oldValue) => {
                const startTime = performance.now();
                // Simulate processing time
                requestAnimationFrame(() => {
                    const endTime = performance.now();
                    this.metrics.stateUpdateTimes.push({
                        path,
                        duration: endTime - startTime,
                        timestamp: new Date().toISOString()
                    });
                });
            });
        }
    }

    getReport() {
        return {
            avgStateUpdateTime: this.getAverageUpdateTime(),
            slowestUpdates: this.getSlowestUpdates(),
            totalUpdates: this.metrics.stateUpdateTimes.length
        };
    }

    getAverageUpdateTime() {
        const times = this.metrics.stateUpdateTimes.map(m => m.duration);
        return times.reduce((a, b) => a + b, 0) / times.length;
    }

    getSlowestUpdates() {
        return this.metrics.stateUpdateTimes
            .sort((a, b) => b.duration - a.duration)
            .slice(0, 5);
    }
}

// Initialize in development
if (process.env.NODE_ENV === 'development') {
    window.WizardPerformanceMonitor = new WizardPerformanceMonitor();
}
```

---

## 🚀 Test Execution Plan

### Phase 1: Core Functionality (Week 1)
- [ ] Setup Cypress test environment
- [ ] Implement basic flow tests
- [ ] Test state management functionality
- [ ] Fix any critical bugs discovered

### Phase 2: Advanced Testing (Week 2)
- [ ] Cross-step navigation tests
- [ ] Error handling validation
- [ ] Performance benchmarking
- [ ] Browser compatibility tests

### Phase 3: Production Readiness (Week 3)
- [ ] Load testing with multiple users
- [ ] Mobile device testing
- [ ] Accessibility compliance testing
- [ ] Final bug fixes and optimization

---

## 📊 Success Metrics

### 🎯 Target Metrics
- **Zero Alpine.js Expression Errors** in console
- **< 2 second** wizard activation time
- **< 500ms** average step navigation time
- **100% state consistency** across all steps
- **95%+ test pass rate** across all browsers
- **Zero critical bugs** in production

### 📈 Monitoring Dashboard
- Real-time error tracking
- Performance metrics visualization
- State change history
- User completion funnel analysis

---

## 🛡️ Quality Assurance

### Automated Testing Pipeline
1. **Pre-commit hooks** - Run basic tests before commits
2. **CI/CD integration** - Full test suite on pull requests
3. **Staging deployment** - E2E tests on staging environment
4. **Production monitoring** - Continuous error tracking

### Manual Testing Checklist
- [ ] Complete wizard flow on desktop
- [ ] Complete wizard flow on mobile
- [ ] Test with slow network conditions
- [ ] Verify accessibility with screen readers
- [ ] Cross-browser compatibility verification

---

Tento plan testów E2E zapewni maksymalną stabilność i jakość Pet Sitter Wizard v3.0. 🐾