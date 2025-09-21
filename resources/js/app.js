import './bootstrap';
import './alpine-components';
import './error-logger';
import { createMapComponent } from './map-component';
import './map-performance';
import './mobile-touch';
import './dark-mode';
import './accessibility';

// Safe clipboard function to prevent clipboard API errors
window.copyToClipboard = async function(text) {
    try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(text);
            return true;
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                return successful;
            } catch (err) {
                document.body.removeChild(textArea);
                console.error('Fallback copy failed:', err);
                return false;
            }
        }
    } catch (err) {
        console.error('Copy to clipboard failed:', err);
        return false;
    }
};

// Ensure map component is available immediately
window.createMapComponent = createMapComponent;

// Register with Alpine and Livewire
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, map component available:', typeof window.createMapComponent);
});

// Register with Alpine when it initializes
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('mapComponent', createMapComponent);
        console.log('Map component registered with Alpine');
    }
});

// Also register when Livewire loads
document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized, map component available:', typeof window.createMapComponent);
});