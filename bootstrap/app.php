<?php

use App\Http\Middleware\KeamananHeader;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (dan hosting lain di belakang reverse proxy) mengirim
        // X-Forwarded-Proto: https, tapi $_SERVER['HTTPS'] tidak di-set.
        // Tanpa trustProxies, UrlGenerator menganggap request-nya http dan
        // membuat URL aset jadi http://, yang diblokir browser sebagai mixed
        // content sehingga CSS tidak termuat sama sekali.
        $middleware->trustProxies(at: '*');

        $middleware->append(KeamananHeader::class);

        $middleware->redirectGuestsTo(fn (): ?string => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $pesan404 = fn () => response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan. Kemungkinan data sudah dihapus atau diubah oleh pengguna lain. Daftar akan dimuat ulang.',
        ], 404);

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($pesan404) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $pesan404();
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($pesan404) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $pesan404();
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi berakhir. Silakan masuk kembali.',
                ], 401);
            }
        });
    })->create();
