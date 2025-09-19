/**
 * Accessibility Helper for PetHelp Application
 * Implements WCAG 2.1 guidelines and improvements
 */

class AccessibilityHelper {
    constructor() {
        this.trapFocus = this.trapFocus.bind(this);
        this.handleKeyboardNavigation = this.handleKeyboardNavigation.bind(this);
        this.announceToScreenReader = this.announceToScreenReader.bind(this);
        this.init();
    }

    init() {
        this.setupFocusManagement();
        this.setupKeyboardNavigation();
        this.setupAriaLiveRegions();
        this.setupSkipNavigation();
        this.setupModalAccessibility();
        this.setupFormAccessibility();
        this.setupSearchResultsAccessibility();
        this.setupMapAccessibility();
        this.setupToastNotifications();
    }

    // Focus Management
    setupFocusManagement() {
        // Store focus when modal opens
        document.addEventListener('focusin', (e) => {
            if (e.target.closest('.modal-accessible')) {
                this.lastFocusedElement = document.activeElement;
            }
        });

        // Return focus when modal closes
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-close')) {
                if (this.lastFocusedElement) {
                    this.lastFocusedElement.focus();
                    this.lastFocusedElement = null;
                }
            }
        });
    }

    trapFocus(container) {
        const focusableElements = container.querySelectorAll(
            'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        container.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            }
        });

        // Focus first element when container is shown
        setTimeout(() => firstElement.focus(), 100);
    }

    // Keyboard Navigation
    setupKeyboardNavigation() {
        document.addEventListener('keydown', this.handleKeyboardNavigation);

        // Show keyboard navigation helper
        let keyPressCount = 0;
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                keyPressCount++;
                if (keyPressCount === 3) {
                    this.showKeyboardHelper();
                }
            }
        });
    }

    handleKeyboardNavigation(e) {
        // Escape key handling
        if (e.key === 'Escape') {
            this.handleEscapeKey();
        }

        // Enter key on buttons
        if (e.key === 'Enter' && e.target.getAttribute('role') === 'button') {
            e.target.click();
        }

        // Arrow key navigation for menus
        if (['ArrowUp', 'ArrowDown'].includes(e.key)) {
            this.handleArrowNavigation(e);
        }

        // Help shortcut (Alt + H)
        if (e.altKey && e.key === 'h') {
            e.preventDefault();
            this.showKeyboardHelper();
        }
    }

    handleEscapeKey() {
        // Close modals
        const openModal = document.querySelector('.modal-accessible[aria-hidden="false"]');
        if (openModal) {
            this.closeModal(openModal);
            return;
        }

        // Close dropdowns
        const openDropdown = document.querySelector('[aria-expanded="true"]');
        if (openDropdown) {
            openDropdown.setAttribute('aria-expanded', 'false');
            openDropdown.focus();
        }
    }

    handleArrowNavigation(e) {
        const menu = e.target.closest('[role="menu"], [role="menubar"]');
        if (!menu) return;

        const items = Array.from(menu.querySelectorAll('[role="menuitem"]'));
        const currentIndex = items.indexOf(e.target);

        if (currentIndex === -1) return;

        let nextIndex;
        if (e.key === 'ArrowDown') {
            nextIndex = currentIndex === items.length - 1 ? 0 : currentIndex + 1;
        } else {
            nextIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
        }

        items[nextIndex].focus();
        e.preventDefault();
    }

    showKeyboardHelper() {
        const helper = document.querySelector('.keyboard-nav-helper') || this.createKeyboardHelper();
        helper.classList.add('show');
        helper.focus();

        setTimeout(() => {
            helper.classList.remove('show');
        }, 5000);
    }

    createKeyboardHelper() {
        const helper = document.createElement('div');
        helper.className = 'keyboard-nav-helper';
        helper.setAttribute('tabindex', '-1');
        helper.innerHTML = `
            <h3>Skróty klawiszowe</h3>
            <ul style="text-align: left; margin: 10px 0;">
                <li><strong>Tab</strong> - Przejdź do następnego elementu</li>
                <li><strong>Shift + Tab</strong> - Przejdź do poprzedniego elementu</li>
                <li><strong>Enter/Spacja</strong> - Aktywuj element</li>
                <li><strong>Escape</strong> - Zamknij modal lub dropdown</li>
                <li><strong>Alt + H</strong> - Pokaż tę pomoc</li>
                <li><strong>Strzałki</strong> - Nawigacja w menu</li>
            </ul>
            <button onclick="this.parentElement.classList.remove('show')" style="margin-top: 10px; padding: 5px 10px;">Zamknij</button>
        `;
        document.body.appendChild(helper);
        return helper;
    }

    // ARIA Live Regions
    setupAriaLiveRegions() {
        if (!document.querySelector('#aria-live-polite')) {
            const politeRegion = document.createElement('div');
            politeRegion.id = 'aria-live-polite';
            politeRegion.setAttribute('aria-live', 'polite');
            politeRegion.className = 'sr-only';
            document.body.appendChild(politeRegion);
        }

        if (!document.querySelector('#aria-live-assertive')) {
            const assertiveRegion = document.createElement('div');
            assertiveRegion.id = 'aria-live-assertive';
            assertiveRegion.setAttribute('aria-live', 'assertive');
            assertiveRegion.className = 'sr-only';
            document.body.appendChild(assertiveRegion);
        }
    }

    announceToScreenReader(message, priority = 'polite') {
        const region = document.querySelector(`#aria-live-${priority}`);
        if (region) {
            region.textContent = '';
            setTimeout(() => {
                region.textContent = message;
            }, 100);
        }
    }

    // Skip Navigation
    setupSkipNavigation() {
        if (!document.querySelector('.skip-navigation')) {
            const skipLink = document.createElement('a');
            skipLink.className = 'skip-navigation';
            skipLink.href = '#main-content';
            skipLink.textContent = 'Przejdź do głównej treści';
            document.body.insertBefore(skipLink, document.body.firstChild);
        }

        // Ensure main content has proper ID
        const main = document.querySelector('main');
        if (main && !main.id) {
            main.id = 'main-content';
            main.setAttribute('tabindex', '-1');
        }
    }

    // Modal Accessibility
    setupModalAccessibility() {
        document.addEventListener('click', (e) => {
            if (e.target.hasAttribute('data-modal-trigger')) {
                const modalId = e.target.getAttribute('data-modal-trigger');
                const modal = document.querySelector(`#${modalId}`);
                if (modal) {
                    this.openModal(modal);
                }
            }
        });
    }

    openModal(modal) {
        modal.setAttribute('aria-hidden', 'false');
        modal.style.display = 'flex';
        this.trapFocus(modal);

        // Prevent background scroll
        document.body.style.overflow = 'hidden';

        this.announceToScreenReader('Modal otwarty', 'assertive');
    }

    closeModal(modal) {
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';

        // Restore background scroll
        document.body.style.overflow = '';

        if (this.lastFocusedElement) {
            this.lastFocusedElement.focus();
            this.lastFocusedElement = null;
        }

        this.announceToScreenReader('Modal zamknięty', 'assertive');
    }

    // Form Accessibility
    setupFormAccessibility() {
        // Associate labels with form controls
        document.querySelectorAll('input, select, textarea').forEach(input => {
            if (!input.id) {
                input.id = `field-${Math.random().toString(36).substr(2, 9)}`;
            }

            const label = input.closest('.form-field-accessible')?.querySelector('label');
            if (label && !label.getAttribute('for')) {
                label.setAttribute('for', input.id);
            }

            // Add required attribute announcement
            if (input.hasAttribute('required')) {
                input.setAttribute('aria-required', 'true');
            }

            // Error state handling
            input.addEventListener('invalid', (e) => {
                this.handleFormError(e.target);
            });

            input.addEventListener('input', (e) => {
                this.clearFormError(e.target);
            });
        });
    }

    handleFormError(input) {
        const container = input.closest('.form-field-accessible');
        if (!container) return;

        input.setAttribute('aria-invalid', 'true');

        let errorElement = container.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.id = `error-${input.id}`;
            container.appendChild(errorElement);
        }

        const errorMessage = input.validationMessage || 'To pole jest wymagane';
        errorElement.textContent = errorMessage;
        input.setAttribute('aria-describedby', errorElement.id);

        this.announceToScreenReader(`Błąd w polu ${input.labels?.[0]?.textContent || input.name}: ${errorMessage}`, 'assertive');
    }

    clearFormError(input) {
        input.removeAttribute('aria-invalid');
        const container = input.closest('.form-field-accessible');
        const errorElement = container?.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
            input.removeAttribute('aria-describedby');
        }
    }

    // Search Results Accessibility
    setupSearchResultsAccessibility() {
        const searchForms = document.querySelectorAll('form[role="search"], .search-form');
        searchForms.forEach(form => {
            form.addEventListener('submit', () => {
                setTimeout(() => {
                    this.updateSearchResultsAnnouncement();
                }, 1000);
            });
        });

        // Monitor for Livewire search updates
        document.addEventListener('livewire:updated', () => {
            this.updateSearchResultsAnnouncement();
        });
    }

    updateSearchResultsAnnouncement() {
        const resultsContainer = document.querySelector('.search-results-accessible, [wire\\:poll], .search-results');
        if (!resultsContainer) return;

        const resultItems = resultsContainer.querySelectorAll('.search-result-item, .result-item, [data-result-item]');
        const count = resultItems.length;

        const message = count === 0
            ? 'Nie znaleziono wyników'
            : `Znaleziono ${count} ${count === 1 ? 'wynik' : count < 5 ? 'wyniki' : 'wyników'}`;

        resultsContainer.setAttribute('data-results-count', message);
        this.announceToScreenReader(message, 'polite');
    }

    // Map Accessibility
    setupMapAccessibility() {
        const mapContainers = document.querySelectorAll('#map, .map-container, [data-map]');
        mapContainers.forEach(mapContainer => {
            mapContainer.classList.add('map-accessible');
            mapContainer.setAttribute('role', 'application');
            mapContainer.setAttribute('aria-label', 'Interaktywna mapa z lokalizacjami');
            mapContainer.setAttribute('tabindex', '0');

            // Add keyboard instructions
            const instructions = document.createElement('div');
            instructions.className = 'sr-only';
            instructions.textContent = 'Użyj klawiszy strzałek do poruszania się po mapie, spacji do interakcji z markerami';
            mapContainer.appendChild(instructions);
        });
    }

    // Toast Notifications
    setupToastNotifications() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.id = 'toast-container';
        this.toastContainer.setAttribute('aria-live', 'polite');
        this.toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(this.toastContainer);
    }

    showToast(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        toast.className = `toast-accessible toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" style="margin-left: 10px; background: none; border: none; font-size: 18px; cursor: pointer;" aria-label="Zamknij powiadomienie">&times;</button>
            </div>
        `;

        this.toastContainer.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, duration);

        return toast;
    }

    // Loading State Management
    setLoadingState(element, isLoading, loadingText = 'Ładowanie...') {
        if (isLoading) {
            element.setAttribute('aria-busy', 'true');
            element.setAttribute('aria-label', loadingText);
            element.classList.add('loading-accessible');
        } else {
            element.removeAttribute('aria-busy');
            element.removeAttribute('aria-label');
            element.classList.remove('loading-accessible');
        }
    }

    // Utility: Check if element is visible to screen readers
    isElementAccessible(element) {
        const style = window.getComputedStyle(element);
        return style.display !== 'none' &&
               style.visibility !== 'hidden' &&
               style.opacity !== '0' &&
               !element.hasAttribute('aria-hidden');
    }

    // Utility: Get accessible name of element
    getAccessibleName(element) {
        // Check aria-label first
        if (element.hasAttribute('aria-label')) {
            return element.getAttribute('aria-label');
        }

        // Check aria-labelledby
        if (element.hasAttribute('aria-labelledby')) {
            const labelId = element.getAttribute('aria-labelledby');
            const labelElement = document.getElementById(labelId);
            if (labelElement) {
                return labelElement.textContent.trim();
            }
        }

        // Check associated label
        if (element.labels && element.labels.length > 0) {
            return element.labels[0].textContent.trim();
        }

        // Fallback to text content
        return element.textContent.trim();
    }
}

// Initialize accessibility helper when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.accessibilityHelper = new AccessibilityHelper();
});

// Export for use in other modules
window.AccessibilityHelper = AccessibilityHelper;