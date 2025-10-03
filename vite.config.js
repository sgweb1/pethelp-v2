import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'resources/css/filament/admin/theme.css', // Tymczasowo wyłączone - użyjemy domyślnego theme
            ],
            refresh: true,
        }),
    ],
    esbuild: {
        drop: ['console', 'debugger'], // Usuwa console.log i debugger w produkcji
    },
});