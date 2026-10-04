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

        $response->headers->set('Referrer-Policy', 'no-referrer');

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        $response->headers->set('X-XSS-Protection', '0');

        if ($this->adalahAsetStatis($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }
        elseif ($this->adalahHtml($request, $response)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
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
        if ($request->is('api/*') || $request->expectsJson()) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}
