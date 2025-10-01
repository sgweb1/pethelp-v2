/**
 * Dark mode management for PetHelp application
 */

class DarkModeManager {
    constructor() {
        this.storageKey = 'pethelp-dark-mode';
        this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)');

        this.init();
    }

    init() {
        // Initialize dark mode based on saved preference or system preference
        this.initializeDarkMode();

        // Listen for system preference changes
        this.systemPreference.addEventListener('change', this.handleSystemPreferenceChange.bind(this));

        // Set up DOM event listeners
        this.setupEventListeners();

        // Add CSS classes and transitions
        this.setupDarkModeStyles();

        // Initialize Alpine.js data
        this.initializeAlpineData();
    }

    initializeDarkMode() {
        const savedMode = this.getSavedMode();

        if (savedMode === 'dark' || (savedMode === 'auto' && this.systemPreference.matches)) {
            this.enableDarkMode();
        } else {
            this.disableDarkMode();
        }
    }

    getSavedMode() {
        try {
            return localStorage.getItem(this.storageKey) || 'auto';
        } catch (e) {
            return 'auto';
        }
    }

    setSavedMode(mode) {
        try {
            localStorage.setItem(this.storageKey, mode);
        } catch (e) {
            console.warn('Could not save dark mode preference');
        }
    }

    enableDarkMode() {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark-mode');

        // Update meta theme color for mobile
        this.updateThemeColor('#1f2937');

        // Dispatch event for other components
        this.dispatchDarkModeEvent(true);
    }

    disableDarkMode() {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark-mode');

        // Update meta theme color for mobile
        this.updateThemeColor('#3b82f6');

        // Dispatch event for other components
        this.dispatchDarkModeEvent(false);
    }

    updateThemeColor(color) {
        const themeColorMeta = document.querySelector('meta[name="theme-color"]');
        if (themeColorMeta) {
            themeColorMeta.setAttribute('content', color);
        }
    }

    dispatchDarkModeEvent(isDark) {
        window.dispatchEvent(new CustomEvent('dark-mode-changed', {
            detail: {
                isDark,
                mode: this.getCurrentMode(),
                systemPreference: this.systemPreference.matches
            }
        }));
    }

    getCurrentMode() {
        const saved = this.getSavedMode();
        return saved;
    }

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    toggle() {
        if (this.isDarkMode()) {
            this.setMode('light');
        } else {
            this.setMode('dark');
        }
    }

    setMode(mode) {
        this.setSavedMode(mode);

        switch (mode) {
            case 'dark':
                this.enableDarkMode();
                break;
            case 'light':
                this.disableDarkMode();
                break;
            case 'auto':
                if (this.systemPreference.matches) {
                    this.enableDarkMode();
                } else {
                    this.disableDarkMode();
                }
                break;
        }
    }

    handleSystemPreferenceChange(e) {
        // Only respond to system changes if user has set mode to 'auto'
        if (this.getSavedMode() === 'auto') {
            if (e.matches) {
                this.enableDarkMode();
            } else {
                this.disableDarkMode();
            }
        }
    }

    setupEventListeners() {
        // Listen for manual toggle events
        document.addEventListener('toggle-dark-mode', () => {
            this.toggle();
        });

        // Listen for mode setting events
        document.addEventListener('set-dark-mode', (e) => {
            this.setMode(e.detail.mode);
        });

        // Keyboard shortcut (Ctrl/Cmd + Shift + D)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                this.toggle();
            }
        });
    }

    setupDarkModeStyles() {
        // Add smooth transitions for dark mode changes
        const style = document.createElement('style');
        style.textContent = `
            :root {
                --transition-duration: 0.3s;
            }

            * {
                transition: background-color var(--transition-duration) ease,
                           border-color var(--transition-duration) ease,
                           color var(--transition-duration) ease,
                           box-shadow var(--transition-duration) ease;
            }

            /* Disable transitions during initial load */
            .preload * {
                transition: none !important;
            }

            /* Dark mode specific adjustments */
            .dark-mode {
                color-scheme: dark;
            }

            /* Map dark mode adjustments */
            .dark .leaflet-control-container {
                filter: invert(1) hue-rotate(180deg);
            }

            .dark .leaflet-popup-content-wrapper {
                background: #374151;
                color: #f9fafb;
            }

            .dark .leaflet-popup-tip {
                background: #374151;
            }

            /* Custom scrollbar for dark mode */
            .dark ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            .dark ::-webkit-scrollbar-track {
                background: #374151;
            }

            .dark ::-webkit-scrollbar-thumb {
                background: #6b7280;
                border-radius: 4px;
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            /* Selection colors in dark mode */
            .dark ::selection {
                background: #3b82f6;
                color: #ffffff;
            }

            /* Focus indicators in dark mode */
            .dark *:focus {
                outline-color: #60a5fa;
            }

            /* Image adjustments in dark mode */
            .dark img:not(.no-dark-filter) {
                filter: brightness(0.9);
            }
        `;
        document.head.appendChild(style);

        // Remove preload class after initial load
        document.body.classList.add('preload');
        setTimeout(() => {
            document.body.classList.remove('preload');
        }, 100);
    }

    initializeAlpineData() {
        // Make dark mode data available to Alpine.js
        window.darkMode = {
            isDark: () => this.isDarkMode(),
            mode: () => this.getCurrentMode(),
            toggle: () => this.toggle(),
            setMode: (mode) => this.setMode(mode),
            systemPrefers: () => this.systemPreference.matches
        };

        // Register Alpine.js component for dark mode toggle
        document.addEventListener('alpine:init', () => {
            if (window.Alpine) {
                Alpine.data('darkModeToggle', () => ({
                    isDark: false,
                    currentMode: 'auto',
                    systemPreference: false,
                    showOptions: false,

                    init() {
                        this.updateState({
                            isDark: window.darkMode?.isDark() || false,
                            mode: window.darkMode?.mode() || 'auto',
                            systemPreference: window.darkMode?.systemPrefers() || false
                        });
                    },

                    updateState(detail) {
                        this.isDark = detail.isDark;
                        this.currentMode = detail.mode;
                        this.systemPreference = detail.systemPreference;
                    },

                    toggle() {
                        if (window.darkMode) {
                            window.darkMode.toggle();
                        }
                        this.showOptions = false;
                    },

                    setMode(mode) {
                        if (window.darkMode) {
                            window.darkMode.setMode(mode);
                        }
                        this.showOptions = false;
                    },

                    getModeLabel(mode) {
                        switch(mode) {
                            case 'light': return 'Jasny';
                            case 'dark': return 'Ciemny';
                            case 'auto': return 'Auto';
                            default: return 'Auto';
                        }
                    }
                }));
            }
        });
    }

    // Public API methods
    static init() {
        if (!window.darkModeManager) {
            window.darkModeManager = new DarkModeManager();
        }
        return window.darkModeManager;
    }

    static toggle() {
        if (window.darkModeManager) {
            window.darkModeManager.toggle();
        }
    }

    static setMode(mode) {
        if (window.darkModeManager) {
            window.darkModeManager.setMode(mode);
        }
    }

    static isDark() {
        return window.darkModeManager?.isDarkMode() || false;
    }

    static getMode() {
        return window.darkModeManager?.getCurrentMode() || 'auto';
    }
}

// Auto-initialize before DOM is ready to prevent flash
DarkModeManager.init();

// Also initialize on DOM ready for safety
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        DarkModeManager.init();
    });
} else {
    DarkModeManager.init();
}

// Export for use in other modules
window.DarkModeManager = DarkModeManager;