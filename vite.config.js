import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        minify: 'esbuild', // Changed from terser to esbuild
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
            },
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5.x still uses the legacy @import API.
                // Silence deprecation warnings from dependencies until
                // Bootstrap ships a @use-based version.
                quietDeps: true,
                silenceDeprecations: [
                    'import',
                    'global-builtin',
                    'color-functions',
                    'if-function',
                ],
            },
        },
    },
});
