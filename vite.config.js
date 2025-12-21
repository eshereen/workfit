import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/checkout.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    build: {
        // Enhanced minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.logs in production
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
        },
        // Generate source maps for debugging (set to false for smaller builds)
        sourcemap: false,
        // Chunk size warnings
        chunkSizeWarningLimit: 1000,
        // Code splitting optimization
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor code
                    'vendor': ['alpinejs', 'axios'],
                },
            },
        },
    },
    // CSS optimization
    css: {
        devSourcemap: false,
    },
});
