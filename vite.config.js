import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1', // 必要に応じて 'localhost' も試してください
        port: 5173,        // Vite のデフォルトポート
        hmr: {
            host: '127.0.0.1', // HMR 用のホスト
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});


