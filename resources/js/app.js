import './bootstrap';
import './alpine-components';
import './error-logger';
import { createMapComponent } from './map-component';
import './map-performance';
import './mobile-touch';
import './dark-mode';
import './accessibility';

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