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
        // Header keamanan + larangan cache HTML, dipasang di semua route.
        $middleware->append(KeamananHeader::class);

        // App ini SPA tanpa route /login. Default Laravel memanggil
        // route('login') untuk request yang tidak expectsJson() (mis. dibuka
        // langsung di address bar, atau scanner), dan karena route itu tidak
        // ada hasilnya RouteNotFoundException -> HTTP 500 beserta stack trace
        // penuh yang menulis belasan KB ke laravel.log.
        //
        // Kembalikan null supaya Laravel melempar AuthenticationException yang
        // di-handle sebagai 401 JSON -- respons yang benar dan tidak membocorkan
        // detail internal. Request dari aplikasi tidak terpengaruh karena
        // apiFetch selalu mengirim header Accept: application/json.
        $middleware->redirectGuestsTo(fn (): ?string => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Route model binding gagal (mis. id hadiah sudah dihapus) -> balas
        // JSON 404 yang bersih, jangan sampai detail internal database
        // ("No query results for model ...") bocor ke user.
        //
        // Di-register untuk DUA jenis exception karena route model binding
        // membungkus ModelNotFoundException jadi NotFoundHttpException.
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

        // Request tanpa token ke endpoint terproteksi.
        //
        // Authenticate sudah melempar AuthenticationException dengan redirect
        // null (lihat redirectGuestsTo di withMiddleware). Tanpa handler ini,
        // Handler bawaan akan fallback ke route('login') yang tidak ada di app
        // ini -> RouteNotFoundException -> HTTP 500 + stack trace penuh di log.
        //
        // Dibalas sebagai 401 JSON: status yang benar, dan tidak membocorkan
        // detail internal. Halaman login tidak terpengaruh karena /api/login
        // tidak memakai middleware auth.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi berakhir. Silakan masuk kembali.',
                ], 401);
            }
        });
    })->create();
