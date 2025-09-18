// Modal Component for Alpine.js
export default () => ({
    show: false,

    open() {
        this.show = true;
        document.body.style.overflow = 'hidden';
    },

    close() {
        this.show = false;
        document.body.style.overflow = 'auto';
    },

    init() {
        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.show) {
                this.close();
            }
        });
    }
});