/**
 * Bezpieczny renderer ikon SVG używający SVG.js
 *
 * Zapewnia spójne, bezpieczne renderowanie ikon SVG bez problemów
 * z innerHTML i setAttribute. Wszystkie ikony są renderowane
 * programatically przez SVG.js API.
 */

import { SVG } from '@svgdotjs/svg.js';

/**
 * Klasa zarządzająca bezpiecznym renderowaniem ikon SVG.
 */
class SafeSVGIcons {
    constructor() {
        // Cache dla zrenderowanych ikon
        this.iconCache = new Map();

        // Definicje ikon z bezpiecznymi parametrami
        this.iconDefinitions = {
            // Loading spinner
            loading: {
                size: { width: 16, height: 16 },
                classes: 'animate-spin text-white',
                paths: [
                    {
                        type: 'circle',
                        attrs: {
                            cx: 12, cy: 12, r: 10,
                            stroke: 'currentColor',
                            'stroke-width': 4,
                            fill: 'none',
                            opacity: 0.25
                        }
                    },
                    {
                        type: 'path',
                        attrs: {
                            fill: 'currentColor',
                            opacity: 0.75,
                            d: 'M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'
                        }
                    }
                ]
            },

            // Checkmark
            check: {
                size: { width: 20, height: 20 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'fill-rule': 'evenodd',
                            'clip-rule': 'evenodd',
                            fill: 'currentColor',
                            d: 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z'
                        }
                    }
                ]
            },

