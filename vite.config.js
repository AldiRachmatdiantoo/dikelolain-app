import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Only the VCMS editor is bundled. The public site keeps its template CSS/JS in public/,
// so no Tailwind here: its reset would break the template styling.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/vcms.css', 'resources/js/vcms/editor.js'],
            refresh: true,
        }),
    ],
});
