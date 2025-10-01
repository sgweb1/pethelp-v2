# Pet Sitter Wizard - Nowa Architektura v3.0

## Problemy z obecną architekturą v2.0

### 🚫 Zidentyfikowane problemy:
- **Scope Pollution**: Każdy krok ma własny x-data, ale używa zmiennych z innych kroków
- **Variable Duplication**: Te same zmienne są zdefiniowane w wielu komponentach
- **Tight Coupling**: Kroki zależą od siebie nawzajem bez jasnych interfejsów
- **Cache Issues**: Zmiany wymagają rebuild i manual cache clear
- **Complex Debugging**: Trudne debugowanie gdzie zmienne są (nie)zdefiniowane

## 🎯 Nowa Architektura v3.0 - "Single Source of Truth"

### 1. Centralized State Management (CSOT - Centralized Source of Truth)

```javascript
// /resources/js/wizard-state-manager.js - Rozszerzony
class WizardStateManager {
    constructor() {
        this.state = {
            // Meta
            currentStep: 1,
            maxSteps: 12,
            isActive: false,

            // Cross-step data - SINGLE SOURCE OF TRUTH
            personalData: {
                name: '',
                email: '',
                city: '',
                motivation: ''
            },

            experience: {
                petExperience: '',
                yearsOfExperience: 0,
                animalTypes: [],
                animalSizes: []
            },

            services: {
                serviceTypes: [],
                specialServices: []
            },

            availability: {
                weeklyAvailability: {},
                flexibleSchedule: false,
                emergencyAvailable: false,
                serviceRadius: 10
            },

            home: {
                homeType: '',
                hasGarden: false,
                isSmoking: false,
                hasOtherPets: false,
                otherPets: []
            },

            verification: {
                hasProfilePhoto: false,
                homePhotosCount: 0,
                hasIdentityDocument: false,
                hasCriminalRecord: false,
                referencesCount: 0
            },

            pricing: {
                pricingStrategy: 'competitive',
                servicePricing: {}
            }
        }

        this.watchers = []
        this.livewire = null
        this.debugging = true
    }

    // Centralized state updates
    update(path, value) {
        const oldValue = this.get(path)
        this.set(path, value)

        // Auto-sync with Livewire
        this.syncToLivewire(path, value)

        // Notify watchers
        this.notify(path, value, oldValue)

        if (this.debugging) {
            console.log(`🏗️ State updated: ${path}`, { old: oldValue, new: value })
        }
    }

    // Safe getter with dot notation
    get(path) {
        return path.split('.').reduce((obj, key) => obj?.[key], this.state)
    }

    // Safe setter with dot notation
    set(path, value) {
        const keys = path.split('.')
        const lastKey = keys.pop()
        const target = keys.reduce((obj, key) => {
            if (!obj[key]) obj[key] = {}
            return obj[key]
        }, this.state)
        target[lastKey] = value
    }
}

// Global singleton
window.WizardState = new WizardStateManager()
```

### 2. Komponenty bez własnego state (Stateless Components)

```blade
{{-- step-7.blade.php - Stateless --}}
<div x-data="wizardStep7()" x-init="init()">
    <!-- HTML bez własnych zmiennych Alpine.js -->
    <label :class="getHomeTypeClasses('house')" @click="selectHomeType('house')">
        <div>Dom</div>
    </label>

    <label :class="getGardenClasses()" @click="toggleGarden()">
        <div>Ogród</div>
    </label>
</div>

<script>
function wizardStep7() {
    return {
        // Brak własnych zmiennych!

        init() {
            console.log('🏠 Step 7: Stateless component initialized')
            this.syncWithGlobalState()
        },

        // Metody operują na globalnym state
        selectHomeType(type) {
            window.WizardState.update('home.homeType', type)
        },

        toggleGarden() {
            const current = window.WizardState.get('home.hasGarden')
            window.WizardState.update('home.hasGarden', !current)
        },

        // Computed properties z global state
        getHomeTypeClasses(type) {
            const current = window.WizardState.get('home.homeType')
            return {
                'selected': current === type,
                'border-emerald-500 bg-emerald-50': current === type,
                'border-gray-200': current !== type
            }
        },

        getGardenClasses() {
            const hasGarden = window.WizardState.get('home.hasGarden')
            return {
                'border-emerald-500 bg-emerald-50': hasGarden,
                'border-gray-200': !hasGarden
            }
        }
    }
}
</script>
```

### 3. Auto-sync między krokami

```javascript
// Każdy komponent automatycznie reaguje na zmiany
class WizardStepComponent {
    constructor(stepNumber) {
        this.stepNumber = stepNumber
        this.setupAutoSync()
    }

    setupAutoSync() {
        // Słuchaj zmian w globalnym state
        window.WizardState.watch((path, newValue, oldValue) => {
            if (this.shouldUpdate(path)) {
                this.onStateChange(path, newValue, oldValue)
            }
        })
    }

    shouldUpdate(path) {
        // Każdy krok definiuje czego słucha
        const dependencies = this.getDependencies()
        return dependencies.some(dep => path.startsWith(dep))
    }

    getDependencies() {
        // Override in each step
        return []
    }
}
```

### 4. Reactive Templates