            // Close/X
            close: {
                size: { width: 24, height: 24 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': 2,
                            stroke: 'currentColor',
                            fill: 'none',
                            d: 'M6 18L18 6M6 6l12 12'
                        }
                    }
                ]
            },

            // Arrow right
            arrowRight: {
                size: { width: 24, height: 24 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': 2,
                            stroke: 'currentColor',
                            fill: 'none',
                            d: 'M13 7l5 5m0 0l-5 5m5-5H6'
                        }
                    }
                ]
            },

            // Arrow left
            arrowLeft: {
                size: { width: 24, height: 24 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': 2,
                            stroke: 'currentColor',
                            fill: 'none',
                            d: 'M15 19l-7-7 7-7'
                        }
                    }
                ]
            },

            // Location pin
            location: {
                size: { width: 24, height: 24 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': 2,
                            stroke: 'currentColor',
                            fill: 'none',
                            d: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'
                        }
                    },
                    {
                        type: 'path',
                        attrs: {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': 2,
                            stroke: 'currentColor',
                            fill: 'none',
                            d: 'M15 11a3 3 0 11-6 0 3 3 0 016 0z'
                        }
                    }
                ]
            },

            // Settings/Gear
            settings: {
                size: { width: 20, height: 20 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'fill-rule': 'evenodd',
                            'clip-rule': 'evenodd',
                            fill: 'currentColor',
                            d: 'M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z'
                        }
                    }
                ]
            },

            // Error/Alert
            error: {
                size: { width: 20, height: 20 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'fill-rule': 'evenodd',
                            'clip-rule': 'evenodd',
                            fill: 'currentColor',
                            d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z'
                        }
                    }
                ]
            },

            // Success/Check circle
            checkCircle: {
                size: { width: 20, height: 20 },
                classes: 'text-current',
                paths: [
                    {
                        type: 'path',
                        attrs: {
                            'fill-rule': 'evenodd',
                            'clip-rule': 'evenodd',
                            fill: 'currentColor',
                            d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
                        }
                    }
                ]
            }
        };
    }

    /**
     * Tworzy bezpieczną ikonę SVG w podanym kontenerze.
     *
     * @param {string} iconName - Nazwa ikony z definicji
     * @param {HTMLElement} container - Element HTML gdzie umieścić ikonę
     * @param {Object} options - Opcje renderowania
     * @returns {Object} SVG.js object lub null jeśli błąd
     */
    createIcon(iconName, container, options = {}) {
        try {
            const definition = this.iconDefinitions[iconName];
            if (!definition) {
                console.warn(`SafeSVGIcons: Unknown icon "${iconName}"`);
                return null;
            }

            // Wyczyść kontener
            container.innerHTML = '';

            // Merge opcji z definicją
            const config = {
                size: { ...definition.size, ...options.size },
                classes: options.classes || definition.classes,
                style: options.style || {}
            };

            // Utwórz SVG element przez SVG.js
            const svg = SVG()
                .addTo(container)
                .size(config.size.width, config.size.height)
                .viewbox(0, 0, 24, 24);

            // Dodaj klasy CSS
            if (config.classes) {
                svg.addClass(config.classes);
            }

            // Dodaj style inline
            if (config.style) {
                Object.keys(config.style).forEach(key => {
                    svg.css(key, config.style[key]);
                });
            }

            // Renderuj ścieżki
            definition.paths.forEach(pathDef => {
                let element;

                switch (pathDef.type) {
                    case 'path':
                        element = svg.path(pathDef.attrs.d);
                        break;
                    case 'circle':
                        element = svg.circle(pathDef.attrs.r * 2)
                            .center(pathDef.attrs.cx, pathDef.attrs.cy);
                        break;
                    case 'rect':
                        element = svg.rect(pathDef.attrs.width, pathDef.attrs.height)
                            .move(pathDef.attrs.x || 0, pathDef.attrs.y || 0);
                        break;
                    default:
                        console.warn(`SafeSVGIcons: Unknown path type "${pathDef.type}"`);
                        return;
                }

                // Zastosuj atrybuty
                Object.keys(pathDef.attrs).forEach(attr => {
                    if (!['d', 'cx', 'cy', 'r', 'width', 'height', 'x', 'y'].includes(attr)) {
                        element.attr(attr, pathDef.attrs[attr]);
                    }
                });
            });

            return svg;

        } catch (error) {
            console.error('SafeSVGIcons: Error creating icon:', error);
            return null;
        }
    }

    /**
     * Inicjalizuje wszystkie ikony w kontenerze z atrybutem data-svg-icon.
     *
     * @param {HTMLElement} container - Kontener do przeszukania
     */
    initializeIcons(container = document) {
        const iconElements = container.querySelectorAll('[data-svg-icon]');

        iconElements.forEach(element => {
            const iconName = element.getAttribute('data-svg-icon');
            const options = {};

            // Parsuj opcje z data-atrybutów
            if (element.hasAttribute('data-svg-size')) {
                const sizeStr = element.getAttribute('data-svg-size');
                const [width, height] = sizeStr.split('x').map(Number);
                options.size = { width, height };
            }

            if (element.hasAttribute('data-svg-classes')) {
                let classes = element.getAttribute('data-svg-classes');
                // Usuń problematyczne cudzysłowy z początku i końca
                if (classes && (classes.startsWith('"') || classes.endsWith('"'))) {
                    classes = classes.replace(/^["']|["']$/g, '');
                    console.warn('SafeSVGIcons: Znaleziono problematyczne cudzysłowy w klasach CSS:', element.getAttribute('data-svg-classes'), 'Naprawiono na:', classes);
                }
                options.classes = classes;
            }

            this.createIcon(iconName, element, options);
        });
    }

    /**
     * Dodaje nową definicję ikony.
     *
     * @param {string} name - Nazwa ikony
     * @param {Object} definition - Definicja ikony
     */
    addIcon(name, definition) {
        this.iconDefinitions[name] = definition;
    }

    /**
     * Pomocnicza metoda do szybkiego tworzenia loading spinnera.
     *
     * @param {HTMLElement} container - Kontener dla spinnera
     * @param {Object} options - Opcje spinnera
     */
    createLoadingSpinner(container, options = {}) {
        return this.createIcon('loading', container, {
            classes: 'animate-spin ' + (options.classes || 'text-white'),
            size: options.size || { width: 16, height: 16 },
            ...options
        });
    }
}

// Utwórz globalną instancję
const safeSVGIcons = new SafeSVGIcons();

// Export dla modułów
export default safeSVGIcons;

// Udostępnij globalnie dla Alpine.js i Livewire
window.SafeSVGIcons = safeSVGIcons;

// Auto-inicjalizacja na DOM ready
document.addEventListener('DOMContentLoaded', () => {
    safeSVGIcons.initializeIcons();
});

// Re-inicjalizacja dla dynamicznej zawartości (np. Livewire)
document.addEventListener('livewire:navigated', () => {
    safeSVGIcons.initializeIcons();
});

document.addEventListener('livewire:morph.updated', (event) => {
    // Nie inicjalizuj ikon wewnątrz kontenerów map - mogą mieć własny stan JS
    const target = event.target;
    if (target && (
        target.id === 'map-container' ||
        target.closest('#map-container') ||
        target.querySelector('#map-container')
    )) {
        console.log('SafeSVGIcons: Pomijanie re-inicjalizacji w kontenerze mapy');
        return;
    }

    safeSVGIcons.initializeIcons(target);
});