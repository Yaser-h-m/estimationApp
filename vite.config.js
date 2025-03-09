import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        port: 5173, // Port to use
        cors: {
          origin: 'https://www.estimationapp-dev.com', // Add the allowed origin
          methods: ['GET', 'POST', 'PUT', 'DELETE'], // You can add specific methods
          allowedHeaders: ['Content-Type', 'Authorization'], // Allow headers
        },
      },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
