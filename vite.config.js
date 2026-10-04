import { defineConfig } from 'vite';
import { networkInterfaces } from 'node:os';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

/**
 * Cari IP LAN (bukan 127.0.0.1 / IPv6) supaya Vite menulis URL hot-file
 * yang bisa dibuka perangkat lain. Kalau tidak ketemu, jatuh ke 'localhost'.
 */
function detectLanHost() {
    if (process.env.VITE_LAN_HOST) return process.env.VITE_LAN_HOST;

    for (const addresses of Object.values(networkInterfaces())) {
        for (const address of addresses ?? []) {
            const isUsable = address.family === 'IPv4' && !address.internal;
            if (isUsable) return address.address;
        }
    }

    return 'localhost';
}

const lanHost = detectLanHost();

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // Wajib: tanpa ini hot-file berisi http://0.0.0.0:5173 dan
        // perangkat lain di jaringan tidak bisa memuat aset.
        hmr: { host: lanHost },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