```blade
{{-- Reaktywne template bez własnych zmiennych --}}
<div x-data="{
    get homeType() { return window.WizardState.get('home.homeType') },
    get hasGarden() { return window.WizardState.get('home.hasGarden') },
    get flexibleSchedule() { return window.WizardState.get('availability.flexibleSchedule') }
}">
    <!-- Templates automatycznie reagują na zmiany -->
    <div x-text="homeType"></div>
    <div x-show="hasGarden">Masz ogród!</div>
    <div :class="{ 'active': flexibleSchedule }">Elastyczny harmonogram</div>
</div>
```

### 5. Debugging i Development Tools

```javascript
// Development Tools
class WizardDevTools {
    static logState() {
        console.table(window.WizardState.state)
    }

    static validateStep(stepNumber) {
        const validator = this.getStepValidator(stepNumber)
        return validator(window.WizardState.state)
    }

    static dumpErrors() {
        return this.errors.slice(-10) // Last 10 errors
    }

    static testAllSteps() {
        for (let step = 1; step <= 12; step++) {
            this.validateStep(step)
        }
    }
}

// Global access
window.WizardDevTools = WizardDevTools

// Console helpers w dev mode
if (process.env.NODE_ENV === 'development') {
    window.dumpState = () => WizardDevTools.logState()
    window.validateStep = (step) => WizardDevTools.validateStep(step)
    window.wizardErrors = () => WizardDevTools.dumpErrors()
}
```

### 6. Testing Strategy

```javascript
// E2E Tests
describe('Pet Sitter Wizard', () => {
    beforeEach(() => {
        cy.visit('/profil/zostan-pet-sitterem')
        cy.get('[data-test="activate-wizard"]').click()
    })

    it('should maintain state between steps', () => {
        // Step 1
        cy.get('[data-test="motivation"]').type('Test motivation')
        cy.get('[data-test="next-step"]').click()

        // Step 7
        cy.get('[data-test="go-to-step-7"]').click()
        cy.get('[data-test="home-type-house"]').click()
        cy.get('[data-test="has-garden"]').click()

        // Step 6
        cy.get('[data-test="go-to-step-6"]').click()
        cy.get('[data-test="flexible-schedule"]').click()

        // Verify state consistency
        cy.window().then((win) => {
            expect(win.WizardState.get('home.hasGarden')).to.be.true
            expect(win.WizardState.get('availability.flexibleSchedule')).to.be.true
        })

        // Back to step 7 - should remember values
        cy.get('[data-test="go-to-step-7"]').click()
        cy.get('[data-test="has-garden"]').should('have.class', 'selected')
    })
})
```

## 🚀 Benefits nowej architektury:

### 1. **Single Source of Truth**
- Wszystkie zmienne w jednym miejscu
- Brak duplikacji i konfliktów
- Łatwiejsze debugging

### 2. **Reactive by Design**
- Automatyczne updaty między krokami
- Komponenty reagują na zmiany
- Konsystentny state wszędzie

### 3. **Testable & Debuggable**
- Jasny state do testowania
- Development tools
- Console debugging helpers

### 4. **Maintainable**
- Czytelny kod
- Łatwy refactoring
- Modułowa struktura

### 5. **Performance**
- Brak niepotrzebnego re-renderowania
- Efficient state updates
- Lazy loading komponentów

## 📊 Migration Plan

### Faza 1: State Manager (1-2 dni)
- [ ] Implementuj WizardStateManager
- [ ] Dodaj Livewire sync
- [ ] Stwórz development tools

### Faza 2: Refactor Steps (3-4 dni)
- [ ] Przekonwertuj step-1 do nowej architektury
- [ ] Przekonwertuj step-2, step-3...
- [ ] Przetestuj każdy krok

### Faza 3: Testing & Polish (2-3 dni)
- [ ] E2E tests
- [ ] Performance optimization
- [ ] Documentation update

### Faza 4: Deployment (1 dzień)
- [ ] Production testing
- [ ] Rollback plan
- [ ] Monitor błędy

## 🛠️ Development Guidelines

### 1. Komponenty zawsze stateless
```javascript
// ❌ Zły sposób
function wizardStep() {
    return {
        homeType: '', // Własny state - NO!
        toggle() { this.homeType = 'house' }
    }
}

// ✅ Dobry sposób
function wizardStep() {
    return {
        toggle() {
            window.WizardState.update('home.homeType', 'house')
        }
    }
}
```

### 2. Wszystkie zmiany przez State Manager
```javascript
// ❌ Direct Livewire calls
this.$wire.set('homeType', value)

// ✅ Through State Manager
window.WizardState.update('home.homeType', value)
```

### 3. Używaj computed properties dla reactivity
```javascript
// W Alpine.js components
{
    get homeType() {
        return window.WizardState.get('home.homeType')
    }
}
```

## 📈 Success Metrics

1. **Zero Alpine.js Expression Errors** w console
2. **100% state consistency** między krokami
3. **< 2 sekundy** czas ładowania każdego kroku
4. **90%+ test coverage** dla E2E scenarios
5. **Zero manual state sync** - wszystko automatic

Ta architektura rozwiązuje wszystkie obecne problemy i zapewnia skalowalność na przyszłość.