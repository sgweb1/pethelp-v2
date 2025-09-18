// Alpine.js Components Registry
import modal from './components/modal.js';
import dropdown from './components/dropdown.js';
import geolocation from './components/geolocation.js';
import toast from './components/toast.js';

// Register components globally
document.addEventListener('alpine:init', () => {
    Alpine.data('modal', modal);
    Alpine.data('dropdown', dropdown);
    Alpine.data('geolocation', geolocation);
    Alpine.data('toast', toast);
});

// Global utility functions for Alpine.js
window.Alpine = window.Alpine || {};

// Utility to show toast notifications from anywhere in the app
window.showToast = (message, type = 'info', duration = 5000) => {
    document.dispatchEvent(new CustomEvent('show-toast', {
        detail: { message, type, duration }
    }));
};

// Utility to format currency
window.formatCurrency = (amount, currency = 'PLN') => {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: currency
    }).format(amount);
};

// Utility to format date
window.formatDate = (date, options = {}) => {
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