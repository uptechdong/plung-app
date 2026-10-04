<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan untuk seluruh respons aplikasi.
 *
 * Tujuan:
 * - Halaman HTML (termasuk index) TIDAK boleh di-cache browser/proxy, supaya
 *   data warga & sesi login yang tampil selalu segar dan tidak bisa dibaca
 *   dari cache oleh orang lain.
 * - Asset build (CSS/JS/gambar) boleh di-cache karena namanya sudah ber-hash.
 * - Cegah halaman aplikasi dibungkus iframe (clickjacking) dan dibocorkan
 *   lewat header referer.
 */
class KeamananHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Jangan bocorkan URL aplikasi ke situs lain.
        $response->headers->set('Referrer-Policy', 'no-referrer');

        // Larang aplikasi dibuka di dalam iframe / frame lain.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Batasi jenis MIME yang boleh dieksekusi browser.
        $response->headers->set('X-XSS-Protection', '0');

        // 1. Aset build boleh di-cache lama karena namanya sudah ber-hash.
        if ($this->adalahAsetStatis($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }
        // 2. Halaman HTML: jangan pernah disimpan di cache.
        elseif ($this->adalahHtml($request, $response)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        // 3. Respons API WAJIB no-store. Kalau dibiarkan, browser bisa
        //    menyajikan data lama (mis. daftar hadiah yang id-nya sudah
        //    dihapus) sehingga UI menampilkan data yang tidak sinkron
        //    dengan database.
        elseif ($request->is('api/*') || $request->expectsJson()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    /**
     * True untuk aset statis hasil build Vite (sudah immutable by hash).
     */
    private function adalahAsetStatis(Request $request): bool
    {
        return str_starts_with($request->path(), 'build/');
    }

    /**
     * True kalau respons ini dokumen HTML (bukan aset statis / API JSON).
     */
    private function adalahHtml(Request $request, Response $response): bool
    {
        // Jangan sentuh respons API: isinya JSON, bukan dokumen yang
        // perlu di-cache browser.
        if ($request->is('api/*') || $request->expectsJson()) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}
