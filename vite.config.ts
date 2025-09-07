import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { execSync } from 'child_process';
import { defineConfig } from 'vite';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        {
            name: 'ziggy-generate',
            handleHotUpdate({ file }) {
              if (file.endsWith('.php') && file.includes('/routes/')) {
                execSync('php artisan ziggy:generate resources/js/ziggyGenerated.js', {
                  stdio: 'inherit',
                });
              }
            },
        },
        react(),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    esbuild: {
        jsx: 'automatic',
    },
});
