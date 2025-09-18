// Toast Notifications Component for Alpine.js
export default () => ({
    notifications: [],

    show(message, type = 'info', duration = 5000) {
        const id = Date.now();
        const notification = {
            id,
            message,
            type,
            visible: true
        };

        this.notifications.push(notification);

        // Auto remove after duration
        setTimeout(() => {
            this.remove(id);
        }, duration);
    },

    remove(id) {
        const index = this.notifications.findIndex(n => n.id === id);
        if (index > -1) {
            this.notifications[index].visible = false;
            // Remove from array after animation
            setTimeout(() => {
                this.notifications.splice(index, 1);
            }, 300);
        }
    },

    success(message, duration = 5000) {
        this.show(message, 'success', duration);
    },

    error(message, duration = 7000) {
        this.show(message, 'error', duration);
    },

    warning(message, duration = 6000) {
        this.show(message, 'warning', duration);
    },

    info(message, duration = 5000) {
        this.show(message, 'info', duration);
    },

    init() {
        // Listen for global toast events
        document.addEventListener('show-toast', (e) => {
            this.show(e.detail.message, e.detail.type, e.detail.duration);
        });

        // Listen for Livewire flash messages
        window.addEventListener('livewire:load', () => {
            Livewire.on('notify', (data) => {
                this.show(data.message, data.type || 'info', data.duration);
            });
        });
    },

    getTypeClasses(type) {
        const classes = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        return classes[type] || classes.info;
    },

    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }
});