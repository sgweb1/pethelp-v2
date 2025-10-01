// Alpine.js Components Registry
import modal from './components/modal.js';
import dropdown from './components/dropdown.js';
import geolocation from './components/geolocation.js';
import toast from './components/toast.js';
import petSitterWizard from './components/pet-sitter-wizard.js';
import addressAutocomplete from './components/address-autocomplete.js';

// Register components globally
document.addEventListener('alpine:init', () => {
    Alpine.data('modal', modal);
    Alpine.data('dropdown', dropdown);
    Alpine.data('geolocation', geolocation);
    Alpine.data('toast', toast);
    Alpine.data('petSitterWizard', petSitterWizard);
    Alpine.data('addressAutocomplete', addressAutocomplete);

    // Register Wizard v3.0 components - immediate registration with fallbacks
    Alpine.data('wizardStep1', window.wizardStep1 || (() => ({})));
    Alpine.data('wizardStep2', window.wizardStep2 || (() => ({})));
    Alpine.data('wizardStep5', window.wizardStep5 || (() => ({
        mapInitialized: false,
        initializeMap() { console.warn('wizardStep5 not loaded'); }
    })));
    Alpine.data('wizardStep6', window.wizardStep6 || (() => ({})));
    Alpine.data('wizardStep7', window.wizardStep7 || (() => ({})));
    Alpine.data('wizardStep8', window.wizardStep8 || (() => ({})));
    Alpine.data('wizardStep9', window.wizardStep9 || (() => ({})));
    Alpine.data('wizardStep10', window.wizardStep10 || (() => ({})));

    console.log('Alpine components registered:', {
        modal: typeof modal,
        dropdown: typeof dropdown,
        geolocation: typeof geolocation,
        toast: typeof toast,
        petSitterWizard: typeof petSitterWizard,
        addressAutocomplete: typeof addressAutocomplete,
        // Wizard v3.0 components
        wizardStep1: typeof window.wizardStep1,
        wizardStep2: typeof window.wizardStep2,
        wizardStep5: typeof window.wizardStep5,
        wizardStep6: typeof window.wizardStep6,
        wizardStep7: typeof window.wizardStep7,
        wizardStep8: typeof window.wizardStep8,
        wizardStep9: typeof window.wizardStep9,
        wizardStep10: typeof window.wizardStep10
    });

    // Global stores
    Alpine.store('mobileMenu', {
        open: false,
        toggle() {
            this.open = !this.open;
            console.log('Mobile menu toggled:', this.open);
        },
        close() {
            this.open = false;
            console.log('Mobile menu closed');
        }
    });

    console.log('Alpine mobileMenu store initialized');
});

// ===== UTILITIES =====

// Utility function to format Polish currency
window.formatCurrency = (amount, currency = 'PLN') => {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

// Utility function to format Polish dates
window.formatPolishDate = (date, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return new Intl.DateTimeFormat('pl-PL', { ...defaultOptions, ...options }).format(new Date(date));
};

// Utility to debounce function calls
window.debounce = (func, wait, immediate = false) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
};

// Utility to throttle function calls
window.throttle = (func, limit) => {
    let lastFunc;
    let lastRan;
    return function(...args) {
        if (!lastRan) {
            func(...args);
            lastRan = Date.now();
        } else {
            clearTimeout(lastFunc);
            lastFunc = setTimeout(() => {
                if ((Date.now() - lastRan) >= limit) {
                    func(...args);
                    lastRan = Date.now();
                }
            }, limit - (Date.now() - lastRan));
        }
    }
};