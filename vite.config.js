import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    esbuild: {
        drop: ['console', 'debugger'], // Usuwa console.log i debugger w produkcji
    },
});