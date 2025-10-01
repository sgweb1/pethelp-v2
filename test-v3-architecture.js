/**
 * Test architektury v3.0 Pet Sitter Wizard
 *
 * Ten plik testuje czy wszystkie komponenty v3.0 są poprawnie załadowane
 * i czy WizardStateManager działa poprawnie.
 */

console.log('🧪 Testing Pet Sitter Wizard Architecture v3.0');

// Test 1: WizardStateManager v3.0 dostępność
console.log('\n=== Test 1: WizardStateManager v3.0 Availability ===');
if (typeof window !== 'undefined' && window.WizardState) {
    console.log('✅ window.WizardState is available');

    // Test podstawowych metod
    if (typeof window.WizardState.get === 'function') {
        console.log('✅ WizardState.get() method available');
    } else {
        console.log('❌ WizardState.get() method missing');
    }

    if (typeof window.WizardState.update === 'function') {
        console.log('✅ WizardState.update() method available');
    } else {
        console.log('❌ WizardState.update() method missing');
    }
} else {
    console.log('❌ window.WizardState is not available');
}

// Test 2: Step Components v3.0 dostępność
console.log('\n=== Test 2: Step Components v3.0 Availability ===');
const stepComponents = ['wizardStep1', 'wizardStep6', 'wizardStep7'];

stepComponents.forEach(component => {
    if (typeof window !== 'undefined' && typeof window[component] === 'function') {
        console.log(`✅ ${component} component available`);
    } else {
        console.log(`❌ ${component} component missing`);
    }
});

// Test 3: WizardStateManager funkcjonalność
console.log('\n=== Test 3: WizardStateManager Functionality ===');
if (typeof window !== 'undefined' && window.WizardState) {
    try {
        // Test update i get
        window.WizardState.update('test.value', 'hello world');
        const retrievedValue = window.WizardState.get('test.value');

        if (retrievedValue === 'hello world') {
            console.log('✅ Basic update/get functionality works');
        } else {
            console.log('❌ Basic update/get functionality failed');
        }

        // Test nested paths
        window.WizardState.update('nested.deep.path', 42);
        const nestedValue = window.WizardState.get('nested.deep.path');

        if (nestedValue === 42) {
            console.log('✅ Nested path functionality works');
        } else {
            console.log('❌ Nested path functionality failed');
        }

        // Test state structure
        const personalMotivation = window.WizardState.get('personalData.motivation');
        console.log('✅ personalData.motivation accessible:', typeof personalMotivation);

        const homeType = window.WizardState.get('home.homeType');
        console.log('✅ home.homeType accessible:', typeof homeType);

        const availability = window.WizardState.get('availability.weeklyAvailability');
        console.log('✅ availability.weeklyAvailability accessible:', typeof availability);

    } catch (error) {
        console.log('❌ WizardStateManager functionality test failed:', error.message);
    }
}

// Test 4: Component initialization
console.log('\n=== Test 4: Component Initialization ===');
stepComponents.forEach(componentName => {
    if (typeof window !== 'undefined' && typeof window[componentName] === 'function') {
        try {
            const component = window[componentName]();

            if (typeof component.init === 'function') {
                console.log(`✅ ${componentName} has init() method`);
            } else {
                console.log(`❌ ${componentName} missing init() method`);
            }

            // Test computed properties
            const hasComputedProperties = Object.getOwnPropertyNames(component)
                .concat(Object.getOwnPropertyNames(Object.getPrototypeOf(component)))
                .some(prop => {
                    const descriptor = Object.getOwnPropertyDescriptor(component, prop) ||
                                    Object.getOwnPropertyDescriptor(Object.getPrototypeOf(component), prop);
                    return descriptor && typeof descriptor.get === 'function';
                });

            if (hasComputedProperties) {
                console.log(`✅ ${componentName} has computed properties`);
            } else {
                console.log(`⚠️  ${componentName} may not have computed properties`);
            }

        } catch (error) {
            console.log(`❌ ${componentName} initialization failed:`, error.message);
        }
    }
});

// Test 5: Debug Tools
console.log('\n=== Test 5: Debug Tools Availability ===');
if (typeof window !== 'undefined' && window.WizardDebug) {
    console.log('✅ WizardDebug tools available');

    const debugMethods = ['showState', 'fillTestData', 'validateAllSteps', 'resetState'];
    debugMethods.forEach(method => {
        if (typeof window.WizardDebug[method] === 'function') {
            console.log(`✅ WizardDebug.${method}() available`);
        } else {
            console.log(`❌ WizardDebug.${method}() missing`);
        }
    });
} else {
    console.log('❌ WizardDebug tools not available');
}

console.log('\n🎉 Architecture v3.0 Test Complete!');

// Export dla Node.js testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        testAvailable: true,
        components: stepComponents
    };
}