import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Write build/manifest.json (not build/.vite/manifest.json) so
        // Laravel's @vite directive finds it without extra config.
        manifest: 'manifest.json',
    },
});
