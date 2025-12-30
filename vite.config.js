import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss({
            // Pass configuration directly to the plugin
            config: {
                content: [
                    './resources/views/**/*.blade.php',
                    './modules/**/*.blade.php',
                    './modules/**/*.js',
                ],
                // Any other theme/plugin configs can go here
                plugins: [require('daisyui')],
            }
        }),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
