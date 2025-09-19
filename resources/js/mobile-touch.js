/**
 * Mobile touch interactions and responsive behavior
 */

class MobileTouchHandler {
    constructor() {
        this.isMobile = this.detectMobile();
        this.touchStartY = 0;
        this.touchStartX = 0;
        this.isScrolling = false;

        this.init();
    }

    init() {
        if (!this.isMobile) return;

        this.setupTouchEvents();
        this.setupResizeHandler();
        this.setupViewportHandler();
        this.optimizeForMobile();
    }

    detectMobile() {
        return /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
               window.innerWidth <= 768 ||
               ('ontouchstart' in window);
    }

    setupTouchEvents() {
        // Improve touch responsiveness
        document.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
        document.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: false });
        document.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: true });

        // Add touch feedback to interactive elements
        this.addTouchFeedback();
    }

    handleTouchStart(e) {
        this.touchStartY = e.touches[0].clientY;
        this.touchStartX = e.touches[0].clientX;
        this.isScrolling = false;

        // Add active state to touched element
        const target = e.target.closest('button, a, [role="button"]');
        if (target) {
            target.classList.add('touch-active');
        }
    }

    handleTouchMove(e) {
        if (!this.touchStartY || !this.touchStartX) return;

        const touchY = e.touches[0].clientY;
        const touchX = e.touches[0].clientX;
        const diffY = this.touchStartY - touchY;
        const diffX = this.touchStartX - touchX;

        // Determine scroll direction
        if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 10) {
            this.isScrolling = true;
        }

        // Remove active state if scrolling
        if (this.isScrolling) {
            document.querySelectorAll('.touch-active').forEach(el => {
                el.classList.remove('touch-active');
            });
        }
    }

    handleTouchEnd(e) {
        this.touchStartY = 0;
        this.touchStartX = 0;
        this.isScrolling = false;

        // Remove active state
        setTimeout(() => {
            document.querySelectorAll('.touch-active').forEach(el => {
                el.classList.remove('touch-active');
            });
        }, 150);
    }

    addTouchFeedback() {
        const style = document.createElement('style');
        style.textContent = `
            .touch-active {
                transform: scale(0.95);
                opacity: 0.8;
                transition: transform 0.1s ease, opacity 0.1s ease;
            }

            button, [role="button"], a {
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }

            .touch-friendly {
                user-select: none;
                -webkit-user-select: none;
            }
        `;
        document.head.appendChild(style);
    }

    setupResizeHandler() {
        let resizeTimeout;

        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });

        // Handle orientation change
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                this.handleOrientationChange();
            }, 500);
        });
    }

    handleResize() {
        // Update mobile detection
        this.isMobile = this.detectMobile();

        // Refresh map if visible
        this.refreshMapIfVisible();

        // Update viewport units
        this.updateViewportUnits();

        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('mobile-resize', {
            detail: {
                width: window.innerWidth,
                height: window.innerHeight,
                isMobile: this.isMobile
            }
        }));
    }

    handleOrientationChange() {
        // Fix iOS viewport issues
        if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
            document.body.style.height = window.innerHeight + 'px';
            setTimeout(() => {
                document.body.style.height = '';
            }, 500);
        }

        this.refreshMapIfVisible();

        window.dispatchEvent(new CustomEvent('mobile-orientation-change', {
            detail: {
                orientation: screen.orientation?.angle || window.orientation
            }
        }));
    }

    setupViewportHandler() {
        // Set CSS custom properties for viewport dimensions
        this.updateViewportUnits();

        // Handle iOS viewport issues
        this.handleIOSViewport();
    }

    updateViewportUnits() {
        const vh = window.innerHeight * 0.01;
        const vw = window.innerWidth * 0.01;

        document.documentElement.style.setProperty('--vh', `${vh}px`);
        document.documentElement.style.setProperty('--vw', `${vw}px`);

        // Set safe area values
        if (CSS.supports('padding: env(safe-area-inset-top)')) {
            document.documentElement.classList.add('has-safe-area');
        }
    }

    handleIOSViewport() {
        if (!/iPhone|iPad|iPod/.test(navigator.userAgent)) return;

        // Prevent zoom on input focus
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (input.style.fontSize !== '16px') {
                    input.style.fontSize = '16px';
                }
            });
        });

        // Handle iOS keyboard
        window.addEventListener('focusin', () => {
            document.body.classList.add('keyboard-open');
        });

        window.addEventListener('focusout', () => {
            setTimeout(() => {
                document.body.classList.remove('keyboard-open');
                this.updateViewportUnits();
            }, 300);
        });
    }

    optimizeForMobile() {
        if (!this.isMobile) return;

        // Add mobile class to body
        document.body.classList.add('is-mobile');

        // Optimize images for mobile
        this.optimizeImages();

        // Setup lazy loading for better performance
        this.setupLazyLoading();

        // Improve form interactions
        this.improveFormInteractions();

        // Setup pull-to-refresh prevention
        this.preventPullToRefresh();
    }

    optimizeImages() {
        const images = document.querySelectorAll('img[data-mobile-src]');
        images.forEach(img => {
            if (window.innerWidth <= 768) {
                img.src = img.dataset.mobileSrc;
            }
        });
    }

    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    improveFormInteractions() {
        // Auto-capitalize first letter in text inputs
        const textInputs = document.querySelectorAll('input[type="text"], textarea');
        textInputs.forEach(input => {
            if (!input.hasAttribute('autocapitalize')) {
                input.setAttribute('autocapitalize', 'sentences');
            }
        });

        // Improve number inputs
        const numberInputs = document.querySelectorAll('input[type="number"]');
        numberInputs.forEach(input => {
            input.setAttribute('inputmode', 'numeric');
        });

        // Improve email inputs
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.setAttribute('inputmode', 'email');
        });
    }

    preventPullToRefresh() {
        // Prevent pull-to-refresh on the document
        document.addEventListener('touchstart', (e) => {
            if (e.touches.length !== 1) return;

            const touch = e.touches[0];
            const target = e.target;

            // Allow pull-to-refresh only on scrollable elements at the top
            if (window.scrollY === 0 && !target.closest('.scrollable')) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchmove', (e) => {
            if (e.touches.length !== 1) return;

            if (window.scrollY === 0 && e.touches[0].clientY > this.touchStartY) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    refreshMapIfVisible() {
        const mapElement = document.getElementById('search-map');
        if (mapElement && mapElement.offsetParent !== null) {
            // Trigger map resize event
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('map-resize'));
            }, 100);
        }
    }

    // Public API
    static init() {
        if (!window.mobileTouchHandler) {
            window.mobileTouchHandler = new MobileTouchHandler();
        }
        return window.mobileTouchHandler;
    }

    static isMobileDevice() {
        return window.mobileTouchHandler?.isMobile || false;
    }

    static addTouchClass(element, className = 'touch-active') {
        if (window.mobileTouchHandler?.isMobile) {
            element.classList.add(className);
        }
    }

    static removeTouchClass(element, className = 'touch-active') {
        element.classList.remove(className);
    }
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        MobileTouchHandler.init();
    });
} else {
    MobileTouchHandler.init();
}

// Export for use in other modules
window.MobileTouchHandler = MobileTouchHandler;