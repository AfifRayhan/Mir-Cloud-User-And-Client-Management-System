import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom-login.css',
                'resources/css/custom-dashboard.css',
                'resources/css/custom-add-customer.css',
                'resources/css/custom-nav.css',
                'resources/css/custom-customer-index.css',
                'resources/css/custom-resource-allocation.css',
                'resources/js/app.js',
                'resources/js/custom-js-styles.js',
                'resources/css/custom-tech-resource.css'
            ],
            refresh: true,
        }),
    ],
});
