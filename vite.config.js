import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Definir refreshPaths si es necesario
const refreshPaths = [
    // Agrega aquí las rutas necesarias, por ejemplo:
    'resources/views/**',
    'resources/js/components/**',
];

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
    ],
});
