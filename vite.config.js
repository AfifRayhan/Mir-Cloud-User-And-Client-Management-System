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
                'resources/css/custom-tech-resource.css',
                'resources/css/custom-kam-task-management.css',
                'resources/css/custom-billing-task-management.css',
                // Extracted view-specific JS files
                'resources/views/resource-allocation/resource-allocation.js',
                'resources/views/tech-resource-allocation/tech-resource-allocation.js',
                'resources/views/my-tasks/my-tasks.js',
                'resources/views/task-management/task-management.js',
                'resources/views/task-management/kam-task-management.js',
                'resources/views/task-management/billing-task-management.js',
                'resources/views/customers/customer-edit.js',
                'resources/views/customers/customer-show.js',
                'resources/views/users/user-form.js',
            ],
            refresh: true,
        }),
    ],
});

