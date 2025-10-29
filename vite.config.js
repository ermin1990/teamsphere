import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'lodash'], // Split vendor libraries
                    livewire: ['@livewire/livewire'], // Split Livewire
                },
            },
        },
        chunkSizeWarningLimit: 1000, // Increase chunk size warning limit
    },
});
