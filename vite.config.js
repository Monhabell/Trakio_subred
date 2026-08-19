import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        manifest: true,
        outDir: 'public/build',  // Mantén la salida en la carpeta build
        target: 'esnext'
    },
    plugins: [
        laravel({
            input: [
                // Archivos CSS
                'resources/css/adm/main-adm.css',
                'resources/css/adm/shell.css',
                'resources/css/dig/shell.css',
                'resources/css/dig/components.css',
                'resources/css/dig/main-dig.css',
                'resources/css/members/shell.css',
                'resources/css/members/home.css',
                'resources/css/members/tool-control.css',
                'resources/css/members/main-members.css',
                'resources/css/auth/register.css',
                'resources/css/auth/inx_new.css',
                'resources/css/shared/firma.css',
                'resources/css/env/shell.css',
                'resources/css/env/components.css',
                'resources/css/env/main-env.css',
                'resources/css/env/review-files.css',
                'resources/css/env/prepare-delivery.css',
                'resources/css/env/traceability.css',
                'resources/css/global.css',
                'resources/css/validators/style.css',
                'resources/css/odin/odin.css',

                // Archivos JavaScript
                'resources/js/adm/main-adm.js',
                'resources/js/env/main-env.js',
                'resources/js/members/main-members.js',
                'resources/js/documents/main-docs.js',
                'resources/js/assets/profile.js',
                'resources/js/documents/firmas.js',
                'resources/js/index/main-index.js',
                'resources/js/dig/main-dig.js',
                'resources/js/dig/productivity.js',
                'resources/js/chatbot/index.js',
                'resources/js/nav/sb-admin-2.js',
                'resources/js/env/dashboard.js',
                'resources/js/env/review-files.js',
                'resources/js/env/review-files-edit.js',
                'resources/js/validators/script.js'
            ],
            refresh: true,
            buildDirectory: 'build',
        })
    ],
});