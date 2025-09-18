// PetHelp UI Components JavaScript
// Obsługa interaktywności komponentów z Alpine.js

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

// Rejestruj Alpine plugins
Alpine.plugin(collapse)

// Toast notifications system
window.showToast = function(message, type = 'info', timeout = 5000) {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.innerHTML = `
        <div x-data="{
            show: true,
            init() {
                setTimeout(() => this.show = false, ${timeout});
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 transform translate-y-2 scale-95"
        class="mb-4 max-w-sm w-full bg-white border rounded-lg shadow-lg pointer-events-auto toast-${type}"
        style="display: none;">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${getToastIcon(type)}
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="inline-flex text-current opacity-70 hover:opacity-100 focus:outline-none transition-opacity">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    toastContainer.appendChild(toast);
    Alpine.initTree(toast);

    // Auto remove after timeout + animation
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, timeout + 500);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed top-4 right-4 z-50';
    document.body.appendChild(container);
    return container;
}

function getToastIcon(type) {
    const icons = {
        success: '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
        error: '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
        warning: '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
        info: '<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
    };
    return icons[type] || icons.info;
}

// Form validation helpers
window.FormValidator = {
    validateRequired(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    validateMinLength(value, minLength) {
        return value && value.toString().length >= minLength;
    },

    validateMaxLength(value, maxLength) {
        return !value || value.toString().length <= maxLength;
    }
};

// Component animations
window.ComponentAnimations = {
    fadeIn: 'transition ease-out duration-300',
    fadeOut: 'transition ease-in duration-200',
    slideDown: 'transition ease-out duration-300 transform',
    slideUp: 'transition ease-in duration-200 transform'
};

// Initialize Alpine
window.Alpine = Alpine
Alpine.start()

console.log('PetHelp UI Components loaded successfully!');